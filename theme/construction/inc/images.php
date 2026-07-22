<?php
/**
 * Content images via WordPress Media Library.
 *
 * Source files live outside the theme (Desktop/construction-images).
 * On import they are uploaded to wp-content/uploads and attachment IDs
 * are stored in the construction_media_ids option.
 *
 * Theme assets keep only chrome: logo placeholder SVG + screenshot.png.
 * Site logo is uploaded via Appearance → Construction (Media Library).
 *
 * @package Construction
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Absolute path to the external image source folder.
 */
function construction_media_source_dir(): string {
	$candidates = array(
		dirname( get_template_directory(), 3 ) . '/construction-images', // Desktop/construction-images when theme is Desktop/construction/theme/construction
		'/Users/aigarspeda/Desktop/construction-images',
	);

	foreach ( $candidates as $dir ) {
		if ( is_dir( $dir ) ) {
			return $dir;
		}
	}

	return $candidates[0];
}

/**
 * Media catalog: logical key => metadata + source filename in construction-images/.
 *
 * Homepage and gallery share photos by key; duplicate files are uploaded once.
 *
 * @return array<string, array{file: string, author: string, photo_id: string, unsplash_url: string, alt_key?: string}>
 */
function construction_media_catalog(): array {
	return array(
		'hero'       => array(
			'file'         => 'frames-for-your-heart-VoI2jd75M6Q-unsplash.jpg',
			'author'       => 'Frames for Your Heart',
			'photo_id'     => 'VoI2jd75M6Q',
			'unsplash_url' => 'https://unsplash.com/photos/VoI2jd75M6Q',
			'alt_key'      => 'hero.alt',
		),
		'service_1'  => array(
			'file'         => 'pedro-miranda-3QzMBrvCeyQ-unsplash.jpg',
			'author'       => 'Pedro Miranda',
			'photo_id'     => '3QzMBrvCeyQ',
			'unsplash_url' => 'https://unsplash.com/photos/3QzMBrvCeyQ',
		),
		'service_2'  => array(
			'file'         => 'ray-donnelly-YybYC5zC1Mk-unsplash.jpg',
			'author'       => 'Ray Donnelly',
			'photo_id'     => 'YybYC5zC1Mk',
			'unsplash_url' => 'https://unsplash.com/photos/YybYC5zC1Mk',
		),
		'service_3'  => array(
			'file'         => 'tolu-olubode-PlBsJ5MybGc-unsplash.jpg',
			'author'       => 'Tolu Olubode',
			'photo_id'     => 'PlBsJ5MybGc',
			'unsplash_url' => 'https://unsplash.com/photos/PlBsJ5MybGc',
		),
		'quality_1'  => array(
			'file'         => 'frames-for-your-heart-VoI2jd75M6Q-unsplash.jpg',
			'author'       => 'Frames for Your Heart',
			'photo_id'     => 'VoI2jd75M6Q',
			'unsplash_url' => 'https://unsplash.com/photos/VoI2jd75M6Q',
		),
		'quality_2'  => array(
			'file'         => 'pedro-miranda-3QzMBrvCeyQ-unsplash.jpg',
			'author'       => 'Pedro Miranda',
			'photo_id'     => '3QzMBrvCeyQ',
			'unsplash_url' => 'https://unsplash.com/photos/3QzMBrvCeyQ',
		),
		'quality_3'  => array(
			'file'         => 'ray-donnelly-YybYC5zC1Mk-unsplash.jpg',
			'author'       => 'Ray Donnelly',
			'photo_id'     => 'YybYC5zC1Mk',
			'unsplash_url' => 'https://unsplash.com/photos/YybYC5zC1Mk',
		),
		'quality_4'  => array(
			'file'         => 'tolu-olubode-PlBsJ5MybGc-unsplash.jpg',
			'author'       => 'Tolu Olubode',
			'photo_id'     => 'PlBsJ5MybGc',
			'unsplash_url' => 'https://unsplash.com/photos/PlBsJ5MybGc',
		),
		'project_1'  => array(
			'file'         => 'ben-allan-BIeC4YK2MTA-unsplash.jpg',
			'author'       => 'Ben Allan',
			'photo_id'     => 'BIeC4YK2MTA',
			'unsplash_url' => 'https://unsplash.com/photos/BIeC4YK2MTA',
			'alt_key'      => 'projects.alt.1',
		),
		'project_2'  => array(
			'file'         => 'frames-for-your-heart-VoI2jd75M6Q-unsplash.jpg',
			'author'       => 'Frames for Your Heart',
			'photo_id'     => 'VoI2jd75M6Q',
			'unsplash_url' => 'https://unsplash.com/photos/VoI2jd75M6Q',
			'alt_key'      => 'projects.alt.2',
		),
		'project_3'  => array(
			'file'         => 'ivan-bandura-0-no6ywKMPY-unsplash.jpg',
			'author'       => 'Ivan Bandura',
			'photo_id'     => '0-no6ywKMPY',
			'unsplash_url' => 'https://unsplash.com/photos/0-no6ywKMPY',
			'alt_key'      => 'projects.alt.3',
		),
		'project_4'  => array(
			'file'         => 'jeriden-villegas-niSnhfMjiMI-unsplash.jpg',
			'author'       => 'Jeriden Villegas',
			'photo_id'     => 'niSnhfMjiMI',
			'unsplash_url' => 'https://unsplash.com/photos/niSnhfMjiMI',
			'alt_key'      => 'projects.alt.4',
		),
		'project_5'  => array(
			'file'         => 'josue-isai-ramos-figueroa-qvBYnMuNJ9A-unsplash.jpg',
			'author'       => 'Josue Isai Ramos Figueroa',
			'photo_id'     => 'qvBYnMuNJ9A',
			'unsplash_url' => 'https://unsplash.com/photos/qvBYnMuNJ9A',
			'alt_key'      => 'projects.alt.5',
		),
		'project_6'  => array(
			'file'         => 'mitchell-luo-TtX79Vkm8gs-unsplash.jpg',
			'author'       => 'Mitchell Luo',
			'photo_id'     => 'TtX79Vkm8gs',
			'unsplash_url' => 'https://unsplash.com/photos/TtX79Vkm8gs',
			'alt_key'      => 'projects.alt.6',
		),
		'project_7'  => array(
			'file'         => 'pedro-miranda-3QzMBrvCeyQ-unsplash.jpg',
			'author'       => 'Pedro Miranda',
			'photo_id'     => '3QzMBrvCeyQ',
			'unsplash_url' => 'https://unsplash.com/photos/3QzMBrvCeyQ',
			'alt_key'      => 'projects.alt.7',
		),
		'project_8'  => array(
			'file'         => 'ray-donnelly-YybYC5zC1Mk-unsplash.jpg',
			'author'       => 'Ray Donnelly',
			'photo_id'     => 'YybYC5zC1Mk',
			'unsplash_url' => 'https://unsplash.com/photos/YybYC5zC1Mk',
			'alt_key'      => 'projects.alt.8',
		),
		'project_9'  => array(
			'file'         => 'ryunosuke-kikuno-d3vp0M7oT6c-unsplash.jpg',
			'author'       => 'Ryunosuke Kikuno',
			'photo_id'     => 'd3vp0M7oT6c',
			'unsplash_url' => 'https://unsplash.com/photos/d3vp0M7oT6c',
			'alt_key'      => 'projects.alt.9',
		),
		'project_10' => array(
			'file'         => 'saad-salim-PqRvLsjD_TU-unsplash.jpg',
			'author'       => 'Saad Salim',
			'photo_id'     => 'PqRvLsjD_TU',
			'unsplash_url' => 'https://unsplash.com/photos/PqRvLsjD_TU',
			'alt_key'      => 'projects.alt.10',
		),
		'project_11' => array(
			'file'         => 'sol-tZw3fcjUIpM-unsplash.jpg',
			'author'       => 'Sol',
			'photo_id'     => 'tZw3fcjUIpM',
			'unsplash_url' => 'https://unsplash.com/photos/tZw3fcjUIpM',
			'alt_key'      => 'projects.alt.11',
		),
		'project_12' => array(
			'file'         => 'tolu-olubode-PlBsJ5MybGc-unsplash.jpg',
			'author'       => 'Tolu Olubode',
			'photo_id'     => 'PlBsJ5MybGc',
			'unsplash_url' => 'https://unsplash.com/photos/PlBsJ5MybGc',
			'alt_key'      => 'projects.alt.12',
		),
		'project_13' => array(
			'file'         => 'troy-mortier-eKY6_9W_iqY-unsplash.jpg',
			'author'       => 'Troy Mortier',
			'photo_id'     => 'eKY6_9W_iqY',
			'unsplash_url' => 'https://unsplash.com/photos/eKY6_9W_iqY',
			'alt_key'      => 'projects.alt.13',
		),
	);
}

