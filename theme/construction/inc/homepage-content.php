<?php
/**
 * Build homepage content as editable Gutenberg blocks (per language).
 *
 * @package Construction
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get one string for an explicit language.
 */
function construction_string( string $key, string $lang ): string {
	$strings = construction_strings();
	if ( isset( $strings[ $key ][ $lang ] ) ) {
		return $strings[ $key ][ $lang ];
	}
	if ( isset( $strings[ $key ]['en'] ) ) {
		return $strings[ $key ]['en'];
	}
	return $key;
}

/**
 * Service journey phases for the homepage services section.
 *
 * @return list<array{key: string, items: list<int>}>
 */
function construction_service_phases(): array {
	return array(
		array(
			'key'   => 'before',
			'items' => array( 1, 2, 4 ),
		),
		array(
			'key'   => 'during',
			'items' => array( 5, 6, 3 ),
		),
		array(
			'key'   => 'control',
			'items' => array( 7, 8 ),
		),
	);
}

/**
 * Block markup for one language's services journey (seed / rebuild).
 * Chapter title left, phase sections stacked on the right (desktop).
 */
function construction_services_section_markup( string $lang ): string {
	$t = static function ( string $key ) use ( $lang ): string {
		return esc_html( construction_string( $key, $lang ) );
	};

	$phases = '';
	foreach ( construction_service_phases() as $phase ) {
		$key   = (string) $phase['key'];
		$num   = $t( "services.phase.{$key}.num" );
		$label = $t( "services.phase.{$key}.label" );
		$lead  = $t( "services.phase.{$key}.lead" );
		$items = '';
		foreach ( $phase['items'] as $i ) {
			$i     = (int) $i;
			$title = $t( "services.item{$i}.title" );
			$text  = $t( "services.item{$i}.text" );
			$items .= <<<ITEM
			<!-- wp:group {"className":"construction-service-card","layout":{"type":"default"}} -->
			<div class="wp-block-group construction-service-card">
				<!-- wp:heading {"level":3,"className":"construction-service-card__title"} -->
				<h3 class="wp-block-heading construction-service-card__title">{$title}</h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"className":"construction-service-card__text"} -->
				<p class="construction-service-card__text">{$text}</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->

ITEM;
		}

		$phases .= <<<PHASE
		<!-- wp:group {"className":"construction-services-phase construction-services-phase--{$key}","layout":{"type":"default"}} -->
		<div class="wp-block-group construction-services-phase construction-services-phase--{$key}">
			<!-- wp:group {"className":"construction-services-phase__head","layout":{"type":"default"}} -->
			<div class="wp-block-group construction-services-phase__head">
				<!-- wp:heading {"level":2,"className":"construction-services-phase__label"} -->
				<h2 class="wp-block-heading construction-services-phase__label"><span class="construction-services-phase__num">{$num}</span> {$label}</h2>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"className":"construction-services-phase__lead"} -->
				<p class="construction-services-phase__lead">{$lead}</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
			<!-- wp:group {"className":"construction-services-phase__items","layout":{"type":"default"}} -->
			<div class="wp-block-group construction-services-phase__items">
{$items}			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->

PHASE;
	}

	return <<<HTML
<!-- wp:group {"align":"full","className":"construction-services","layout":{"type":"default"},"anchor":"services"} -->
<div class="wp-block-group alignfull construction-services" id="services">
	<!-- wp:group {"className":"construction-services__layout","layout":{"type":"default"}} -->
	<div class="wp-block-group construction-services__layout">
		<!-- wp:group {"className":"construction-services__intro","layout":{"type":"default"}} -->
		<div class="wp-block-group construction-services__intro">
			<!-- wp:heading -->
			<h2 class="wp-block-heading">{$t( 'services.title' )}</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p>{$t( 'services.intro' )}</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"construction-services__journey","layout":{"type":"default"}} -->
		<div class="wp-block-group construction-services__journey">
{$phases}		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
HTML;
}

/**
 * Homepage block content for one language (visual editor friendly).
 */
