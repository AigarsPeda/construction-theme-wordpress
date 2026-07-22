<?php
/**
 * Title: Services
 * Slug: buvnams/services
 * Categories: services, buvnams
 * Description: Services intro with three service rows.
 *
 * @package BuvNams
 */
?>
<!-- wp:group {"align":"full","className":"buvnams-services","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull buvnams-services" id="services">
	<!-- wp:columns {"align":"wide","className":"buvnams-services__grid"} -->
	<div class="wp-block-columns alignwide buvnams-services__grid">
		<!-- wp:column {"width":"42%","className":"buvnams-services__intro"} -->
		<div class="wp-block-column buvnams-services__intro" style="flex-basis:42%">
			<!-- wp:heading {"fontSize":"x-large"} -->
			<h2 class="wp-block-heading has-x-large-font-size"><?php echo esc_html( buvnams_t( 'We handle design, planning, and installation' ) ); ?></h2>
			<!-- /wp:heading -->

			<!-- wp:paragraph -->
			<p><?php esc_html_e( 'No tehniskā projekta līdz fasādes montāžai — viena komanda, skaidri termiņi un caurspīdīgs process.', 'buvnams' ); ?></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"58%","className":"buvnams-services__list"} -->
		<div class="wp-block-column buvnams-services__list" style="flex-basis:58%">
			<!-- wp:group {"className":"buvnams-service-card","layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center"}} -->
			<div class="wp-block-group buvnams-service-card">
				<!-- wp:group {"className":"buvnams-service-card__thumb buvnams-service-card__thumb--1","layout":{"type":"constrained"}} -->
				<div class="wp-block-group buvnams-service-card__thumb buvnams-service-card__thumb--1"></div>
				<!-- /wp:group -->
				<!-- wp:group {"layout":{"type":"constrained"}} -->
				<div class="wp-block-group">
					<!-- wp:heading {"level":3} -->
					<h3 class="wp-block-heading"><?php esc_html_e( 'Projektēšana', 'buvnams' ); ?></h3>
					<!-- /wp:heading -->
					<!-- wp:paragraph -->
					<p><?php esc_html_e( 'Aprēķini, tehniskās shēmas un projektēšana no A līdz Z.', 'buvnams' ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->

			<!-- wp:group {"className":"buvnams-service-card","layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center"}} -->
			<div class="wp-block-group buvnams-service-card">
				<!-- wp:group {"className":"buvnams-service-card__thumb buvnams-service-card__thumb--2","layout":{"type":"constrained"}} -->
				<div class="wp-block-group buvnams-service-card__thumb buvnams-service-card__thumb--2"></div>
				<!-- /wp:group -->
				<!-- wp:group {"layout":{"type":"constrained"}} -->
				<div class="wp-block-group">
					<!-- wp:heading {"level":3} -->
					<h3 class="wp-block-heading"><?php esc_html_e( 'Fasāžu dizains', 'buvnams' ); ?></h3>
					<!-- /wp:heading -->
					<!-- wp:paragraph -->
					<p><?php esc_html_e( 'Ventilējamās fasādes ar arhitektūras vizualizāciju.', 'buvnams' ); ?></p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->

			<!-- wp:group {"className":"buvnams-service-card","layout":{"type":"flex","flexWrap":"nowrap","verticalAlignment":"center"}} -->
			<div class="wp-block-group buvnams-service-card">
				<!-- wp:group {"className":"buvnams-service-card__thumb buvnams-service-card__thumb--3","layout":{"type":"constrained"}} -->
				<div class="wp-block-group buvnams-service-card__thumb buvnams-service-card__thumb--3"></div>
				<!-- /wp:group -->
				<!-- wp:group {"layout":{"type":"constrained"}} -->
				<div class="wp-block-group">
					<!-- wp:heading {"level":3} -->
					<h3 class="wp-block-heading"><?php esc_html_e( 'Fasāžu montāža', 'buvnams' ); ?></h3>
					<!-- /wp:heading -->
					<!-- wp:paragraph -->
					<p><?php esc_html_e( 'Profesionāla montāža un apdares materiālu uzstādīšana.', 'buvnams' ); ?></p>
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
