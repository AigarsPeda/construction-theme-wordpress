<?php
/**
 * Title: Header
 * Slug: construction/header
 * Categories: header, construction
 * Block Types: core/template-part/header
 * Description: Responsive site header with logo, navigation, phone, and language switcher.
 *
 * @package Construction
 */

$lang = construction_current_lang();
?>
<!-- wp:group {"align":"full","className":"construction-header","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull construction-header">
	<!-- wp:group {"className":"construction-header__inner","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
	<div class="wp-block-group construction-header__inner">
		<!-- wp:site-title {"level":0,"className":"construction-logo"} /-->

		<!-- wp:navigation {"overlayMenu":"mobile","overlayBackgroundColor":"base","overlayTextColor":"contrast","className":"construction-nav","layout":{"type":"flex","justifyContent":"center"}} /-->

		<!-- wp:html -->
		<div class="construction-header__actions">
			<p class="construction-phone"><a href="tel:+37120000000">+371 2000 0000</a></p>
			<ul class="construction-lang" aria-label="<?php echo esc_attr( construction_t( 'lang.label' ) ); ?>">
				<li><a href="<?php echo esc_url( construction_lang_url( 'lv' ) ); ?>"<?php echo 'lv' === $lang ? ' aria-current="true"' : ''; ?>>LV</a></li>
				<li><a href="<?php echo esc_url( construction_lang_url( 'en' ) ); ?>"<?php echo 'en' === $lang ? ' aria-current="true"' : ''; ?>>EN</a></li>
				<li><a href="<?php echo esc_url( construction_lang_url( 'ru' ) ); ?>"<?php echo 'ru' === $lang ? ' aria-current="true"' : ''; ?>>RU</a></li>
			</ul>
		</div>
		<!-- /wp:html -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
