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
 * Block markup for the Projects page in one language (named projects + per-project galleries).
 */
function construction_projects_page_content_for_lang( string $lang ): string {
	$t = static function ( string $key ) use ( $lang ): string {
		return esc_html( construction_string( $key, $lang ) );
	};

	$contact_cta  = $t( 'projects.cta' );
	$contact_href = esc_url( construction_contacts_url_for_lang( $lang ) );
	$label_close  = esc_attr( construction_string( 'projects.close', $lang ) );
	$label_prev   = esc_attr( construction_string( 'projects.prev', $lang ) );
	$label_next   = esc_attr( construction_string( 'projects.next', $lang ) );

	$cards = '';
	foreach ( construction_project_entries() as $entry ) {
		$slug    = sanitize_title( (string) $entry['slug'] );
		$gallery = 'project-' . $slug;
		$title   = $t( "projects.item.{$slug}.title" );
		$text    = $t( "projects.item.{$slug}.text" );
		$cover   = (string) $entry['cover'];
		$images  = isset( $entry['images'] ) && is_array( $entry['images'] ) ? $entry['images'] : array( $cover );

		$cover_alt = $title;
		$cover_meta = construction_media_catalog()[ $cover ] ?? null;
		if ( is_array( $cover_meta ) && ! empty( $cover_meta['alt_key'] ) ) {
			$cover_alt = construction_string( (string) $cover_meta['alt_key'], $lang );
		}

		// Cover + extras keep Media Library images; full-size hrefs feed the in-page viewer.
		$cover_block = construction_media_image_block(
			$cover,
			'construction-project-card__cover',
			$cover_alt,
			'large',
			true,
			false,
			$gallery
		);

		$more = '';
		foreach ( $images as $img_key ) {
			$img_key = (string) $img_key;
			if ( $img_key === $cover ) {
				continue;
			}
			$alt_key = 'projects.title';
			$meta    = construction_media_catalog()[ $img_key ] ?? null;
			if ( is_array( $meta ) && ! empty( $meta['alt_key'] ) ) {
				$alt_key = (string) $meta['alt_key'];
			}
			$more .= construction_media_image_block(
				$img_key,
				'construction-project-card__extra',
				construction_string( $alt_key, $lang ),
				'medium_large',
				true,
				false,
				$gallery
			);
		}

		$cards .= <<<CARD
			<!-- wp:group {"className":"construction-project-card","layout":{"type":"constrained"},"anchor":"{$slug}"} -->
			<div class="wp-block-group construction-project-card" id="{$slug}" data-project-slug="{$slug}">
{$cover_block}				<!-- wp:heading {"level":2,"className":"construction-project-card__title"} -->
				<h2 class="wp-block-heading construction-project-card__title">{$title}</h2>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"className":"construction-project-card__text"} -->
				<p class="construction-project-card__text">{$text}</p>
				<!-- /wp:paragraph -->
				<!-- wp:group {"className":"construction-project-card__more","layout":{"type":"default"}} -->
				<div class="wp-block-group construction-project-card__more">
{$more}				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->

CARD;
	}

	return <<<HTML
<!-- wp:group {"align":"full","className":"construction-projects","layout":{"type":"default"},"anchor":"projects"} -->
<div class="wp-block-group alignfull construction-projects" id="projects" data-label-close="{$label_close}" data-label-prev="{$label_prev}" data-label-next="{$label_next}">
	<!-- wp:group {"className":"construction-projects__inner","layout":{"type":"default"}} -->
	<div class="wp-block-group construction-projects__inner">
		<!-- wp:group {"className":"construction-projects__head","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"end"}} -->
		<div class="wp-block-group construction-projects__head">
			<!-- wp:heading {"level":1,"className":"construction-projects__title"} -->
			<h1 class="wp-block-heading construction-projects__title">{$t( 'projects.title' )}</h1>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"className":"construction-projects__cta-inline"} -->
			<p class="construction-projects__cta-inline"><a href="{$contact_href}">{$contact_cta} →</a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:paragraph {"className":"construction-projects__intro"} -->
		<p class="construction-projects__intro">{$t( 'projects.intro' )}</p>
		<!-- /wp:paragraph -->

		<!-- wp:html -->
		<div class="construction-project-viewer" hidden aria-live="polite"></div>
		<!-- /wp:html -->

		<!-- wp:group {"className":"construction-projects__grid","layout":{"type":"default"}} -->
		<div class="wp-block-group construction-projects__grid">
{$cards}		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
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
