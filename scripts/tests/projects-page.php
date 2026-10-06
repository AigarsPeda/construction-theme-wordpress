<?php
/**
 * Run with WP-CLI eval-file on construction.local. Temporary page removed on exit.
 */

if ( get_option( 'home' ) !== 'http://construction.local' ) {
	WP_CLI::error( 'Run this regression check on construction.local only.' );
}

$fixture = 0;
$original_post = $GLOBALS['post'] ?? null;
$failures = array();
$checks = 0;
$check = static function ( bool $passed, string $message ) use ( &$failures, &$checks ): void {
	$checks++;
	if ( ! $passed ) $failures[] = $message;
};
$render = static function ( int $page_id ): string {
	$block = new WP_Block(
		array(
			'blockName' => 'construction/projects-grid',
			'attrs' => array( 'align' => 'full', 'intro' => 'Projekti' ),
			'innerBlocks' => array(),
			'innerHTML' => '',
			'innerContent' => array(),
		),
		array( 'postId' => $page_id )
	);
	return $block->render();
};

try {
	foreach ( construction_get_projects_page_ids() as $lang => $page_id ) {
		$html = $render( $page_id );
		$expected = '<h1 class="construction-projects__title">' . esc_html( get_the_title( $page_id ) ) . '</h1>';
		$check( str_contains( $html, $expected ), $lang . ' heading must match the saved admin page title.' );
		$check( ! str_contains( $html, 'construction-projects__intro' ), $lang . ' must omit the old introduction even with legacy attributes.' );
		$seed = parse_blocks( construction_projects_page_content_for_lang( $lang ) );
		$check( ! isset( $seed[0]['attrs']['intro'] ), 'New ' . $lang . ' pages must not seed an introduction.' );
	}

	$fixture = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'draft', 'post_title' => 'Admin projects & updates' ), true );
	if ( is_wp_error( $fixture ) ) WP_CLI::error( $fixture->get_error_message() );
	// A different global post must not override the page supplied through block context.
	$GLOBALS['post'] = get_post( get_option( 'page_on_front' ) );
	preg_match( '/<h1 class="construction-projects__title">(.*?)<\/h1>/', $render( $fixture ), $heading );
	$check( isset( $heading[1] ) && html_entity_decode( $heading[1], ENT_QUOTES | ENT_HTML5, 'UTF-8' ) === 'Admin projects & updates' && ! str_contains( $heading[1], '& updates' ), 'Heading must use block context and escape the admin title.' );
	wp_update_post( array( 'ID' => $fixture, 'post_title' => 'Changed in admin' ) );
	$check( str_contains( $render( $fixture ), '>Changed in admin</h1>' ), 'Changing the saved page title must immediately change the rendered heading.' );
	$check( ! str_contains( $render( $fixture ), '>Projekti</p>' ), 'Legacy Projekti text must not appear under the heading.' );
} finally {
	if ( is_int( $fixture ) && $fixture > 0 ) wp_delete_post( $fixture, true );
	$GLOBALS['post'] = $original_post;
}

if ( $failures ) WP_CLI::error( implode( "\n", $failures ) );
WP_CLI::success( $checks . ' projects page checks passed: admin titles, context, edits, escaping and removed introduction.' );
