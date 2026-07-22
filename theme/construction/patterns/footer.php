<?php
/**
 * Title: Footer
 * Slug: construction/footer
 * Categories: footer, construction
 * Block Types: core/template-part/footer
 * Description: Footer with text links and back-to-top (editable blocks).
 *
 * @package Construction
 */

$lang         = construction_current_lang();
$project_ids  = get_option( 'construction_projects_page_ids', array() );
$projects_url = ( is_array( $project_ids ) && ! empty( $project_ids[ $lang ] ) )
	? (string) get_permalink( (int) $project_ids[ $lang ] )
	: home_url( '/projekti/' );
$home_url     = construction_front_url_for_lang( $lang );
?>
<!-- wp:group {"align":"full","className":"construction-footer","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull construction-footer">
	<!-- wp:group {"className":"construction-footer__inner","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
	<div class="wp-block-group construction-footer__inner">
		<!-- wp:paragraph {"className":"construction-footer__links"} -->
		<p class="construction-footer__links"><a href="<?php echo esc_url( $projects_url ); ?>"><?php echo esc_html( construction_t( 'nav.projects' ) ); ?></a> · <a href="<?php echo esc_url( trailingslashit( $home_url ) . '#about' ); ?>"><?php echo esc_html( construction_t( 'nav.about' ) ); ?></a> · <a href="<?php echo esc_url( trailingslashit( $home_url ) . '#contact' ); ?>"><?php echo esc_html( construction_t( 'nav.contact' ) ); ?></a></p>
		<!-- /wp:paragraph -->

		<!-- wp:paragraph {"className":"construction-footer__top"} -->
		<p class="construction-footer__top"><a href="#top" aria-label="<?php echo esc_attr( construction_t( 'back.top' ) ); ?>">↑</a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