function construction_homepage_content_for_lang( string $lang ): string {
	if ( ! in_array( $lang, construction_languages(), true ) ) {
		$lang = 'lv';
	}

	$t = static function ( string $key ) use ( $lang ): string {
		return esc_html( construction_string( $key, $lang ) );
	};

	$hero_img         = construction_media_image_block( 'hero', 'construction-hero__image', construction_string( 'hero.alt', $lang ), 'construction-hero', false, true );
	$services_section = construction_services_section_markup( $lang );

	$mail_href = esc_url( construction_contact_mail_href( $lang ) );
	$email     = esc_html( construction_contact( 'email' ) );
	$phone     = esc_html( construction_contact( 'phone' ) );
	$phone_href = esc_url( construction_contact_phone_href() );
	$address   = esc_html( construction_contact_address( $lang ) );

	$credits_blocks = '';
	foreach ( construction_image_credits() as $credit ) {
		$label = esc_html(
			sprintf(
				/* translators: %s: photographer name */
				__( 'Photo by %s on Unsplash', 'construction' ),
				$credit['author']
			)
		);
		$url             = esc_url( $credit['unsplash_url'] );
		$credits_blocks .= <<<ITEM
			<!-- wp:paragraph {"className":"construction-credits__item"} -->
			<p class="construction-credits__item"><a href="{$url}" target="_blank" rel="noopener noreferrer">{$label}</a></p>
			<!-- /wp:paragraph -->

ITEM;
	}

	return <<<HTML
<!-- wp:group {"align":"full","className":"construction-hero","layout":{"type":"default"},"anchor":"top"} -->
<div class="wp-block-group alignfull construction-hero" id="top">
	<!-- wp:columns {"className":"construction-hero__grid"} -->
	<div class="wp-block-columns construction-hero__grid">
		<!-- wp:column {"width":"48%","className":"construction-hero__copy"} -->
		<div class="wp-block-column construction-hero__copy" style="flex-basis:48%">
			<!-- wp:heading {"level":1,"className":"construction-hero__title"} -->
			<h1 class="wp-block-heading construction-hero__title">{$t( 'hero.title' )}</h1>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"className":"construction-hero__text"} -->
			<p class="construction-hero__text">{$t( 'hero.text' )}</p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"className":"construction-hero__actions"} -->
			<div class="wp-block-buttons construction-hero__actions">
				<!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#contact">{$t( 'hero.cta' )}</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->

			<!-- wp:paragraph {"className":"construction-hero__since"} -->
			<p class="construction-hero__since">{$t( 'hero.since' )}</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"52%","className":"construction-hero__media"} -->
		<div class="wp-block-column construction-hero__media" style="flex-basis:52%">
{$hero_img}		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->

{$services_section}

<!-- wp:construction/home-projects {"align":"full"} /-->

