<?php
/**
 * Title: Header
 * Slug: construction/header
 * Categories: header, construction
 * Block Types: core/template-part/header
 * Description: Visual header: logo placeholder, name, menu, phone, language links.
 *
 * @package Construction
 */

$lang     = construction_current_lang();
$lv       = esc_url( construction_lang_url( 'lv' ) );
$en       = esc_url( construction_lang_url( 'en' ) );
$ru       = esc_url( construction_lang_url( 'ru' ) );
$home_url = esc_url( home_url( '/' ) );
$logo_src = esc_url( get_template_directory_uri() . '/assets/images/logo-placeholder.svg' );
?>
<!-- wp:group {"align":"full","className":"construction-header","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull construction-header">
	<!-- wp:group {"className":"construction-header__inner","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
	<div class="wp-block-group construction-header__inner">
		<!-- wp:group {"className":"construction-brand","layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center"}} -->
		<div class="wp-block-group construction-brand">
			<!-- wp:image {"sizeSlug":"full","linkDestination":"custom","className":"construction-logo-mark"} -->
			<figure class="wp-block-image size-full construction-logo-mark"><a href="<?php echo $home_url; ?>"><img src="<?php echo $logo_src; ?>" alt="Logo"/></a></figure>
			<!-- /wp:image -->

			<!-- wp:site-title {"level":0,"className":"construction-logo"} /-->
		</div>
		<!-- /wp:group -->

		<!-- wp:navigation {"overlayMenu":"mobile","overlayBackgroundColor":"base","overlayTextColor":"contrast","className":"construction-nav","__unstableLocation":"primary","layout":{"type":"flex","justifyContent":"center"}} /-->

		<!-- wp:group {"className":"construction-header__actions","layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center"}} -->
		<div class="wp-block-group construction-header__actions">
			<!-- wp:paragraph {"className":"construction-phone"} -->
			<p class="construction-phone"><a href="tel:+37120000000">+371 2000 0000</a></p>
			<!-- /wp:paragraph -->

			<!-- wp:paragraph {"className":"construction-lang"} -->
			<p class="construction-lang">
				<a href="<?php echo $lv; ?>"<?php echo 'lv' === $lang ? ' aria-current="true"' : ''; ?>>LV</a>
				<a href="<?php echo $en; ?>"<?php echo 'en' === $lang ? ' aria-current="true"' : ''; ?>>EN</a>
				<a href="<?php echo $ru; ?>"<?php echo 'ru' === $lang ? ' aria-current="true"' : ''; ?>>RU</a>
			</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
