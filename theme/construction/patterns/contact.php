<?php
/**
 * Title: Contact
 * Slug: construction/contact
 * Categories: contact, construction
 * Description: Contact strip with email, Telegram, and lead field.
 *
 * @package Construction
 */
?>
<!-- wp:group {"align":"full","className":"construction-contact","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull construction-contact" id="contact">
	<!-- wp:columns {"align":"wide","className":"construction-contact__grid","verticalAlignment":"center"} -->
	<div class="wp-block-columns alignwide construction-contact__grid are-vertically-aligned-center">
		<!-- wp:column {"verticalAlignment":"center","width":"35%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:35%">
			<!-- wp:paragraph {"className":"construction-contact__label"} -->
			<p class="construction-contact__label"><?php echo esc_html( construction_t( 'Get in touch' ) ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"className":"construction-contact__email"} -->
			<p class="construction-contact__email"><a href="mailto:info@construction.lv">info@construction.lv</a></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"className":"construction-contact__telegram"} -->
			<p class="construction-contact__telegram"><a href="https://t.me/construction" target="_blank" rel="noopener">Telegram · @construction</a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"verticalAlignment":"center","width":"65%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:65%">
			<!-- wp:html -->
			<form class="construction-lead-form" action="mailto:info@construction.lv" method="post" enctype="text/plain">
				<label class="screen-reader-text" for="construction-lead"><?php esc_html_e( 'E-pasts / tālrunis', 'construction' ); ?></label>
				<div class="construction-lead-form__row">
					<input id="construction-lead" type="text" name="contact" placeholder="<?php esc_attr_e( 'e-pasts / numurs', 'construction' ); ?>" required />
					<button type="submit" aria-label="<?php esc_attr_e( 'Nosūtīt', 'construction' ); ?>">→</button>
				</div>
			</form>
			<!-- /wp:html -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
