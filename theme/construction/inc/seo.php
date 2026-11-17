<?php
/**
 * SEO basics: document titles, meta description, Open Graph, hreflang.
 *
 * When a dedicated SEO plugin is active, the theme steps aside for titles
 * and meta tags so you can edit them in the plugin UI (not hardcoded).
 *
 * @package Construction
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether a common SEO plugin is handling titles/meta.
 */
function construction_seo_plugin_active(): bool {
	if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) ) {
		return true;
	}

	if ( defined( 'SEOPRESS_VERSION' ) || defined( 'SLIM_SEO_VER' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' ) ) {
		return true;
	}

	// Plugin file checks (constants vary by version).
	if (
		construction_is_plugin_active( 'wordpress-seo/wp-seo.php' )
		|| construction_is_plugin_active( 'seo-by-rank-math/rank-math.php' )
		|| construction_is_plugin_active( 'all-in-one-seo-pack/all_in_one_seo_pack.php' )
		|| construction_is_plugin_active( 'wp-seopress/seopress.php' )
		|| construction_is_plugin_active( 'slim-seo/slim-seo.php' )
		|| construction_is_plugin_active( 'autodescription/autodescription.php' )
	) {
		return true;
	}

	return false;
}

/**
 * Safe plugin-active check (front-end included).
 */
