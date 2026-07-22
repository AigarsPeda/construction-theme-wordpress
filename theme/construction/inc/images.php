<?php
/**
 * Unsplash image registry + attribution helpers.
 *
 * Filenames follow: {author-slug}-{photoId}-unsplash.jpg
 *
 * @package Construction
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Image catalog with Unsplash attribution metadata.
 *
 * @return array<string, array{file: string, author: string, photo_id: string, unsplash_url: string}>
 */
function construction_images(): array {
	return array(
		'hero'         => array(
			'file'         => 'frames-for-your-heart.jpg',
			'author'       => 'Frames for Your Heart',
			'photo_id'     => 'VoI2jd75M6Q',
			'unsplash_url' => 'https://unsplash.com/photos/VoI2jd75M6Q',
		),
		'service_1'    => array(
			'file'         => 'pedro-miranda.jpg',
			'author'       => 'Pedro Miranda',
			'photo_id'     => '3QzMBrvCeyQ',
			'unsplash_url' => 'https://unsplash.com/photos/3QzMBrvCeyQ',
		),
		'service_2'    => array(
			'file'         => 'ray-donnelly.jpg',
			'author'       => 'Ray Donnelly',
			'photo_id'     => 'YybYC5zC1Mk',
			'unsplash_url' => 'https://unsplash.com/photos/YybYC5zC1Mk',
		),
		'service_3'    => array(
			'file'         => 'tolu-olubode.jpg',
			'author'       => 'Tolu Olubode',
			'photo_id'     => 'PlBsJ5MybGc',
			'unsplash_url' => 'https://unsplash.com/photos/PlBsJ5MybGc',
		),
		'quality_1'    => array(
			'file'         => 'frames-for-your-heart.jpg',
			'author'       => 'Frames for Your Heart',
			'photo_id'     => 'VoI2jd75M6Q',
			'unsplash_url' => 'https://unsplash.com/photos/VoI2jd75M6Q',
		),
		'quality_2'    => array(
			'file'         => 'pedro-miranda.jpg',
			'author'       => 'Pedro Miranda',
			'photo_id'     => '3QzMBrvCeyQ',
			'unsplash_url' => 'https://unsplash.com/photos/3QzMBrvCeyQ',
		),
		'quality_3'    => array(
			'file'         => 'ray-donnelly.jpg',
			'author'       => 'Ray Donnelly',
			'photo_id'     => 'YybYC5zC1Mk',
			'unsplash_url' => 'https://unsplash.com/photos/YybYC5zC1Mk',
		),
		'quality_4'    => array(
			'file'         => 'tolu-olubode.jpg',
			'author'       => 'Tolu Olubode',
			'photo_id'     => 'PlBsJ5MybGc',
			'unsplash_url' => 'https://unsplash.com/photos/PlBsJ5MybGc',
		),
		'screenshot'   => array(
			'file'         => 'sven-mieke.jpg',
			'author'       => 'Sven Mieke',
			'photo_id'     => 'fteR0e2BzKo',
			'unsplash_url' => 'https://unsplash.com/photos/fteR0e2BzKo',
		),
	);
}

/**
 * Public URL for a catalog image.
 */
function construction_image_url( string $key ): string {
	$images = construction_images();
	if ( ! isset( $images[ $key ] ) ) {
		return '';
	}

	return trailingslashit( get_template_directory_uri() ) . 'assets/images/' . $images[ $key ]['file'];
}

/**
 * Unique credits for footer attribution.
 *
 * @return list<array{author: string, photo_id: string, unsplash_url: string}>
 */
function construction_image_credits(): array {
	$unique = array();

	foreach ( construction_images() as $image ) {
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
