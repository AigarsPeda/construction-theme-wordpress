<?php
/**
 * Title: Services
 * Slug: construction/services
 * Categories: services, construction
 * Description: Services intro with three service rows (editable blocks).
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
			<!-- wp:group {"className":"construction-service-card","layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"top"}} -->
			<div class="wp-block-group construction-service-card">
				<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"construction-service-card__thumb"} -->
				<figure class="wp-block-image size-full construction-service-card__thumb"><img src="<?php echo esc_url( construction_image_url( 'service_1' ) ); ?>" alt=""/></figure>
				<!-- /wp:image -->

				<!-- wp:group {"layout":{"type":"constrained"}} -->
				<div class="wp-block-group">
					<!-- wp:heading {"level":3} -->
					<h3 class="wp-block-heading"><?php echo esc_html( construction_t( 'services.item1.title' ) ); ?></h3>
					<!-- /wp:heading -->
					<!-- wp:paragraph -->
					<p><?php echo esc_html( construction_t( 'services.item1.text' ) ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->

			<!-- wp:group {"className":"construction-service-card","layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"top"}} -->
			<div class="wp-block-group construction-service-card">
				<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"construction-service-card__thumb"} -->
				<figure class="wp-block-image size-full construction-service-card__thumb"><img src="<?php echo esc_url( construction_image_url( 'service_2' ) ); ?>" alt=""/></figure>
				<!-- /wp:image -->
				<!-- wp:group {"layout":{"type":"constrained"}} -->
				<div class="wp-block-group">
					<!-- wp:heading {"level":3} -->
					<h3 class="wp-block-heading"><?php echo esc_html( construction_t( 'services.item2.title' ) ); ?></h3>
					<!-- /wp:heading -->
					<!-- wp:paragraph -->
					<p><?php echo esc_html( construction_t( 'services.item2.text' ) ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->

			<!-- wp:group {"className":"construction-service-card","layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"top"}} -->
			<div class="wp-block-group construction-service-card">
				<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"construction-service-card__thumb"} -->
				<figure class="wp-block-image size-full construction-service-card__thumb"><img src="<?php echo esc_url( construction_image_url( 'service_3' ) ); ?>" alt=""/></figure>
				<!-- /wp:image -->
				<!-- wp:group {"layout":{"type":"constrained"}} -->
				<div class="wp-block-group">
					<!-- wp:heading {"level":3} -->
					<h3 class="wp-block-heading"><?php echo esc_html( construction_t( 'services.item3.title' ) ); ?></h3>
					<!-- /wp:heading -->
					<!-- wp:paragraph -->
					<p><?php echo esc_html( construction_t( 'services.item3.text' ) ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