/**
 * Project gallery keys only.
 *
 * @return array<string, array{file: string, author: string, photo_id: string, unsplash_url: string, alt_key?: string}>
 */
function construction_project_images(): array {
	$all = construction_media_catalog();
	$out = array();
	foreach ( $all as $key => $meta ) {
		if ( str_starts_with( $key, 'project_' ) ) {
			$out[ $key ] = $meta;
		}
	}
	return $out;
}

/**
 * @deprecated Use construction_media_catalog(); kept for older call sites.
 *
 * @return array<string, array{file: string, author: string, photo_id: string, unsplash_url: string}>
 */
function construction_images(): array {
	$home_keys = array( 'hero', 'service_1', 'service_2', 'service_3', 'quality_1', 'quality_2', 'quality_3', 'quality_4' );
	$all       = construction_media_catalog();
	$out       = array();
	foreach ( $home_keys as $key ) {
		if ( isset( $all[ $key ] ) ) {
			$out[ $key ] = $all[ $key ];
		}
	}
	return $out;
}

/**
 * Stored attachment IDs keyed by catalog key.
 *
 * @return array<string, int>
 */
function construction_media_ids(): array {
	$ids = get_option( 'construction_media_ids', array() );
	return is_array( $ids ) ? array_map( 'intval', $ids ) : array();
}

