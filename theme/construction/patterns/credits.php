<?php
/**
 * Title: Photo credits
 * Slug: construction/credits
 * Categories: construction
 * Description: Unsplash photo attribution from filename metadata.
 *
 * @package Construction
 */
?>
<!-- wp:group {"align":"full","className":"construction-credits","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull construction-credits" id="credits">
	<!-- wp:html -->
	<div class="construction-credits__inner">
		<p class="construction-credits__title"><?php echo esc_html( construction_t( 'credits.title' ) ); ?></p>
		<ul class="construction-credits__list">
			<?php foreach ( construction_image_credits() as $credit ) : ?>
				<li>
					<a href="<?php echo esc_url( $credit['unsplash_url'] ); ?>" target="_blank" rel="noopener noreferrer">
						<?php
						printf(
							/* translators: %s: photographer name */
							esc_html__( 'Photo by %s on Unsplash', 'construction' ),
							esc_html( $credit['author'] )
						);
						?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<!-- /wp:html -->
</div>
<!-- /wp:group -->