<!-- wp:group {"align":"full","className":"construction-faq","layout":{"type":"default"},"anchor":"faq"} -->
<div class="wp-block-group alignfull construction-faq" id="faq">
	<!-- wp:group {"className":"construction-faq__inner","layout":{"type":"default"}} -->
	<div class="wp-block-group construction-faq__inner">
		<!-- wp:group {"className":"construction-faq__header","layout":{"type":"default"}} -->
		<div class="wp-block-group construction-faq__header">
			<!-- wp:paragraph {"className":"construction-eyebrow"} -->
			<p class="construction-eyebrow">{$t( 'faq.eyebrow' )}</p>
			<!-- /wp:paragraph -->
			<!-- wp:heading -->
			<h2 class="wp-block-heading">{$t( 'faq.title' )}</h2>
			<!-- /wp:heading -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"construction-faq__list","layout":{"type":"default"}} -->
		<div class="wp-block-group construction-faq__list">
			<!-- wp:details {"className":"construction-faq-item"} -->
			<details class="wp-block-details construction-faq-item">
				<summary>{$t( 'faq.1.q' )}</summary>
				<!-- wp:group {"className":"construction-faq-item__panel","layout":{"type":"default"}} -->
				<div class="wp-block-group construction-faq-item__panel">
					<!-- wp:paragraph -->
					<p>{$t( 'faq.1.a' )}</p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</details>
			<!-- /wp:details -->

			<!-- wp:details {"className":"construction-faq-item"} -->
			<details class="wp-block-details construction-faq-item">
				<summary>{$t( 'faq.2.q' )}</summary>
				<!-- wp:group {"className":"construction-faq-item__panel","layout":{"type":"default"}} -->
				<div class="wp-block-group construction-faq-item__panel">
					<!-- wp:paragraph -->
					<p>{$t( 'faq.2.a' )}</p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</details>
			<!-- /wp:details -->

			<!-- wp:details {"className":"construction-faq-item"} -->
			<details class="wp-block-details construction-faq-item">
				<summary>{$t( 'faq.3.q' )}</summary>
				<!-- wp:group {"className":"construction-faq-item__panel","layout":{"type":"default"}} -->
				<div class="wp-block-group construction-faq-item__panel">
					<!-- wp:paragraph -->
					<p>{$t( 'faq.3.a' )}</p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</details>
			<!-- /wp:details -->

			<!-- wp:details {"className":"construction-faq-item"} -->
			<details class="wp-block-details construction-faq-item">
				<summary>{$t( 'faq.4.q' )}</summary>
				<!-- wp:group {"className":"construction-faq-item__panel","layout":{"type":"default"}} -->
				<div class="wp-block-group construction-faq-item__panel">
					<!-- wp:paragraph -->
					<p>{$t( 'faq.4.a' )}</p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</details>
			<!-- /wp:details -->

			<!-- wp:details {"className":"construction-faq-item"} -->
			<details class="wp-block-details construction-faq-item">
				<summary>{$t( 'faq.5.q' )}</summary>
				<!-- wp:group {"className":"construction-faq-item__panel","layout":{"type":"default"}} -->
				<div class="wp-block-group construction-faq-item__panel">
					<!-- wp:paragraph -->
					<p>{$t( 'faq.5.a' )}</p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</details>
			<!-- /wp:details -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"construction-contact","layout":{"type":"default"},"anchor":"contact"} -->
<div class="wp-block-group alignfull construction-contact" id="contact">
	<!-- wp:columns {"className":"construction-contact__grid","verticalAlignment":"center"} -->
	<div class="wp-block-columns construction-contact__grid are-vertically-aligned-center">
		<!-- wp:column {"verticalAlignment":"center","width":"40%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:40%">
			<!-- wp:paragraph {"className":"construction-contact__label"} -->
			<p class="construction-contact__label">{$t( 'contact.label' )}</p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"className":"construction-contact__email"} -->
			<p class="construction-contact__email"><a href="{$mail_href}">{$email}</a></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"className":"construction-contact__phone"} -->
			<p class="construction-contact__phone"><a href="{$phone_href}">{$phone}</a></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"className":"construction-contact__address"} -->
			<p class="construction-contact__address">{$address}</p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"className":"construction-contact__social"} -->
			<p class="construction-contact__social"><a href="https://instagram.com/construction" target="_blank" rel="noopener">Instagram</a> · <a href="https://facebook.com/construction" target="_blank" rel="noopener">Facebook</a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"verticalAlignment":"center","width":"60%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:60%">
			<!-- wp:group {"className":"construction-lead-form construction-lead-form--visual","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
			<div class="wp-block-group construction-lead-form construction-lead-form--visual">
				<!-- wp:paragraph {"className":"construction-lead-form__hint"} -->
				<p class="construction-lead-form__hint">{$t( 'contact.hint' )}</p>
				<!-- /wp:paragraph -->

				<!-- wp:buttons {"className":"construction-lead-form__actions"} -->
				<div class="wp-block-buttons construction-lead-form__actions">
					<!-- wp:button {"className":"construction-lead-form__go"} -->
					<div class="wp-block-button construction-lead-form__go"><a class="wp-block-button__link wp-element-button" href="{$mail_href}">{$t( 'contact.mail_cta' )} →</a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"construction-credits","layout":{"type":"default"},"anchor":"credits"} -->
