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
<!-- wp:group {"align":"full","className":"construction-contact","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull construction-contact" id="contact">
	<!-- wp:html -->
	<div class="construction-contact__grid">
		<div class="construction-contact__info">
			<p class="construction-contact__label"><?php echo esc_html( construction_t( 'contact.label' ) ); ?></p>
			<p class="construction-contact__email"><a href="mailto:info@construction.lv">info@construction.lv</a></p>
			<p class="construction-contact__telegram"><a href="https://t.me/construction" target="_blank" rel="noopener">Telegram · @construction</a></p>
		</div>
		<form class="construction-lead-form" action="mailto:info@construction.lv" method="post" enctype="text/plain">
			<label class="screen-reader-text" for="construction-lead"><?php echo esc_html( construction_t( 'contact.field' ) ); ?></label>
			<div class="construction-lead-form__row">
				<input id="construction-lead" type="text" name="contact" placeholder="<?php echo esc_attr( construction_t( 'contact.placeholder' ) ); ?>" required />
				<button type="submit" aria-label="<?php echo esc_attr( construction_t( 'contact.submit' ) ); ?>">→</button>
			</div>
		</form>
	</div>
	<!-- /wp:html -->
</div>
<!-- /wp:group -->
