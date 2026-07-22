<?php
/**
 * Title: Quality
 * Slug: construction/quality
 * Categories: featured, construction
 * Description: Four-column quality highlights grid.
 *
 * @package Construction
 */
?>
<!-- wp:group {"align":"full","className":"construction-quality","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull construction-quality" id="about">
	<!-- wp:html -->
	<div class="construction-quality__inner">
		<h2><?php echo esc_html( construction_t( 'quality.title' ) ); ?></h2>
		<div class="construction-quality__grid">
			<div class="construction-quality__card">
				<div class="construction-quality__media">
					<img src="<?php echo esc_url( construction_image_url( 'quality_1' ) ); ?>" alt="" loading="lazy" decoding="async" />
				</div>
				<h3><?php echo esc_html( construction_t( 'quality.item1' ) ); ?></h3>
			</div>
			<div class="construction-quality__card">
				<div class="construction-quality__media">
					<img src="<?php echo esc_url( construction_image_url( 'quality_2' ) ); ?>" alt="" loading="lazy" decoding="async" />
				</div>
				<h3><?php echo esc_html( construction_t( 'quality.item2' ) ); ?></h3>
			</div>
			<div class="construction-quality__card">
				<div class="construction-quality__media">
					<img src="<?php echo esc_url( construction_image_url( 'quality_3' ) ); ?>" alt="" loading="lazy" decoding="async" />
				</div>
				<h3><?php echo esc_html( construction_t( 'quality.item3' ) ); ?></h3>
			</div>
			<div class="construction-quality__card">
				<div class="construction-quality__media">
					<img src="<?php echo esc_url( construction_image_url( 'quality_4' ) ); ?>" alt="" loading="lazy" decoding="async" />
				</div>
				<h3><?php echo esc_html( construction_t( 'quality.item4' ) ); ?></h3>
			</div>
		</div>
	</div>
	<!-- /wp:html -->
</div>
<!-- /wp:group -->
