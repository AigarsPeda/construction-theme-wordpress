<?php
/**
 * SEO basics: document titles, meta description, Open Graph.
 *
 * @package Construction
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Site brand name for titles.
 */
function construction_seo_brand(): string {
	$name = get_bloginfo( 'name' );
	return $name !== '' ? $name : 'Construction';
}

/**
 * Meta description for the current view.
 */
function construction_seo_description(): string {
	if ( is_front_page() ) {
		return construction_t( 'seo.home.desc' );
	}

	if ( is_page() ) {
		$slug = get_post_field( 'post_name', get_queried_object_id() );
		if ( in_array( $slug, array( 'projekti', 'projects', 'proekty' ), true ) ) {
			return construction_t( 'seo.projects.desc' );
		}

		$excerpt = get_the_excerpt();
		if ( is_string( $excerpt ) && $excerpt !== '' ) {
			return wp_strip_all_tags( $excerpt );
		}
	}

	$tagline = get_bloginfo( 'description' );
	return $tagline !== '' ? $tagline : construction_t( 'seo.home.desc' );
}

/**
 * Absolute image URL for Open Graph.
 */
function construction_seo_image(): string {
	if ( is_page() ) {
		$slug = get_post_field( 'post_name', get_queried_object_id() );
		if ( in_array( $slug, array( 'projekti', 'projects', 'proekty' ), true ) ) {
			$url = construction_project_image_url( 'project_1' );
			if ( $url !== '' ) {
				return $url;
			}
		}
	}

	$url = construction_image_url( 'hero' );
	return $url !== '' ? $url : '';
}

/**
 * Improve <title> for home and projects.
 *
 * @param array<string, string> $parts Title parts.
 * @return array<string, string>
 */
function construction_document_title_parts( array $parts ): array {
	$brand = construction_seo_brand();

	if ( is_front_page() ) {
		$parts['title'] = construction_t( 'seo.home.title' );
		$parts['site']  = $brand;
		return $parts;
	}

	if ( is_page() ) {
		$slug = get_post_field( 'post_name', get_queried_object_id() );
		if ( in_array( $slug, array( 'projekti', 'projects', 'proekty' ), true ) ) {
			$parts['title'] = construction_t( 'seo.projects.title' );
			$parts['site']  = $brand;
		}
	}

	return $parts;
}
add_filter( 'document_title_parts', 'construction_document_title_parts' );

/**
 * Print meta description + Open Graph tags.
 */
function construction_seo_head(): void {
	$desc  = construction_seo_description();
	$title = wp_get_document_title();
	$url   = is_singular() ? (string) get_permalink() : home_url( '/' );
	$image = construction_seo_image();
	$lang  = construction_current_lang();
	$og_locale = array(
		'lv' => 'lv_LV',
		'en' => 'en_US',
		'ru' => 'ru_RU',
	)[ $lang ] ?? 'lv_LV';

	echo '<meta name="description" content="' . esc_attr( $desc ) . '" />' . "\n";
	echo '<meta property="og:type" content="' . esc_attr( is_front_page() ? 'website' : 'article' ) . '" />' . "\n";
	echo '<meta property="og:site_name" content="' . esc_attr( construction_seo_brand() ) . '" />' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $desc ) . '" />' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $url ) . '" />' . "\n";
	echo '<meta property="og:locale" content="' . esc_attr( $og_locale ) . '" />' . "\n";
	if ( $image !== '' ) {
		echo '<meta property="og:image" content="' . esc_url( $image ) . '" />' . "\n";
	}
	echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '" />' . "\n";
	echo '<meta name="twitter:description" content="' . esc_attr( $desc ) . '" />' . "\n";
	if ( $image !== '' ) {
		echo '<meta name="twitter:image" content="' . esc_url( $image ) . '" />' . "\n";
	}

	// hreflang alternates for the current singular page (or front page).
	if ( function_exists( 'pll_get_post' ) && function_exists( 'pll_languages_list' ) ) {
		$post_id = is_singular() ? get_queried_object_id() : (int) get_option( 'page_on_front' );
		if ( $post_id > 0 ) {
			foreach ( construction_languages() as $code ) {
				$translated = pll_get_post( $post_id, $code );
				if ( ! $translated ) {
					continue;
				}
				$href = get_permalink( (int) $translated );
				if ( ! is_string( $href ) || $href === '' ) {
					continue;
				}
				echo '<link rel="alternate" hreflang="' . esc_attr( $code ) . '" href="' . esc_url( $href ) . '" />' . "\n";
			}
			$default = pll_get_post( $post_id, 'lv' );
			if ( $default ) {
				$href = get_permalink( (int) $default );
				if ( is_string( $href ) && $href !== '' ) {
					echo '<link rel="alternate" hreflang="x-default" href="' . esc_url( $href ) . '" />' . "\n";
				}
			}
		}
	}
}
add_action( 'wp_head', 'construction_seo_head', 1 );
