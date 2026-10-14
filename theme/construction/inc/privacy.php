<?php
/** Consent controls and editable WordPress settings; no tag is printed by PHP. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function construction_privacy_copy_defaults( $language ) {
	if ( 'ru' === $language ) {
		return array(
			'title' => 'Ваш выбор файлов cookie',
			'text' => 'Необходимые файлы cookie сохраняют выбранный язык и ваши настройки. С вашего разрешения Google Analytics помогает понять, как используется сайт. Статистика необязательна. Вы можете изменить свой выбор в любое время.',
			'accept' => 'Разрешить статистику', 'reject' => 'Отклонить статистику',
			'settings' => 'Настройки cookie', 'privacy' => 'Конфиденциальность и cookie', 'close' => 'Закрыть без изменений',
		);
	}
	return $language === 'en' ? array(
		'title' => 'Your cookie choice',
		'text' => 'We use essential cookies to remember your language and cookie choice. With your permission, Google Analytics helps us understand which pages are useful and improve the website. Analytics is optional. You can change your choice at any time.',
		'accept' => 'Accept analytics', 'reject' => 'Reject analytics',
		'settings' => 'Cookie settings', 'privacy' => 'Privacy & cookies', 'close' => 'Close without changing',
	) : array(
		'title' => 'Jūsu sīkdatņu izvēle',
		'text' => 'Nepieciešamās sīkdatnes saglabā valodas un sīkdatņu izvēli. Ar jūsu atļauju Google Analytics palīdz saprast, kuras lapas ir noderīgas, un uzlabot vietni. Analītika nav obligāta. Savu izvēli varat mainīt jebkurā laikā.',
		'accept' => 'Atļaut analītiku', 'reject' => 'Noraidīt analītiku',
		'settings' => 'Sīkdatņu iestatījumi', 'privacy' => 'Privātums un sīkdatnes', 'close' => 'Aizvērt, nemainot izvēli',
	);
}

function construction_privacy_options() {
	return get_option( 'construction_privacy', array() );
}

function construction_privacy_copy() {
	$options = construction_privacy_options();
	$lang = construction_current_lang();
	return array_replace( construction_privacy_copy_defaults( $lang ), $options[ $lang ] ?? array() );
}

function construction_privacy_url() {
	$options = construction_privacy_options();
	$id = absint( $options[ 'page_' . construction_current_lang() ] ?? 0 );
	return $id && get_post_status( $id ) === 'publish' ? get_permalink( $id ) : '';
}

function construction_privacy_sanitize( $input ) {
	$input = is_array( $input ) ? $input : array();
	$id = strtoupper( sanitize_text_field( $input['measurement_id'] ?? '' ) );
	$invalid_id = false;
	if ( $id && ! preg_match( '/^G-[A-Z0-9]+$/', $id ) ) {
		add_settings_error( 'construction_privacy', 'measurement_id', 'Enter a valid GA4 measurement ID (G-…). Analytics has been disabled.' );
		$id = '';
		$invalid_id = true;
	}
	$result = array( 'measurement_id' => $id, 'enabled' => ! empty( $input['enabled'] ) && ! $invalid_id );
	foreach ( array( 'lv', 'en', 'ru' ) as $lang ) {
		$result[ 'page_' . $lang ] = absint( $input[ 'page_' . $lang ] ?? 0 );
		foreach ( construction_privacy_copy_defaults( $lang ) as $key => $default ) {
			$result[ $lang ][ $key ] = sanitize_textarea_field( $input[ $lang ][ $key ] ?? $default );
		}
	}
	return $result;
}

add_action( 'admin_init', function () {
	register_setting( 'construction_privacy', 'construction_privacy', array( 'sanitize_callback' => 'construction_privacy_sanitize' ) );
} );
add_action( 'admin_menu', function () {
	add_options_page( 'Privacy & Analytics', 'Privacy & Analytics', 'manage_options', 'construction-privacy', 'construction_privacy_settings_page' );
} );

function construction_privacy_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) { return; }
	$options = construction_privacy_options();
	echo '<div class="wrap"><h1>Privacy & Analytics</h1><p>GA4 uses basic consent mode: the Google tag loads only after analytics consent. Advertising stays disabled. Local, staging and logged-in visits are excluded. The connected Site Kit measurement ID is used automatically. Review the privacy pages and GA4 property settings before enabling collection.</p><form method="post" action="options.php">';
	settings_fields( 'construction_privacy' );
	echo '<p><label for="construction-measurement">GA4 measurement ID override, if Site Kit is not connected</label><br><input id="construction-measurement" name="construction_privacy[measurement_id]" value="' . esc_attr( $options['measurement_id'] ?? '' ) . '" placeholder="G-…" class="regular-text"></p>';
	echo '<p><label><input type="checkbox" name="construction_privacy[enabled]" value="1" ' . checked( ! empty( $options['enabled'] ), true, false ) . '> Enable analytics on the production website after consent</label></p>';
	foreach ( array( 'lv' => 'Latviešu', 'en' => 'English', 'ru' => 'Русский' ) as $lang => $label ) {
		echo '<h2>' . esc_html( $label ) . '</h2><p><label for="construction-page-' . esc_attr( $lang ) . '">Privacy & cookies page</label><br>';
		wp_dropdown_pages( array( 'name' => 'construction_privacy[page_' . $lang . ']', 'id' => 'construction-page-' . $lang, 'selected' => $options[ 'page_' . $lang ] ?? 0, 'show_option_none' => 'Choose a published page', 'option_none_value' => 0, 'echo' => true ) );
		echo '</p>';
		foreach ( construction_privacy_copy_defaults( $lang ) as $key => $default ) {
			echo '<p><label for="construction-copy-' . esc_attr( $lang . '-' . $key ) . '">' . esc_html( ucfirst( $key ) ) . '</label><br><textarea class="large-text" rows="' . ( $key === 'text' ? '3' : '1' ) . '" id="construction-copy-' . esc_attr( $lang . '-' . $key ) . '" name="construction_privacy[' . esc_attr( $lang ) . '][' . esc_attr( $key ) . ']">' . esc_textarea( $options[ $lang ][ $key ] ?? $default ) . '</textarea></p>';
		}
	}
	submit_button();
	echo '</form></div>';
}

add_action( 'wp_enqueue_scripts', function () {
	$version = (string) filemtime( get_template_directory() . '/assets/js/privacy.js' );
	$options = construction_privacy_options();
	$host = wp_parse_url( home_url(), PHP_URL_HOST );
	$production = wp_get_environment_type() === 'production' && in_array( $host, array( 'oaksbuild.lv', 'www.oaksbuild.lv' ), true );
	$site_kit = get_option( 'googlesitekit_analytics-4_settings', array() );
	$id = $site_kit['measurementID'] ?? '';
	if ( ! preg_match( '/^G-[A-Z0-9]+$/', $id ) ) { $id = $options['measurement_id'] ?? ''; }
	$enabled = ! empty( $options['enabled'] ) && preg_match( '/^G-[A-Z0-9]+$/', $id ) && $production && ! is_user_logged_in();
	wp_enqueue_style( 'construction-privacy', get_template_directory_uri() . '/assets/css/privacy.css', array( 'construction-main' ), $version );
	wp_enqueue_script( 'construction-privacy', get_template_directory_uri() . '/assets/js/privacy.js', array(), $version, true );
	wp_add_inline_script( 'construction-privacy', 'window.constructionPrivacy = ' . wp_json_encode( array( 'measurementId' => $enabled ? $id : '', 'language' => construction_current_lang(), 'version' => 1 ) ) . ';', 'before' );
} );

function construction_privacy_footer_links() {
	$copy = construction_privacy_copy();
	echo '<div class="construction-privacy-links">';
	if ( construction_privacy_url() ) { echo '<a href="' . esc_url( construction_privacy_url() ) . '">' . esc_html( $copy['privacy'] ) . '</a>'; }
	echo '<button type="button" data-cookie-settings hidden aria-controls="construction-cookie-banner" aria-expanded="false">' . esc_html( $copy['settings'] ) . '</button></div>';
}

add_action( 'wp_footer', function () {
	$copy = construction_privacy_copy();
	echo '<section class="construction-cookie-banner" id="construction-cookie-banner" aria-labelledby="construction-cookie-title" hidden><div class="construction-cookie-copy"><h2 id="construction-cookie-title">' . esc_html( $copy['title'] ) . '</h2><p>' . esc_html( $copy['text'] ) . '</p>';
	if ( construction_privacy_url() ) { echo '<a href="' . esc_url( construction_privacy_url() ) . '">' . esc_html( $copy['privacy'] ) . '</a>'; }
	echo '</div><div class="construction-cookie-actions"><button type="button" data-cookie-choice="false">' . esc_html( $copy['reject'] ) . '</button><button type="button" data-cookie-choice="true">' . esc_html( $copy['accept'] ) . '</button><button type="button" class="construction-cookie-close" data-cookie-close hidden>' . esc_html( $copy['close'] ) . '</button></div></section>';
} );
