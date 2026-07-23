<?php
/**
 * Editable site settings (Appearance → Construction).
 * Logo, contact details. Languages come from Polylang when available.
 *
 * @package Construction
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default contact values.
 *
 * @return array{email: string, phone: string, phone_href: string, address_lv: string, address_en: string, address_ru: string, logo_id: string}
 */
function construction_contact_defaults(): array {
	return array(
		'email'      => 'info@construction.lv',
		'phone'      => '+371 2000 0000',
		'phone_href' => '+37120000000',
		'address_lv' => 'Rīga, Latvija',
		'address_en' => 'Riga, Latvia',
		'address_ru' => 'Рига, Латвия',
		'logo_id'    => '0',
	);
}

/**
 * Saved contact settings merged with defaults.
 *
 * @return array{email: string, phone: string, phone_href: string, address_lv: string, address_en: string, address_ru: string, logo_id: string}
 */
function construction_contact_settings(): array {
	$saved = get_option( 'construction_contact', array() );
	if ( ! is_array( $saved ) ) {
		$saved = array();
	}

	return array_merge( construction_contact_defaults(), array_map( 'strval', $saved ) );
}

/**
 * Header logo URL (uploaded Media Library image, else theme placeholder).
 */
function construction_logo_url(): string {
	$id = (int) construction_contact( 'logo_id' );
	if ( $id > 0 ) {
		$url = wp_get_attachment_image_url( $id, 'full' );
		if ( is_string( $url ) && $url !== '' ) {
			return $url;
		}
	}

	return get_template_directory_uri() . '/assets/images/logo-placeholder.svg';
}

/**
 * Alt text for the header logo.
 */
function construction_logo_alt(): string {
	$id = (int) construction_contact( 'logo_id' );
	if ( $id > 0 ) {
		$alt = get_post_meta( $id, '_wp_attachment_image_alt', true );
		if ( is_string( $alt ) && $alt !== '' ) {
			return $alt;
		}
	}

	$name = get_bloginfo( 'name' );
	return is_string( $name ) && $name !== '' ? $name : 'Logo';
}

/**
 * One contact field.
 */
function construction_contact( string $key ): string {
	$settings = construction_contact_settings();
	return isset( $settings[ $key ] ) ? (string) $settings[ $key ] : '';
}

/**
 * Address for a language (falls back to LV, then EN).
 */
function construction_contact_address( ?string $lang = null ): string {
	$lang = $lang ? $lang : construction_current_lang();
	$key  = 'address_' . $lang;
	$val  = construction_contact( $key );
	if ( $val !== '' ) {
		return $val;
	}
	$fallback = construction_contact( 'address_lv' );
	return $fallback !== '' ? $fallback : construction_contact( 'address_en' );
}

/**
 * tel: href from stored phone.
 */
function construction_contact_phone_href(): string {
	$href = construction_contact( 'phone_href' );
	if ( $href !== '' ) {
		return 'tel:' . preg_replace( '/[^\d+]/', '', $href );
	}
	$phone = construction_contact( 'phone' );
	return 'tel:' . preg_replace( '/[^\d+]/', '', $phone );
}

/**
 * mailto: href with optional subject.
 */
function construction_contact_mail_href( ?string $lang = null ): string {
	$email = construction_contact( 'email' );
	$lang  = $lang ? $lang : construction_current_lang();
	$subject = rawurlencode( construction_string( 'contact.mail_subject', $lang ) );
	return 'mailto:' . $email . '?subject=' . $subject;
}

/**
 * Language switcher items from Polylang (fallback: lv/en/ru).
 *
 * @return list<array{code: string, url: string, name: string, current: bool}>
 */
