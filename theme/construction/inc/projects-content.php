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
 * Named project entries for the Projekti page and homepage teaser.
 *
 * @return list<array{slug: string, cover: string, images: list<string>}>
 */
function construction_project_entries(): array {
	return array(
		array(
			'slug'   => 'sloka',
			'cover'  => 'project_1',
			'images' => array( 'project_1', 'project_2', 'project_3' ),
		),
		array(
			'slug'   => 'salaspils',
			'cover'  => 'project_4',
			'images' => array( 'project_4', 'project_5', 'project_6' ),
		),
		array(
			'slug'   => 'adazi',
			'cover'  => 'project_7',
			'images' => array( 'project_7', 'project_8', 'project_9' ),
		),
		array(
			'slug'   => 'kekava',
			'cover'  => 'project_10',
			'images' => array( 'project_10', 'project_11' ),
		),
		array(
			'slug'   => 'limbazi',
			'cover'  => 'project_12',
			'images' => array( 'project_12', 'project_13' ),
		),
	);
}

/**
 * Projects page URL for a language (optional #slug deep link).
 */
function construction_projects_url_for_lang( string $lang, string $slug = '' ): string {
	$ids = construction_get_projects_page_ids();
	$url = '';
	if ( ! empty( $ids[ $lang ] ) ) {
		$permalink = get_permalink( (int) $ids[ $lang ] );
		if ( is_string( $permalink ) && $permalink !== '' ) {
			$url = $permalink;
		}
	}
	if ( $url === '' ) {
		$slugs = array(
			'lv' => 'projekti',
			'en' => 'projects',
			'ru' => 'proekty',
		);
		$path = $slugs[ $lang ] ?? 'projekti';
		if ( function_exists( 'pll_home_url' ) ) {
			$url = trailingslashit( (string) pll_home_url( $lang ) ) . $path . '/';
		} else {
			$url = home_url( '/' . $path . '/' );
		}
	}
	if ( $slug !== '' ) {
		$url = trailingslashit( $url ) . '#' . sanitize_title( $slug );
	}
	return $url;
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
 * Block markup for the Projects page in one language (dynamic projects grid).
 */
function construction_projects_page_content_for_lang( string $lang ): string { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
	return <<<HTML
<!-- wp:construction/projects-grid {"align":"full"} /-->
HTML;
}

/**
 * Create / refresh linked LV / EN / RU Projects pages.
 *
 * Default: create missing only — never overwrite existing DB content.
 * $force = true: delete and reseed (destructive).
 *
 * @return array{lv?:int,en?:int,ru?:int}|WP_Error
 */
function construction_rebuild_polylang_projects( bool $force = false ) {
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

	$existing = construction_get_projects_page_ids();
	if ( ! $force && count( $existing ) === 3 ) {
		update_option( 'construction_projects_page_ids', $existing );
		return $existing;
	}

	if ( $force ) {
		foreach ( construction_find_projects_page_candidate_ids() as $old_id ) {
			wp_delete_post( $old_id, true );
		}
		$existing = array();
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

	$ids = $existing;
	foreach ( $defs as $lang => $def ) {
		if ( isset( $ids[ $lang ] ) && get_post( (int) $ids[ $lang ] ) ) {
			continue;
		}

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

	if ( count( $ids ) === 3 ) {
		pll_save_post_translations( $ids );
	}
	update_option( 'construction_projects_page_ids', $ids );
	update_option( 'construction_flush_rewrites', '1' );

	construction_rebuild_language_menus();

	return $ids;
}

/**
 * @return array{lv?:int,en?:int,ru?:int}
 */
function construction_get_projects_page_ids(): array {
	$stored = get_option( 'construction_projects_page_ids', array() );
	$ids    = array();
	if ( is_array( $stored ) ) {
		foreach ( construction_languages() as $lang ) {
			if ( ! empty( $stored[ $lang ] ) && get_post( (int) $stored[ $lang ] ) ) {
				$ids[ $lang ] = (int) $stored[ $lang ];
			}
		}
		if ( count( $ids ) === 3 ) {
			return $ids;
		}
	}

	$slugs = array(
		'lv' => 'projekti',
		'en' => 'projects',
		'ru' => 'proekty',
	);
	foreach ( $slugs as $lang => $slug ) {
		$found = get_posts(
			array(
				'name'           => $slug,
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		if ( ! empty( $found[0] ) ) {
			$ids[ $lang ] = (int) $found[0];
		}
	}

	return $ids;
}

/**
 * @return list<int>
 */
function construction_find_projects_page_candidate_ids(): array {
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

	return array_values( array_unique( array_map( 'intval', $old_ids ) ) );
}