function construction_is_plugin_active( string $plugin ): bool {
	if ( ! function_exists( 'is_plugin_active' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	return function_exists( 'is_plugin_active' ) && is_plugin_active( $plugin );
}

/**
 * Site brand name for titles.
 */
function construction_seo_brand(): string {
	$name = get_bloginfo( 'name' );
	return $name !== '' ? $name : 'Construction';
}

/**
 * Meta description for the current view (theme fallback only).
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
		if ( in_array( $slug, array( 'kontakti', 'contacts', 'kontakty' ), true ) ) {
			return construction_t( 'seo.contacts.desc' );
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
 * Theme title fallback for home and projects (skipped if an SEO plugin is active).
 *
 * @param array<string, string> $parts Title parts.
 * @return array<string, string>
 */
function construction_document_title_parts( array $parts ): array {
	if ( construction_seo_plugin_active() ) {
		return $parts;
	}

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
		if ( in_array( $slug, array( 'kontakti', 'contacts', 'kontakty' ), true ) ) {
			$parts['title'] = construction_t( 'seo.contacts.title' );
			$parts['site']  = $brand;
		}
	}

	return $parts;
}
add_filter( 'document_title_parts', 'construction_document_title_parts' );

/**
 * Print meta description + Open Graph tags (theme fallback only).
 * Polylang owns hreflang output, including the x-default filter below.
 */
function construction_seo_head(): void {
	if ( ! construction_seo_plugin_active() ) {
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
	}
}
add_action( 'wp_head', 'construction_seo_head', 1 );

/**
 * Use Polylang's reciprocal translation set, with the default translation as fallback.
 * Do not advertise homepage translations on search, archive or error URLs.
 */
function construction_seo_hreflang( array $links ): array {
	if ( ! is_singular( 'page' ) ) {
		return array();
	}

	$default = function_exists( 'pll_default_language' ) ? pll_default_language() : 'lv';
	if ( isset( $links[ $default ] ) ) {
		$links['x-default'] = $links[ $default ];
	}
	return $links;
}
add_filter( 'pll_rel_hreflang_attributes', 'construction_seo_hreflang' );

/**
 * Refine Rank Math's graph without creating a second JSON-LD provider.
 * These are service and portfolio pages, rather than editorial articles.
 */
function construction_seo_schema( array $data ): array {
	if ( ! is_page() ) {
		return $data;
	}

	$slug     = (string) get_post_field( 'post_name', get_queried_object_id() );
	$services = array(
		'projektu-attistitajiem' => 'Būvniecības projektu vadība attīstītājiem',
		'for-property-developers' => 'Construction project management for developers',
		'dlya-developerov' => 'Управление строительными проектами для девелоперов',
		'privatmaju-buvetajiem' => 'Privātmājas būvuzraudzība un būvniecības vadība',
		'for-private-home-builders' => 'Private home construction management and supervision',
		'dlya-zastroyshchikov-chastnyh-domov' => 'Управление строительством и надзор за частным домом',
		'tamesana' => 'Būvniecības tāmju sastādīšana',
		'cost-estimating' => 'Construction cost estimating',
		'sostavlenie-smet' => 'Составление смет на строительство',
	);

	foreach ( $data as &$entity ) {
		if ( ! is_array( $entity ) ) {
			continue;
		}
		if ( in_array( $entity['@type'] ?? '', array( 'WebPage', 'ContactPage', 'CollectionPage' ), true ) ) {
			if ( in_array( $slug, array( 'kontakti', 'contacts', 'kontakty' ), true ) ) {
				$entity['@type'] = 'ContactPage';
			} elseif ( in_array( $slug, array( 'projekti', 'projects', 'proekty' ), true ) ) {
				$entity['@type'] = 'CollectionPage';
			}
			if ( isset( $services[ $slug ] ) ) {
				$entity['mainEntity'] = array( '@id' => get_permalink() . '#service' );
			}
		}

		// Rank Math can fall back to 200x200 for images extracted from block HTML.
		if ( 'ImageObject' === ( $entity['@type'] ?? '' ) && ! empty( $entity['url'] ) ) {
			$entity = array_replace( $entity, construction_seo_local_image_dimensions( $entity['url'] ) );
		}
	}
	unset( $entity );

	if ( isset( $services[ $slug ] ) ) {
		$data['construction_service'] = array(
			'@type'            => 'Service',
			'@id'              => get_permalink() . '#service',
			'name'             => $services[ $slug ],
			'serviceType'      => $services[ $slug ],
			'url'              => get_permalink(),
			'description'      => (string) get_post_meta( get_queried_object_id(), 'rank_math_description', true ),
			'provider'         => array( '@id' => home_url( '/#organization' ) ),
			'mainEntityOfPage' => array( '@id' => get_permalink() . '#webpage' ),
		) + construction_seo_service_details( $slug );
	}

	return $data;
}
add_filter( 'rank_math/json_ld', 'construction_seo_schema', 99 );

/**
 * Extra Service facts for the cost-estimating pages: the other names people
 * search for, the country served, and the two offers described on the page.
 * Everything here restates visible page content; nothing is shown to visitors.
 *
 * @return array<string, mixed>
 */
function construction_seo_service_details( string $slug ): array {
	$details = array(
		'tamesana'         => array(
			'names'   => array( 'Tāmēšana', 'Tāmēšanas pakalpojumi', 'Tāmju sagatavošana', 'Būvniecības izmaksu tāme' ),
			'country' => 'Latvija',
			'catalog' => 'Tāmēšanas pakalpojumi',
			'offers'  => array( 'Privātmājas būvniecības izmaksu tāme', 'Nekustamā īpašuma attīstības projekta izmaksu un rentabilitātes izvērtējums' ),
		),
		'cost-estimating'  => array(
			'names'   => array( 'Construction cost estimate', 'Building cost estimate' ),
			'country' => 'Latvia',
			'catalog' => 'Cost estimating services',
			'offers'  => array( 'Private house construction cost estimate', 'Property development cost and profitability assessment' ),
		),
		'sostavlenie-smet' => array(
			'names'   => array( 'Сметы на строительство', 'Смета на строительство дома', 'Услуги сметчика' ),
			'country' => 'Латвия',
			'catalog' => 'Сметные услуги',
			'offers'  => array( 'Смета на строительство частного дома', 'Оценка затрат и рентабельности девелоперского проекта' ),
		),
	);
	if ( ! isset( $details[ $slug ] ) ) {
		return array();
	}

	$detail = $details[ $slug ];
	return array(
		'alternateName'   => $detail['names'],
		'areaServed'      => array(
			'@type' => 'Country',
			'name'  => $detail['country'],
		),
		'hasOfferCatalog' => array(
			'@type'           => 'OfferCatalog',
			'name'            => $detail['catalog'],
			'itemListElement' => array_map(
				static fn( string $name ): array => array(
					'@type'       => 'Offer',
					'itemOffered' => array(
						'@type' => 'Service',
						'name'  => $name,
					),
				),
				$detail['offers']
			),
		),
	);
}

/**
 * Feed documents are useful to readers but should not compete with page URLs.
 */
function construction_seo_feed_headers( array $headers ): array {
	if ( is_feed() ) {
		$headers['X-Robots-Tag'] = 'noindex, follow';
	}
	return $headers;
}
add_filter( 'wp_headers', 'construction_seo_feed_headers' );

/** Service and company pages use the website Open Graph type. */
add_filter( 'rank_math/opengraph/type', function ( $type ) {
	return is_page() ? 'website' : $type;
} );

/** Keep WebPage and Organization entities when the default Article schema is off. */
add_filter( 'rank_math/schema/add_global_entities', function ( $enabled ) {
	if ( is_page() && ! post_password_required() ) {
		$robots = (array) get_post_meta( get_queried_object_id(), 'rank_math_robots', true );
		return ! in_array( 'noindex', $robots, true );
	}
	return $enabled;
} );

/** Read dimensions only for local uploads; never fetch an external image. */
function construction_seo_local_image_dimensions( string $url ): array {
	$uploads = wp_upload_dir();
	if ( ! str_starts_with( $url, $uploads['baseurl'] . '/' ) ) {
		return array();
	}
	$file = $uploads['basedir'] . substr( $url, strlen( $uploads['baseurl'] ) );
	$size = is_readable( $file ) ? wp_getimagesize( $file ) : false;
	return $size ? array( 'width' => $size[0], 'height' => $size[1] ) : array();
}

/**
 * Content images stored as URLs can lack Open Graph dimensions in Rank Math.
 * Rank Math versions differ in what they pass (an image array or a plain URL),
 * so anything that is not an image array is returned untouched.
 *
 * @param mixed $image Rank Math image data.
 * @return mixed
 */
function construction_seo_social_image_dimensions( $image ) {
	if ( is_array( $image ) && ! empty( $image['url'] ) && is_string( $image['url'] ) ) {
		$image = array_replace( $image, construction_seo_local_image_dimensions( $image['url'] ) );
	}
	return $image;
}
add_filter( 'rank_math/opengraph/facebook/image_array', 'construction_seo_social_image_dimensions' );
add_filter( 'rank_math/opengraph/twitter/image_array', 'construction_seo_social_image_dimensions' );

/**
 * Defer supporting audience-page images below the hero. Hero images stay eager:
 * the private-home hero can hold up to three images, all above the fold.
 */
add_filter( 'render_block_core/post-content', function ( $html ) {
	$hero_end = strpos( $html, 'construction-audience-page__intro' );
	if ( ! str_contains( $html, 'construction-audience-page__hero-media' ) || false === $hero_end ) {
		return $html;
	}
	// Split at the opening "<" of the tag that starts the first section after the hero.
	$split = strrpos( substr( $html, 0, $hero_end ), '<' );
	if ( false === $split ) {
		return $html;
	}
	$tags = new WP_HTML_Tag_Processor( substr( $html, $split ) );
	while ( $tags->next_tag( 'IMG' ) ) {
		if ( 'high' !== $tags->get_attribute( 'fetchpriority' ) ) {
			$tags->set_attribute( 'loading', 'lazy' );
		}
	}
	return substr( $html, 0, $split ) . $tags->get_updated_html();
} );
