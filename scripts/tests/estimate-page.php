<?php
/**
 * Run with WP-CLI eval-file on construction.local. Read-only: checks the seeded
 * cost-estimating (Tāmēšana) pages, their SEO fields and menu items.
 */

if ( get_option( 'home' ) !== 'http://construction.local' ) {
	WP_CLI::error( 'Run this regression check on construction.local only.' );
}

$failures = array();
$checks   = 0;
$check    = static function ( bool $passed, string $message ) use ( &$failures, &$checks ): void {
	$checks++;
	if ( ! $passed ) {
		$failures[] = $message;
	}
};

$definitions = construction_estimate_page_definitions();
$ids         = construction_get_estimate_page_ids();
$check( count( $ids ) === 3, 'LV, EN and RU estimate pages must all exist.' );

if ( function_exists( 'pll_get_post_translations' ) && ! empty( $ids['lv'] ) ) {
	$translations = pll_get_post_translations( $ids['lv'] );
	ksort( $translations );
	$expected = $ids;
	ksort( $expected );
	$check( $translations === $expected, 'Estimate pages must be linked as Polylang translations.' );
}

$flatten = static function ( array $blocks ) use ( &$flatten ): array {
	$out = array();
	foreach ( $blocks as $block ) {
		if ( ! empty( $block['blockName'] ) ) {
			$out[] = $block;
		}
		$out = array_merge( $out, $flatten( $block['innerBlocks'] ?? array() ) );
	}
	return $out;
};

foreach ( $ids as $lang => $page_id ) {
	$post = get_post( $page_id );
	$copy = $definitions[ $lang ];
	$html = (string) $post->post_content;

	$check( $post->post_name === $copy['slug'], "$lang slug must be {$copy['slug']}." );
	$check( function_exists( 'pll_get_post_language' ) && pll_get_post_language( $page_id ) === $lang, "$lang page must carry its Polylang language." );
	$check( substr_count( $html, '<h1' ) === 1, "$lang page must have exactly one H1." );
	preg_match( '/<h1[^>]*>(.*?)<\/h1>/s', $html, $h1 );
	$h1_text = trim( (string) preg_replace( '/\s+/', ' ', wp_strip_all_tags( str_replace( '<br>', ' ', $h1[1] ?? '' ) ) ) );
	$check( $h1_text === $copy['title'], "$lang H1 must read as the page title." );
	$check( str_contains( $html, 'construction-estimate-page__panel--private' ) && str_contains( $html, 'construction-estimate-page__panel--business' ), "$lang page must contain both halves." );
	$blocks = $flatten( parse_blocks( $html ) );
	$covers = array_values( array_filter( $blocks, static fn( $b ) => 'core/cover' === $b['blockName'] ) );
	$cover  = $covers[0] ?? array( 'attrs' => array(), 'innerBlocks' => array(), 'innerHTML' => '' );
	$hero   = construction_media_id( 'estimate_hero' );
	$alt    = trim( (string) ( $cover['attrs']['alt'] ?? '' ) );
	$check( count( $covers ) === 1 && $hero > 0 && (int) ( $cover['attrs']['id'] ?? 0 ) === $hero, "$lang hero must be one Cover block showing the Media Library hero photo." );
	$check( '' !== $alt && str_contains( (string) $cover['innerHTML'], 'alt="' . esc_attr( $alt ) . '"' ), "$lang hero photo must have alt text." );
	$check( 'mist-veil' === ( $cover['attrs']['gradient'] ?? '' ) && 'var:preset|duotone|greyscale' === ( $cover['attrs']['style']['color']['duotone'] ?? '' ), "$lang hero must use the Mist veil overlay and Greyscale filter presets." );
	$check( in_array( 'construction-estimate-page__title', array_map( static fn( $b ) => (string) ( $b['attrs']['className'] ?? '' ), $cover['innerBlocks'] ), true ), "$lang H1 must sit inside the hero." );
	$check( substr_count( $html, 'wp-block-image' ) === 0 && ! str_contains( $html, 'alt=""' ), "$lang halves must not repeat photo strips, and no image may lack alt text." );

	$contact = construction_contacts_url_for_lang( $lang );
	$check( substr_count( $html, 'href="' . esc_url( $contact ) . '"' ) === 2, "$lang buttons must link to the $lang contacts page." );

	foreach ( $blocks as $block ) {
		$check( ! str_starts_with( (string) $block['blockName'], 'core/missing' ), "$lang page must not contain unknown blocks." );
	}

	foreach ( array( 'rank_math_title', 'rank_math_description', 'rank_math_focus_keyword' ) as $meta_key ) {
		$check( (string) get_post_meta( $page_id, $meta_key, true ) !== '', "$lang page must have $meta_key." );
	}
	$check( (int) get_post_thumbnail_id( $page_id ) === $hero, "$lang featured image (social previews) must be the hero photo." );
	if ( 'lv' === $lang ) {
		$check( false !== mb_stripos( (string) get_post_meta( $page_id, 'rank_math_description', true ), explode( ',', (string) $copy['seo']['keywords'] )[0] ), 'LV meta description must contain the focus keyword.' );
	}
}

// The seed produces the same hero for a fresh install.
$seeded = $flatten( parse_blocks( construction_estimate_page_content( 'lv' ) ) );
$check( 1 === count( array_filter( $seeded, static fn( $b ) => 'core/cover' === $b['blockName'] && (int) ( $b['attrs']['id'] ?? 0 ) === construction_media_id( 'estimate_hero' ) ) ), 'Seeded LV content must contain the hero Cover block.' );

$gradients = wp_list_pluck( (array) wp_get_global_settings( array( 'color', 'gradients', 'theme' ) ), 'slug' );
$duotones  = wp_list_pluck( (array) wp_get_global_settings( array( 'color', 'duotone', 'theme' ) ), 'slug' );
$check( in_array( 'mist-veil', $gradients, true ) && in_array( 'greyscale', $duotones, true ), 'theme.json must offer the Mist veil gradient and Greyscale duotone presets in the editor.' );

$menus = array(
	'lv' => 'Primary LV',
	'en' => 'Primary EN',
	'ru' => 'Primary RU',
);
foreach ( $menus as $lang => $menu_name ) {
	$items = wp_get_nav_menu_items( $menu_name );
	$found = 0;
	foreach ( is_array( $items ) ? $items : array() as $item ) {
		if ( 'page' === $item->object && (int) $item->object_id === (int) ( $ids[ $lang ] ?? 0 ) ) {
			++$found;
		}
	}
	$check( 1 === $found, "$menu_name must link the estimate page exactly once." );
}

$media = construction_media_ids();
foreach ( construction_estimate_page_image_keys() as $key ) {
	$check( ! empty( $media[ $key ] ) && get_post( (int) $media[ $key ] ), "Media Library must hold the $key image." );
}

if ( $failures ) {
	WP_CLI::error( implode( "\n", $failures ) );
}
WP_CLI::success( $checks . ' estimate page checks passed: pages, translations, markup, links, SEO fields, menus and media.' );