function construction_language_items(): array {
	$current = construction_current_lang();
	$items   = array();

	if ( function_exists( 'pll_the_languages' ) ) {
		$raw = pll_the_languages(
			array(
				'raw'                  => 1,
				'hide_if_empty'        => 0,
				'hide_current'         => 0,
				'hide_if_no_translation' => 0,
			)
		);
		if ( is_array( $raw ) ) {
			foreach ( $raw as $lang ) {
				$code = isset( $lang['slug'] ) ? (string) $lang['slug'] : '';
				if ( $code === '' ) {
					continue;
				}
				$items[] = array(
					'code'    => $code,
					'url'     => isset( $lang['url'] ) ? (string) $lang['url'] : construction_lang_url( $code ),
					'name'    => isset( $lang['name'] ) ? (string) $lang['name'] : strtoupper( $code ),
					'current' => ! empty( $lang['current_lang'] ) || $code === $current,
				);
			}
		}
	}

	if ( empty( $items ) ) {
		foreach ( construction_languages() as $code ) {
			$items[] = array(
				'code'    => $code,
				'url'     => construction_lang_url( $code ),
				'name'    => strtoupper( $code ),
				'current' => $code === $current,
			);
		}
	}

	return $items;
}

/**
 * Markup for language links.
 */
function construction_language_switcher_html( string $class ): string {
	$html = '<p class="' . esc_attr( $class ) . '">';
	foreach ( construction_language_items() as $item ) {
		$current_attr = ! empty( $item['current'] ) ? ' aria-current="true"' : '';
		$html        .= '<a href="' . esc_url( $item['url'] ) . '"' . $current_attr . '>' . esc_html( strtoupper( $item['code'] ) ) . '</a>';
	}
	$html .= '</p>';
	return $html;
}

/**
 * Register settings page under Appearance.
 */
function construction_register_settings_page(): void {
	add_theme_page(
		__( 'Construction settings', 'construction' ),
		__( 'Construction', 'construction' ),
		'manage_options',
		'construction-settings',
		'construction_render_settings_page'
	);
}
add_action( 'admin_menu', 'construction_register_settings_page' );

/**
 * Register option.
 */
function construction_register_settings(): void {
	register_setting(
		'construction_settings',
		'construction_contact',
		array(
			'type'              => 'array',
			'sanitize_callback' => 'construction_sanitize_contact_settings',
			'default'           => construction_contact_defaults(),
		)
	);
}
add_action( 'admin_init', 'construction_register_settings' );

/**
 * @param mixed $input Raw form input.
 * @return array<string, string>
 */
function construction_sanitize_contact_settings( $input ): array {
	$defaults = construction_contact_defaults();
	if ( ! is_array( $input ) ) {
		return $defaults;
	}

	$email = isset( $input['email'] ) ? sanitize_email( (string) $input['email'] ) : $defaults['email'];
	$phone = isset( $input['phone'] ) ? sanitize_text_field( (string) $input['phone'] ) : $defaults['phone'];
	$href  = isset( $input['phone_href'] ) ? sanitize_text_field( (string) $input['phone_href'] ) : '';
	if ( $href === '' ) {
		$href = preg_replace( '/[^\d+]/', '', $phone ) ?: $defaults['phone_href'];
	}

	$logo_id = isset( $input['logo_id'] ) ? absint( $input['logo_id'] ) : 0;
	if ( $logo_id > 0 && ! wp_attachment_is_image( $logo_id ) ) {
		$logo_id = 0;
	}

	return array(
		'email'      => $email !== '' ? $email : $defaults['email'],
		'phone'      => $phone !== '' ? $phone : $defaults['phone'],
		'phone_href' => (string) $href,
		'address_lv' => isset( $input['address_lv'] ) ? sanitize_text_field( (string) $input['address_lv'] ) : $defaults['address_lv'],
		'address_en' => isset( $input['address_en'] ) ? sanitize_text_field( (string) $input['address_en'] ) : $defaults['address_en'],
		'address_ru' => isset( $input['address_ru'] ) ? sanitize_text_field( (string) $input['address_ru'] ) : $defaults['address_ru'],
		'logo_id'    => (string) $logo_id,
	);
}

/**
 * Media library picker on the Construction settings screen.
 */
