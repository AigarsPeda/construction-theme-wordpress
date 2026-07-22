<?php
/**
 * Title: Reviews
 * Slug: buvnams/reviews
 * Categories: testimonials, buvnams
 * Description: Dark testimonials section with rating summary.
 *
 * @package BuvNams
 */
?>
<!-- wp:group {"align":"full","className":"buvnams-reviews","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull buvnams-reviews" id="reviews">
	<!-- wp:heading {"textAlign":"left","align":"wide","textColor":"base","fontSize":"x-large"} -->
	<h2 class="wp-block-heading alignwide has-text-align-left has-base-color has-text-color has-x-large-font-size"><?php echo esc_html( buvnams_t( 'The main measure of our quality is a satisfied client' ) ); ?></h2>
	<!-- /wp:heading -->

	<!-- wp:group {"align":"wide","className":"buvnams-reviews__summary","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
	<div class="wp-block-group alignwide buvnams-reviews__summary">
		<!-- wp:paragraph -->
		<p><strong>4.9 ★</strong> · <?php esc_html_e( 'Vidējais vērtējums', 'buvnams' ); ?></p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph -->
		<p><?php esc_html_e( '48 atsauksmes · Google / Facebook', 'buvnams' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:columns {"align":"wide","className":"buvnams-reviews__grid"} -->
	<div class="wp-block-columns alignwide buvnams-reviews__grid">
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"className":"buvnams-review-card","layout":{"type":"constrained"}} -->
			<div class="wp-block-group buvnams-review-card">
				<!-- wp:paragraph {"className":"buvnams-review-card__meta"} -->
				<p class="buvnams-review-card__meta"><strong>Anna K.</strong> · ★★★★★</p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph -->
				<p><?php esc_html_e( 'Profesionāla komanda, skaidri termiņi un skaists rezultāts. Iesakām!', 'buvnams' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"className":"buvnams-review-card","layout":{"type":"constrained"}} -->
			<div class="wp-block-group buvnams-review-card">
				<!-- wp:paragraph {"className":"buvnams-review-card__meta"} -->
				<p class="buvnams-review-card__meta"><strong>Jānis P.</strong> · ★★★★★</p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph -->
				<p><?php esc_html_e( 'No projekta līdz montāžai viss noritēja raiti. Māja izdevās tieši tā, kā gribējām.', 'buvnams' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"className":"buvnams-review-card","layout":{"type":"constrained"}} -->
			<div class="wp-block-group buvnams-review-card">
				<!-- wp:paragraph {"className":"buvnams-review-card__meta"} -->
				<p class="buvnams-review-card__meta"><strong>Elena M.</strong> · ★★★★★</p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph -->
				<p><?php esc_html_e( 'Lieliska komunikācija un kvalitatīva fasāde. Paldies BūvNams komandai.', 'buvnams' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"className":"buvnams-review-card","layout":{"type":"constrained"}} -->
			<div class="wp-block-group buvnams-review-card">
				<!-- wp:paragraph {"className":"buvnams-review-card__meta"} -->
				<p class="buvnams-review-card__meta"><strong>Mārtiņš L.</strong> · ★★★★★</p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph -->
				<p><?php esc_html_e( 'Ātri, kārtīgi un bez pārsteigumiem budžetā. Noteikti strādāsim atkal.', 'buvnams' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
