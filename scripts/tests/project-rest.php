<?php
/**
 * Run with WP-CLI eval-file on construction.local. Fixtures are deleted on exit.
 */

if ( get_option( 'home' ) !== 'http://construction.local' ) {
	WP_CLI::error( 'Run this regression check on construction.local only.' );
}

if ( ! defined( 'REST_REQUEST' ) ) {
	define( 'REST_REQUEST', true );
}
$admins = get_users( array( 'role' => 'administrator', 'number' => 1 ) );
wp_set_current_user( $admins[0]->ID );

$fixtures = array();
$failures = array();
$checks = 0;
$check = static function ( bool $passed, string $message ) use ( &$failures, &$checks ): void {
	++$checks;
	if ( ! $passed ) {
		$failures[] = $message;
		WP_CLI::warning( $message );
	}
};
$save = static function ( int $id, array $data ): WP_REST_Response {
	$request = new WP_REST_Request( 'POST', '/wp/v2/construction-projects/' . $id );
	$request->set_body_params( $data );
	return rest_do_request( $request );
};

try {
	$seed = array(
		'lv' => array( 'title' => 'Old LV title', 'excerpt' => '<!-- wp:paragraph --><p>Old LV body</p><!-- /wp:paragraph -->' ),
		'en' => array( 'title' => 'Old EN title', 'excerpt' => '<h2>English heading</h2><p>English body</p>' ),
		'ru' => array( 'title' => 'Old RU title', 'excerpt' => 'Old RU body' ),
	);
	$id = wp_insert_post( array(
		'post_type' => CONSTRUCTION_PROJECT_POST_TYPE,
		'post_status' => 'publish',
		'post_title' => $seed['lv']['title'],
		'post_content' => $seed['lv']['excerpt'],
		'post_name' => 'regression-' . wp_generate_uuid4(),
	) );
	$fixtures[] = $id;
	update_post_meta( $id, CONSTRUCTION_PROJECT_I18N_META, $seed );
	// A prior full-editor language selection must not affect quick edits.
	update_post_meta( $id, CONSTRUCTION_PROJECT_EDIT_LANG_META, 'ru' );
	$edited = $seed;
	$edited['lv'] = array( 'title' => 'New LV title', 'excerpt' => 'New LV description' );
	$slug = 'regression-edited-' . $id;
	$response = $save( $id, array( 'slug' => $slug, 'i18n' => $edited ) );
	$check( $response->get_status() === 200, 'Quick edit should succeed.' );
	$check( construction_get_project_i18n( $id ) === $edited, 'Quick edit must save LV text and preserve EN/RU including rich markup.' );
	$check( $response->get_data()['i18n'] === $edited, 'Save response must contain the submitted translations.' );
	$post = get_post( $id );
	$check( $post->post_title === $edited['lv']['title'] && $post->post_content === $edited['lv']['excerpt'], 'Latvian post fields must match quick-edit translations.' );
	$check( $post->post_name === $slug, 'Unique share-link slug must persist.' );
	foreach ( construction_languages() as $lang ) {
		$html = construction_render_project_grid_card( $post, $lang );
		$check( strpos( $html, $edited[$lang]['title'] ) !== false && strpos( $html, $edited[$lang]['excerpt'] ) !== false, 'Rendered card must use saved ' . $lang . ' title and description.' );
	}

	$response = $save( $id, array( 'slug' => $slug . '-only' ) );
	$check( $response->get_status() === 200 && construction_get_project_i18n( $id ) === $edited, 'Slug-only save must preserve all translations.' );

	$edited['en'] = array( 'title' => 'New EN title', 'excerpt' => 'New EN description' );
	$edited['ru'] = array( 'title' => 'New RU title', 'excerpt' => 'New RU description' );
	$save( $id, array( 'i18n' => $edited ) );
	$check( construction_get_project_i18n( $id ) === $edited, 'Quick edit must save EN/RU descriptions too.' );
	$edited['lv']['excerpt'] = '';
	$save( $id, array( 'i18n' => $edited ) );
	$check( construction_get_project_i18n( $id ) === $edited && get_post( $id )->post_content === '', 'Clearing the LV description must remove the old fallback content.' );

	// Main block editor saves post fields and registered meta, not the i18n REST field.
	$edited['en'] = array( 'title' => 'Block EN title', 'excerpt' => '<!-- wp:heading --><h2>Block EN body</h2><!-- /wp:heading -->' );
	$response = $save( $id, array(
		'title' => $edited['en']['title'],
		'content' => $edited['en']['excerpt'],
		'meta' => array(
			CONSTRUCTION_PROJECT_I18N_META => $edited,
			CONSTRUCTION_PROJECT_EDIT_LANG_META => 'en',
		),
	) );
	$check( $response->get_status() === 200 && construction_get_project_i18n( $id ) === $edited, 'Full editor must save the requested language without overwriting another translation.' );
	$check( get_post( $id )->post_content === $edited['lv']['excerpt'] && get_post( $id )->post_title === $edited['lv']['title'], 'Full editor must keep canonical post fields in LV.' );
	$save( $id, array( 'content' => '' ) );
	$edited['en']['excerpt'] = '';
	$check( construction_get_project_i18n( $id ) === $edited, 'Full editor must support clearing the active description.' );

	$collision = wp_insert_post( array(
		'post_type' => CONSTRUCTION_PROJECT_POST_TYPE,
		'post_status' => 'draft',
		'post_title' => 'Slug collision fixture',
		'post_name' => 'regression-taken-' . $id,
	) );
	$fixtures[] = $collision;
	$before_slug = get_post( $id )->post_name;
	$response = $save( $id, array( 'slug' => get_post( $collision )->post_name, 'i18n' => $seed ) );
	$check( $response->get_status() === 400, 'Taken slug must return a visible REST error.' );
	$check( get_post( $id )->post_name === $before_slug && construction_get_project_i18n( $id ) === $edited, 'Rejected slug must leave the project unchanged.' );
	$normalized_collision = strtoupper( str_replace( '-', ' ', get_post( $collision )->post_name ) );
	$response = $save( $id, array( 'slug' => $normalized_collision ) );
	$check( $response->get_status() === 400 && get_post( $id )->post_name === $before_slug, 'Equivalent slugs with spaces and uppercase letters must also be rejected.' );
	$duplicate_create = new WP_REST_Request( 'POST', '/wp/v2/construction-projects' );
	$duplicate_create->set_body_params( array( 'title' => 'Duplicate slug fixture', 'status' => 'draft', 'slug' => $normalized_collision ) );
	$response = rest_do_request( $duplicate_create );
	if ( ! empty( $response->get_data()['id'] ) ) {
		$fixtures[] = $response->get_data()['id'];
	}
	$check( $response->get_status() === 400, 'Creating a new project with an existing slug must be rejected.' );
	$response = $save( $id, array( 'slug' => $before_slug ) );
	$check( $response->get_status() === 200 && get_post( $id )->post_name === $before_slug, 'A project must be allowed to keep its own slug.' );
	$response = $save( $id, array( 'slug' => '' ) );
	$check( $response->get_status() === 400, 'Empty share-link slug must return a visible REST error.' );

	$media_ids = get_posts( array( 'post_type' => 'attachment', 'post_mime_type' => 'image', 'numberposts' => 2, 'fields' => 'ids' ) );
	if ( $media_ids ) {
		$response = $save( $id, array( 'featured_media' => $media_ids[0], 'gallery' => $media_ids ) );
		$check( $response->get_status() === 200 && get_post_thumbnail_id( $id ) === $media_ids[0] && construction_get_project_gallery_ids( $id ) === $media_ids, 'Cover and gallery updates must still save.' );
		$check( construction_get_project_i18n( $id ) === $edited, 'Media-only updates must preserve all descriptions.' );
	}

	$request = new WP_REST_Request( 'POST', '/wp/v2/construction-projects' );
	$request->set_body_params( array( 'title' => $seed['lv']['title'], 'status' => 'draft', 'i18n' => $seed ) );
	$response = rest_do_request( $request );
	$new_id = $response->get_data()['id'] ?? 0;
	if ( $new_id ) {
		$fixtures[] = $new_id;
	}
	$check( $response->get_status() === 201 && $new_id && construction_get_project_i18n( $new_id ) === $seed, 'Creating a project must save its initial translations and body.' );
	$check( $new_id && get_post( $new_id )->post_content === $seed['lv']['excerpt'], 'New projects must have canonical LV content immediately.' );
} finally {
	foreach ( $fixtures as $fixture ) {
		wp_delete_post( $fixture, true );
	}
}

if ( $failures ) {
	WP_CLI::error( count( $failures ) . ' of ' . $checks . ' checks failed.' );
}
WP_CLI::success( $checks . ' project REST regression checks passed.' );
