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
ob_start();
wp_nav_menu(
	array(
		'theme_location' => 'primary',
		'container'      => false,
		'menu_class'     => 'construction-nav-list',
		'fallback_cb'    => false,
		'depth'          => 1,
	)
);
$menu_html = trim( (string) ob_get_clean() );
?>
<!-- wp:group {"align":"full","className":"construction-header","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull construction-header">
	<!-- wp:html -->
	<div class="construction-header__inner">
		<p class="construction-logo"><a href="<?php echo esc_url( construction_lang_url( $lang ) ); ?>"><?php bloginfo( 'name' ); ?></a></p>
		<nav class="construction-nav" aria-label="<?php esc_attr_e( 'Primary', 'construction' ); ?>">
			<?php echo $menu_html ? $menu_html : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_nav_menu markup ?>
		</nav>
		<div class="construction-header__actions">
			<p class="construction-phone"><a href="tel:+37120000000">+371 2000 0000</a></p>
			<ul class="construction-lang" aria-label="<?php echo esc_attr( construction_t( 'lang.label' ) ); ?>">
				<li><a href="<?php echo esc_url( construction_lang_url( 'lv' ) ); ?>"<?php echo 'lv' === $lang ? ' aria-current="true"' : ''; ?>>LV</a></li>
				<li><a href="<?php echo esc_url( construction_lang_url( 'en' ) ); ?>"<?php echo 'en' === $lang ? ' aria-current="true"' : ''; ?>>EN</a></li>
				<li><a href="<?php echo esc_url( construction_lang_url( 'ru' ) ); ?>"<?php echo 'ru' === $lang ? ' aria-current="true"' : ''; ?>>RU</a></li>
			</ul>
		</div>
	</div>
	<!-- /wp:html -->
</div>
<!-- /wp:group -->
