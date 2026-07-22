<?php
/**
 * Title: Hero
 * Slug: buvnams/hero
 * Categories: banner, buvnams
 * Description: Split hero with headline, CTA, and house visual.
 *
 * @package BuvNams
 */
?>
<!-- wp:group {"align":"full","className":"buvnams-hero","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull buvnams-hero" id="top">
	<!-- wp:columns {"className":"buvnams-hero__grid","style":{"spacing":{"blockGap":{"top":"0","left":"0"}}}} -->
	<div class="wp-block-columns buvnams-hero__grid">
		<!-- wp:column {"width":"48%","className":"buvnams-hero__copy"} -->
		<div class="wp-block-column buvnams-hero__copy" style="flex-basis:48%">
			<!-- wp:heading {"level":1,"className":"buvnams-hero__title","fontSize":"xx-large"} -->
			<h1 class="wp-block-heading buvnams-hero__title has-xx-large-font-size"><?php echo esc_html( buvnams_t( 'Your house — your rules' ) ); ?></h1>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"className":"buvnams-hero__text"} -->
			<p class="buvnams-hero__text"><?php esc_html_e( 'Projektējam un būvējam karkasa mājas pēc jūsu vajadzībām — no idejas līdz atslēgai.', 'buvnams' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#contact"><?php echo esc_html( buvnams_t( 'Take the quiz' ) ); ?></a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->

			<!-- wp:paragraph {"className":"buvnams-hero__since"} -->
			<p class="buvnams-hero__since"><?php echo esc_html( buvnams_t( 'Building frame houses since 2008' ) ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"52%","className":"buvnams-hero__media"} -->
		<div class="wp-block-column buvnams-hero__media" style="flex-basis:52%">
			<!-- wp:group {"className":"buvnams-hero__visual","layout":{"type":"constrained"}} -->
			<div class="wp-block-group buvnams-hero__visual" role="img" aria-label="<?php esc_attr_e( 'Modernas karkasa mājas vizuālis', 'buvnams' ); ?>"></div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
