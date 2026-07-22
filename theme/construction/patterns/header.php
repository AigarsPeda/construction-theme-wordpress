<?php
/**
 * Title: Header
 * Slug: construction/header
 * Categories: header, construction
 * Block Types: core/template-part/header
 * Description: Site header with logo, navigation, and phone.
 *
 * @package Construction
 */
?>
<!-- wp:group {"align":"full","className":"construction-header","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull construction-header">
	<!-- wp:group {"className":"construction-header__inner","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
	<div class="wp-block-group construction-header__inner">
		<!-- wp:site-title {"level":0,"className":"construction-logo"} /-->

		<!-- wp:navigation {"overlayBackgroundColor":"base","overlayTextColor":"contrast","className":"construction-nav","layout":{"type":"flex","justifyContent":"center"}} /-->

		<!-- wp:paragraph {"className":"construction-phone"} -->
		<p class="construction-phone"><a href="tel:+37120000000">+371 2000 0000</a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
