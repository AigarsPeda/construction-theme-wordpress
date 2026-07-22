<?php
/**
 * Title: Hero
 * Slug: construction/hero
 * Categories: banner, construction
 * Description: Full-bleed split hero with headline, CTA, and house visual.
 *
 * @package Construction
 */
?>
<!-- wp:group {"align":"full","className":"construction-hero","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull construction-hero" id="top">
	<!-- wp:html -->
	<div class="construction-hero__grid">
		<div class="construction-hero__copy">
			<h1 class="construction-hero__title"><?php echo esc_html( construction_t( 'hero.title' ) ); ?></h1>
			<p class="construction-hero__text"><?php echo esc_html( construction_t( 'hero.text' ) ); ?></p>
			<div class="construction-hero__actions">
				<a class="wp-block-button__link wp-element-button" href="#contact"><?php echo esc_html( construction_t( 'hero.cta' ) ); ?></a>
			</div>
			<p class="construction-hero__since"><?php echo esc_html( construction_t( 'hero.since' ) ); ?></p>
		</div>
		<div class="construction-hero__media">
			<figure class="construction-hero__image">
				<img
					src="<?php echo esc_url( construction_image_url( 'hero' ) ); ?>"
					alt="<?php echo esc_attr( construction_t( 'hero.alt' ) ); ?>"
					width="1600"
					height="1200"
					loading="eager"
					decoding="async"
				/>
			</figure>
		</div>
	</div>
	<!-- /wp:html -->
</div>
<!-- /wp:group -->