<div class="wp-block-group alignfull construction-credits" id="credits">
	<!-- wp:group {"className":"construction-credits__inner","layout":{"type":"default"}} -->
	<div class="wp-block-group construction-credits__inner">
		<!-- wp:paragraph {"className":"construction-credits__title"} -->
		<p class="construction-credits__title">{$t( 'credits.title' )}</p>
		<!-- /wp:paragraph -->
{$credits_blocks}	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
HTML;
}

/**
 * Delete old front pages and recreate linked LV / EN / RU homes for Polylang.
 *
 * Default ($force = false): create missing language homes only — never deletes
 * or overwrites existing page content in the database.
 * $force = true: wipe matching homes and reseed from theme defaults (destructive).
 *
 * @return array{lv?:int,en?:int,ru?:int}|WP_Error
 */
function construction_rebuild_polylang_homes( bool $force = false ) {
	if ( ! function_exists( 'pll_set_post_language' ) || ! function_exists( 'pll_save_post_translations' ) ) {
		return new WP_Error( 'no_polylang', 'Polylang is not active.' );
	}

	$media = construction_import_media_library();
	if ( is_wp_error( $media ) ) {
		return $media;
	}

	$existing = construction_get_home_page_ids();
	if ( ! $force && count( $existing ) === 3 ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $existing['lv'] );
		update_option( 'construction_home_page_id', $existing['lv'] );
		return $existing;
	}

	if ( $force ) {
		foreach ( construction_find_home_page_candidate_ids() as $old_id ) {
			wp_delete_post( $old_id, true );
		}
		$existing = array();
	}

	$defs = array(
		'lv' => array(
			'title'   => 'Sākums',
			'slug'    => 'sakums',
			'content' => construction_homepage_content_for_lang( 'lv' ),
		),
		'en' => array(
			'title'   => 'Home',
			'slug'    => 'home',
			'content' => construction_homepage_content_for_lang( 'en' ),
		),
		'ru' => array(
			'title'   => 'Главная',
			'slug'    => 'glavnaya',
			'content' => construction_homepage_content_for_lang( 'ru' ),
		),
	);

	$ids = $existing;
	foreach ( $defs as $lang => $def ) {
		if ( isset( $ids[ $lang ] ) && get_post( (int) $ids[ $lang ] ) ) {
			continue; // Keep WordPress content.
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

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $ids['lv'] );
	update_option( 'construction_home_page_id', $ids['lv'] );
	update_option( 'construction_flush_rewrites', '1' );

	construction_rebuild_language_menus( $ids );

	return $ids;
}

/**
 * Existing Polylang-linked home page IDs (if any).
 *
 * @return array{lv?:int,en?:int,ru?:int}
 */
function construction_get_home_page_ids(): array {
	$ids   = array();
	$front = (int) get_option( 'page_on_front' );

	if ( $front > 0 && function_exists( 'pll_get_post' ) ) {
		foreach ( construction_languages() as $lang ) {
			$translated = pll_get_post( $front, $lang );
			if ( $translated ) {
				$ids[ $lang ] = (int) $translated;
			}
		}
		if ( count( $ids ) === 3 ) {
			return $ids;
		}
	}

	$slugs = array(
		'lv' => 'sakums',
		'en' => 'home',
		'ru' => 'glavnaya',
	);
	foreach ( $slugs as $lang => $slug ) {
		$found = get_posts(
			array(
				'name'             => $slug,
				'post_type'        => 'page',
				'post_status'      => 'publish',
				'posts_per_page'   => 1,
				'fields'           => 'ids',
				'suppress_filters' => false,
			)
		);
		if ( ! empty( $found[0] ) ) {
			$ids[ $lang ] = (int) $found[0];
		}
	}

	return $ids;
}

/**
 * Candidate page IDs that look like home pages (for forced reset only).
 *
 * @return list<int>
 */
