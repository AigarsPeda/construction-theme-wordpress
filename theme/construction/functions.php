<?php
/**
 * Construction theme functions.
 *
 * @package Construction
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CONSTRUCTION_VERSION', '0.1.5' );

require get_template_directory() . '/inc/i18n.php';
require get_template_directory() . '/inc/images.php';

/**
 * Theme setup.
 */
function construction_setup(): void {
	load_theme_textdomain( 'construction', get_template_directory() . '/languages' );

	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/main.css' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'construction' ),
		)
	);
}
add_action( 'after_setup_theme', 'construction_setup' );

/**
 * Enqueue front-end assets.
 */
function construction_enqueue_assets(): void {
	wp_enqueue_style(
		'construction-fonts',
		'https://fonts.googleapis.com/css2?family=Archivo:wght@500;600;700;800&family=Source+Sans+3:ital,wght@0,400;0,500;0,600;1,400&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'construction-main',
		get_template_directory_uri() . '/assets/css/main.css',
		array( 'construction-fonts' ),
		CONSTRUCTION_VERSION
	);

	wp_enqueue_script(
		'construction-main',
		get_template_directory_uri() . '/assets/js/main.js',
		array(),
		CONSTRUCTION_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'construction_enqueue_assets' );

/**
 * Register pattern category.
 */
function construction_register_pattern_categories(): void {
	register_block_pattern_category(
		'construction',
		array(
			'label' => __( 'Construction', 'construction' ),
		)
	);
}
add_action( 'init', 'construction_register_pattern_categories' );

/**
 * Default homepage block content (theme patterns).
 */
function construction_home_page_content(): string {
	$patterns = array(
		'construction/hero',
		'construction/services',
		'construction/quality',
		'construction/reviews',
		'construction/contact',
		'construction/credits',
	);

	$blocks = '';
	foreach ( $patterns as $slug ) {
		$blocks .= '<!-- wp:pattern {"slug":"' . $slug . '"} /-->' . "\n";
	}

	return $blocks;
}

/**
 * Create/assign the homepage under Pages → Sākums.
 */
function construction_ensure_home_page(): void {
	if ( ! is_admin() && 'wp_loaded' === current_action() ) {
		return;
	}

	$existing_id = (int) get_option( 'construction_home_page_id', 0 );
	$page        = $existing_id ? get_post( $existing_id ) : null;

	if ( ! $page || 'page' !== $page->post_type || 'trash' === $page->post_status ) {
		$found = get_posts(
			array(
				'name'           => 'sakums',
				'post_type'      => 'page',
				'post_status'    => array( 'publish', 'draft' ),
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);

		if ( ! empty( $found ) ) {
			$existing_id = (int) $found[0];
			$page        = get_post( $existing_id );
		}
	}

	if ( ! $page ) {
		$existing_id = wp_insert_post(
			array(
				'post_title'   => 'Sākums',
				'post_name'    => 'sakums',
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => construction_home_page_content(),
			),
			true
		);

		if ( is_wp_error( $existing_id ) || ! $existing_id ) {
			return;
		}

		$page = get_post( (int) $existing_id );
	}

	if ( ! $page ) {
		return;
	}

	update_option( 'construction_home_page_id', (int) $page->ID );

	// Keep homepage content synced to theme patterns if still empty.
	if ( '' === trim( (string) $page->post_content ) ) {
		wp_update_post(
			array(
				'ID'           => (int) $page->ID,
				'post_content' => construction_home_page_content(),
			)
		);
	}

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', (int) $page->ID );
}
add_action( 'after_switch_theme', 'construction_ensure_home_page' );
add_action( 'admin_init', 'construction_ensure_home_page' );

/**
 * Admin notice on the homepage edit screen.
 */
function construction_home_page_admin_notice(): void {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'page' !== $screen->id || 'post' !== $screen->base ) {
		return;
	}

	$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$home_id = (int) get_option( 'page_on_front' );

	if ( ! $post_id || $post_id !== $home_id ) {
		return;
	}

	echo '<div class="notice notice-info"><p>';
	echo esc_html__( 'This is your site homepage (index). Sections come from Construction theme patterns (Hero, Services, …). Language text switches with LV / EN / RU.', 'construction' );
	echo '</p></div>';
}
add_action( 'admin_notices', 'construction_home_page_admin_notice' );

/**
 * Mark html lang attribute for current language.
 *
 * @param string $output language_attributes output.
 */
function construction_language_attributes( string $output ): string {
	$lang = construction_current_lang();
	$map  = array(
		'lv' => 'lv',
		'en' => 'en',
		'ru' => 'ru',
	);

	if ( preg_match( '/lang="[^"]*"/', $output ) ) {
		return (string) preg_replace( '/lang="[^"]*"/', 'lang="' . esc_attr( $map[ $lang ] ) . '"', $output );
	}

	return $output . ' lang="' . esc_attr( $map[ $lang ] ) . '"';
}
add_filter( 'language_attributes', 'construction_language_attributes' );
