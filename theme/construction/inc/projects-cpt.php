<?php
/**
 * Project CPT: one post per project, LV/EN/RU fields inside, shared gallery.
 *
 * @package Construction
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const CONSTRUCTION_PROJECT_POST_TYPE   = 'construction_project';
const CONSTRUCTION_PROJECT_GALLERY_META = '_construction_project_gallery';
const CONSTRUCTION_PROJECT_I18N_META    = '_construction_project_i18n';

/**
 * Register Project CPT (not Polylang-translated — languages live in meta).
 */
function construction_register_project_cpt(): void {
	$labels = array(
		'name'                  => __( 'Projects', 'construction' ),
		'singular_name'         => __( 'Project', 'construction' ),
		'add_new'               => __( 'Add Project', 'construction' ),
		'add_new_item'          => __( 'Add New Project', 'construction' ),
		'edit_item'             => __( 'Edit Project', 'construction' ),
		'new_item'              => __( 'New Project', 'construction' ),
		'view_item'             => __( 'View Project', 'construction' ),
		'search_items'          => __( 'Search Projects', 'construction' ),
		'not_found'             => __( 'No projects found.', 'construction' ),
		'not_found_in_trash'    => __( 'No projects found in Trash.', 'construction' ),
		'menu_name'             => __( 'Projects', 'construction' ),
		'featured_image'        => __( 'Cover image', 'construction' ),
		'set_featured_image'    => __( 'Set cover image', 'construction' ),
		'remove_featured_image' => __( 'Remove cover image', 'construction' ),
		'use_featured_image'    => __( 'Use as cover image', 'construction' ),
	);

	register_post_type(
		CONSTRUCTION_PROJECT_POST_TYPE,
		array(
			'labels'              => $labels,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => true,
			'rest_base'           => 'construction-projects',
			'menu_position'       => 20,
			'menu_icon'           => 'dashicons-portfolio',
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'supports'            => array( 'title', 'thumbnail', 'revisions', 'page-attributes' ),
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
		)
	);
}
add_action( 'init', 'construction_register_project_cpt', 5 );

/**
 * Keep this CPT out of Polylang (one row per project).
 *
 * @param array<string, mixed> $types Post types.
 * @return array<string, mixed>
 */
function construction_pll_exclude_project_post_type( array $types ): array {
	unset( $types[ CONSTRUCTION_PROJECT_POST_TYPE ] );
	return $types;
}
add_filter( 'pll_get_post_types', 'construction_pll_exclude_project_post_type', 20, 1 );

/**
 * Empty i18n structure.
 *
 * @return array<string, array{title: string, excerpt: string}>
 */
function construction_project_i18n_defaults(): array {
	$out = array();
	foreach ( construction_languages() as $lang ) {
		$out[ $lang ] = array(
			'title'   => '',
			'excerpt' => '',
		);
	}
	return $out;
}

/**
 * Sanitized i18n map for a project.
 *
 * @return array<string, array{title: string, excerpt: string}>
 */
function construction_get_project_i18n( int $post_id ): array {
	$raw  = get_post_meta( $post_id, CONSTRUCTION_PROJECT_I18N_META, true );
	$i18n = construction_project_i18n_defaults();
	if ( ! is_array( $raw ) ) {
		$post = get_post( $post_id );
		if ( $post instanceof WP_Post ) {
			$i18n['lv']['title']   = $post->post_title;
			$i18n['lv']['excerpt'] = $post->post_excerpt;
		}
		return $i18n;
	}
	foreach ( construction_languages() as $lang ) {
		if ( ! isset( $raw[ $lang ] ) || ! is_array( $raw[ $lang ] ) ) {
			continue;
		}
		$i18n[ $lang ]['title']   = isset( $raw[ $lang ]['title'] ) ? sanitize_text_field( (string) $raw[ $lang ]['title'] ) : '';
		$i18n[ $lang ]['excerpt'] = isset( $raw[ $lang ]['excerpt'] ) ? sanitize_textarea_field( (string) $raw[ $lang ]['excerpt'] ) : '';
	}
	return $i18n;
}

/**
 * @param array<string, mixed> $value Raw i18n.
 * @return array<string, array{title: string, excerpt: string}>
 */
function construction_sanitize_project_i18n( $value ): array {
	$i18n = construction_project_i18n_defaults();
	if ( ! is_array( $value ) ) {
		return $i18n;
	}
	foreach ( construction_languages() as $lang ) {
		if ( ! isset( $value[ $lang ] ) || ! is_array( $value[ $lang ] ) ) {
			continue;
		}
		$i18n[ $lang ]['title']   = isset( $value[ $lang ]['title'] ) ? sanitize_text_field( (string) $value[ $lang ]['title'] ) : '';
		$i18n[ $lang ]['excerpt'] = isset( $value[ $lang ]['excerpt'] ) ? sanitize_textarea_field( (string) $value[ $lang ]['excerpt'] ) : '';
	}
	return $i18n;
}

/**
 * Title + excerpt for a language (falls back to LV, then post fields).
 *
 * @return array{title: string, excerpt: string}
 */
function construction_project_localized( int $post_id, ?string $lang = null ): array {
	$lang = $lang ? $lang : construction_current_lang();
	$i18n = construction_get_project_i18n( $post_id );
	$title = $i18n[ $lang ]['title'] ?? '';
	$excerpt = $i18n[ $lang ]['excerpt'] ?? '';
	if ( $title === '' && $lang !== 'lv' ) {
		$title = $i18n['lv']['title'] ?? '';
	}
	if ( $excerpt === '' && $lang !== 'lv' ) {
		$excerpt = $i18n['lv']['excerpt'] ?? '';
	}
	if ( $title === '' || $excerpt === '' ) {
		$post = get_post( $post_id );
		if ( $post instanceof WP_Post ) {
			if ( $title === '' ) {
				$title = $post->post_title;
			}
			if ( $excerpt === '' ) {
				$excerpt = $post->post_excerpt;
			}
		}
	}
	return array(
		'title'   => $title,
		'excerpt' => $excerpt,
	);
}