/**
 * Attachment ID for a catalog key (0 if missing).
 */
function construction_media_id( string $key ): int {
	$ids = construction_media_ids();
	$id  = isset( $ids[ $key ] ) ? (int) $ids[ $key ] : 0;
	if ( $id > 0 && get_post( $id ) ) {
		return $id;
	}
	return 0;
}

/**
 * Public URL for a media catalog image (Media Library).
 */
function construction_image_url( string $key, string $size = 'large' ): string {
	$id = construction_media_id( $key );
	if ( $id > 0 ) {
		$url = wp_get_attachment_image_url( $id, $size );
		if ( is_string( $url ) && $url !== '' ) {
			return $url;
		}
		$full = wp_get_attachment_url( $id );
		if ( is_string( $full ) && $full !== '' ) {
			return $full;
		}
	}
	return '';
}

/**
 * Alias for project keys.
 */
function construction_project_image_url( string $key, string $size = 'large' ): string {
	return construction_image_url( $key, $size );
}

/**
 * Gutenberg image block markup bound to a Media Library attachment.
 *
 * Uses srcset/sizes from WordPress; lazy-loads below-fold images.
 *
 * @param string $key        Catalog key.
 * @param string $class_name Extra figure class.
 * @param string $alt        Alt text.
 * @param string $size       Image size slug (avoid "full" for display).
 * @param bool   $lightbox   Wrap in lightbox link to full-size image.
 * @param bool   $priority   LCP candidate: eager + fetchpriority=high.
 */
function construction_media_image_block( string $key, string $class_name, string $alt, string $size = 'large', bool $lightbox = false, bool $priority = false ): string {
	$id = construction_media_id( $key );
	if ( $id <= 0 ) {
		return '';
	}

	// Fall back if a custom size was never generated for this attachment.
	$meta = wp_get_attachment_metadata( $id );
	if (
		$size !== 'full'
		&& is_array( $meta )
		&& empty( $meta['sizes'][ $size ] )
		&& ! in_array( $size, array( 'thumbnail', 'medium', 'medium_large', 'large' ), true )
	) {
		$size = 'large';
	}

	$class_name = trim( $class_name );
	$classes    = 'wp-block-image size-' . esc_attr( $size );
	if ( $class_name !== '' ) {
		$classes .= ' ' . esc_attr( $class_name );
	}

	$img_attrs = array(
		'class'    => 'wp-image-' . $id,
		'alt'      => $alt,
		'decoding' => 'async',
		'sizes'    => construction_image_sizes_attr( $class_name ),
	);

	if ( $priority ) {
		$img_attrs['loading']       = 'eager';
		$img_attrs['fetchpriority'] = 'high';
	} else {
		$img_attrs['loading'] = 'lazy';
	}

	$img = wp_get_attachment_image( $id, $size, false, $img_attrs );
	if ( ! is_string( $img ) || $img === '' ) {
		return '';
	}

	if ( $lightbox ) {
		$full = esc_url( construction_image_url( $key, 'full' ) );
		if ( $full === '' ) {
			$full = esc_url( construction_image_url( $key, $size ) );
		}
		$link_dest = 'custom';
		$inner     = '<a href="' . $full . '" class="construction-lightbox glightbox" data-gallery="construction-projects">' . $img . '</a>';
	} else {
		$link_dest = 'none';
		$inner     = $img;
	}

	return <<<HTML
			<!-- wp:image {"id":{$id},"sizeSlug":"{$size}","linkDestination":"{$link_dest}","className":"{$class_name}"} -->
			<figure class="{$classes}">{$inner}</figure>
			<!-- /wp:image -->

HTML;
}

