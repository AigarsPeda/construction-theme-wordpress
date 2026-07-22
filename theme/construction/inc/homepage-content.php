<?php
/**
 * Build static homepage HTML for a specific language (Polylang-friendly).
 *
 * Each language page stores its own text, so the editor shows the correct language.
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
 * Homepage block/HTML content for one language.
 */
function construction_homepage_content_for_lang( string $lang ): string {
	if ( ! in_array( $lang, construction_languages(), true ) ) {
		$lang = 'lv';
	}

	$t = static function ( string $key ) use ( $lang ): string {
		return esc_html( construction_string( $key, $lang ) );
	};

	$ta = static function ( string $key ) use ( $lang ): string {
		return esc_attr( construction_string( $key, $lang ) );
	};

	$img = static function ( string $key ): string {
		return esc_url( construction_image_url( $key ) );
	};

	$credits = '';
	foreach ( construction_image_credits() as $credit ) {
		$credits .= '<li><a href="' . esc_url( $credit['unsplash_url'] ) . '" target="_blank" rel="noopener noreferrer">';
		$credits .= esc_html(
			sprintf(
				/* translators: %s: photographer name */
				__( 'Photo by %s on Unsplash', 'construction' ),
				$credit['author']
			)
		);
		$credits .= '</a></li>';
	}

	return <<<HTML
<!-- wp:html -->
<div class="construction-hero" id="top">
	<div class="construction-hero__grid">
		<div class="construction-hero__copy">
			<h1 class="construction-hero__title">{$t( 'hero.title' )}</h1>
			<p class="construction-hero__text">{$t( 'hero.text' )}</p>
			<div class="construction-hero__actions">
				<a class="wp-block-button__link wp-element-button" href="#contact">{$t( 'hero.cta' )}</a>
			</div>
			<p class="construction-hero__since">{$t( 'hero.since' )}</p>
		</div>
		<div class="construction-hero__media">
			<figure class="construction-hero__image">
				<img src="{$img( 'hero' )}" alt="{$t( 'hero.alt' )}" width="1600" height="1200" loading="eager" decoding="async" />
			</figure>
		</div>
	</div>
</div>

<div class="construction-services" id="services">
	<div class="construction-services__grid">
		<div class="construction-services__intro">
			<h2>{$t( 'services.title' )}</h2>
			<p>{$t( 'services.intro' )}</p>
		</div>
		<div class="construction-services__list">
			<div class="construction-service-card">
				<div class="construction-service-card__thumb"><img src="{$img( 'service_1' )}" alt="" loading="lazy" decoding="async" /></div>
				<div>
					<h3>{$t( 'services.item1.title' )}</h3>
					<p>{$t( 'services.item1.text' )}</p>
				</div>
			</div>
			<div class="construction-service-card">
				<div class="construction-service-card__thumb"><img src="{$img( 'service_2' )}" alt="" loading="lazy" decoding="async" /></div>
				<div>
					<h3>{$t( 'services.item2.title' )}</h3>
					<p>{$t( 'services.item2.text' )}</p>
				</div>
			</div>
			<div class="construction-service-card">
				<div class="construction-service-card__thumb"><img src="{$img( 'service_3' )}" alt="" loading="lazy" decoding="async" /></div>
				<div>
					<h3>{$t( 'services.item3.title' )}</h3>
					<p>{$t( 'services.item3.text' )}</p>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="construction-quality" id="about">
	<div class="construction-quality__inner">
		<h2>{$t( 'quality.title' )}</h2>
		<div class="construction-quality__grid">
			<div class="construction-quality__card">
				<div class="construction-quality__media"><img src="{$img( 'quality_1' )}" alt="" loading="lazy" decoding="async" /></div>
				<h3>{$t( 'quality.item1' )}</h3>
			</div>
			<div class="construction-quality__card">
				<div class="construction-quality__media"><img src="{$img( 'quality_2' )}" alt="" loading="lazy" decoding="async" /></div>
				<h3>{$t( 'quality.item2' )}</h3>
			</div>
			<div class="construction-quality__card">
				<div class="construction-quality__media"><img src="{$img( 'quality_3' )}" alt="" loading="lazy" decoding="async" /></div>
				<h3>{$t( 'quality.item3' )}</h3>
			</div>
			<div class="construction-quality__card">
				<div class="construction-quality__media"><img src="{$img( 'quality_4' )}" alt="" loading="lazy" decoding="async" /></div>
				<h3>{$t( 'quality.item4' )}</h3>
			</div>
		</div>
	</div>
</div>

<div class="construction-reviews" id="reviews">
	<div class="construction-reviews__inner">
		<h2>{$t( 'reviews.title' )}</h2>
		<div class="construction-reviews__summary">
			<p><strong>4.9 ★</strong> · {$t( 'reviews.avg' )}</p>
			<p>{$t( 'reviews.count' )}</p>
		</div>
		<div class="construction-reviews__grid">
			<article class="construction-review-card">
				<p class="construction-review-card__meta"><strong>Anna K.</strong> · ★★★★★</p>
				<p>{$t( 'reviews.1' )}</p>
			</article>
			<article class="construction-review-card">
				<p class="construction-review-card__meta"><strong>Jānis P.</strong> · ★★★★★</p>
				<p>{$t( 'reviews.2' )}</p>
			</article>
			<article class="construction-review-card">
				<p class="construction-review-card__meta"><strong>Elena M.</strong> · ★★★★★</p>
				<p>{$t( 'reviews.3' )}</p>
			</article>
			<article class="construction-review-card">
				<p class="construction-review-card__meta"><strong>Mārtiņš L.</strong> · ★★★★★</p>
				<p>{$t( 'reviews.4' )}</p>
			</article>
		</div>
	</div>
</div>

<div class="construction-contact" id="contact">
	<div class="construction-contact__grid">
		<div class="construction-contact__info">
			<p class="construction-contact__label">{$t( 'contact.label' )}</p>
			<p class="construction-contact__email"><a href="mailto:info@construction.lv">info@construction.lv</a></p>
			<p class="construction-contact__telegram"><a href="https://t.me/construction" target="_blank" rel="noopener">Telegram · @construction</a></p>
		</div>
		<form class="construction-lead-form" action="mailto:info@construction.lv" method="post" enctype="text/plain">
			<label class="screen-reader-text" for="construction-lead-{$lang}">{$t( 'contact.field' )}</label>
			<div class="construction-lead-form__row">
				<input id="construction-lead-{$lang}" type="text" name="contact" placeholder="{$ta( 'contact.placeholder' )}" required />
				<button type="submit" aria-label="{$ta( 'contact.submit' )}">→</button>
			</div>
		</form>
	</div>
</div>

<div class="construction-credits" id="credits">
	<div class="construction-credits__inner">
		<p class="construction-credits__title">{$t( 'credits.title' )}</p>
		<ul class="construction-credits__list">{$credits}</ul>
	</div>
</div>
<!-- /wp:html -->
HTML;
}

