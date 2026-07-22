<?php
/**
 * Title: Reviews
 * Slug: construction/reviews
 * Categories: testimonials, construction
 * Description: Dark testimonials section with rating summary.
 *
 * @package Construction
 */
?>
<!-- wp:group {"align":"full","className":"construction-reviews","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull construction-reviews" id="reviews">
	<!-- wp:html -->
	<div class="construction-reviews__inner">
		<h2><?php echo esc_html( construction_t( 'reviews.title' ) ); ?></h2>
		<div class="construction-reviews__summary">
			<p><strong>4.9 ★</strong> · <?php echo esc_html( construction_t( 'reviews.avg' ) ); ?></p>
			<p><?php echo esc_html( construction_t( 'reviews.count' ) ); ?></p>
		</div>
		<div class="construction-reviews__grid">
			<article class="construction-review-card">
				<p class="construction-review-card__meta"><strong>Anna K.</strong> · ★★★★★</p>
				<p><?php echo esc_html( construction_t( 'reviews.1' ) ); ?></p>
			</article>
			<article class="construction-review-card">
				<p class="construction-review-card__meta"><strong>Jānis P.</strong> · ★★★★★</p>
				<p><?php echo esc_html( construction_t( 'reviews.2' ) ); ?></p>
			</article>
			<article class="construction-review-card">
				<p class="construction-review-card__meta"><strong>Elena M.</strong> · ★★★★★</p>
				<p><?php echo esc_html( construction_t( 'reviews.3' ) ); ?></p>
			</article>
			<article class="construction-review-card">
				<p class="construction-review-card__meta"><strong>Mārtiņš L.</strong> · ★★★★★</p>
				<p><?php echo esc_html( construction_t( 'reviews.4' ) ); ?></p>
			</article>
		</div>
	</div>
	<!-- /wp:html -->
</div>
<!-- /wp:group -->
