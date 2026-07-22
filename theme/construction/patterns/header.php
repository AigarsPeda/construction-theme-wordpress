<?php
/**
 * Title: Header
 * Slug: construction/header
 * Categories: header, construction
 * Block Types: core/template-part/header
 * Description: Logo left, menu center, phone + languages right.
 *
 * @package Construction
 */

$lang     = construction_current_lang();
$lv       = esc_url( construction_lang_url( 'lv' ) );
$en       = esc_url( construction_lang_url( 'en' ) );
$ru       = esc_url( construction_lang_url( 'ru' ) );
$home_url = esc_url( home_url( '/' ) );
$logo_src = esc_url( get_template_directory_uri() . '/assets/images/logo-placeholder.svg' );

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
?>
<!-- wp:group {"align":"full","className":"construction-header","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull construction-header">
	<!-- wp:html -->
	<div class="construction-header__inner">
		<div class="construction-brand">
			<figure class="construction-logo-mark"><a href="<?php echo $home_url; ?>"><img src="<?php echo $logo_src; ?>" alt="Logo" width="40" height="40"/></a></figure>
			<p class="construction-logo"><a href="<?php echo $home_url; ?>" rel="home"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></a></p>
		</div>

		<nav class="construction-nav" aria-label="<?php echo esc_attr( construction_t( 'nav.projects' ) ); ?>">
			<?php echo $nav_html ? $nav_html : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_nav_menu escapes. ?>
		</nav>

		<div class="construction-header__actions">
			<p class="construction-phone"><a href="tel:+37120000000">+371 2000 0000</a></p>
			<p class="construction-lang">
				<a href="<?php echo $lv; ?>"<?php echo 'lv' === $lang ? ' aria-current="true"' : ''; ?>>LV</a>
				<a href="<?php echo $en; ?>"<?php echo 'en' === $lang ? ' aria-current="true"' : ''; ?>>EN</a>
				<a href="<?php echo $ru; ?>"<?php echo 'ru' === $lang ? ' aria-current="true"' : ''; ?>>RU</a>
			</p>
		</div>
	</div>
	<!-- /wp:html -->
</div>
<!-- /wp:group -->
