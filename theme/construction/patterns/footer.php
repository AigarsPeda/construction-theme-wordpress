<?php
/**
 * Title: Footer
 * Slug: construction/footer
 * Categories: footer, construction
 * Block Types: core/template-part/footer
 * Description: Footer with text links and back-to-top (editable blocks).
 *
 * @package Construction
 */
?>
<!-- wp:group {"align":"full","className":"construction-footer","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull construction-footer">
	<!-- wp:group {"className":"construction-footer__inner","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
	<div class="wp-block-group construction-footer__inner">
		<!-- wp:paragraph {"className":"construction-footer__links"} -->
		<p class="construction-footer__links"><a href="#services"><?php echo esc_html( construction_t( 'nav.projects' ) ); ?></a> · <a href="#about"><?php echo esc_html( construction_t( 'nav.photos' ) ); ?></a> · <a href="#about"><?php echo esc_html( construction_t( 'nav.about' ) ); ?></a></p>
		<!-- /wp:paragraph -->

		<!-- wp:paragraph {"className":"construction-footer__top"} -->
		<p class="construction-footer__top"><a href="#top" aria-label="<?php echo esc_attr( construction_t( 'back.top' ) ); ?>">↑</a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
