<?php
/**
 * Title: Projects
 * Slug: construction/projects
 * Categories: gallery, construction
 * Description: Named projects with top detail viewer and grid below.
 *
 * @package Construction
 */

$lang         = construction_current_lang();
$contact_href = esc_url( construction_contacts_url_for_lang( $lang ) );
$label_close  = esc_attr( construction_t( 'projects.close' ) );
$label_prev   = esc_attr( construction_t( 'projects.prev' ) );
$label_next   = esc_attr( construction_t( 'projects.next' ) );
?>
<!-- wp:group {"align":"full","className":"construction-projects","layout":{"type":"default"},"anchor":"projects"} -->
<div class="wp-block-group alignfull construction-projects" id="projects" data-label-close="<?php echo $label_close; ?>" data-label-prev="<?php echo $label_prev; ?>" data-label-next="<?php echo $label_next; ?>">
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

		<!-- wp:paragraph {"className":"construction-projects__intro"} -->
		<p class="construction-projects__intro"><?php echo esc_html( construction_t( 'projects.intro' ) ); ?></p>
		<!-- /wp:paragraph -->

		<!-- wp:html -->
		<div class="construction-project-viewer" hidden aria-live="polite"></div>
		<!-- /wp:html -->

		<!-- wp:group {"className":"construction-projects__grid","layout":{"type":"default"}} -->
		<div class="wp-block-group construction-projects__grid">
			<?php foreach ( construction_project_entries() as $entry ) : ?>
				<?php
				$slug    = sanitize_title( (string) $entry['slug'] );
				$gallery = 'project-' . $slug;
				$cover   = (string) $entry['cover'];
				$images  = isset( $entry['images'] ) && is_array( $entry['images'] ) ? $entry['images'] : array( $cover );
				$title   = construction_t( "projects.item.{$slug}.title" );
				$text    = construction_t( "projects.item.{$slug}.text" );
				$cover_alt = $title;
				$cover_meta = construction_media_catalog()[ $cover ] ?? null;
				if ( is_array( $cover_meta ) && ! empty( $cover_meta['alt_key'] ) ) {
					$cover_alt = construction_t( (string) $cover_meta['alt_key'] );
				}
				?>
			<!-- wp:group {"className":"construction-project-card","layout":{"type":"constrained"},"anchor":"<?php echo esc_attr( $slug ); ?>"} -->
			<div class="wp-block-group construction-project-card" id="<?php echo esc_attr( $slug ); ?>" data-project-slug="<?php echo esc_attr( $slug ); ?>">
				<?php
				echo construction_media_image_block( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$cover,
					'construction-project-card__cover',
					$cover_alt,
					'large',
					true,
					false,
					$gallery
				);
				?>
				<!-- wp:heading {"level":2,"className":"construction-project-card__title"} -->
				<h2 class="wp-block-heading construction-project-card__title"><?php echo esc_html( $title ); ?></h2>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"className":"construction-project-card__text"} -->
				<p class="construction-project-card__text"><?php echo esc_html( $text ); ?></p>
				<!-- /wp:paragraph -->
				<!-- wp:group {"className":"construction-project-card__more","layout":{"type":"default"}} -->
				<div class="wp-block-group construction-project-card__more">
					<?php
					foreach ( $images as $img_key ) {
						$img_key = (string) $img_key;
						if ( $img_key === $cover ) {
							continue;
						}
						$alt_key = 'projects.title';
						$meta    = construction_media_catalog()[ $img_key ] ?? null;
						if ( is_array( $meta ) && ! empty( $meta['alt_key'] ) ) {
							$alt_key = (string) $meta['alt_key'];
						}
						echo construction_media_image_block( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							$img_key,
							'construction-project-card__extra',
							construction_t( $alt_key ),
							'medium_large',
							true,
							false,
							$gallery
						);
					}
					?>
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->
			<?php endforeach; ?>
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
