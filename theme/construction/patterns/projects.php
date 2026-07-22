<?php
/**
 * Title: Projects gallery
 * Slug: construction/projects
 * Categories: gallery, construction
 * Description: Project photo gallery page (Media Library images).
 *
 * @package Construction
 */

$contact_href = esc_url( trailingslashit( home_url( '/' ) ) . '#contact' );
?>
<!-- wp:group {"align":"full","className":"construction-projects","layout":{"type":"default"},"anchor":"projects"} -->
<div class="wp-block-group alignfull construction-projects" id="projects">
	<!-- wp:group {"className":"construction-projects__inner","layout":{"type":"default"}} -->
	<div class="wp-block-group construction-projects__inner">
		<!-- wp:paragraph {"className":"construction-eyebrow"} -->
		<p class="construction-eyebrow"><?php echo esc_html( construction_t( 'projects.eyebrow' ) ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:heading {"level":1,"className":"construction-projects__title"} -->
		<h1 class="wp-block-heading construction-projects__title"><?php echo esc_html( construction_t( 'projects.title' ) ); ?></h1>
		<!-- /wp:heading -->

		<!-- wp:paragraph {"className":"construction-projects__intro"} -->
		<p class="construction-projects__intro"><?php echo esc_html( construction_t( 'projects.intro' ) ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:group {"className":"construction-projects__grid","layout":{"type":"default"}} -->
		<div class="wp-block-group construction-projects__grid">
			<?php foreach ( construction_project_images() as $key => $meta ) : ?>
				<?php
				$alt_key = isset( $meta['alt_key'] ) ? $meta['alt_key'] : 'projects.title';
				echo construction_media_image_block( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- helper escapes.
					$key,
					'construction-projects__item',
					construction_t( $alt_key ),
					'large'
				);
				?>
			<?php endforeach; ?>
		</div>
		<!-- /wp:group -->

		<!-- wp:buttons {"className":"construction-projects__actions"} -->
		<div class="wp-block-buttons construction-projects__actions">
			<!-- wp:button -->
			<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo $contact_href; ?>"><?php echo esc_html( construction_t( 'projects.cta' ) ); ?></a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
