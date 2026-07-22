<?php
/**
 * BūvNams theme functions.
 *
 * @package BuvNams
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BUVNAMS_VERSION', '0.1.0' );

/**
 * Theme setup.
 */
function buvnams_setup(): void {
	load_theme_textdomain( 'buvnams', get_template_directory() . '/languages' );

	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/main.css' );

	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'buvnams' ),
		)
	);
}
add_action( 'after_setup_theme', 'buvnams_setup' );

/**
 * Enqueue front-end assets.
 */
function buvnams_enqueue_assets(): void {
	wp_enqueue_style(
		'buvnams-fonts',
		'https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,400;0,500;0,600;1,400&family=Syne:wght@500;600;700;800&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'buvnams-main',
		get_template_directory_uri() . '/assets/css/main.css',
		array( 'buvnams-fonts' ),
		BUVNAMS_VERSION
	);

	wp_enqueue_script(
		'buvnams-main',
		get_template_directory_uri() . '/assets/js/main.js',
		array(),
		BUVNAMS_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'buvnams_enqueue_assets' );

/**
 * Register pattern category.
 */
function buvnams_register_pattern_categories(): void {
	register_block_pattern_category(
		'buvnams',
		array(
			'label' => __( 'BūvNams', 'buvnams' ),
		)
	);
}
add_action( 'init', 'buvnams_register_pattern_categories' );

/**
 * Translate helper that prefers Polylang when available.
 *
 * @param string $text Text to translate.
 */
function buvnams_t( string $text ): string {
	if ( function_exists( 'pll__' ) ) {
		return pll__( $text );
	}

	return __( $text, 'buvnams' );
}

/**
 * Register theme strings with Polylang (when plugin is active).
 */
function buvnams_register_polylang_strings(): void {
	if ( ! function_exists( 'pll_register_string' ) ) {
		return;
	}

	$strings = array(
		'Your house — your rules',
		'Take the quiz',
		'Building frame houses since 2008',
		'We handle design, planning, and installation',
		'Fast timelines and high-quality work',
		'The main measure of our quality is a satisfied client',
		'Projects',
		'Photos',
		'About us',
		'Get in touch',
	);

	foreach ( $strings as $string ) {
		pll_register_string( 'buvnams_' . sanitize_title( $string ), $string, 'BūvNams' );
	}
}
add_action( 'init', 'buvnams_register_polylang_strings' );
