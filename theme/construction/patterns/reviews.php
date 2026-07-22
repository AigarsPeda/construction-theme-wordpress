<?php
/**
 * Title: Reviews
 * Slug: construction/reviews
 * Categories: testimonials, construction
 * Description: Testimonials section with rating summary (editable blocks).
 *
 * @package Construction
 */
?>
<!-- wp:group {"align":"full","className":"construction-reviews","layout":{"type":"default"},"anchor":"reviews"} -->
<div class="wp-block-group alignfull construction-reviews" id="reviews">
	<!-- wp:group {"className":"construction-reviews__inner","layout":{"type":"default"}} -->
	<div class="wp-block-group construction-reviews__inner">
		<!-- wp:heading -->
		<h2 class="wp-block-heading"><?php echo esc_html( construction_t( 'reviews.title' ) ); ?></h2>
		<!-- /wp:heading -->

		<!-- wp:group {"className":"construction-reviews__summary","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
		<div class="wp-block-group construction-reviews__summary">
			<!-- wp:paragraph -->
			<p><strong>4.9 ★</strong> · <?php echo esc_html( construction_t( 'reviews.avg' ) ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph -->
			<p><?php echo esc_html( construction_t( 'reviews.count' ) ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:columns {"className":"construction-reviews__grid"} -->
		<div class="wp-block-columns construction-reviews__grid">
			<!-- wp:column -->
			<div class="wp-block-column">
				<!-- wp:group {"className":"construction-review-card","layout":{"type":"constrained"}} -->
				<div class="wp-block-group construction-review-card">
					<!-- wp:paragraph {"className":"construction-review-card__meta"} -->
					<p class="construction-review-card__meta"><strong>Anna K.</strong> · ★★★★★</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph -->
					<p><?php echo esc_html( construction_t( 'reviews.1' ) ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column -->
			<div class="wp-block-column">
				<!-- wp:group {"className":"construction-review-card","layout":{"type":"constrained"}} -->
				<div class="wp-block-group construction-review-card">
					<!-- wp:paragraph {"className":"construction-review-card__meta"} -->
					<p class="construction-review-card__meta"><strong>Jānis P.</strong> · ★★★★★</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph -->
					<p><?php echo esc_html( construction_t( 'reviews.2' ) ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column -->
			<div class="wp-block-column">
				<!-- wp:group {"className":"construction-review-card","layout":{"type":"constrained"}} -->
				<div class="wp-block-group construction-review-card">
					<!-- wp:paragraph {"className":"construction-review-card__meta"} -->
					<p class="construction-review-card__meta"><strong>Elena M.</strong> · ★★★★★</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph -->
					<p><?php echo esc_html( construction_t( 'reviews.3' ) ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column -->
			<div class="wp-block-column">
				<!-- wp:group {"className":"construction-review-card","layout":{"type":"constrained"}} -->
				<div class="wp-block-group construction-review-card">
					<!-- wp:paragraph {"className":"construction-review-card__meta"} -->
					<p class="construction-review-card__meta"><strong>Mārtiņš L.</strong> · ★★★★★</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph -->
					<p><?php echo esc_html( construction_t( 'reviews.4' ) ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