/**
 * Delete old front pages and recreate linked LV / EN / RU homes for Polylang.
 *
 * @return array{lv:int,en:int,ru:int}|WP_Error
 */
function construction_rebuild_polylang_homes() {
	if ( ! function_exists( 'pll_set_post_language' ) || ! function_exists( 'pll_save_post_translations' ) ) {
		return new WP_Error( 'no_polylang', 'Polylang is not active.' );
	}

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

	// Also remove by known titles.
	$title_query = new WP_Query(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'draft', 'trash', 'private' ),
			'posts_per_page' => 50,
			'fields'         => 'ids',
			's'              => '',
		)
	);
	foreach ( $title_query->posts as $pid ) {
		$title = get_the_title( (int) $pid );
		if ( in_array( $title, array( 'Sākums', 'Home', 'Главная', 'Sākums - English', 'Sākums - Русский' ), true ) ) {
			$old_ids[] = (int) $pid;
		}
	}

	$old_ids = array_unique( array_map( 'intval', $old_ids ) );
	foreach ( $old_ids as $old_id ) {
		wp_delete_post( $old_id, true );
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

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $ids['lv'] );
	update_option( 'construction_home_page_id', $ids['lv'] );
	update_option( 'construction_flush_rewrites', '1' );

	return $ids;
}
