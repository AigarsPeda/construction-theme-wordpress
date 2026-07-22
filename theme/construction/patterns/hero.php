<?php
/**
 * Title: Hero
 * Slug: construction/hero
 * Categories: banner, construction
 * Description: Split hero with headline, CTA, and house visual.
 *
 * @package Construction
 */
?>
<!-- wp:group {"align":"full","className":"construction-hero","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull construction-hero" id="top">
	<!-- wp:columns {"className":"construction-hero__grid","style":{"spacing":{"blockGap":{"top":"0","left":"0"}}}} -->
	<div class="wp-block-columns construction-hero__grid">
		<!-- wp:column {"width":"48%","className":"construction-hero__copy"} -->
		<div class="wp-block-column construction-hero__copy" style="flex-basis:48%">
			<!-- wp:heading {"level":1,"className":"construction-hero__title","fontSize":"xx-large"} -->
			<h1 class="wp-block-heading construction-hero__title has-xx-large-font-size"><?php echo esc_html( construction_t( 'Your house — your rules' ) ); ?></h1>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"className":"construction-hero__text"} -->
			<p class="construction-hero__text"><?php esc_html_e( 'Projektējam un būvējam karkasa mājas pēc jūsu vajadzībām — no idejas līdz atslēgai.', 'construction' ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#contact"><?php echo esc_html( construction_t( 'Take the quiz' ) ); ?></a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->

			<!-- wp:paragraph {"className":"construction-hero__since"} -->
			<p class="construction-hero__since"><?php echo esc_html( construction_t( 'Building frame houses since 2008' ) ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"52%","className":"construction-hero__media"} -->
		<div class="wp-block-column construction-hero__media" style="flex-basis:52%">
			<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"construction-hero__image"} -->
			<figure class="wp-block-image size-full construction-hero__image"><img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/hero-house.jpg" alt="<?php esc_attr_e( 'Modernas karkasa mājas vizuālis', 'construction' ); ?>"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
