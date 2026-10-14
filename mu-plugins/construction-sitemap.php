<?php
/**
 * Plugin Name: Construction sitemap URLs
 * Description: Uses canonical page permalinks for translated homepages in the Rank Math sitemap.
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'rank_math/sitemap/entry', function ( $url, $type, $post ) {
	if ( 'post' !== $type || ! isset( $post->ID, $post->post_type ) || 'page' !== $post->post_type || ! function_exists( 'pll_get_post_translations' ) ) {
		return $url;
	}

	$front_page = (int) get_option( 'page_on_front' );
	$homepages = array_map( 'intval', pll_get_post_translations( $front_page ) );
	if ( in_array( (int) $post->ID, $homepages, true ) ) {
		$url['loc'] = get_permalink( $post->ID );
	}
	return $url;
}, 99, 3 );