function construction_find_home_page_candidate_ids(): array {
	$old_ids = array();
	foreach ( array( 'sakums', 'home', 'glavnaya', 'sakums-english' ) as $slug ) {
		$found = get_posts(
			array(
				'name'             => $slug,
				'post_type'        => 'page',
				'post_status'      => array( 'publish', 'draft', 'trash', 'private' ),
				'posts_per_page'   => 20,
				'fields'           => 'ids',
				'suppress_filters' => false,
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
		if ( in_array( $title, array( 'Sākums', 'Home', 'Главная', 'Sākums - English', 'Sākums - Русский' ), true ) ) {
			$old_ids[] = (int) $pid;
		}
	}

	return array_values( array_unique( array_map( 'intval', $old_ids ) ) );
}

/**
 * Create Primary menus per language with Projects page link.
 *
 * @param array{lv?:int,en?:int,ru?:int} $page_ids Unused; kept for call-site compatibility.
 */
function construction_rebuild_language_menus( array $page_ids = array() ): void {
	if ( ! function_exists( 'pll_languages_list' ) ) {
		return;
	}

	$menu_defs = array(
		'lv' => 'Primary LV',
		'en' => 'Primary EN',
		'ru' => 'Primary RU',
	);

	$project_ids = get_option( 'construction_projects_page_ids', array() );
	if ( ! is_array( $project_ids ) ) {
		$project_ids = array();
	}
	if ( empty( $project_ids ) && function_exists( 'construction_get_projects_page_ids' ) ) {
		$project_ids = construction_get_projects_page_ids();
	}

	$contact_ids = get_option( 'construction_contacts_page_ids', array() );
	if ( ! is_array( $contact_ids ) ) {
		$contact_ids = array();
	}
	if ( empty( $contact_ids ) && function_exists( 'construction_get_contacts_page_ids' ) ) {
		$contact_ids = construction_get_contacts_page_ids();
	}

	$menu_ids = array();

	foreach ( $menu_defs as $lang => $name ) {
		$existing = wp_get_nav_menu_object( $name );
		if ( $existing ) {
			$menu_id = (int) $existing->term_id;
			$items   = wp_get_nav_menu_items( $menu_id );
			if ( is_array( $items ) ) {
				foreach ( $items as $item ) {
					wp_delete_post( (int) $item->ID, true );
				}
			}
		} else {
			$created = wp_create_nav_menu( $name );
			if ( is_wp_error( $created ) ) {
				continue;
			}
			$menu_id = (int) $created;
		}

		if ( ! empty( $project_ids[ $lang ] ) ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'     => construction_string( 'nav.projects', $lang ),
					'menu-item-object'    => 'page',
					'menu-item-object-id' => (int) $project_ids[ $lang ],
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				)
			);
		}

		if ( ! empty( $contact_ids[ $lang ] ) ) {
			wp_update_nav_menu_item(
				$menu_id,
				0,
				array(
					'menu-item-title'     => construction_string( 'nav.contact', $lang ),
					'menu-item-object'    => 'page',
					'menu-item-object-id' => (int) $contact_ids[ $lang ],
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
				)
			);
		}

		$menu_ids[ $lang ] = $menu_id;
	}

	if ( empty( $menu_ids ) ) {
		return;
	}

	$locations            = get_theme_mod( 'nav_menu_locations', array() );
	$locations            = is_array( $locations ) ? $locations : array();
	$locations['primary'] = $menu_ids['lv'] ?? reset( $menu_ids );
	set_theme_mod( 'nav_menu_locations', $locations );

	$pll = get_option( 'polylang', array() );
	if ( ! is_array( $pll ) ) {
		$pll = array();
	}
	if ( ! isset( $pll['nav_menus'] ) || ! is_array( $pll['nav_menus'] ) ) {
		$pll['nav_menus'] = array();
	}
	$theme = get_option( 'stylesheet' );
	if ( ! isset( $pll['nav_menus'][ $theme ] ) || ! is_array( $pll['nav_menus'][ $theme ] ) ) {
		$pll['nav_menus'][ $theme ] = array();
	}
	$pll['nav_menus'][ $theme ]['primary'] = $menu_ids;
	update_option( 'polylang', $pll );
}
