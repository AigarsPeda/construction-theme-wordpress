<?php
/**
 * Title: Header
 * Slug: construction/header
 * Categories: header, construction
 * Block Types: core/template-part/header
 * Description: Logo left, menu center, phone + languages right. Mobile drawer for nav + languages.
 *
 * @package Construction
 */

$lang         = construction_current_lang();
$home_url     = esc_url( construction_home_url() );
$logo_src     = esc_url( construction_logo_url() );
$logo_alt     = esc_attr( construction_logo_alt() );
$menu_label   = esc_attr( construction_t( 'nav.menu' ) );
$close_label  = esc_attr( construction_t( 'nav.close' ) );
$lang_label   = esc_html( construction_t( 'nav.language' ) );
$phone        = esc_html( construction_contact( 'phone' ) );
$phone_href   = esc_url( construction_contact_phone_href() );
$email        = esc_html( construction_contact( 'email' ) );
$mail_href    = esc_url( 'mailto:' . construction_contact( 'email' ) );
$address      = esc_html( construction_contact_address( $lang ) );

$nav_html = wp_nav_menu(
	array(
		'theme_location' => 'primary',
		'container'      => false,
		'menu_class'     => 'construction-nav-list',
		'fallback_cb'    => false,
		'echo'           => false,
		'depth'          => 1,
	)
);

$drawer_nav_html = wp_nav_menu(
	array(
		'theme_location' => 'primary',
		'container'      => false,
		'menu_class'     => 'construction-drawer__nav-list',
		'fallback_cb'    => false,
		'echo'           => false,
		'depth'          => 1,
	)
);
?>
<!-- wp:group {"align":"full","className":"construction-header","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull construction-header">
	<!-- wp:html -->
	<div class="construction-header__inner">
		<div class="construction-brand">
			<figure class="construction-logo-mark"><a href="<?php echo $home_url; ?>"><img src="<?php echo $logo_src; ?>" alt="<?php echo $logo_alt; ?>" width="40" height="40"/></a></figure>
			<p class="construction-logo"><a href="<?php echo $home_url; ?>" rel="home"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></a></p>
		</div>

		<nav class="construction-nav construction-nav--desktop" aria-label="<?php echo esc_attr( construction_t( 'nav.projects' ) ); ?>">
			<?php echo $nav_html ? $nav_html : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</nav>

		<div class="construction-header__actions">
			<p class="construction-phone"><a href="<?php echo $phone_href; ?>"><?php echo $phone; ?></a></p>
			<?php echo construction_language_switcher_html( 'construction-lang construction-lang--desktop' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<button
				type="button"
				class="construction-menu-toggle"
				aria-expanded="false"
				aria-controls="construction-drawer"
				aria-label="<?php echo $menu_label; ?>"
				data-construction-menu-open
			>
				<span class="construction-menu-toggle__bars" aria-hidden="true"></span>
			</button>
		</div>
	</div>

	<div class="construction-drawer-root" data-construction-drawer hidden>
		<div class="construction-drawer__backdrop" data-construction-menu-close tabindex="-1"></div>
		<aside
			id="construction-drawer"
			class="construction-drawer"
			role="dialog"
			aria-modal="true"
			aria-label="<?php echo $menu_label; ?>"
		>
			<div class="construction-drawer__top">
				<button type="button" class="construction-drawer__close" data-construction-menu-close aria-label="<?php echo $close_label; ?>">
					<span aria-hidden="true">×</span>
				</button>
			</div>

			<nav class="construction-drawer__nav" aria-label="<?php echo $menu_label; ?>">
				<?php echo $drawer_nav_html ? $drawer_nav_html : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</nav>

			<div class="construction-drawer__langs">
				<p class="construction-drawer__langs-label"><?php echo $lang_label; ?></p>
				<?php echo construction_language_switcher_html( 'construction-lang construction-lang--drawer' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>

			<div class="construction-drawer__contact">
				<a class="construction-drawer__contact-link" href="<?php echo $mail_href; ?>"><?php echo $email; ?></a>
				<a class="construction-drawer__contact-link" href="<?php echo $phone_href; ?>"><?php echo $phone; ?></a>
				<p class="construction-drawer__contact-address">
					<span class="construction-drawer__contact-label"><?php echo esc_html( construction_t( 'contact.address_label' ) ); ?></span>
					<?php echo $address; ?>
				</p>
			</div>
		</aside>
	</div>
	<!-- /wp:html -->
</div>
<!-- /wp:group -->
