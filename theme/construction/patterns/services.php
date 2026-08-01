<?php
/**
 * Title: Services
 * Slug: construction/services
 * Categories: services, construction
 * Description: Services intro with eight service cards (editable blocks).
 *
 * @package Construction
 */
?>
<!-- wp:group {"align":"full","className":"construction-services","layout":{"type":"default"},"anchor":"services"} -->
<div class="wp-block-group alignfull construction-services" id="services">
	<!-- wp:columns {"className":"construction-services__grid"} -->
	<div class="wp-block-columns construction-services__grid">
		<!-- wp:column {"width":"42%","className":"construction-services__intro"} -->
		<div class="wp-block-column construction-services__intro" style="flex-basis:42%">
			<!-- wp:heading -->
			<h2 class="wp-block-heading"><?php echo esc_html( construction_t( 'services.title' ) ); ?></h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p><?php echo esc_html( construction_t( 'services.intro' ) ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"58%","className":"construction-services__list"} -->
		<div class="wp-block-column construction-services__list" style="flex-basis:58%">
			<?php for ( $i = 1; $i <= 8; $i++ ) : ?>
			<!-- wp:group {"className":"construction-service-card","layout":{"type":"constrained"}} -->
			<div class="wp-block-group construction-service-card">
				<!-- wp:heading {"level":3} -->
				<h3 class="wp-block-heading"><?php echo esc_html( construction_t( "services.item{$i}.title" ) ); ?></h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph -->
				<p><?php echo esc_html( construction_t( "services.item{$i}.text" ) ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
			<?php endfor; ?>
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
