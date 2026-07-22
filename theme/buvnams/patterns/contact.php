<?php
/**
 * Title: Contact
 * Slug: buvnams/contact
 * Categories: contact, buvnams
 * Description: Contact strip with email, Telegram, and lead field.
 *
 * @package BuvNams
 */
?>
<!-- wp:group {"align":"full","className":"buvnams-contact","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull buvnams-contact" id="contact">
	<!-- wp:columns {"align":"wide","className":"buvnams-contact__grid","verticalAlignment":"center"} -->
	<div class="wp-block-columns alignwide buvnams-contact__grid are-vertically-aligned-center">
		<!-- wp:column {"verticalAlignment":"center","width":"35%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:35%">
			<!-- wp:paragraph {"className":"buvnams-contact__label"} -->
			<p class="buvnams-contact__label"><?php echo esc_html( buvnams_t( 'Get in touch' ) ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"className":"buvnams-contact__email"} -->
			<p class="buvnams-contact__email"><a href="mailto:info@buvnams.lv">info@buvnams.lv</a></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"className":"buvnams-contact__telegram"} -->
			<p class="buvnams-contact__telegram"><a href="https://t.me/buvnams" target="_blank" rel="noopener">Telegram · @buvnams</a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"verticalAlignment":"center","width":"65%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:65%">
			<!-- wp:html -->
			<form class="buvnams-lead-form" action="mailto:info@buvnams.lv" method="post" enctype="text/plain">
				<label class="screen-reader-text" for="buvnams-lead"><?php esc_html_e( 'E-pasts / tālrunis', 'buvnams' ); ?></label>
				<div class="buvnams-lead-form__row">
					<input id="buvnams-lead" type="text" name="contact" placeholder="<?php esc_attr_e( 'e-pasts / numurs', 'buvnams' ); ?>" required />
					<button type="submit" aria-label="<?php esc_attr_e( 'Nosūtīt', 'buvnams' ); ?>">→</button>
				</div>
			</form>
			<!-- /wp:html -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