/**
 * Responsive sizes= hint from figure class context.
 */
function construction_image_sizes_attr( string $class_name ): string {
	if ( str_contains( $class_name, 'construction-hero__image' ) ) {
		return '(max-width: 900px) 100vw, 52vw';
	}
	if ( str_contains( $class_name, 'construction-service-card__thumb' ) ) {
		return '(max-width: 900px) 120px, 160px';
	}
	if ( str_contains( $class_name, 'construction-quality__media' ) ) {
		return '(max-width: 700px) 100vw, 25vw';
	}
	if ( str_contains( $class_name, 'construction-projects__item' ) ) {
		return '(max-width: 600px) 100vw, (max-width: 1000px) 50vw, 33vw';
	}

	return '(max-width: 900px) 100vw, 800px';
}

/**
 * Unique Unsplash credits from the catalog.
 *
 * @return list<array{author: string, photo_id: string, unsplash_url: string}>
 */
function construction_image_credits(): array {
	$unique = array();
	foreach ( construction_media_catalog() as $image ) {
		$id = $image['photo_id'];
		if ( isset( $unique[ $id ] ) ) {
			continue;
		}
		$unique[ $id ] = array(
			'author'       => $image['author'],
			'photo_id'     => $image['photo_id'],
			'unsplash_url' => $image['unsplash_url'],
		);
	}
	return array_values( $unique );
}

/**
 * Upload catalog images into the Media Library (idempotent).
 *
 * @return array{imported:int, reused:int, missing:list<string>}|WP_Error
 */
function construction_import_media_library() {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$source_dir = construction_media_source_dir();
	if ( ! is_dir( $source_dir ) ) {
		return new WP_Error( 'no_source', 'Image source folder not found: ' . $source_dir );
	}

	$ids      = construction_media_ids();
	$by_photo = array(); // photo_id => attachment id (dedupe uploads)
	foreach ( $ids as $key => $aid ) {
		$aid = (int) $aid;
		if ( $aid > 0 && get_post( $aid ) && isset( construction_media_catalog()[ $key ] ) ) {
			$by_photo[ construction_media_catalog()[ $key ]['photo_id'] ] = $aid;
		}
	}

	$imported = 0;
	$reused   = 0;
	$missing  = array();

	foreach ( construction_media_catalog() as $key => $meta ) {
		$existing = isset( $ids[ $key ] ) ? (int) $ids[ $key ] : 0;
		if ( $existing > 0 && get_post( $existing ) ) {
			++$reused;
			continue;
		}

		$photo_id = $meta['photo_id'];
		if ( isset( $by_photo[ $photo_id ] ) ) {
			$ids[ $key ] = $by_photo[ $photo_id ];
			++$reused;
			continue;
		}

		$path = trailingslashit( $source_dir ) . $meta['file'];
		if ( ! is_readable( $path ) ) {
			$missing[] = $meta['file'];
			continue;
		}

		$tmp = wp_tempnam( $meta['file'] );
		if ( ! $tmp || ! copy( $path, $tmp ) ) {
			$missing[] = $meta['file'];
			continue;
		}

		$file_array = array(
			'name'     => $meta['file'],
			'tmp_name' => $tmp,
		);

		$attachment_id = media_handle_sideload(
			$file_array,
			0,
			$meta['author'] . ' — ' . $photo_id
		);

		if ( is_wp_error( $attachment_id ) ) {
			@unlink( $tmp ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			$missing[] = $meta['file'] . ' (' . $attachment_id->get_error_message() . ')';
			continue;
		}

		update_post_meta( (int) $attachment_id, '_construction_photo_id', $photo_id );
		update_post_meta( (int) $attachment_id, '_construction_unsplash', $meta['unsplash_url'] );
		wp_update_post(
			array(
				'ID'           => (int) $attachment_id,
				'post_excerpt' => sprintf( 'Photo by %s on Unsplash', $meta['author'] ),
			)
		);

		$ids[ $key ]           = (int) $attachment_id;
		$by_photo[ $photo_id ] = (int) $attachment_id;
		++$imported;
	}

	update_option( 'construction_media_ids', $ids );

	return array(
		'imported' => $imported,
		'reused'   => $reused,
		'missing'  => $missing,
	);
}
