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

define( 'CONSTRUCTION_VERSION', '0.3.8' );

require get_template_directory() . '/inc/i18n.php';
require get_template_directory() . '/inc/images.php';
require get_template_directory() . '/inc/homepage-content.php';

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
		'gsap',
		'https://cdn.jsdelivr.net/npm/gsap@3.12.7/dist/gsap.min.js',
		array(),
		'3.12.7',
		true
	);

	wp_enqueue_script(
		'construction-main',
		get_template_directory_uri() . '/assets/js/main.js',
		array( 'gsap' ),
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
 * Rebuild LV/EN/RU homepages from scratch (admin URL or one-time key).
 *
 * Admin: /wp-admin/?construction_rebuild_homes=1
 * Or one-time: /?construction_rebuild_homes_key=KEY
 */
function construction_admin_rebuild_homes(): void {
	$by_admin = is_admin()
		&& current_user_can( 'manage_options' )
		&& isset( $_GET['construction_rebuild_homes'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	$key       = (string) get_option( 'construction_rebuild_key', '' );
	$by_key    = $key !== ''
		&& isset( $_GET['construction_rebuild_homes_key'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		&& hash_equals( $key, (string) wp_unslash( $_GET['construction_rebuild_homes_key'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( ! $by_admin && ! $by_key ) {
		return;
	}

	$result = construction_rebuild_polylang_homes();
	if ( is_wp_error( $result ) ) {
		wp_die( esc_html( $result->get_error_message() ) );
	}

	delete_option( 'construction_rebuild_key' );
	flush_rewrite_rules( false );
	delete_option( 'construction_flush_rewrites' );

	if ( $by_admin ) {
		wp_safe_redirect( admin_url( 'edit.php?post_type=page&construction_homes_ready=1' ) );
		exit;
	}

	wp_safe_redirect( home_url( '/?construction_homes_ready=1' ) );
	exit;
}
add_action( 'admin_init', 'construction_admin_rebuild_homes' );
add_action( 'init', 'construction_admin_rebuild_homes', 5 );

/**
 * Rebuild menus only (admin URL or one-time key).
 *
 * Admin: /wp-admin/?construction_rebuild_menus=1
 */
function construction_admin_rebuild_menus(): void {
	$by_admin = is_admin()
		&& current_user_can( 'manage_options' )
		&& isset( $_GET['construction_rebuild_menus'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	$key    = (string) get_option( 'construction_rebuild_menus_key', '' );
	$by_key = $key !== ''
		&& isset( $_GET['construction_rebuild_menus_key'] ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		&& hash_equals( $key, (string) wp_unslash( $_GET['construction_rebuild_menus_key'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( ! $by_admin && ! $by_key ) {
		return;
	}

	construction_rebuild_language_menus();
	delete_option( 'construction_rebuild_menus_key' );

	if ( $by_admin ) {
		wp_safe_redirect( admin_url( 'nav-menus.php?construction_menus_ready=1' ) );
		exit;
	}

	wp_safe_redirect( home_url( '/?construction_menus_ready=1' ) );
	exit;
}
add_action( 'admin_init', 'construction_admin_rebuild_menus' );
add_action( 'init', 'construction_admin_rebuild_menus', 6 );

/**
 * Notice after successful rebuild.
 */
function construction_homes_ready_notice(): void {
	if ( ! isset( $_GET['construction_homes_ready'] ) && ! isset( $_GET['construction_menus_ready'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	echo '<div class="notice notice-success is-dismissible"><p>';
	if ( isset( $_GET['construction_menus_ready'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		echo esc_html__( 'Menus ready: Primary LV / EN / RU. Edit them under Appearance → Menus.', 'construction' );
	} else {
		echo esc_html__( 'Homepages rebuilt: Sākums (LV), Home (EN), Главная (RU). Open each page — text matches that language. Use LV/EN/RU on the site to switch.', 'construction' );
	}
	echo '</p></div>';
}
add_action( 'admin_notices', 'construction_homes_ready_notice' );

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

/**
 * One-time permalink flush.
 */
function construction_maybe_flush_rewrites(): void {
	if ( ! get_option( 'construction_flush_rewrites' ) ) {
		return;
	}

	flush_rewrite_rules( false );
	delete_option( 'construction_flush_rewrites' );
}
add_action( 'init', 'construction_maybe_flush_rewrites', 99 );
