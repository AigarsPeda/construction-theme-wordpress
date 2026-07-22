<?php
/**
 * Projects gallery page content (LV / EN / RU) + Polylang rebuild.
 *
 * @package Construction
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Front-page URL for a language (for #contact links from other pages).
 */
function construction_front_url_for_lang( string $lang ): string {
	if ( function_exists( 'pll_get_post' ) ) {
		$front_id = (int) get_option( 'page_on_front' );
		if ( $front_id > 0 ) {
			$translated = pll_get_post( $front_id, $lang );
			if ( $translated ) {
				$url = get_permalink( (int) $translated );
				if ( is_string( $url ) && $url !== '' ) {
					return $url;
				}
			}
		}
	}

	if ( function_exists( 'pll_home_url' ) ) {
		$url = pll_home_url( $lang );
		if ( is_string( $url ) && $url !== '' ) {
			return $url;
		}
	}

	return home_url( '/' );
}

/**
 * Block markup for the Projects gallery page in one language.
 */
function construction_projects_page_content_for_lang( string $lang ): string {
	$t = static function ( string $key ) use ( $lang ): string {
		return esc_html( construction_string( $key, $lang ) );
	};

	$items = '';
	foreach ( construction_project_images() as $key => $meta ) {
		$alt_key = isset( $meta['alt_key'] ) ? $meta['alt_key'] : 'projects.title';
		$items  .= construction_media_image_block(
			$key,
			'construction-projects__item',
			construction_string( $alt_key, $lang ),
			'large'
		);
	}

	$contact_cta  = $t( 'projects.cta' );
	$contact_href = esc_url( trailingslashit( construction_front_url_for_lang( $lang ) ) . '#contact' );

	return <<<HTML
<!-- wp:group {"align":"full","className":"construction-projects","layout":{"type":"default"},"anchor":"projects"} -->
<div class="wp-block-group alignfull construction-projects" id="projects">
	<!-- wp:group {"className":"construction-projects__inner","layout":{"type":"default"}} -->
	<div class="wp-block-group construction-projects__inner">
		<!-- wp:paragraph {"className":"construction-eyebrow"} -->
		<p class="construction-eyebrow">{$t( 'projects.eyebrow' )}</p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"level":1,"className":"construction-projects__title"} -->
		<h1 class="wp-block-heading construction-projects__title">{$t( 'projects.title' )}</h1>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"className":"construction-projects__intro"} -->
		<p class="construction-projects__intro">{$t( 'projects.intro' )}</p>
		<!-- /wp:paragraph -->

		<!-- wp:group {"className":"construction-projects__grid","layout":{"type":"default"}} -->
		<div class="wp-block-group construction-projects__grid">
{$items}		</div>
		<!-- /wp:group -->

		<!-- wp:buttons {"className":"construction-projects__actions"} -->
		<div class="wp-block-buttons construction-projects__actions">
			<!-- wp:button -->
			<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="{$contact_href}">{$contact_cta}</a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
HTML;
}

/**
 * Create / refresh linked LV / EN / RU Projects pages.
 *
 * @return array{lv:int,en:int,ru:int}|WP_Error
 */
function construction_rebuild_polylang_projects() {
	if ( ! function_exists( 'pll_set_post_language' ) || ! function_exists( 'pll_save_post_translations' ) ) {
		return new WP_Error( 'no_polylang', 'Polylang is not active.' );
	}

	$media = construction_import_media_library();
	if ( is_wp_error( $media ) ) {
		return $media;
	}
	if ( ! empty( $media['missing'] ) ) {
		return new WP_Error( 'missing_images', 'Missing source images: ' . implode( ', ', $media['missing'] ) );
	}

	$old_ids = array();
	foreach ( array( 'projekti', 'projects', 'proekty' ) as $slug ) {
		$found = get_posts(
			array(
				'name'           => $slug,
				'post_type'      => 'page',
				'post_status'    => array( 'publish', 'draft', 'trash', 'private' ),
				'posts_per_page' => 20,
				'fields'         => 'ids',
			)
		);
		$old_ids = array_merge( $old_ids, $found );
	}

	$title_query = new WP_Query(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'draft', 'trash', 'private' ),
			'posts_per_page' => 50,
			'fields'         => 'ids',
		)
	);
	foreach ( $title_query->posts as $pid ) {
		$title = get_the_title( (int) $pid );
		if ( in_array( $title, array( 'Projekti', 'Projects', 'Проекты' ), true ) ) {
			$old_ids[] = (int) $pid;
		}
	}

	$old_ids = array_unique( array_map( 'intval', $old_ids ) );
	foreach ( $old_ids as $old_id ) {
		wp_delete_post( $old_id, true );
	}

	$defs = array(
		'lv' => array(
			'title'   => 'Projekti',
			'slug'    => 'projekti',
			'content' => construction_projects_page_content_for_lang( 'lv' ),
		),
		'en' => array(
			'title'   => 'Projects',
			'slug'    => 'projects',
			'content' => construction_projects_page_content_for_lang( 'en' ),
		),
		'ru' => array(
			'title'   => 'Проекты',
			'slug'    => 'proekty',
			'content' => construction_projects_page_content_for_lang( 'ru' ),
		),
	);

	$ids = array();
	foreach ( $defs as $lang => $def ) {
		$id = wp_insert_post(
			array(
				'post_title'   => $def['title'],
				'post_name'    => $def['slug'],
				'post_status'  => 'publish',
				'post_type'    => 'page',
				'post_content' => $def['content'],
			),
			true
		);
		if ( is_wp_error( $id ) ) {
			return $id;
		}
		$ids[ $lang ] = (int) $id;
		pll_set_post_language( (int) $id, $lang );
	}

	pll_save_post_translations( $ids );
	update_option( 'construction_projects_page_ids', $ids );
	update_option( 'construction_flush_rewrites', '1' );

	construction_rebuild_language_menus();

	return $ids;
}
