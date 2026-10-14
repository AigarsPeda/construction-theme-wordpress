<?php
/** Explicit Local-only privacy page setup. Existing authored content is preserved. */
if ( 'construction.local' !== wp_parse_url( home_url(), PHP_URL_HOST ) ) {
	WP_CLI::error( 'This setup is only for construction.local.' );
}
$copy_path = $args[0] ?? dirname( __DIR__ ) . '/content/privacy-policies.json';
$copy = json_decode( file_get_contents( $copy_path ), true );
if ( ! is_array( $copy ) || count( $copy ) !== 3 ) {
	WP_CLI::error( 'Pass the LV/EN/RU policy JSON file.' );
}
$options = construction_privacy_options();
$ids = array();
foreach ( $copy as $lang => $page ) {
	$key = 'page_' . $lang;
	if ( array_key_exists( $key, $options ) ) {
		$ids[ $lang ] = (int) $options[ $key ];
		continue;
	}
	$existing = get_posts( array( 'post_type' => 'page', 'post_status' => array( 'publish', 'draft', 'private', 'trash' ), 'meta_key' => '_construction_privacy_language', 'meta_value' => $lang, 'numberposts' => 1 ) );
	if ( $existing ) {
		$id = (int) $existing[0]->ID;
	} else {
		$inner = serialize_block( array( 'blockName' => 'core/heading', 'attrs' => array( 'level' => 1 ), 'innerBlocks' => array(), 'innerHTML' => '<h1 class="wp-block-heading">' . esc_html( $page['title'] ) . '</h1>', 'innerContent' => array( '<h1 class="wp-block-heading">' . esc_html( $page['title'] ) . '</h1>' ) ) );
		foreach ( $page['sections'] as list( $heading, $text ) ) {
			$inner .= '<!-- wp:heading --><h2 class="wp-block-heading">' . esc_html( $heading ) . '</h2><!-- /wp:heading -->';
			$inner .= '<!-- wp:paragraph --><p>' . wp_kses_post( $text ) . '</p><!-- /wp:paragraph -->';
		}
		$content = '<!-- wp:group {"className":"construction-policy","layout":{"type":"default"}} --><div class="wp-block-group construction-policy">' . $inner . '</div><!-- /wp:group -->';
		$id = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => $page['title'], 'post_name' => $page['slug'], 'post_content' => $content ), true );
		if ( is_wp_error( $id ) ) { WP_CLI::error( $id->get_error_message() ); }
		update_post_meta( $id, '_construction_privacy_language', $lang );
		update_post_meta( $id, 'rank_math_robots', array( 'noindex', 'follow' ) );
	}
	pll_set_post_language( $id, $lang );
	$ids[ $lang ] = $id;
	$options[ $key ] = $id;
}
if ( ! in_array( 0, $ids, true ) ) { pll_save_post_translations( $ids ); }
$options += array( 'enabled' => false, 'measurement_id' => '' );
update_option( 'construction_privacy', $options );
if ( ! get_option( 'wp_page_for_privacy_policy' ) && ! empty( $ids['lv'] ) ) {
	update_option( 'wp_page_for_privacy_policy', $ids['lv'] );
}
\RankMath\Sitemap\Cache::invalidate_storage();
WP_CLI::success( 'Local privacy previews ready: ' . wp_json_encode( $ids ) );