function construction_settings_admin_assets( string $hook_suffix ): void {
	if ( $hook_suffix !== 'appearance_page_construction-settings' ) {
		return;
	}

	wp_enqueue_media();
	wp_enqueue_script(
		'construction-settings',
		get_template_directory_uri() . '/assets/js/settings-admin.js',
		array( 'jquery' ),
		CONSTRUCTION_VERSION,
		true
	);
	wp_localize_script(
		'construction-settings',
		'constructionSettings',
		array(
			'placeholderLogo' => get_template_directory_uri() . '/assets/images/logo-placeholder.svg',
		)
	);
}
add_action( 'admin_enqueue_scripts', 'construction_settings_admin_assets' );

/**
 * Settings page markup.
 */
function construction_render_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$c       = construction_contact_settings();
	$logo_id = (int) $c['logo_id'];
	$logo_url = construction_logo_url();
	?>
	<div class="wrap">
		<h1><?php echo esc_html__( 'Construction settings', 'construction' ); ?></h1>
		<p><?php echo esc_html__( 'Logo and contact details for the header and mobile menu (live). Page body text (hero, services, FAQ, etc.) is edited under Pages — it lives in the WordPress database, not here.', 'construction' ); ?></p>

		<form method="post" action="options.php">
			<?php settings_fields( 'construction_settings' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php echo esc_html__( 'Logo', 'construction' ); ?></th>
					<td>
						<div id="construction-logo-preview" style="margin-bottom:12px;">
							<img src="<?php echo esc_url( $logo_url ); ?>" alt="" style="max-width:80px;max-height:80px;width:auto;height:auto;display:block;background:#f0f0f1;padding:8px;" />
						</div>
						<input type="hidden" name="construction_contact[logo_id]" id="construction_logo_id" value="<?php echo esc_attr( (string) $logo_id ); ?>" />
						<button type="button" class="button" id="construction-logo-upload"><?php echo esc_html__( 'Select logo', 'construction' ); ?></button>
						<button type="button" class="button" id="construction-logo-remove"<?php echo $logo_id > 0 ? '' : ' style="display:none"'; ?>><?php echo esc_html__( 'Remove logo', 'construction' ); ?></button>
						<p class="description"><?php echo esc_html__( 'SVG or PNG recommended. Shown at about 40×40 in the header. Leave empty to use the theme placeholder.', 'construction' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="construction_email"><?php echo esc_html__( 'Email', 'construction' ); ?></label></th>
					<td><input name="construction_contact[email]" id="construction_email" type="email" class="regular-text" value="<?php echo esc_attr( $c['email'] ); ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="construction_phone"><?php echo esc_html__( 'Phone (display)', 'construction' ); ?></label></th>
					<td><input name="construction_contact[phone]" id="construction_phone" type="text" class="regular-text" value="<?php echo esc_attr( $c['phone'] ); ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="construction_phone_href"><?php echo esc_html__( 'Phone (tel link)', 'construction' ); ?></label></th>
					<td>
						<input name="construction_contact[phone_href]" id="construction_phone_href" type="text" class="regular-text" value="<?php echo esc_attr( $c['phone_href'] ); ?>" />
						<p class="description"><?php echo esc_html__( 'Digits for the tap-to-call link, e.g. +37120000000', 'construction' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="construction_address_lv"><?php echo esc_html__( 'Address (LV)', 'construction' ); ?></label></th>
					<td><input name="construction_contact[address_lv]" id="construction_address_lv" type="text" class="regular-text" value="<?php echo esc_attr( $c['address_lv'] ); ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="construction_address_en"><?php echo esc_html__( 'Address (EN)', 'construction' ); ?></label></th>
					<td><input name="construction_contact[address_en]" id="construction_address_en" type="text" class="regular-text" value="<?php echo esc_attr( $c['address_en'] ); ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="construction_address_ru"><?php echo esc_html__( 'Address (RU)', 'construction' ); ?></label></th>
					<td><input name="construction_contact[address_ru]" id="construction_address_ru" type="text" class="regular-text" value="<?php echo esc_attr( $c['address_ru'] ); ?>" /></td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
