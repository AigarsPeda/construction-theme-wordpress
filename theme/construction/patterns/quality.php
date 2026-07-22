<?php
/**
 * Title: Quality
 * Slug: construction/quality
 * Categories: featured, construction
 * Description: Four-column quality highlights grid (editable blocks).
 *
 * @package Construction
 */
?>
<!-- wp:group {"align":"full","className":"construction-quality","layout":{"type":"default"},"anchor":"about"} -->
<div class="wp-block-group alignfull construction-quality" id="about">
	<!-- wp:group {"className":"construction-quality__inner","layout":{"type":"constrained","contentSize":"1200px"}} -->
	<div class="wp-block-group construction-quality__inner">
		<!-- wp:heading -->
		<h2 class="wp-block-heading"><?php echo esc_html( construction_t( 'quality.title' ) ); ?></h2>
		<!-- /wp:heading -->

		<!-- wp:columns {"className":"construction-quality__grid"} -->
		<div class="wp-block-columns construction-quality__grid">
			<!-- wp:column -->
			<div class="wp-block-column">
				<!-- wp:group {"className":"construction-quality__card","layout":{"type":"constrained"}} -->
				<div class="wp-block-group construction-quality__card">
					<!-- wp:image {"sizeSlug":"large","className":"construction-quality__media"} -->
					<figure class="wp-block-image size-large construction-quality__media"><img src="<?php echo esc_url( construction_image_url( 'quality_1' ) ); ?>" alt=""/></figure>
					<!-- /wp:image -->
					<!-- wp:heading {"level":3} -->
					<h3 class="wp-block-heading"><?php echo esc_html( construction_t( 'quality.item1' ) ); ?></h3>
					<!-- /wp:heading -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column -->
			<div class="wp-block-column">
				<!-- wp:group {"className":"construction-quality__card","layout":{"type":"constrained"}} -->
				<div class="wp-block-group construction-quality__card">
					<!-- wp:image {"sizeSlug":"large","className":"construction-quality__media"} -->
					<figure class="wp-block-image size-large construction-quality__media"><img src="<?php echo esc_url( construction_image_url( 'quality_2' ) ); ?>" alt=""/></figure>
					<!-- /wp:image -->
					<!-- wp:heading {"level":3} -->
					<h3 class="wp-block-heading"><?php echo esc_html( construction_t( 'quality.item2' ) ); ?></h3>
					<!-- /wp:heading -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column -->
			<div class="wp-block-column">
				<!-- wp:group {"className":"construction-quality__card","layout":{"type":"constrained"}} -->
				<div class="wp-block-group construction-quality__card">
					<!-- wp:image {"sizeSlug":"large","className":"construction-quality__media"} -->
					<figure class="wp-block-image size-large construction-quality__media"><img src="<?php echo esc_url( construction_image_url( 'quality_3' ) ); ?>" alt=""/></figure>
					<!-- /wp:image -->
					<!-- wp:heading {"level":3} -->
					<h3 class="wp-block-heading"><?php echo esc_html( construction_t( 'quality.item3' ) ); ?></h3>
					<!-- /wp:heading -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:column -->

			<!-- wp:column -->
			<div class="wp-block-column">
				<!-- wp:group {"className":"construction-quality__card","layout":{"type":"constrained"}} -->
				<div class="wp-block-group construction-quality__card">
					<!-- wp:image {"sizeSlug":"large","className":"construction-quality__media"} -->
					<figure class="wp-block-image size-large construction-quality__media"><img src="<?php echo esc_url( construction_image_url( 'quality_4' ) ); ?>" alt=""/></figure>
					<!-- /wp:image -->
					<!-- wp:heading {"level":3} -->
					<h3 class="wp-block-heading"><?php echo esc_html( construction_t( 'quality.item4' ) ); ?></h3>
					<!-- /wp:heading -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
