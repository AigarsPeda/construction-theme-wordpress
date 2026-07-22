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
<!-- wp:group {"align":"full","className":"construction-services","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull construction-services" id="services">
	<!-- wp:columns {"align":"wide","className":"construction-services__grid"} -->
	<div class="wp-block-columns alignwide construction-services__grid">
		<!-- wp:column {"width":"42%","className":"construction-services__intro"} -->
		<div class="wp-block-column construction-services__intro" style="flex-basis:42%">
			<!-- wp:heading {"fontSize":"x-large"} -->
			<h2 class="wp-block-heading has-x-large-font-size"><?php echo esc_html( construction_t( 'We handle design, planning, and installation' ) ); ?></h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p><?php esc_html_e( 'No tehniskā projekta līdz fasādes montāžai — viena komanda, skaidri termiņi un caurspīdīgs process.', 'construction' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"58%","className":"construction-services__list"} -->
		<div class="wp-block-column construction-services__list" style="flex-basis:58%">
			<!-- wp:group {"className":"construction-service-card","layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center"}} -->
			<div class="wp-block-group construction-service-card">
				<!-- wp:group {"className":"construction-service-card__thumb construction-service-card__thumb--1","layout":{"type":"constrained"}} -->
				<div class="wp-block-group construction-service-card__thumb construction-service-card__thumb--1"></div>
				<!-- /wp:group -->
				<!-- wp:group {"layout":{"type":"constrained"}} -->
				<div class="wp-block-group">
					<!-- wp:heading {"level":3} -->
					<h3 class="wp-block-heading"><?php esc_html_e( 'Projektēšana', 'construction' ); ?></h3>
					<!-- /wp:heading -->
					<!-- wp:paragraph -->
					<p><?php esc_html_e( 'Aprēķini, tehniskās shēmas un projektēšana no A līdz Z.', 'construction' ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->

			<!-- wp:group {"className":"construction-service-card","layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center"}} -->
			<div class="wp-block-group construction-service-card">
				<!-- wp:group {"className":"construction-service-card__thumb construction-service-card__thumb--2","layout":{"type":"constrained"}} -->
				<div class="wp-block-group construction-service-card__thumb construction-service-card__thumb--2"></div>
				<!-- /wp:group -->
				<!-- wp:group {"layout":{"type":"constrained"}} -->
				<div class="wp-block-group">
					<!-- wp:heading {"level":3} -->
					<h3 class="wp-block-heading"><?php esc_html_e( 'Fasāžu dizains', 'construction' ); ?></h3>
					<!-- /wp:heading -->
					<!-- wp:paragraph -->
					<p><?php esc_html_e( 'Ventilējamās fasādes ar arhitektūras vizualizāciju.', 'construction' ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->

			<!-- wp:group {"className":"construction-service-card","layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center"}} -->
			<div class="wp-block-group construction-service-card">
				<!-- wp:group {"className":"construction-service-card__thumb construction-service-card__thumb--3","layout":{"type":"constrained"}} -->
				<div class="wp-block-group construction-service-card__thumb construction-service-card__thumb--3"></div>
				<!-- /wp:group -->
				<!-- wp:group {"layout":{"type":"constrained"}} -->
				<div class="wp-block-group">
					<!-- wp:heading {"level":3} -->
					<h3 class="wp-block-heading"><?php esc_html_e( 'Fasāžu montāža', 'construction' ); ?></h3>
					<!-- /wp:heading -->
					<!-- wp:paragraph -->
					<p><?php esc_html_e( 'Profesionāla montāža un apdares materiālu uzstādīšana.', 'construction' ); ?></p>
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
