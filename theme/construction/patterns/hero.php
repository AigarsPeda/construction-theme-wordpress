<?php
/**
 * Title: Hero
 * Slug: construction/hero
 * Categories: banner, construction
 * Description: Full-bleed split hero with headline, CTA, and house visual (editable blocks).
 *
 * @package Construction
 */
?>
<!-- wp:group {"align":"full","className":"construction-hero","layout":{"type":"default"},"anchor":"top"} -->
<div class="wp-block-group alignfull construction-hero" id="top">
	<!-- wp:columns {"className":"construction-hero__grid"} -->
	<div class="wp-block-columns construction-hero__grid">
		<!-- wp:column {"width":"48%","className":"construction-hero__copy"} -->
		<div class="wp-block-column construction-hero__copy" style="flex-basis:48%">
			<!-- wp:heading {"level":1,"className":"construction-hero__title"} -->
			<h1 class="wp-block-heading construction-hero__title"><?php echo esc_html( construction_t( 'hero.title' ) ); ?></h1>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"className":"construction-hero__text"} -->
			<p class="construction-hero__text"><?php echo esc_html( construction_t( 'hero.text' ) ); ?></p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"className":"construction-hero__actions"} -->
			<div class="wp-block-buttons construction-hero__actions">
				<!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#contact"><?php echo esc_html( construction_t( 'hero.cta' ) ); ?></a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->

			<!-- wp:paragraph {"className":"construction-hero__since"} -->
			<p class="construction-hero__since"><?php echo esc_html( construction_t( 'hero.since' ) ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"52%","className":"construction-hero__media"} -->
		<div class="wp-block-column construction-hero__media" style="flex-basis:52%">
			<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"construction-hero__image"} -->
			<figure class="wp-block-image size-full construction-hero__image"><img src="<?php echo esc_url( construction_image_url( 'hero' ) ); ?>" alt="<?php echo esc_attr( construction_t( 'hero.alt' ) ); ?>"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