/**
 * Gallery attachment IDs.
 *
 * @return list<int>
 */
function construction_get_project_gallery_ids( int $post_id ): array {
	$raw = get_post_meta( $post_id, CONSTRUCTION_PROJECT_GALLERY_META, true );
	if ( ! is_array( $raw ) ) {
		return array();
	}
	$ids = array();
	foreach ( $raw as $id ) {
		$id = (int) $id;
		if ( $id > 0 && wp_attachment_is_image( $id ) ) {
			$ids[] = $id;
		}
	}
	return array_values( array_unique( $ids ) );
}

/**
 * Whether a project slug is taken by another project.
 */
function construction_project_slug_is_taken( string $slug, int $exclude_id = 0 ): bool {
	$slug = sanitize_title( $slug );
	if ( $slug === '' ) {
		return true;
	}
	$query = new WP_Query(
		array(
			'post_type'              => CONSTRUCTION_PROJECT_POST_TYPE,
			'name'                   => $slug,
			'post_status'            => array( 'publish', 'draft', 'pending', 'future', 'private' ),
			'posts_per_page'         => 5,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);
	$ids = array_map( 'intval', $query->posts );
	if ( $exclude_id > 0 ) {
		$ids = array_values( array_diff( $ids, array( $exclude_id ) ) );
	}
	return $ids !== array();
}

/**
 * Register meta for REST.
 */
function construction_register_project_meta(): void {
	register_post_meta(
		CONSTRUCTION_PROJECT_POST_TYPE,
		CONSTRUCTION_PROJECT_GALLERY_META,
		array(
			'type'              => 'array',
			'single'            => true,
			'default'           => array(),
			'show_in_rest'      => array(
				'schema' => array(
					'type'  => 'array',
					'items' => array( 'type' => 'integer' ),
				),
			),
			'auth_callback'     => static function (): bool {
				return current_user_can( 'edit_posts' );
			},
			'sanitize_callback' => static function ( $value ): array {
				if ( ! is_array( $value ) ) {
					return array();
				}
				$ids = array();
				foreach ( $value as $id ) {
					$id = (int) $id;
					if ( $id > 0 ) {
						$ids[] = $id;
					}
				}
				return array_values( array_unique( $ids ) );
			},
		)
	);

	register_post_meta(
		CONSTRUCTION_PROJECT_POST_TYPE,
		CONSTRUCTION_PROJECT_I18N_META,
		array(
			'type'              => 'object',
			'single'            => true,
			'default'           => array(),
			'show_in_rest'      => array(
				'schema' => array(
					'type'       => 'object',
					'properties' => array(
						'lv' => array(
							'type'       => 'object',
							'properties' => array(
								'title'   => array( 'type' => 'string' ),
								'excerpt' => array( 'type' => 'string' ),
							),
						),
						'en' => array(
							'type'       => 'object',
							'properties' => array(
								'title'   => array( 'type' => 'string' ),
								'excerpt' => array( 'type' => 'string' ),
							),
						),
						'ru' => array(
							'type'       => 'object',
							'properties' => array(
								'title'   => array( 'type' => 'string' ),
								'excerpt' => array( 'type' => 'string' ),
							),
						),
					),
				),
			),
			'auth_callback'     => static function (): bool {
				return current_user_can( 'edit_posts' );
			},
			'sanitize_callback' => 'construction_sanitize_project_i18n',
		)
	);
}
add_action( 'init', 'construction_register_project_meta', 6 );

/**
 * REST fields: gallery + i18n (reliable vs underscore meta).
 */
function construction_register_project_rest_fields(): void {
	register_rest_field(
		CONSTRUCTION_PROJECT_POST_TYPE,
		'gallery',
		array(
			'get_callback'    => static function ( array $object ): array {
				return construction_get_project_gallery_ids( (int) $object['id'] );
			},
			'update_callback' => static function ( $value, WP_Post $post ): bool {
				$ids = array();
				if ( is_array( $value ) ) {
					foreach ( $value as $id ) {
						$id = (int) $id;
						if ( $id > 0 && wp_attachment_is_image( $id ) ) {
							$ids[] = $id;
						}
					}
				}
				update_post_meta( (int) $post->ID, CONSTRUCTION_PROJECT_GALLERY_META, array_values( array_unique( $ids ) ) );
				return true;
			},
			'schema'          => array(
				'type'    => 'array',
				'items'   => array( 'type' => 'integer' ),
				'context' => array( 'view', 'edit' ),
			),
		)
	);

	register_rest_field(
		CONSTRUCTION_PROJECT_POST_TYPE,
		'i18n',
		array(
			'get_callback'    => static function ( array $object ): array {
				return construction_get_project_i18n( (int) $object['id'] );
			},
			'update_callback' => static function ( $value, WP_Post $post ): bool {
				$i18n = construction_sanitize_project_i18n( $value );
				update_post_meta( (int) $post->ID, CONSTRUCTION_PROJECT_I18N_META, $i18n );
				$lv_title = $i18n['lv']['title'] !== '' ? $i18n['lv']['title'] : ( $i18n['en']['title'] ?? '' );
				wp_update_post(
					array(
						'ID'           => (int) $post->ID,
						'post_title'   => $lv_title !== '' ? $lv_title : $post->post_title,
						'post_excerpt' => $i18n['lv']['excerpt'],
					)
				);
				return true;
			},
			'schema'          => array(
				'type'    => 'object',
				'context' => array( 'view', 'edit' ),
			),
		)
	);
}
add_action( 'rest_api_init', 'construction_register_project_rest_fields' );

/**
 * Meta boxes.
 */
function construction_project_add_meta_boxes(): void {
	add_meta_box(
		'construction_project_i18n',
		__( 'Project text (all languages)', 'construction' ),
		'construction_project_render_i18n_meta_box',
		CONSTRUCTION_PROJECT_POST_TYPE,
		'normal',
		'high'
	);
	add_meta_box(
		'construction_project_share',
		__( 'Shareable link', 'construction' ),
		'construction_project_render_share_meta_box',
		CONSTRUCTION_PROJECT_POST_TYPE,
		'side',
		'high'
	);
	add_meta_box(
		'construction_project_gallery',
		__( 'Project gallery', 'construction' ),
		'construction_project_render_gallery_meta_box',
		CONSTRUCTION_PROJECT_POST_TYPE,
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'construction_project_add_meta_boxes' );

/**
 * Language titles + descriptions.
 */
function construction_project_render_i18n_meta_box( WP_Post $post ): void {
	wp_nonce_field( 'construction_project_i18n_save', 'construction_project_i18n_nonce' );
	$i18n  = construction_get_project_i18n( (int) $post->ID );
	$names = array(
		'lv' => 'Latviešu',
		'en' => 'English',
		'ru' => 'Русский',
	);
	?>
	<p class="description"><?php esc_html_e( 'One project for every language. Switch tabs to edit each translation. Photos below are shared.', 'construction' ); ?></p>
	<div class="construction-project-i18n-tabs" style="margin:12px 0 8px;display:flex;gap:6px;flex-wrap:wrap;">
		<?php foreach ( construction_languages() as $i => $lang ) : ?>
			<button type="button" class="button construction-project-i18n-tab<?php echo 0 === $i ? ' button-primary' : ''; ?>" data-lang="<?php echo esc_attr( $lang ); ?>">
				<?php echo esc_html( $names[ $lang ] ?? strtoupper( $lang ) ); ?>
			</button>
		<?php endforeach; ?>
	</div>
	<?php foreach ( construction_languages() as $i => $lang ) : ?>
		<div class="construction-project-i18n-panel" data-lang="<?php echo esc_attr( $lang ); ?>" style="<?php echo 0 === $i ? '' : 'display:none;'; ?>">
			<p>
				<label for="construction-i18n-title-<?php echo esc_attr( $lang ); ?>"><strong><?php esc_html_e( 'Title', 'construction' ); ?></strong></label>
				<input class="widefat" type="text" id="construction-i18n-title-<?php echo esc_attr( $lang ); ?>" name="construction_project_i18n[<?php echo esc_attr( $lang ); ?>][title]" value="<?php echo esc_attr( $i18n[ $lang ]['title'] ); ?>" />
			</p>
			<p>
				<label for="construction-i18n-excerpt-<?php echo esc_attr( $lang ); ?>"><strong><?php esc_html_e( 'Description', 'construction' ); ?></strong></label>
				<textarea class="widefat" rows="4" id="construction-i18n-excerpt-<?php echo esc_attr( $lang ); ?>" name="construction_project_i18n[<?php echo esc_attr( $lang ); ?>][excerpt]"><?php echo esc_textarea( $i18n[ $lang ]['excerpt'] ); ?></textarea>
			</p>
		</div>
	<?php endforeach; ?>
	<script>
	(function(){
		var tabs = document.querySelectorAll('.construction-project-i18n-tab');
		var panels = document.querySelectorAll('.construction-project-i18n-panel');
		tabs.forEach(function(tab){
			tab.addEventListener('click', function(e){
				e.preventDefault();
				var lang = tab.getAttribute('data-lang');
				tabs.forEach(function(t){ t.classList.remove('button-primary'); });
				tab.classList.add('button-primary');
				panels.forEach(function(p){
					p.style.display = p.getAttribute('data-lang') === lang ? '' : 'none';
				});
			});
		});
	})();
	</script>
	<?php
}

/**
 * Share URL.
 */
function construction_project_render_share_meta_box( WP_Post $post ): void {
	$slug = $post->post_name !== '' ? $post->post_name : sanitize_title( $post->post_title );
	$url  = $slug !== '' ? construction_projects_url_for_lang( 'lv', $slug ) : construction_projects_url_for_lang( 'lv' );
	?>
	<p class="description" style="margin-top:0;">
		<?php esc_html_e( 'Permalink slug under the title is shared across languages (e.g. /projekti/#salaspils).', 'construction' ); ?>
	</p>
	<p>
		<label for="construction-project-share-url"><strong><?php esc_html_e( 'Share URL (LV)', 'construction' ); ?></strong></label>
		<input type="text" class="widefat" id="construction-project-share-url" readonly value="<?php echo esc_attr( $url ); ?>" onclick="this.select();" />
	</p>
	<?php
}

/**
 * Gallery meta box.
 */
function construction_project_render_gallery_meta_box( WP_Post $post ): void {
	wp_nonce_field( 'construction_project_gallery_save', 'construction_project_gallery_nonce' );
	$ids = construction_get_project_gallery_ids( (int) $post->ID );
	?>
	<p class="description"><?php esc_html_e( 'Photos for the project modal. Shared for all languages. Cover image should usually be first in the gallery too.', 'construction' ); ?></p>
	<ul id="construction-project-gallery-list" class="construction-project-gallery-list">
		<?php foreach ( $ids as $id ) : ?>
			<?php
			$thumb = wp_get_attachment_image_url( $id, 'thumbnail' );
			if ( ! is_string( $thumb ) || $thumb === '' ) {
				continue;
			}
			?>
			<li data-id="<?php echo esc_attr( (string) $id ); ?>">
				<img src="<?php echo esc_url( $thumb ); ?>" alt="" />
				<button type="button" class="button-link construction-project-gallery-remove" aria-label="<?php esc_attr_e( 'Remove', 'construction' ); ?>">&times;</button>
				<input type="hidden" name="construction_project_gallery[]" value="<?php echo esc_attr( (string) $id ); ?>" />
			</li>
		<?php endforeach; ?>
	</ul>
	<p>
		<button type="button" class="button" id="construction-project-gallery-add"><?php esc_html_e( 'Add images', 'construction' ); ?></button>
	</p>
	<style>
		.construction-project-gallery-list{display:flex;flex-wrap:wrap;gap:10px;margin:12px 0;padding:0;list-style:none;}
		.construction-project-gallery-list li{position:relative;width:96px;height:96px;margin:0;border:1px solid #c3c4c7;border-radius:4px;overflow:hidden;background:#f0f0f1;cursor:grab;}
		.construction-project-gallery-list img{display:block;width:100%;height:100%;object-fit:cover;}
		.construction-project-gallery-remove{position:absolute;top:2px;right:4px;color:#fff;background:rgba(0,0,0,.55);border:0;border-radius:50%;width:22px;height:22px;line-height:20px;text-align:center;cursor:pointer;}
		.construction-project-gallery-remove:hover{background:#b32d2e;color:#fff;}
	</style>
	<?php
}

/**
 * Save i18n + gallery.
 */
function construction_project_save_meta( int $post_id ): void {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( get_post_type( $post_id ) !== CONSTRUCTION_PROJECT_POST_TYPE ) {
		return;
	}

	if ( isset( $_POST['construction_project_i18n_nonce'] )
		&& wp_verify_nonce( sanitize_text_field( wp_unslash( (string) $_POST['construction_project_i18n_nonce'] ) ), 'construction_project_i18n_save' )
		&& isset( $_POST['construction_project_i18n'] )
		&& is_array( $_POST['construction_project_i18n'] )
	) {
		$i18n = construction_sanitize_project_i18n( wp_unslash( $_POST['construction_project_i18n'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		update_post_meta( $post_id, CONSTRUCTION_PROJECT_I18N_META, $i18n );
		$lv_title = $i18n['lv']['title'] !== '' ? $i18n['lv']['title'] : get_the_title( $post_id );
		remove_action( 'save_post_' . CONSTRUCTION_PROJECT_POST_TYPE, 'construction_project_save_meta' );
		wp_update_post(
			array(
				'ID'           => $post_id,
				'post_title'   => $lv_title,
				'post_excerpt' => $i18n['lv']['excerpt'],
			)
		);
		add_action( 'save_post_' . CONSTRUCTION_PROJECT_POST_TYPE, 'construction_project_save_meta' );
	}

	if ( isset( $_POST['construction_project_gallery_nonce'] )
		&& wp_verify_nonce( sanitize_text_field( wp_unslash( (string) $_POST['construction_project_gallery_nonce'] ) ), 'construction_project_gallery_save' )
	) {
		$ids = array();
		if ( isset( $_POST['construction_project_gallery'] ) && is_array( $_POST['construction_project_gallery'] ) ) {
			foreach ( wp_unslash( $_POST['construction_project_gallery'] ) as $id ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$id = (int) $id;
				if ( $id > 0 && wp_attachment_is_image( $id ) ) {
					$ids[] = $id;
				}
			}
		}
		update_post_meta( $post_id, CONSTRUCTION_PROJECT_GALLERY_META, array_values( array_unique( $ids ) ) );
	}
}
add_action( 'save_post_' . CONSTRUCTION_PROJECT_POST_TYPE, 'construction_project_save_meta' );

/**
 * Unique slug among projects.
 *
 * @param array<string, mixed> $data    Post data.
 * @param array<string, mixed> $postarr Raw.
 * @return array<string, mixed>
 */
function construction_project_unique_slug( array $data, array $postarr ): array {
	if ( ( $data['post_type'] ?? '' ) !== CONSTRUCTION_PROJECT_POST_TYPE ) {
		return $data;
	}
	if ( in_array( (string) ( $data['post_status'] ?? '' ), array( 'trash', 'auto-draft' ), true ) ) {
		return $data;
	}
	$exclude_id = isset( $postarr['ID'] ) ? (int) $postarr['ID'] : 0;
	$slug       = sanitize_title( (string) ( $data['post_name'] ?? '' ) );
	if ( $slug === '' ) {
		$slug = sanitize_title( (string) ( $data['post_title'] ?? '' ) );
	}
	if ( $slug === '' ) {
		set_transient( 'construction_project_slug_error_' . get_current_user_id(), 'empty', 45 );
		return $data;
	}
	if ( construction_project_slug_is_taken( $slug, $exclude_id ) ) {
		set_transient( 'construction_project_slug_error_' . get_current_user_id(), $slug, 45 );
		if ( $exclude_id > 0 ) {
			$existing = get_post( $exclude_id );
			if ( $existing instanceof WP_Post && $existing->post_name !== '' ) {
				$data['post_name'] = $existing->post_name;
			}
		}
		return $data;
	}
	$data['post_name'] = $slug;
	return $data;
}
add_filter( 'wp_insert_post_data', 'construction_project_unique_slug', 20, 2 );

/**
 * Admin notice for slug conflicts.
 */
function construction_project_slug_admin_notice(): void {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || $screen->post_type !== CONSTRUCTION_PROJECT_POST_TYPE ) {
		return;
	}
	$key   = 'construction_project_slug_error_' . get_current_user_id();
	$error = get_transient( $key );
	if ( ! is_string( $error ) || $error === '' ) {
		return;
	}
	delete_transient( $key );
	if ( $error === 'empty' ) {
		echo '<div class="notice notice-error"><p>' . esc_html__( 'Project slug cannot be empty.', 'construction' ) . '</p></div>';
		return;
	}
	echo '<div class="notice notice-error"><p>' . esc_html(
		sprintf(
			/* translators: %s: slug */
			__( 'The slug “%s” is already used by another project.', 'construction' ),
			$error
		)
	) . '</p></div>';
}
add_action( 'admin_notices', 'construction_project_slug_admin_notice' );

/**
 * Gallery admin script.
 *
 * @param string $hook_suffix Hook.
 */
function construction_project_admin_assets( string $hook_suffix ): void {
	if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
		return;
	}
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || $screen->post_type !== CONSTRUCTION_PROJECT_POST_TYPE ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script(
		'construction-projects-admin',
		get_template_directory_uri() . '/assets/js/projects-admin.js',
		array( 'jquery', 'jquery-ui-sortable' ),
		CONSTRUCTION_VERSION,
		true
	);
	wp_localize_script(
		'construction-projects-admin',
		'constructionProjectsAdmin',
		array(
			'title'  => __( 'Select project images', 'construction' ),
			'button' => __( 'Add to gallery', 'construction' ),
			'remove' => __( 'Remove', 'construction' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'construction_project_admin_assets' );

/**
 * Published projects (language-agnostic list).
 *
 * @return list<WP_Post>
 */
function construction_query_projects( ?string $lang = null ): array { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
	$query = new WP_Query(
		array(
			'post_type'              => CONSTRUCTION_PROJECT_POST_TYPE,
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'orderby'                => array(
				'menu_order' => 'ASC',
				'title'      => 'ASC',
			),
			'no_found_rows'          => true,
			'update_post_term_cache' => false,
		)
	);
	$posts = array();
	foreach ( $query->posts as $post ) {
		if ( $post instanceof WP_Post ) {
			$posts[] = $post;
		}
	}
	return $posts;
}

/**
 * Image IDs for modal (cover first).
 *
 * @return list<int>
 */
function construction_project_image_ids_for_modal( int $post_id ): array {
	$gallery = construction_get_project_gallery_ids( $post_id );
	$cover   = (int) get_post_thumbnail_id( $post_id );
	$ids     = array();
	if ( $cover > 0 ) {
		$ids[] = $cover;
	}
	foreach ( $gallery as $id ) {
		if ( ! in_array( $id, $ids, true ) ) {
			$ids[] = $id;
		}
	}
	return $ids;
}

/**
 * Figure HTML for an attachment.
 */
function construction_attachment_figure_html(
	int $attachment_id,
	string $class_name,
	string $alt,
	string $size = 'large',
	bool $lightbox = false,
	string $gallery = ''
): string {
	$url = wp_get_attachment_image_url( $attachment_id, $size );
	if ( ! is_string( $url ) || $url === '' ) {
		$url = (string) wp_get_attachment_url( $attachment_id );
	}
	if ( $url === '' ) {
		return '';
	}
	$classes = 'wp-block-image size-' . esc_attr( $size );
	if ( $class_name !== '' ) {
		$classes .= ' ' . esc_attr( $class_name );
	}
	$img = '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( $alt ) . '" class="wp-image-' . $attachment_id . '"/>';
	if ( $lightbox ) {
		$full = wp_get_attachment_image_url( $attachment_id, 'full' );
		if ( ! is_string( $full ) || $full === '' ) {
			$full = $url;
		}
		$gallery = sanitize_title( $gallery !== '' ? $gallery : 'construction-projects' );
		$inner   = '<a href="' . esc_url( $full ) . '" class="construction-lightbox glightbox" data-gallery="' . esc_attr( $gallery ) . '">' . $img . '</a>';
	} else {
		$inner = $img;
	}
	return '<figure class="' . $classes . '">' . $inner . '</figure>';
}

/**
 * Projects page card HTML.
 */
function construction_render_project_grid_card( WP_Post $post, ?string $lang = null ): string {
	$lang  = $lang ? $lang : construction_current_lang();
	$loc   = construction_project_localized( (int) $post->ID, $lang );
	$slug  = sanitize_title( $post->post_name !== '' ? $post->post_name : (string) $post->ID );
	$title = $loc['title'];
	$text  = $loc['excerpt'];
	$ids   = construction_project_image_ids_for_modal( (int) $post->ID );
	if ( $ids === array() || $title === '' ) {
		return '';
	}
	$gallery_key = 'project-' . $slug;
	$cover_id    = $ids[0];
	$cover_alt   = (string) get_post_meta( $cover_id, '_wp_attachment_image_alt', true );
	if ( $cover_alt === '' ) {
		$cover_alt = $title;
	}
	$cover_html = construction_attachment_figure_html( $cover_id, 'construction-project-card__cover', $cover_alt, 'large', true, $gallery_key );
	$more       = '';
	foreach ( array_slice( $ids, 1 ) as $img_id ) {
		$alt = (string) get_post_meta( $img_id, '_wp_attachment_image_alt', true );
		if ( $alt === '' ) {
			$alt = $title;
		}
		$more .= construction_attachment_figure_html( $img_id, 'construction-project-card__extra', $alt, 'medium_large', true, $gallery_key );
	}
	$title_e = esc_html( $title );
	$text_e  = esc_html( $text );
	$slug_e  = esc_attr( $slug );
	return <<<HTML
<div class="construction-project-card" id="{$slug_e}" data-project-slug="{$slug_e}">
{$cover_html}<h2 class="construction-project-card__title">{$title_e}</h2>
<p class="construction-project-card__text">{$text_e}</p>
<div class="construction-project-card__more">
{$more}</div>
</div>
HTML;
}

/**
 * Homepage marquee card HTML.
 */
function construction_render_home_project_card( WP_Post $post, string $lang ): string {
	$loc   = construction_project_localized( (int) $post->ID, $lang );
	$slug  = sanitize_title( $post->post_name !== '' ? $post->post_name : (string) $post->ID );
	$title = $loc['title'];
	$text  = $loc['excerpt'];
	$href  = esc_url( construction_projects_url_for_lang( $lang, $slug ) );
	$ids   = construction_project_image_ids_for_modal( (int) $post->ID );
	if ( $ids === array() || $title === '' ) {
		return '';
	}
	$cover_id  = $ids[0];
	$cover_alt = (string) get_post_meta( $cover_id, '_wp_attachment_image_alt', true );
	if ( $cover_alt === '' ) {
		$cover_alt = $title;
	}
	$cover_html = construction_attachment_figure_html( $cover_id, 'construction-home-projects__media', $cover_alt, 'medium_large', false, '' );
	$gallery    = '';
	foreach ( $ids as $img_id ) {
		$alt = (string) get_post_meta( $img_id, '_wp_attachment_image_alt', true );
		if ( $alt === '' ) {
			$alt = $title;
		}
		$gallery .= construction_attachment_figure_html( $img_id, 'construction-home-projects__gallery-item', $alt, 'medium_large', true, 'home-project-' . $slug );
	}
	$title_e = esc_html( $title );
	$label_e = esc_attr( $title );
	$text_e  = esc_html( $text );
	$slug_e  = esc_attr( $slug );
	return <<<HTML
<div class="construction-home-projects__card" data-project-slug="{$slug_e}">
{$cover_html}<h3 class="construction-home-projects__name">{$title_e}</h3>
<p class="construction-home-projects__blurb">{$text_e}</p>
<div class="construction-home-projects__gallery" hidden>
{$gallery}</div>
<p class="construction-home-projects__card-hit"><a href="{$href}" data-project-open="{$slug_e}" aria-label="{$label_e}">{$title_e}</a></p>
</div>
HTML;
}

/**
 * Normalize slug for merge (sloka-2 → sloka).
 */
function construction_project_base_slug( string $slug ): string {
	$slug = sanitize_title( $slug );
	$slug = (string) preg_replace( '/-(?:lv|en|ru|\d+)$/', '', $slug );
	$known = array();
	foreach ( construction_project_entries() as $entry ) {
		$known[] = sanitize_title( (string) $entry['slug'] );
	}
	if ( in_array( $slug, $known, true ) ) {
		return $slug;
	}
	return $slug;
}

/**
 * Match a project title to a catalog slug (any language).
 */
function construction_project_catalog_slug_for_title( string $title ): string {
	$title   = trim( $title );
	$strings = construction_strings();
	foreach ( construction_project_entries() as $entry ) {
		$slug = sanitize_title( (string) $entry['slug'] );
		foreach ( construction_languages() as $lang ) {
			$key = 'projects.item.' . $slug . '.title';
			if ( isset( $strings[ $key ][ $lang ] ) && (string) $strings[ $key ][ $lang ] === $title ) {
				return $slug;
			}
		}
	}
	return '';
}

/**
 * Detect language slug for a project post (Polylang or title match).
 */
function construction_project_detect_lang( WP_Post $post ): string {
	if ( function_exists( 'pll_get_post_language' ) ) {
		$detected = (string) pll_get_post_language( (int) $post->ID, 'slug' );
		if ( $detected !== '' && in_array( $detected, construction_languages(), true ) ) {
			return $detected;
		}
	}
	$title   = trim( $post->post_title );
	$strings = construction_strings();
	foreach ( construction_project_entries() as $entry ) {
		$slug = sanitize_title( (string) $entry['slug'] );
		foreach ( construction_languages() as $lang ) {
			$key = 'projects.item.' . $slug . '.title';
			if ( isset( $strings[ $key ][ $lang ] ) && (string) $strings[ $key ][ $lang ] === $title ) {
				return $lang;
			}
		}
	}
	return 'lv';
}

/**
 * Merge Polylang-split projects into one post per project.
 *
 * @return array{merged:int, deleted:int}
 */
function construction_migrate_projects_to_single(): array {
	$posts = get_posts(
		array(
			'post_type'              => CONSTRUCTION_PROJECT_POST_TYPE,
			'post_status'            => 'any',
			'posts_per_page'         => -1,
			'lang'                   => '',
			'suppress_filters'       => true,
			'orderby'                => 'ID',
			'order'                  => 'ASC',
			'update_post_meta_cache' => true,
		)
	);

	$seen   = array();
	$groups = array();

	foreach ( $posts as $post ) {
		if ( ! $post instanceof WP_Post || isset( $seen[ (int) $post->ID ] ) ) {
			continue;
		}

		$by_lang = array();

		// 1) Prefer Polylang translation group.
		if ( function_exists( 'pll_get_post_translations' ) ) {
			$translations = pll_get_post_translations( (int) $post->ID );
			if ( is_array( $translations ) && $translations !== array() ) {
				foreach ( $translations as $lang => $tid ) {
					$tid = (int) $tid;
					if ( $tid <= 0 || isset( $seen[ $tid ] ) ) {
						continue;
					}
					$translated = get_post( $tid );
					if ( ! $translated instanceof WP_Post || $translated->post_type !== CONSTRUCTION_PROJECT_POST_TYPE ) {
						continue;
					}
					$lang_slug = is_string( $lang ) && in_array( $lang, construction_languages(), true )
						? $lang
						: construction_project_detect_lang( $translated );
					$by_lang[ $lang_slug ] = $translated;
					$seen[ $tid ]          = true;
				}
			}
		}

		// 2) Fallback: group by catalog slug / normalized post_name.
		if ( $by_lang === array() ) {
			$base = construction_project_catalog_slug_for_title( $post->post_title );
			if ( $base === '' ) {
				$base = construction_project_base_slug( $post->post_name !== '' ? $post->post_name : $post->post_title );
			}
			if ( $base === '' ) {
				$base = 'project-' . $post->ID;
			}
			foreach ( $posts as $candidate ) {
				if ( ! $candidate instanceof WP_Post || isset( $seen[ (int) $candidate->ID ] ) ) {
					continue;
				}
				$cand_base = construction_project_catalog_slug_for_title( $candidate->post_title );
				if ( $cand_base === '' ) {
					$cand_base = construction_project_base_slug( $candidate->post_name !== '' ? $candidate->post_name : $candidate->post_title );
				}
				if ( $cand_base !== $base ) {
					continue;
				}
				$lang_slug             = construction_project_detect_lang( $candidate );
				$by_lang[ $lang_slug ] = $candidate;
				$seen[ (int) $candidate->ID ] = true;
			}
		}

		if ( $by_lang === array() ) {
			$lang_slug             = construction_project_detect_lang( $post );
			$by_lang[ $lang_slug ] = $post;
			$seen[ (int) $post->ID ] = true;
		}

		$base = '';
		foreach ( $by_lang as $p ) {
			if ( $p instanceof WP_Post ) {
				$base = construction_project_catalog_slug_for_title( $p->post_title );
				if ( $base !== '' ) {
					break;
				}
			}
		}
		if ( $base === '' ) {
			$sample = reset( $by_lang );
			$base   = $sample instanceof WP_Post
				? construction_project_base_slug( $sample->post_name !== '' ? $sample->post_name : $sample->post_title )
				: 'project';
		}
		if ( $base === '' ) {
			$base = 'project-' . (int) ( reset( $by_lang )->ID ?? 0 );
		}

		// Avoid colliding group keys if two unrelated clusters share a blank base.
		$key = $base;
		$n   = 2;
		while ( isset( $groups[ $key ] ) ) {
			$key = $base . '-' . $n;
			++$n;
		}
		$groups[ $key ] = $by_lang;
	}

	$merged  = 0;
	$deleted = 0;

	foreach ( $groups as $base => $by_lang ) {
		foreach ( $by_lang as $p ) {
			if ( ! $p instanceof WP_Post ) {
				continue;
			}
			$from_title = construction_project_catalog_slug_for_title( $p->post_title );
			if ( $from_title !== '' ) {
				$base = $from_title;
				break;
			}
		}
		$base = construction_project_base_slug( (string) $base );
		if ( $base === '' ) {
			$sample = reset( $by_lang );
			$base   = $sample instanceof WP_Post ? 'project-' . (int) $sample->ID : 'project';
		}

		// Pick keeper: prefer LV, else lowest ID.
		$keeper = $by_lang['lv'] ?? null;
		if ( ! $keeper instanceof WP_Post ) {
			$keeper = reset( $by_lang );
		}
		if ( ! $keeper instanceof WP_Post ) {
			continue;
		}

		$i18n = construction_project_i18n_defaults();
		foreach ( $by_lang as $lang => $post ) {
			if ( ! $post instanceof WP_Post ) {
				continue;
			}
			$existing = get_post_meta( (int) $post->ID, CONSTRUCTION_PROJECT_I18N_META, true );
			if ( is_array( $existing ) && isset( $existing[ $lang ] ) ) {
				$i18n[ $lang ]['title']   = (string) ( $existing[ $lang ]['title'] ?? $post->post_title );
				$i18n[ $lang ]['excerpt'] = (string) ( $existing[ $lang ]['excerpt'] ?? $post->post_excerpt );
			} else {
				$i18n[ $lang ]['title']   = $post->post_title;
				$i18n[ $lang ]['excerpt'] = $post->post_excerpt;
			}
			// Also fold any already-merged i18n from keeper/other posts.
			if ( is_array( $existing ) ) {
				foreach ( construction_languages() as $fold_lang ) {
					if ( ( $i18n[ $fold_lang ]['title'] ?? '' ) === '' && ! empty( $existing[ $fold_lang ]['title'] ) ) {
						$i18n[ $fold_lang ]['title']   = (string) $existing[ $fold_lang ]['title'];
						$i18n[ $fold_lang ]['excerpt'] = (string) ( $existing[ $fold_lang ]['excerpt'] ?? '' );
					}
				}
			}
		}

		// Fill from catalog strings if a language is still empty.
		foreach ( construction_languages() as $lang ) {
			if ( ( $i18n[ $lang ]['title'] ?? '' ) !== '' ) {
				continue;
			}
			$key_title = 'projects.item.' . $base . '.title';
			$key_text  = 'projects.item.' . $base . '.text';
			$strings   = construction_strings();
			if ( isset( $strings[ $key_title ][ $lang ] ) ) {
				$i18n[ $lang ]['title'] = (string) $strings[ $key_title ][ $lang ];
			}
			if ( isset( $strings[ $key_text ][ $lang ] ) ) {
				$i18n[ $lang ]['excerpt'] = (string) $strings[ $key_text ][ $lang ];
			}
		}

		$gallery = construction_get_project_gallery_ids( (int) $keeper->ID );
		foreach ( $by_lang as $post ) {
			if ( ! $post instanceof WP_Post || (int) $post->ID === (int) $keeper->ID ) {
				continue;
			}
			foreach ( construction_get_project_gallery_ids( (int) $post->ID ) as $gid ) {
				if ( ! in_array( $gid, $gallery, true ) ) {
					$gallery[] = $gid;
				}
			}
			$extra_thumb = (int) get_post_thumbnail_id( (int) $post->ID );
			if ( $extra_thumb > 0 && (int) get_post_thumbnail_id( (int) $keeper->ID ) <= 0 ) {
				set_post_thumbnail( (int) $keeper->ID, $extra_thumb );
			}
		}

		update_post_meta( (int) $keeper->ID, CONSTRUCTION_PROJECT_I18N_META, $i18n );
		update_post_meta( (int) $keeper->ID, CONSTRUCTION_PROJECT_GALLERY_META, $gallery );
		wp_update_post(
			array(
				'ID'           => (int) $keeper->ID,
				'post_title'   => $i18n['lv']['title'] !== '' ? $i18n['lv']['title'] : $keeper->post_title,
				'post_excerpt' => $i18n['lv']['excerpt'],
				'post_name'    => $base,
			)
		);
		++$merged;

		foreach ( $by_lang as $post ) {
			if ( ! $post instanceof WP_Post || (int) $post->ID === (int) $keeper->ID ) {
				continue;
			}
			wp_delete_post( (int) $post->ID, true );
			++$deleted;
		}
	}

	update_option( 'construction_projects_merged_single', '1' );
	return array(
		'merged'  => $merged,
		'deleted' => $deleted,
	);
}

/**
 * Auto-migrate once.
 */
function construction_maybe_migrate_projects_to_single(): void {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( get_option( 'construction_projects_merged_single' ) === '1' ) {
		return;
	}
	$count = (int) wp_count_posts( CONSTRUCTION_PROJECT_POST_TYPE )->publish
		+ (int) wp_count_posts( CONSTRUCTION_PROJECT_POST_TYPE )->draft;
	if ( $count <= 5 && $count > 0 ) {
		// Still run merge to fold i18n into meta if duplicates exist.
	}
	construction_migrate_projects_to_single();
}
add_action( 'admin_init', 'construction_maybe_migrate_projects_to_single', 25 );

/**
 * Seed catalog as single multilingual posts.
 *
 * @return array{created:int, skipped:bool}|WP_Error
 */
function construction_seed_projects_from_catalog( bool $force = false ) {
	construction_import_media_library();

	$existing = get_posts(
		array(
			'post_type'      => CONSTRUCTION_PROJECT_POST_TYPE,
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	if ( ! $force && $existing !== array() ) {
		return array(
			'created' => 0,
			'skipped' => true,
		);
	}

	if ( $force ) {
		$all = get_posts(
			array(
				'post_type'      => CONSTRUCTION_PROJECT_POST_TYPE,
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);
		foreach ( $all as $old_id ) {
			wp_delete_post( (int) $old_id, true );
		}
	}

	$created = 0;
	$order   = 0;
	foreach ( construction_project_entries() as $entry ) {
		$slug   = sanitize_title( (string) $entry['slug'] );
		$cover  = (string) $entry['cover'];
		$images = isset( $entry['images'] ) && is_array( $entry['images'] ) ? $entry['images'] : array( $cover );
		$thumb  = construction_media_id( $cover );
		$gallery_ids = array();
		foreach ( $images as $key ) {
			$id = construction_media_id( (string) $key );
			if ( $id > 0 ) {
				$gallery_ids[] = $id;
			}
		}
		$gallery_ids = array_values( array_unique( $gallery_ids ) );

		$i18n = construction_project_i18n_defaults();
		foreach ( construction_languages() as $lang ) {
			$i18n[ $lang ]['title']   = construction_string( "projects.item.{$slug}.title", $lang );
			$i18n[ $lang ]['excerpt'] = construction_string( "projects.item.{$slug}.text", $lang );
		}

		$id = wp_insert_post(
			array(
				'post_type'    => CONSTRUCTION_PROJECT_POST_TYPE,
				'post_status'  => 'publish',
				'post_title'   => $i18n['lv']['title'],
				'post_excerpt' => $i18n['lv']['excerpt'],
				'post_name'    => $slug,
				'menu_order'   => $order,
			),
			true
		);
		if ( is_wp_error( $id ) ) {
			return $id;
		}
		$id = (int) $id;
		if ( $thumb > 0 ) {
			set_post_thumbnail( $id, $thumb );
		}
		update_post_meta( $id, CONSTRUCTION_PROJECT_GALLERY_META, $gallery_ids );
		update_post_meta( $id, CONSTRUCTION_PROJECT_I18N_META, $i18n );
		++$created;
		++$order;
	}

	update_option( 'construction_projects_seeded', '1' );
	update_option( 'construction_projects_merged_single', '1' );

	return array(
		'created' => $created,
		'skipped' => false,
	);
}

/**
 * Auto-seed if empty.
 */
function construction_maybe_seed_projects(): void {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( get_option( 'construction_projects_seeded' ) === '1' ) {
		return;
	}
	$existing = get_posts(
		array(
			'post_type'      => CONSTRUCTION_PROJECT_POST_TYPE,
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	if ( $existing !== array() ) {
		update_option( 'construction_projects_seeded', '1' );
		return;
	}
	construction_seed_projects_from_catalog( false );
}
add_action( 'admin_init', 'construction_maybe_seed_projects', 30 );

/**
 * Admin: seed / reseed / remigrate.
 *
 * /wp-admin/?construction_seed_projects=1
 * /wp-admin/?construction_seed_projects=1&force=1
 * /wp-admin/?construction_merge_projects=1
 */
function construction_admin_seed_projects(): void {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( isset( $_GET['construction_merge_projects'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		delete_option( 'construction_projects_merged_single' );
		$result = construction_migrate_projects_to_single();
		wp_safe_redirect(
			admin_url(
				'edit.php?post_type=' . CONSTRUCTION_PROJECT_POST_TYPE .
				'&construction_projects_merged=1&merged=' . (int) $result['merged'] .
				'&deleted=' . (int) $result['deleted']
			)
		);
		exit;
	}
	if ( ! isset( $_GET['construction_seed_projects'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}
	$force  = isset( $_GET['force'] ) && (string) wp_unslash( $_GET['force'] ) === '1'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$result = construction_seed_projects_from_catalog( $force );
	if ( is_wp_error( $result ) ) {
		wp_die( esc_html( $result->get_error_message() ) );
	}
	wp_safe_redirect( admin_url( 'edit.php?post_type=' . CONSTRUCTION_PROJECT_POST_TYPE . '&construction_projects_seeded=1' ) );
	exit;
}
add_action( 'admin_init', 'construction_admin_seed_projects', 5 );
