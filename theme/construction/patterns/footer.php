<?php
/**
 * Title: Footer
 * Slug: construction/footer
 * Categories: footer, construction
 * Block Types: core/template-part/footer
 * Description: Compact footer bar with links and back-to-top.
 *
 * @package Construction
 */
?>
<!-- wp:group {"align":"full","className":"construction-footer","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull construction-footer">
	<!-- wp:html -->
	<div class="construction-footer__inner">
		<p class="construction-footer__links">
			<a href="#services"><?php echo esc_html( construction_t( 'nav.projects' ) ); ?></a>
			·
			<a href="#about"><?php echo esc_html( construction_t( 'nav.photos' ) ); ?></a>
			·
			<a href="#about"><?php echo esc_html( construction_t( 'nav.about' ) ); ?></a>
		</p>
		<p class="construction-footer__top">
			<a href="#top" aria-label="<?php echo esc_attr( construction_t( 'back.top' ) ); ?>">↑</a>
		</p>
	</div>
	<!-- /wp:html -->
</div>
<!-- /wp:group -->
