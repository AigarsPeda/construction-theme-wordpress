<?php
/**
 * Title: Photo credits
 * Slug: construction/credits
 * Categories: construction
 * Description: Unsplash photo attribution (editable paragraph blocks).
 *
 * @package Construction
 */
?>
<!-- wp:group {"align":"full","className":"construction-credits","layout":{"type":"default"},"anchor":"credits"} -->
<div class="wp-block-group alignfull construction-credits" id="credits">
	<!-- wp:group {"className":"construction-credits__inner","layout":{"type":"constrained","contentSize":"1200px"}} -->
	<div class="wp-block-group construction-credits__inner">
		<!-- wp:paragraph {"className":"construction-credits__title"} -->
		<p class="construction-credits__title"><?php echo esc_html( construction_t( 'credits.title' ) ); ?></p>
		<!-- /wp:paragraph -->

		<?php foreach ( construction_image_credits() as $credit ) : ?>
			<!-- wp:paragraph {"className":"construction-credits__item"} -->
			<p class="construction-credits__item"><a href="<?php echo esc_url( $credit['unsplash_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php
				printf(
					/* translators: %s: photographer name */
					esc_html__( 'Photo by %s on Unsplash', 'construction' ),
					esc_html( $credit['author'] )
				);
			?></a></p>
			<!-- /wp:paragraph -->
		<?php endforeach; ?>
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
