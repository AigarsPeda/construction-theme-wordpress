<?php
/**
 * Title: Contact
 * Slug: construction/contact
 * Categories: contact, construction
 * Description: Contact strip with email, phone, address, social links, and mailto CTA.
 *
 * @package Construction
 */

$email      = esc_html( construction_contact( 'email' ) );
$phone      = esc_html( construction_contact( 'phone' ) );
$phone_href = esc_url( construction_contact_phone_href() );
$mail_href  = esc_url( construction_contact_mail_href() );
$address    = esc_html( construction_contact_address() );
?>
<!-- wp:group {"align":"full","className":"construction-contact","layout":{"type":"default"},"anchor":"contact"} -->
<div class="wp-block-group alignfull construction-contact" id="contact">
	<!-- wp:columns {"className":"construction-contact__grid","verticalAlignment":"center"} -->
	<div class="wp-block-columns construction-contact__grid are-vertically-aligned-center">
		<!-- wp:column {"verticalAlignment":"center","width":"40%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:40%">
			<!-- wp:paragraph {"className":"construction-contact__label"} -->
			<p class="construction-contact__label"><?php echo esc_html( construction_t( 'contact.label' ) ); ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"className":"construction-contact__email"} -->
			<p class="construction-contact__email"><a href="<?php echo $mail_href; ?>"><?php echo $email; ?></a></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"className":"construction-contact__phone"} -->
			<p class="construction-contact__phone"><a href="<?php echo $phone_href; ?>"><?php echo $phone; ?></a></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"className":"construction-contact__address"} -->
			<p class="construction-contact__address"><?php echo $address; ?></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"className":"construction-contact__social"} -->
			<p class="construction-contact__social"><a href="https://instagram.com/construction" target="_blank" rel="noopener">Instagram</a> · <a href="https://facebook.com/construction" target="_blank" rel="noopener">Facebook</a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"verticalAlignment":"center","width":"60%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:60%">
			<!-- wp:group {"className":"construction-lead-form construction-lead-form--visual","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
			<div class="wp-block-group construction-lead-form construction-lead-form--visual">
				<!-- wp:paragraph {"className":"construction-lead-form__hint"} -->
				<p class="construction-lead-form__hint"><?php echo esc_html( construction_t( 'contact.hint' ) ); ?></p>
				<!-- /wp:paragraph -->
				<!-- wp:buttons {"className":"construction-lead-form__actions"} -->
				<div class="wp-block-buttons construction-lead-form__actions">
					<!-- wp:button {"className":"construction-lead-form__go"} -->
					<div class="wp-block-button construction-lead-form__go"><a class="wp-block-button__link wp-element-button" href="<?php echo $mail_href; ?>"><?php echo esc_html( construction_t( 'contact.mail_cta' ) ); ?> →</a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
