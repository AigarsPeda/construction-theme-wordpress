<?php
/**
 * Contacts page content (LV / EN / RU) + Polylang seed.
 *
 * @package Construction
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Contacts page URL for a language.
 */
function construction_contacts_url_for_lang( string $lang ): string {
	$ids = construction_get_contacts_page_ids();
	if ( ! empty( $ids[ $lang ] ) ) {
		$url = get_permalink( (int) $ids[ $lang ] );
		if ( is_string( $url ) && $url !== '' ) {
			return $url;
		}
	}

	$slugs = array(
		'lv' => 'kontakti',
		'en' => 'contacts',
		'ru' => 'kontakty',
	);
	$slug = $slugs[ $lang ] ?? 'kontakti';

	if ( function_exists( 'pll_home_url' ) ) {
		return trailingslashit( (string) pll_home_url( $lang ) ) . $slug . '/';
	}

	return home_url( '/' . $slug . '/' );
}

/**
 * Block markup for the Contacts page in one language.
 */
function construction_contacts_page_content_for_lang( string $lang ): string {
	$t = static function ( string $key ) use ( $lang ): string {
		return esc_html( construction_string( $key, $lang ) );
	};

	$email      = esc_html( construction_contact( 'email' ) );
	$phone      = esc_html( construction_contact( 'phone' ) );
	$phone_href = esc_url( construction_contact_phone_href() );
	$mail_href  = esc_url( construction_contact_mail_href( $lang ) );
	$address    = esc_html( construction_contact_address( $lang ) );

	return <<<HTML
<!-- wp:group {"align":"full","className":"construction-contacts-page","layout":{"type":"default"},"anchor":"contacts"} -->
<div class="wp-block-group alignfull construction-contacts-page" id="contacts">
	<!-- wp:group {"className":"construction-contacts-page__inner","layout":{"type":"default"}} -->
	<div class="wp-block-group construction-contacts-page__inner">
		<!-- wp:group {"className":"construction-contacts-page__head","layout":{"type":"default"}} -->
		<div class="wp-block-group construction-contacts-page__head">
			<!-- wp:heading {"level":1,"className":"construction-contacts-page__title"} -->
			<h1 class="wp-block-heading construction-contacts-page__title">{$t( 'contacts.title' )}</h1>
			<!-- /wp:heading -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"construction-contacts-page__panel","layout":{"type":"default"}} -->
		<div class="wp-block-group construction-contacts-page__panel">
			<!-- wp:columns {"className":"construction-contacts-page__grid"} -->
			<div class="wp-block-columns construction-contacts-page__grid">
				<!-- wp:column -->
				<div class="wp-block-column">
					<!-- wp:group {"className":"construction-contacts-page__card","layout":{"type":"default"}} -->
					<div class="wp-block-group construction-contacts-page__card">
						<!-- wp:paragraph {"className":"construction-contacts-page__label"} -->
						<p class="construction-contacts-page__label">{$t( 'contacts.email_label' )}</p>
						<!-- /wp:paragraph -->
						<!-- wp:paragraph {"className":"construction-contacts-page__value"} -->
						<p class="construction-contacts-page__value"><a href="{$mail_href}">{$email}</a></p>
						<!-- /wp:paragraph -->
					</div>
					<!-- /wp:group -->
				</div>
				<!-- /wp:column -->

				<!-- wp:column -->
				<div class="wp-block-column">
					<!-- wp:group {"className":"construction-contacts-page__card","layout":{"type":"default"}} -->
					<div class="wp-block-group construction-contacts-page__card">
						<!-- wp:paragraph {"className":"construction-contacts-page__label"} -->
						<p class="construction-contacts-page__label">{$t( 'contacts.phone_label' )}</p>
						<!-- /wp:paragraph -->
						<!-- wp:paragraph {"className":"construction-contacts-page__value"} -->
						<p class="construction-contacts-page__value"><a href="{$phone_href}">{$phone}</a></p>
						<!-- /wp:paragraph -->
					</div>
					<!-- /wp:group -->
				</div>
				<!-- /wp:column -->

				<!-- wp:column -->
				<div class="wp-block-column">
					<!-- wp:group {"className":"construction-contacts-page__card","layout":{"type":"default"}} -->
					<div class="wp-block-group construction-contacts-page__card">
						<!-- wp:paragraph {"className":"construction-contacts-page__label"} -->
						<p class="construction-contacts-page__label">{$t( 'contact.address_label' )}</p>
						<!-- /wp:paragraph -->
						<!-- wp:paragraph {"className":"construction-contacts-page__value"} -->
						<p class="construction-contacts-page__value">{$address}</p>
						<!-- /wp:paragraph -->
					</div>
					<!-- /wp:group -->
				</div>
				<!-- /wp:column -->
			</div>
			<!-- /wp:columns -->

			<!-- wp:group {"className":"construction-lead-form construction-lead-form--visual construction-contacts-page__cta","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
			<div class="wp-block-group construction-lead-form construction-lead-form--visual construction-contacts-page__cta">
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

			<!-- wp:paragraph {"className":"construction-contacts-page__social"} -->
			<p class="construction-contacts-page__social"><a href="https://instagram.com/construction" target="_blank" rel="noopener">Instagram</a> · <a href="https://facebook.com/construction" target="_blank" rel="noopener">Facebook</a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
HTML;
}

/**
 * Create contacts pages if missing (never overwrites existing DB content unless $force).
 *
 * @return array{lv?:int,en?:int,ru?:int}|WP_Error
 */
function construction_rebuild_polylang_contacts( bool $force = false ) {
	if ( ! function_exists( 'pll_set_post_language' ) || ! function_exists( 'pll_save_post_translations' ) ) {
		return new WP_Error( 'no_polylang', 'Polylang is not active.' );
	}

	$existing = construction_get_contacts_page_ids();
	if ( ! $force && count( $existing ) === 3 ) {
		update_option( 'construction_contacts_page_ids', $existing );
		return $existing;
	}

	if ( $force ) {
		foreach ( construction_find_contacts_page_candidate_ids() as $old_id ) {
			wp_delete_post( $old_id, true );
		}
		$existing = array();
	}

	$defs = array(
		'lv' => array(
			'title'   => 'Kontakti',
			'slug'    => 'kontakti',
			'content' => construction_contacts_page_content_for_lang( 'lv' ),
		),
		'en' => array(
			'title'   => 'Contact',
			'slug'    => 'contacts',
			'content' => construction_contacts_page_content_for_lang( 'en' ),
		),
		'ru' => array(
			'title'   => 'Контакты',
			'slug'    => 'kontakty',
			'content' => construction_contacts_page_content_for_lang( 'ru' ),
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
	update_option( 'construction_contacts_page_ids', $ids );
	update_option( 'construction_flush_rewrites', '1' );

	construction_rebuild_language_menus();

	return $ids;
}

/**
 * @return array{lv?:int,en?:int,ru?:int}
 */
function construction_get_contacts_page_ids(): array {
	$stored = get_option( 'construction_contacts_page_ids', array() );
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
		'lv' => 'kontakti',
		'en' => 'contacts',
		'ru' => 'kontakty',
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
function construction_find_contacts_page_candidate_ids(): array {
	$old_ids = array();
	foreach ( array( 'kontakti', 'contacts', 'kontakty' ) as $slug ) {
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
		if ( in_array( $title, array( 'Kontakti', 'Contact', 'Контакты' ), true ) ) {
			$old_ids[] = (int) $pid;
		}
	}

	return array_values( array_unique( array_map( 'intval', $old_ids ) ) );
}
