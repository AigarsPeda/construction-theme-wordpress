<?php
/**
 * Dynamic Gutenberg blocks for projects (server-rendered).
 *
 * @package Construction
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register project blocks.
 */
function construction_register_blocks(): void {
	wp_register_script(
		'construction-blocks-editor',
		get_template_directory_uri() . '/assets/js/blocks-editor.js',
		array(
			'wp-blocks',
			'wp-element',
			'wp-block-editor',
			'wp-components',
			'wp-server-side-render',
			'wp-i18n',
			'wp-api-fetch',
			'wp-data',
			'wp-url',
			'media-editor',
			'media-views',
		),
		CONSTRUCTION_VERSION,
		true
	);

	wp_localize_script(
		'construction-blocks-editor',
		'constructionBlocksEditor',
		array(
			'restBase'    => esc_url_raw( rest_url( 'wp/v2/construction-projects' ) ),
			'mediaBase'   => esc_url_raw( rest_url( 'wp/v2/media' ) ),
			'editPostTpl' => admin_url( 'post.php?post=%d&action=edit' ),
			'languages'   => array_map(
				static function ( string $code ): array {
					$names = array(
						'lv' => 'Latviešu',
						'en' => 'English',
						'ru' => 'Русский',
					);
					return array(
						'slug' => $code,
						'name' => $names[ $code ] ?? strtoupper( $code ),
					);
				},
				construction_languages()
			),
			'strings'     => array(
				'projectsGrid' => __( 'Projects grid', 'construction' ),
				'homeProjects' => __( 'Home projects', 'construction' ),
				'empty'        => __( 'No projects yet. Add one under Projects in the admin.', 'construction' ),
				'editProject'  => __( 'Edit project', 'construction' ),
				'clickToEdit'  => __( 'One card = one project. Click for a quick edit, or open the full editor for headings and blocks in the description.', 'construction' ),
				'languages'    => __( 'Languages', 'construction' ),
				'title'        => __( 'Title', 'construction' ),
				'description'  => __( 'Description (plain text quick edit — use Projects for headings/blocks)', 'construction' ),
				'slug'         => __( 'Slug (share link)', 'construction' ),
				'cover'        => __( 'Cover image', 'construction' ),
				'gallery'      => __( 'Gallery (all languages)', 'construction' ),
				'setCover'     => __( 'Set cover', 'construction' ),
				'removeCover'  => __( 'Remove cover', 'construction' ),
				'addImages'    => __( 'Add images', 'construction' ),
				'remove'       => __( 'Remove', 'construction' ),
				'save'         => __( 'Save project', 'construction' ),
				'saving'       => __( 'Saving…', 'construction' ),
				'saved'        => __( 'Saved.', 'construction' ),
				'close'        => __( 'Close', 'construction' ),
				'openFull'     => __( 'Open full editor (rich description)', 'construction' ),
				'loadError'    => __( 'Could not load projects.', 'construction' ),
				'saveError'    => __( 'Could not save project.', 'construction' ),
				'slugTaken'    => __( 'That slug is already used by another project.', 'construction' ),
			),
		)
	);

	$blocks = array(
		'projects-grid' => 'construction_render_projects_grid_block',
		'home-projects' => 'construction_render_home_projects_block',
	);

	foreach ( $blocks as $dir => $callback ) {
		$path = get_template_directory() . '/blocks/' . $dir;
		if ( ! is_dir( $path ) ) {
			continue;
		}
		register_block_type(
			$path,
			array(
				'render_callback' => $callback,
				'editor_script'   => 'construction-blocks-editor',
			)
		);
	}
}
add_action( 'init', 'construction_register_blocks', 20 );

/**
 * Ensure Media Library frame works inside the block editor for project image picking.
 */
function construction_block_editor_media(): void {
	if ( ! function_exists( 'get_current_screen' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( ! $screen || $screen->base !== 'post' ) {
		return;
	}
	wp_enqueue_media();
}
add_action( 'enqueue_block_editor_assets', 'construction_block_editor_media' );

/**
 * Render: full projects page section from CPT.
 *
 * @param array<string, mixed> $attributes Block attributes.
 */
function construction_render_projects_grid_block( array $attributes = array(), string $content = '', $block = null ): string { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
	$lang = construction_current_lang();
	$t    = static function ( string $key ) use ( $lang ): string {
		return esc_html( construction_string( $key, $lang ) );
	};

	$contact_cta  = $t( 'projects.cta' );
	$contact_href = esc_url( construction_contacts_url_for_lang( $lang ) );
	$label_close  = esc_attr( construction_string( 'projects.close', $lang ) );
	$label_prev   = esc_attr( construction_string( 'projects.prev', $lang ) );
	$label_next   = esc_attr( construction_string( 'projects.next', $lang ) );

	$cards = '';
	foreach ( construction_query_projects( $lang ) as $post ) {
		$cards .= construction_render_project_grid_card( $post, $lang );
	}

	if ( $cards === '' ) {
		$cards = '<p class="construction-projects__empty">' . esc_html__( 'No projects published yet.', 'construction' ) . '</p>';
	}

	return <<<HTML
<div class="alignfull construction-projects" id="projects" data-label-close="{$label_close}" data-label-prev="{$label_prev}" data-label-next="{$label_next}">
	<div class="construction-projects__inner">
		<div class="construction-projects__head">
			<h1 class="construction-projects__title">{$t( 'projects.title' )}</h1>
			<p class="construction-projects__cta-inline"><a href="{$contact_href}">{$contact_cta} →</a></p>
		</div>
		<p class="construction-projects__intro">{$t( 'projects.intro' )}</p>
		<div class="construction-project-viewer" hidden aria-live="polite"></div>
		<div class="construction-projects__grid">
{$cards}		</div>
	</div>
</div>
HTML;
}

/**
 * Render: homepage completed-projects marquee from CPT.
 *
 * @param array<string, mixed> $attributes Block attributes.
 */
function construction_render_home_projects_block( array $attributes = array(), string $content = '', $block = null ): string { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
	$lang = construction_current_lang();
	$t    = static function ( string $key ) use ( $lang ): string {
		return esc_html( construction_string( $key, $lang ) );
	};

	$view_all         = $t( 'projects.view_all' );
	$home_title       = $t( 'projects.home_title' );
	$projects_all_url = esc_url( construction_projects_url_for_lang( $lang ) );
	$label_close      = esc_attr( construction_string( 'projects.close', $lang ) );
	$label_prev       = esc_attr( construction_string( 'projects.prev', $lang ) );
	$label_next       = esc_attr( construction_string( 'projects.next', $lang ) );
	$label_open       = esc_attr( construction_string( 'projects.open_page', $lang ) );

	$cards = '';
	foreach ( construction_query_projects( $lang ) as $post ) {
		$cards .= construction_render_home_project_card( $post, $lang );
	}

	if ( $cards === '' ) {
		return '';
	}

	return <<<HTML
<div class="alignfull construction-home-projects" id="realized-projects" data-home-projects data-label-close="{$label_close}" data-label-prev="{$label_prev}" data-label-next="{$label_next}" data-label-open-page="{$label_open}" data-projects-url="{$projects_all_url}">
	<div class="construction-home-projects__inner">
		<div class="construction-home-projects__head">
			<h2 class="construction-home-projects__title">{$home_title}</h2>
			<p class="construction-home-projects__all"><a href="{$projects_all_url}">{$view_all} →</a></p>
		</div>
	</div>
	<div class="construction-home-projects__marquee" data-home-projects-marquee>
		<div class="construction-home-projects__track" data-home-projects-track>
{$cards}		</div>
	</div>
</div>
HTML;
}
