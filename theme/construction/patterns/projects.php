<?php
/**
 * Title: Projects gallery
 * Slug: construction/projects
 * Categories: gallery, construction
 * Description: Compact project photo gallery page.
 *
 * @package Construction
 */

$contact_href = esc_url( trailingslashit( construction_home_url() ) . '#contact' );
?>
<!-- wp:group {"align":"full","className":"construction-projects","layout":{"type":"default"},"anchor":"projects"} -->
<div class="wp-block-group alignfull construction-projects" id="projects">
	<!-- wp:group {"className":"construction-projects__inner","layout":{"type":"default"}} -->
	<div class="wp-block-group construction-projects__inner">
		<!-- wp:group {"className":"construction-projects__head","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"end"}} -->
		<div class="wp-block-group construction-projects__head">
			<!-- wp:heading {"level":1,"className":"construction-projects__title"} -->
			<h1 class="wp-block-heading construction-projects__title"><?php echo esc_html( construction_t( 'projects.title' ) ); ?></h1>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"className":"construction-projects__cta-inline"} -->
			<p class="construction-projects__cta-inline"><a href="<?php echo $contact_href; ?>"><?php echo esc_html( construction_t( 'projects.cta' ) ); ?> →</a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"construction-projects__grid","layout":{"type":"default"}} -->
		<div class="wp-block-group construction-projects__grid">
			<?php foreach ( construction_project_images() as $key => $meta ) : ?>
				<?php
				$alt_key = isset( $meta['alt_key'] ) ? $meta['alt_key'] : 'projects.title';
				echo construction_media_image_block( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$key,
					'construction-projects__item',
					construction_t( $alt_key ),
					'large',
					true
				);
				?>
			<?php endforeach; ?>
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
