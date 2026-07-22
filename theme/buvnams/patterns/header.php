<?php
/**
 * Title: Header
 * Slug: buvnams/header
 * Categories: header, buvnams
 * Block Types: core/template-part/header
 * Description: Site header with logo, navigation, and phone.
 *
 * @package BuvNams
 */
?>
<!-- wp:group {"align":"full","className":"buvnams-header","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull buvnams-header">
	<!-- wp:group {"className":"buvnams-header__inner","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
	<div class="wp-block-group buvnams-header__inner">
		<!-- wp:site-title {"level":0,"className":"buvnams-logo"} /-->

		<!-- wp:navigation {"overlayBackgroundColor":"base","overlayTextColor":"contrast","className":"buvnams-nav","layout":{"type":"flex","justifyContent":"center"}} /-->

		<!-- wp:paragraph {"className":"buvnams-phone"} -->
		<p class="buvnams-phone"><a href="tel:+37120000000">+371 2000 0000</a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
