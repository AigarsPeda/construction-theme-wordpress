<?php
/**
 * Title: Services
 * Slug: construction/services
 * Categories: services, construction
 * Description: Services intro with three service rows.
 *
 * @package Construction
 */
?>
<!-- wp:group {"align":"full","className":"construction-services","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull construction-services" id="services">
	<!-- wp:html -->
	<div class="construction-services__grid">
		<div class="construction-services__intro">
			<h2><?php echo esc_html( construction_t( 'services.title' ) ); ?></h2>
			<p><?php echo esc_html( construction_t( 'services.intro' ) ); ?></p>
		</div>
		<div class="construction-services__list">
			<div class="construction-service-card">
				<div class="construction-service-card__thumb">
					<img src="<?php echo esc_url( construction_image_url( 'service_1' ) ); ?>" alt="" loading="lazy" decoding="async" />
				</div>
				<div>
					<h3><?php echo esc_html( construction_t( 'services.item1.title' ) ); ?></h3>
					<p><?php echo esc_html( construction_t( 'services.item1.text' ) ); ?></p>
				</div>
			</div>
			<div class="construction-service-card">
				<div class="construction-service-card__thumb">
					<img src="<?php echo esc_url( construction_image_url( 'service_2' ) ); ?>" alt="" loading="lazy" decoding="async" />
				</div>
				<div>
					<h3><?php echo esc_html( construction_t( 'services.item2.title' ) ); ?></h3>
					<p><?php echo esc_html( construction_t( 'services.item2.text' ) ); ?></p>
				</div>
			</div>
			<div class="construction-service-card">
				<div class="construction-service-card__thumb">
					<img src="<?php echo esc_url( construction_image_url( 'service_3' ) ); ?>" alt="" loading="lazy" decoding="async" />
				</div>
				<div>
					<h3><?php echo esc_html( construction_t( 'services.item3.title' ) ); ?></h3>
					<p><?php echo esc_html( construction_t( 'services.item3.text' ) ); ?></p>
				</div>
			</div>
		</div>
	</div>
	<!-- /wp:html -->
</div>
<!-- /wp:group -->
