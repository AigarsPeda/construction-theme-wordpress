<?php
/**
 * Title: Footer
 * Slug: construction/footer
 * Categories: footer, construction
 * Block Types: core/template-part/footer
 * Description: Compact footer bar with CTA and links.
 *
 * @package Construction
 */
?>
<!-- wp:group {"align":"full","className":"construction-footer","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull construction-footer">
	<!-- wp:group {"className":"construction-footer__inner","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
	<div class="wp-block-group construction-footer__inner">
		<!-- wp:buttons -->
		<div class="wp-block-buttons">
			<!-- wp:button {"className":"is-style-fill"} -->
			<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#contact"><?php echo esc_html( construction_t( 'Take the quiz' ) ); ?></a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->

		<!-- wp:paragraph {"className":"construction-footer__links"} -->
		<p class="construction-footer__links"><a href="#projects"><?php echo esc_html( construction_t( 'Projects' ) ); ?></a> · <a href="#photos"><?php echo esc_html( construction_t( 'Photos' ) ); ?></a> · <a href="#about"><?php echo esc_html( construction_t( 'About us' ) ); ?></a></p>
		<!-- /wp:paragraph -->

		<!-- wp:paragraph {"className":"construction-footer__top"} -->
		<p class="construction-footer__top"><a href="#top" aria-label="Augšā">↑</a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
