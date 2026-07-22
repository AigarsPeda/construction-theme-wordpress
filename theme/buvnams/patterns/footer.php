<?php
/**
 * Title: Footer
 * Slug: buvnams/footer
 * Categories: footer, buvnams
 * Block Types: core/template-part/footer
 * Description: Compact footer bar with CTA and links.
 *
 * @package BuvNams
 */
?>
<!-- wp:group {"align":"full","className":"buvnams-footer","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull buvnams-footer">
	<!-- wp:group {"className":"buvnams-footer__inner","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
	<div class="wp-block-group buvnams-footer__inner">
		<!-- wp:buttons -->
		<div class="wp-block-buttons">
			<!-- wp:button {"className":"is-style-fill"} -->
			<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#contact"><?php echo esc_html( buvnams_t( 'Take the quiz' ) ); ?></a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->

		<!-- wp:paragraph {"className":"buvnams-footer__links"} -->
		<p class="buvnams-footer__links"><a href="#projects"><?php echo esc_html( buvnams_t( 'Projects' ) ); ?></a> · <a href="#photos"><?php echo esc_html( buvnams_t( 'Photos' ) ); ?></a> · <a href="#about"><?php echo esc_html( buvnams_t( 'About us' ) ); ?></a></p>
		<!-- /wp:paragraph -->

		<!-- wp:paragraph {"className":"buvnams-footer__top"} -->
		<p class="buvnams-footer__top"><a href="#top" aria-label="Augšā">↑</a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
