<?php
/**
 * Request form ("Pieteikums").
 *
 * A modal form opened by any core Button that carries the CSS class
 * `construction-request` (`construction-request--estimate` and
 * `construction-request--assessment` preset the request type). Editors manage
 * the fields (shown / required), every text, the error messages and the
 * confirmation email per language under Appearance → Request form. The
 * "Request form" block shows the same form inside page content (Contacts).
 *
 * Mail goes out through wp_mail(), so the site's mailer plugin (WP Mail SMTP
 * on production) delivers it. Spam: a honeypot field, a minimum fill time and a
 * per-visitor hourly limit, so the confirmation email cannot be abused to send
 * mail to arbitrary addresses.
 *
 * @package Construction
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const CONSTRUCTION_REQUEST_OPTION     = 'construction_request_form';
const CONSTRUCTION_REQUEST_MIN_MS     = 2500;
const CONSTRUCTION_REQUEST_HOURLY_MAX = 5;

/**
 * Field schema. Order here is the order in the form.
 *
 * @return array<string, array{type: string, autocomplete: string, max: int, admin: string, locked?: bool}>
 */
function construction_request_fields(): array {
	return array(
		'name'     => array( 'type' => 'text', 'autocomplete' => 'name', 'max' => 120, 'admin' => __( 'Name', 'construction' ) ),
		'email'    => array( 'type' => 'email', 'autocomplete' => 'email', 'max' => 190, 'admin' => __( 'Email', 'construction' ), 'locked' => true ),
		'phone'    => array( 'type' => 'tel', 'autocomplete' => 'tel', 'max' => 40, 'admin' => __( 'Phone', 'construction' ) ),
		'company'  => array( 'type' => 'text', 'autocomplete' => 'organization', 'max' => 160, 'admin' => __( 'Company', 'construction' ) ),
		'location' => array( 'type' => 'text', 'autocomplete' => 'off', 'max' => 200, 'admin' => __( 'Property location', 'construction' ) ),
		'message'  => array( 'type' => 'textarea', 'autocomplete' => 'off', 'max' => 5000, 'admin' => __( 'Message', 'construction' ) ),
	);
}

/**
 * Editable texts: key => [admin label, input type].
 *
 * @return array<string, array{0: string, 1: string}>
 */
function construction_request_text_keys(): array {
	return array(
		'inline_title'           => array( __( 'Title of the form on a page (Request form block)', 'construction' ), 'text' ),
		'intro'                  => array( __( 'Introduction under the title', 'construction' ), 'textarea' ),
		'submit'                 => array( __( 'Submit button', 'construction' ), 'text' ),
		'sending'                => array( __( 'Submit button while sending', 'construction' ), 'text' ),
		'close'                  => array( __( 'Close button (screen readers)', 'construction' ), 'text' ),
		'optional'               => array( __( 'Marker for optional fields', 'construction' ), 'text' ),
		'privacy'                => array( __( 'Privacy note', 'construction' ), 'textarea' ),
		'privacy_link'           => array( __( 'Privacy link text', 'construction' ), 'text' ),
		'success_title'          => array( __( 'Success heading', 'construction' ), 'text' ),
		'success_text'           => array( __( 'Success text', 'construction' ), 'textarea' ),
		'placeholder_estimate'   => array( __( 'Message hint for cost estimate requests', 'construction' ), 'textarea' ),
		'placeholder_assessment' => array( __( 'Message hint for project assessment requests', 'construction' ), 'textarea' ),
		'type_general'           => array( __( 'Request type: general (also the default title)', 'construction' ), 'text' ),
		'type_estimate'          => array( __( 'Request type: cost estimate', 'construction' ), 'text' ),
		'type_assessment'        => array( __( 'Request type: project assessment', 'construction' ), 'text' ),
		'err_required'           => array( __( 'Error: required field is empty', 'construction' ), 'text' ),
		'err_email'              => array( __( 'Error: invalid email', 'construction' ), 'text' ),
		'err_phone'              => array( __( 'Error: invalid phone number', 'construction' ), 'text' ),
		'err_form'               => array( __( 'Error: summary next to the button', 'construction' ), 'text' ),
		'err_rate'               => array( __( 'Error: too many requests', 'construction' ), 'textarea' ),
		'err_send'               => array( __( 'Error: sending failed ({email} = your address)', 'construction' ), 'textarea' ),
		'confirm_subject'        => array( __( 'Confirmation email subject', 'construction' ), 'text' ),
		'confirm_body'           => array( __( 'Confirmation email text', 'construction' ), 'body' ),
	);
}

/**
 * Default settings, including all LV / EN / RU texts.
 *
 * @return array<string, mixed>
 */
function construction_request_defaults(): array {
	return array(
		'recipient' => '',
		'confirm'   => true,
		'fields'    => array(
			'name'     => array( 'enabled' => true, 'required' => true ),
			'email'    => array( 'enabled' => true, 'required' => true ),
			'phone'    => array( 'enabled' => true, 'required' => false ),
			'company'  => array( 'enabled' => true, 'required' => false ),
			'location' => array( 'enabled' => false, 'required' => false ),
			'message'  => array( 'enabled' => true, 'required' => true ),
		),
		'lv'        => array(
			'labels'       => array(
				'name'     => 'Vārds, uzvārds',
				'email'    => 'E-pasts',
				'phone'    => 'Tālrunis',
				'company'  => 'Uzņēmums',
				'location' => 'Objekta atrašanās vieta',
				'message'  => 'Ziņa',
			),
			'placeholders' => array(
				'name'     => '',
				'email'    => '',
				'phone'    => '+371',
				'company'  => '',
				'location' => 'Pilsēta vai adrese',
				'message'  => 'Īsi aprakstiet savu projektu vai jautājumu.',
			),
			'texts'        => array(
				'inline_title'           => 'Nosūtiet pieteikumu',
				'intro'                  => 'Atstājiet kontaktinformāciju un īsi aprakstiet savu ieceri. Mēs ar jums sazināsimies.',
				'submit'                 => 'Nosūtīt pieteikumu',
				'sending'                => 'Sūta…',
				'close'                  => 'Aizvērt',
				'optional'               => 'nav obligāti',
				'privacy'                => 'Jūsu datus izmantosim tikai, lai sazinātos ar jums par šo pieteikumu.',
				'privacy_link'           => 'Privātums un sīkdatnes',
				'success_title'          => 'Paldies! Pieteikums ir saņemts.',
				'success_text'           => 'Mēs ar jums sazināsimies pēc iespējas drīzāk.',
				'placeholder_estimate'   => 'Piemēram: mājas tips un platība, projekta stadija, pieejamie rasējumi.',
				'placeholder_assessment' => 'Piemēram: objekta atrašanās vieta, plānotā izmantošana, pieejamā dokumentācija.',
				'type_general'           => 'Pieteikums',
				'type_estimate'          => 'Tāmes izstrāde',
				'type_assessment'        => 'Projekta izvērtēšana',
				'err_required'           => 'Lūdzu, aizpildiet šo lauku.',
				'err_email'              => 'Lūdzu, ievadiet derīgu e-pasta adresi.',
				'err_phone'              => 'Lūdzu, ievadiet derīgu tālruņa numuru.',
				'err_form'               => 'Lūdzu, pārbaudiet iezīmētos laukus.',
				'err_rate'               => 'Pārāk daudz pieteikumu īsā laikā. Lūdzu, mēģiniet vēlreiz vēlāk.',
				'err_send'               => 'Pieteikumu neizdevās nosūtīt. Lūdzu, mēģiniet vēlreiz vai rakstiet mums uz {email}.',
				'confirm_subject'        => 'Jūsu pieteikums ir saņemts — {site}',
				'confirm_body'           => "Labdien, {name}!\n\nPaldies par pieteikumu ({request}). Esam to saņēmuši un sazināsimies ar jums pēc iespējas drīzāk.\n\nJa vēlaties kaut ko papildināt, vienkārši atbildiet uz šo e-pastu.\n\nAr cieņu\n{site}\n{phone}",
			),
		),
		'en'        => array(
			'labels'       => array(
				'name'     => 'Full name',
				'email'    => 'Email',
				'phone'    => 'Phone',
				'company'  => 'Company',
				'location' => 'Property location',
				'message'  => 'Message',
			),
			'placeholders' => array(
				'name'     => '',
				'email'    => '',
				'phone'    => '+371',
				'company'  => '',
				'location' => 'City or address',
				'message'  => 'Briefly describe your project or question.',
			),
			'texts'        => array(
				'inline_title'           => 'Send us a request',
				'intro'                  => 'Leave your contact details and briefly describe your plans. We will get back to you.',
				'submit'                 => 'Send request',
				'sending'                => 'Sending…',
				'close'                  => 'Close',
				'optional'               => 'optional',
				'privacy'                => 'We will use your details only to contact you about this request.',
				'privacy_link'           => 'Privacy & cookies',
				'success_title'          => 'Thank you! Your request has been received.',
				'success_text'           => 'We will get back to you as soon as possible.',
				'placeholder_estimate'   => 'For example: house type and floor area, design stage, drawings available.',
				'placeholder_assessment' => 'For example: property location, intended use, documentation available.',
				'type_general'           => 'Request',
				'type_estimate'          => 'Cost estimate',
				'type_assessment'        => 'Project assessment',
				'err_required'           => 'Please fill in this field.',
				'err_email'              => 'Please enter a valid email address.',
				'err_phone'              => 'Please enter a valid phone number.',
				'err_form'               => 'Please check the highlighted fields.',
				'err_rate'               => 'Too many requests in a short time. Please try again later.',
				'err_send'               => 'We could not send your request. Please try again or email us at {email}.',
				'confirm_subject'        => 'We have received your request — {site}',
				'confirm_body'           => "Hello {name},\n\nThank you for your request ({request}). We have received it and will get back to you as soon as possible.\n\nIf you would like to add anything, simply reply to this email.\n\nKind regards,\n{site}\n{phone}",
			),
		),
		'ru'        => array(
			'labels'       => array(
				'name'     => 'Имя и фамилия',
				'email'    => 'Эл. почта',
				'phone'    => 'Телефон',
				'company'  => 'Компания',
				'location' => 'Расположение объекта',
				'message'  => 'Сообщение',
			),
			'placeholders' => array(
				'name'     => '',
				'email'    => '',
				'phone'    => '+371',
				'company'  => '',
				'location' => 'Город или адрес',
				'message'  => 'Кратко опишите ваш проект или вопрос.',
			),
			'texts'        => array(
				'inline_title'           => 'Отправьте заявку',
				'intro'                  => 'Оставьте контактные данные и кратко опишите ваш замысел. Мы свяжемся с вами.',
				'submit'                 => 'Отправить заявку',
				'sending'                => 'Отправка…',
				'close'                  => 'Закрыть',
				'optional'               => 'необязательно',
				'privacy'                => 'Мы используем ваши данные только для того, чтобы связаться с вами по этой заявке.',
				'privacy_link'           => 'Конфиденциальность и cookie',
				'success_title'          => 'Спасибо! Ваша заявка получена.',
				'success_text'           => 'Мы свяжемся с вами в ближайшее время.',
				'placeholder_estimate'   => 'Например: тип и площадь дома, стадия проекта, имеющиеся чертежи.',
				'placeholder_assessment' => 'Например: расположение объекта, планируемое использование, имеющаяся документация.',
				'type_general'           => 'Заявка',
				'type_estimate'          => 'Составление сметы',
				'type_assessment'        => 'Оценка проекта',
				'err_required'           => 'Пожалуйста, заполните это поле.',
				'err_email'              => 'Пожалуйста, введите действительный адрес электронной почты.',
				'err_phone'              => 'Пожалуйста, введите действительный номер телефона.',
				'err_form'               => 'Пожалуйста, проверьте отмеченные поля.',
				'err_rate'               => 'Слишком много заявок за короткое время. Пожалуйста, попробуйте позже.',
				'err_send'               => 'Не удалось отправить заявку. Пожалуйста, попробуйте еще раз или напишите нам на {email}.',
				'confirm_subject'        => 'Ваша заявка получена — {site}',
				'confirm_body'           => "Здравствуйте, {name}!\n\nСпасибо за заявку ({request}). Мы ее получили и свяжемся с вами в ближайшее время.\n\nЕсли хотите что-то добавить, просто ответьте на это письмо.\n\nС уважением,\n{site}\n{phone}",
			),
		),
	);
}

/**
 * Saved settings merged over the defaults (empty texts fall back to defaults).
 *
 * @return array<string, mixed>
 */
function construction_request_settings(): array {
	$defaults = construction_request_defaults();
	$saved    = get_option( CONSTRUCTION_REQUEST_OPTION, array() );
	$saved    = is_array( $saved ) ? $saved : array();
	$settings = array_replace_recursive( $defaults, $saved );

	foreach ( construction_languages() as $lang ) {
		foreach ( array( 'labels', 'texts' ) as $group ) {
			foreach ( $defaults[ $lang ][ $group ] as $key => $default ) {
				if ( trim( (string) ( $settings[ $lang ][ $group ][ $key ] ?? '' ) ) === '' ) {
					$settings[ $lang ][ $group ][ $key ] = $default;
				}
			}
		}
	}

	// Replies and the confirmation email need an address.
	$settings['fields']['email'] = array( 'enabled' => true, 'required' => true );

	return $settings;
}

/**
 * Address that receives requests.
 */
function construction_request_recipient(): string {
	$settings  = construction_request_settings();
	$recipient = (string) $settings['recipient'];
	if ( $recipient === '' || ! is_email( $recipient ) ) {
		$recipient = construction_contact( 'email' );
	}
	return is_email( $recipient ) ? $recipient : (string) get_option( 'admin_email' );
}

/**
 * Text for one language, with {email}, {site}, {phone} and custom tokens filled.
 *
 * @param array<string, string> $tokens Extra replacements.
 */
function construction_request_text( string $lang, string $key, array $tokens = array() ): string {
	$settings = construction_request_settings();
	$lang     = in_array( $lang, construction_languages(), true ) ? $lang : 'lv';
	$text     = (string) ( $settings[ $lang ]['texts'][ $key ] ?? '' );
	$tokens   = array_merge(
		array(
			'{email}' => construction_request_recipient(),
			'{site}'  => wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
			'{phone}' => construction_contact( 'phone' ),
		),
		$tokens
	);
	return strtr( $text, $tokens );
}

/*
 * ---------------------------------------------------------------------------
 * Admin: Appearance → Request form
 * ---------------------------------------------------------------------------
 */

/**
 * Register the settings page.
 */
function construction_request_admin_menu(): void {
	add_theme_page(
		__( 'Request form', 'construction' ),
		__( 'Request form', 'construction' ),
		'manage_options',
		'construction-request-form',
		'construction_request_render_settings'
	);
}
add_action( 'admin_menu', 'construction_request_admin_menu' );

/**
 * Register the option.
 */
function construction_request_register_setting(): void {
	register_setting(
		'construction_request_form',
		CONSTRUCTION_REQUEST_OPTION,
		array(
			'type'              => 'array',
			'sanitize_callback' => 'construction_request_sanitize',
			'default'           => array(),
		)
	);
}
add_action( 'admin_init', 'construction_request_register_setting' );

/**
 * Sanitize the settings form.
 *
 * @param mixed $input Raw input.
 * @return array<string, mixed>
 */
function construction_request_sanitize( $input ): array {
	$input = is_array( $input ) ? $input : array();
	$out   = array(
		'recipient' => '',
		'confirm'   => ! empty( $input['confirm'] ),
		'fields'    => array(),
	);

	$recipient = sanitize_email( (string) ( $input['recipient'] ?? '' ) );
	if ( $recipient !== '' && is_email( $recipient ) ) {
		$out['recipient'] = $recipient;
	} elseif ( trim( (string) ( $input['recipient'] ?? '' ) ) !== '' ) {
		add_settings_error( CONSTRUCTION_REQUEST_OPTION, 'recipient', __( 'The recipient address is not a valid email. The contact email from Appearance → Construction is used instead.', 'construction' ) );
	}

	foreach ( construction_request_fields() as $key => $field ) {
		$enabled  = ! empty( $field['locked'] ) || ! empty( $input['fields'][ $key ]['enabled'] );
		$required = ! empty( $field['locked'] ) || ( $enabled && ! empty( $input['fields'][ $key ]['required'] ) );

		$out['fields'][ $key ] = array(
			'enabled'  => $enabled,
			'required' => $required,
		);
	}

	foreach ( construction_languages() as $lang ) {
		foreach ( array_keys( construction_request_fields() ) as $key ) {
			$out[ $lang ]['labels'][ $key ]       = sanitize_text_field( (string) ( $input[ $lang ]['labels'][ $key ] ?? '' ) );
			$out[ $lang ]['placeholders'][ $key ] = sanitize_text_field( (string) ( $input[ $lang ]['placeholders'][ $key ] ?? '' ) );
		}
		foreach ( construction_request_text_keys() as $key => $meta ) {
			$value                         = (string) ( $input[ $lang ]['texts'][ $key ] ?? '' );
			$out[ $lang ]['texts'][ $key ] = 'text' === $meta[1] ? sanitize_text_field( $value ) : sanitize_textarea_field( $value );
		}
	}

	return $out;
}

/**
 * Settings page markup.
 */
function construction_request_render_settings(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$settings  = construction_request_settings();
	$saved     = get_option( CONSTRUCTION_REQUEST_OPTION, array() );
	$saved     = is_array( $saved ) ? $saved : array();
	$languages = array(
		'lv' => 'Latviski',
		'en' => 'English',
		'ru' => 'Русский',
	);
	$name      = static function ( string ...$path ): string {
		return CONSTRUCTION_REQUEST_OPTION . '[' . implode( '][', $path ) . ']';
	};
	// Show the value as saved; an empty box falls back to the default on the site.
	$value     = static function ( string $lang, string $group, string $key ) use ( $saved, $settings ): string {
		return (string) ( $saved[ $lang ][ $group ][ $key ] ?? $settings[ $lang ][ $group ][ $key ] ?? '' );
	};
	?>
	<div class="wrap construction-request-admin">
		<h1><?php echo esc_html__( 'Request form', 'construction' ); ?></h1>
		<p><?php echo esc_html__( 'The form opens in a window when a visitor clicks a button that has the CSS class construction-request (Block → Advanced → Additional CSS class). Use construction-request--estimate or construction-request--assessment to preset the request type. The window title is the button text.', 'construction' ); ?></p>
		<p><?php echo esc_html__( 'Emails are sent with the site mailer (WP Mail SMTP). Leave a text box empty to use the default text.', 'construction' ); ?></p>
		<?php settings_errors( CONSTRUCTION_REQUEST_OPTION ); ?>

		<style>
			.construction-request-admin table.widefat td, .construction-request-admin table.widefat th { vertical-align: top; }
			.construction-request-admin .cr-lang input[type="text"], .construction-request-admin .cr-lang textarea { width: 100%; }
			.construction-request-admin .cr-lang textarea { min-height: 4.5em; }
			.construction-request-admin .cr-lang textarea.cr-body { min-height: 14em; }
			.construction-request-admin .cr-sub { display: block; margin: 4px 0 2px; color: #646970; font-size: 12px; }
			.construction-request-admin .cr-check { text-align: center; width: 6em; }
			.construction-request-admin h2 { margin-top: 2em; }
		</style>

		<form method="post" action="options.php">
			<?php settings_fields( 'construction_request_form' ); ?>

			<h2><?php echo esc_html__( 'Fields', 'construction' ); ?></h2>
			<p class="description"><?php echo esc_html__( 'Choose which fields the form shows and which ones are required. Email is always shown and required: replies and the confirmation email need it.', 'construction' ); ?></p>
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php echo esc_html__( 'Field', 'construction' ); ?></th>
						<th class="cr-check"><?php echo esc_html__( 'Show', 'construction' ); ?></th>
						<th class="cr-check"><?php echo esc_html__( 'Required', 'construction' ); ?></th>
						<?php foreach ( $languages as $label ) : ?>
							<th><?php echo esc_html( $label ); ?></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( construction_request_fields() as $key => $field ) : ?>
						<?php $locked = ! empty( $field['locked'] ); ?>
						<tr>
							<th scope="row"><?php echo esc_html( $field['admin'] ); ?></th>
							<td class="cr-check">
								<input type="checkbox" name="<?php echo esc_attr( $name( 'fields', $key, 'enabled' ) ); ?>" value="1" <?php checked( $settings['fields'][ $key ]['enabled'] ); ?> <?php disabled( $locked ); ?> aria-label="<?php echo esc_attr( sprintf( /* translators: %s: field name */ __( 'Show %s', 'construction' ), $field['admin'] ) ); ?>">
							</td>
							<td class="cr-check">
								<input type="checkbox" name="<?php echo esc_attr( $name( 'fields', $key, 'required' ) ); ?>" value="1" <?php checked( $settings['fields'][ $key ]['required'] ); ?> <?php disabled( $locked ); ?> aria-label="<?php echo esc_attr( sprintf( /* translators: %s: field name */ __( '%s is required', 'construction' ), $field['admin'] ) ); ?>">
							</td>
							<?php foreach ( array_keys( $languages ) as $lang ) : ?>
								<td class="cr-lang">
									<span class="cr-sub"><?php echo esc_html__( 'Label', 'construction' ); ?></span>
									<input type="text" name="<?php echo esc_attr( $name( $lang, 'labels', $key ) ); ?>" value="<?php echo esc_attr( $value( $lang, 'labels', $key ) ); ?>">
									<span class="cr-sub"><?php echo esc_html__( 'Hint inside the field', 'construction' ); ?></span>
									<input type="text" name="<?php echo esc_attr( $name( $lang, 'placeholders', $key ) ); ?>" value="<?php echo esc_attr( $value( $lang, 'placeholders', $key ) ); ?>">
								</td>
							<?php endforeach; ?>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<h2><?php echo esc_html__( 'Emails', 'construction' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="cr-recipient"><?php echo esc_html__( 'Send requests to', 'construction' ); ?></label></th>
					<td>
						<input type="email" class="regular-text" id="cr-recipient" name="<?php echo esc_attr( $name( 'recipient' ) ); ?>" value="<?php echo esc_attr( (string) $settings['recipient'] ); ?>" placeholder="<?php echo esc_attr( construction_contact( 'email' ) ); ?>">
						<p class="description"><?php echo esc_html__( 'Empty = the contact email from Appearance → Construction. Replying to a request email answers the visitor directly.', 'construction' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php echo esc_html__( 'Confirmation email', 'construction' ); ?></th>
					<td>
						<label><input type="checkbox" name="<?php echo esc_attr( $name( 'confirm' ) ); ?>" value="1" <?php checked( ! empty( $settings['confirm'] ) ); ?>> <?php echo esc_html__( 'Send the visitor a confirmation in their language', 'construction' ); ?></label>
						<p class="description"><?php echo esc_html__( 'Placeholders: {name} visitor name, {request} request type, {site} site name, {phone} and {email} your contact details. The visitor\'s message is not repeated, so the form cannot be used to send arbitrary text to other people.', 'construction' ); ?></p>
					</td>
				</tr>
			</table>

			<h2><?php echo esc_html__( 'Texts and messages', 'construction' ); ?></h2>
			<table class="widefat striped">
				<thead>
					<tr>
						<th style="width:16em"><?php echo esc_html__( 'Text', 'construction' ); ?></th>
						<?php foreach ( $languages as $label ) : ?>
							<th><?php echo esc_html( $label ); ?></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( construction_request_text_keys() as $key => $meta ) : ?>
						<tr>
							<th scope="row"><?php echo esc_html( $meta[0] ); ?></th>
							<?php foreach ( array_keys( $languages ) as $lang ) : ?>
								<td class="cr-lang">
									<?php if ( 'text' === $meta[1] ) : ?>
										<input type="text" name="<?php echo esc_attr( $name( $lang, 'texts', $key ) ); ?>" value="<?php echo esc_attr( $value( $lang, 'texts', $key ) ); ?>" aria-label="<?php echo esc_attr( $meta[0] . ' (' . $lang . ')' ); ?>">
									<?php else : ?>
										<textarea class="<?php echo 'body' === $meta[1] ? 'cr-body' : ''; ?>" name="<?php echo esc_attr( $name( $lang, 'texts', $key ) ); ?>" aria-label="<?php echo esc_attr( $meta[0] . ' (' . $lang . ')' ); ?>"><?php echo esc_textarea( $value( $lang, 'texts', $key ) ); ?></textarea>
									<?php endif; ?>
								</td>
							<?php endforeach; ?>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<h2><?php echo esc_html__( 'Spam protection', 'construction' ); ?></h2>
			<p class="description"><?php echo esc_html( sprintf( /* translators: %d: requests per hour */ __( 'Always on: a hidden honeypot field that only bots fill in, a minimum time to fill the form, and at most %d requests per visitor per hour. Blocked bots see a normal success message and no email is sent.', 'construction' ), CONSTRUCTION_REQUEST_HOURLY_MAX ) ); ?></p>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/*
 * ---------------------------------------------------------------------------
 * Front end
 * ---------------------------------------------------------------------------
 */

/**
 * Whether the current page has a button that opens the form dialog.
 */
function construction_request_form_needed(): bool {
	$needed = false;
	if ( ! is_admin() && is_singular() ) {
		$content = (string) get_post_field( 'post_content', get_queried_object_id() );
		$needed  = str_contains( $content, 'construction-request' );
	}

	/**
	 * Filters whether the request form dialog is printed on the current page.
	 *
	 * @param bool $needed True when the page content has a request button.
	 */
	return (bool) apply_filters( 'construction_request_form_needed', $needed );
}

/**
 * Enqueue the shared form assets (dialog and inline block).
 */
function construction_request_enqueue_assets(): void {
	wp_enqueue_style(
		'construction-request-form',
		get_template_directory_uri() . '/assets/css/request-form.css',
		array( 'construction-main' ),
		CONSTRUCTION_VERSION
	);
	wp_enqueue_script(
		'construction-request-form',
		get_template_directory_uri() . '/assets/js/request-form.js',
		array(),
		CONSTRUCTION_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}

/**
 * Load assets only where a button opens the dialog (the block loads its own).
 */
function construction_request_enqueue(): void {
	if ( construction_request_form_needed() ) {
		construction_request_enqueue_assets();
	}
}
add_action( 'wp_enqueue_scripts', 'construction_request_enqueue', 20 );

/**
 * Form markup shared by the dialog and the inline block.
 *
 * @param string $prefix Unique id prefix for this instance.
 * @param string $title  Heading text.
 * @param bool   $dialog Whether the form lives in the dialog.
 */
function construction_request_form_markup( string $prefix, string $title, bool $dialog ): string {
	$lang     = construction_current_lang();
	$settings = construction_request_settings();
	$copy     = $settings[ $lang ] ?? $settings['lv'];
	$t        = static function ( string $key ) use ( $lang ): string {
		return construction_request_text( $lang, $key );
	};

	// Two-column pairs for the short fields; an unpaired last one spans the row.
	$fields = array();
	foreach ( construction_request_fields() as $key => $field ) {
		if ( ! empty( $settings['fields'][ $key ]['enabled'] ) ) {
			$fields[ $key ] = $field;
		}
	}
	$half = array_values( array_diff( array_keys( $fields ), array( 'name', 'message' ) ) );
	$wide = count( $half ) % 2 === 1 ? (string) end( $half ) : '';

	$config = array(
		'endpoint' => rest_url( 'construction/v1/request' ),
		'lang'     => $lang,
		'page'     => (int) get_queried_object_id(),
		'token'    => construction_request_token(),
		'title'    => $t( 'type_general' ),
		'messages' => array(
			'required' => $t( 'err_required' ),
			'email'    => $t( 'err_email' ),
			'phone'    => $t( 'err_phone' ),
			'form'     => $t( 'err_form' ),
			'send'     => $t( 'err_send' ),
			'sending'  => $t( 'sending' ),
			'submit'   => $t( 'submit' ),
		),
		'hints'    => array(
			'general'    => (string) ( $copy['placeholders']['message'] ?? '' ),
			'estimate'   => $t( 'placeholder_estimate' ),
			'assessment' => $t( 'placeholder_assessment' ),
		),
	);

	$privacy_url = function_exists( 'construction_privacy_url' ) ? (string) construction_privacy_url() : '';

	ob_start();
	?>
	<div class="construction-request-form__body" data-request-body>
		<h2 class="construction-request-form__title" id="<?php echo esc_attr( $prefix ); ?>-title"><?php echo esc_html( $title ); ?></h2>
		<p class="construction-request-form__intro"><?php echo esc_html( $t( 'intro' ) ); ?></p>

		<form class="construction-request-form__form" novalidate>
			<input type="hidden" name="type" value="general">
			<div class="construction-request-form__grid">
				<?php foreach ( $fields as $key => $field ) : ?>
					<?php
					$id          = $prefix . '-' . $key;
					$required    = ! empty( $settings['fields'][ $key ]['required'] );
					$label       = (string) ( $copy['labels'][ $key ] ?? '' );
					$placeholder = (string) ( $copy['placeholders'][ $key ] ?? '' );
					$is_wide     = in_array( $key, array( 'name', 'message' ), true ) || $key === $wide;
					$attrs       = sprintf(
						'id="%1$s" name="%2$s" autocomplete="%3$s" maxlength="%4$d" aria-describedby="%1$s-error"%5$s%6$s',
						esc_attr( $id ),
						esc_attr( $key ),
						esc_attr( $field['autocomplete'] ),
						(int) $field['max'],
						$required ? ' required aria-required="true"' : '',
						$placeholder !== '' ? ' placeholder="' . esc_attr( $placeholder ) . '"' : ''
					);
					?>
					<div class="construction-request-form__field<?php echo $is_wide ? ' construction-request-form__field--wide' : ''; ?>">
						<label for="<?php echo esc_attr( $id ); ?>">
							<?php echo esc_html( $label ); ?>
							<?php if ( ! $required ) : ?>
								<span class="construction-request-form__optional"><?php echo esc_html( $t( 'optional' ) ); ?></span>
							<?php endif; ?>
						</label>
						<?php if ( 'textarea' === $field['type'] ) : ?>
							<textarea <?php echo $attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?> rows="5"></textarea>
						<?php else : ?>
							<input type="<?php echo esc_attr( $field['type'] ); ?>" <?php echo $attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?><?php echo 'email' === $field['type'] ? ' inputmode="email" spellcheck="false"' : ''; ?>>
						<?php endif; ?>
						<p class="construction-request-form__error" id="<?php echo esc_attr( $id ); ?>-error" hidden></p>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="construction-request-form__trap" aria-hidden="true">
				<label for="<?php echo esc_attr( $prefix ); ?>-website">Website</label>
				<input type="text" id="<?php echo esc_attr( $prefix ); ?>-website" name="website" tabindex="-1" autocomplete="off">
			</div>

			<p class="construction-request-form__privacy">
				<?php echo esc_html( $t( 'privacy' ) ); ?>
				<?php if ( $privacy_url !== '' ) : ?>
					<a href="<?php echo esc_url( $privacy_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $t( 'privacy_link' ) ); ?></a>
				<?php endif; ?>
			</p>

			<div class="construction-request-form__actions">
				<button type="submit" class="construction-request-form__submit"><?php echo esc_html( $t( 'submit' ) ); ?></button>
				<p class="construction-request-form__status" role="status" aria-live="polite"></p>
			</div>
		</form>
	</div>

	<div class="construction-request-form__done" data-request-done hidden>
		<h2 class="construction-request-form__title" tabindex="-1"><?php echo esc_html( $t( 'success_title' ) ); ?></h2>
		<p class="construction-request-form__intro"><?php echo esc_html( $t( 'success_text' ) ); ?></p>
		<?php if ( $dialog ) : ?>
			<button type="button" class="construction-request-form__submit" data-request-close><?php echo esc_html( $t( 'close' ) ); ?></button>
		<?php endif; ?>
	</div>
	<script type="application/json" class="construction-request-form__config"><?php echo wp_json_encode( $config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE ); ?></script>
	<?php
	return (string) ob_get_clean();
}

/**
 * Print the dialog before </body> on pages with request buttons.
 */
function construction_request_render_dialog(): void {
	if ( ! construction_request_form_needed() ) {
		return;
	}

	$lang  = construction_current_lang();
	$close = construction_request_text( $lang, 'close' );
	?>
	<dialog class="construction-request-form construction-request-form--dialog" id="construction-request" aria-labelledby="construction-request-title" data-request-form>
		<div class="construction-request-form__panel">
			<button type="button" class="construction-request-form__close" data-request-close>
				<span class="screen-reader-text"><?php echo esc_html( $close ); ?></span>
			</button>
			<?php echo construction_request_form_markup( 'construction-request', construction_request_text( $lang, 'type_general' ), true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in the builder. ?>
		</div>
	</dialog>
	<?php
}
add_action( 'wp_footer', 'construction_request_render_dialog' );

/**
 * Block "Request form": the same form placed in page content (e.g. Contacts).
 */
function construction_request_register_block(): void {
	wp_register_script(
		'construction-request-form-editor',
		get_template_directory_uri() . '/assets/js/request-form-editor.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-i18n' ),
		CONSTRUCTION_VERSION,
		true
	);
	wp_localize_script(
		'construction-request-form-editor',
		'constructionRequestFormEditor',
		array( 'settingsUrl' => admin_url( 'themes.php?page=construction-request-form' ) )
	);

	register_block_type(
		get_template_directory() . '/blocks/request-form',
		array(
			'render_callback' => 'construction_request_render_block',
			'editor_script'   => 'construction-request-form-editor',
		)
	);
}
add_action( 'init', 'construction_request_register_block', 20 );

/**
 * Render the inline form block.
 */
function construction_request_render_block(): string {
	if ( ! is_admin() && ! ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		construction_request_enqueue_assets();
	}

	$lang = construction_current_lang();
	return sprintf(
		'<section class="construction-request-form construction-request-form--inline" aria-labelledby="construction-request-inline-title" data-request-form><div class="construction-request-form__panel">%s</div></section>',
		construction_request_form_markup( 'construction-request-inline', construction_request_text( $lang, 'inline_title' ), false )
	);
}

/*
 * ---------------------------------------------------------------------------
 * Submission
 * ---------------------------------------------------------------------------
 */

/**
 * REST route: POST /wp-json/construction/v1/request
 */
function construction_request_register_route(): void {
	register_rest_route(
		'construction/v1',
		'/request',
		array(
			'methods'             => 'POST',
			'callback'            => 'construction_request_handle',
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'construction_request_register_route' );

/**
 * Validate a submission against the configured fields.
 *
 * @param array<string, mixed> $raw  Submitted values.
 * @return array{values: array<string, string>, errors: array<string, string>}
 */
function construction_request_validate( array $raw, string $lang ): array {
	$settings = construction_request_settings();
	$values   = array();
	$errors   = array();

	foreach ( construction_request_fields() as $key => $field ) {
		if ( empty( $settings['fields'][ $key ]['enabled'] ) ) {
			continue;
		}

		$value = (string) ( $raw[ $key ] ?? '' );
		$value = 'textarea' === $field['type'] ? sanitize_textarea_field( $value ) : sanitize_text_field( $value );
		$value = trim( mb_substr( $value, 0, (int) $field['max'] ) );

		if ( $value === '' ) {
			if ( ! empty( $settings['fields'][ $key ]['required'] ) ) {
				$errors[ $key ] = construction_request_text( $lang, 'err_required' );
			}
		} elseif ( 'email' === $field['type'] && ! is_email( $value ) ) {
			$errors[ $key ] = construction_request_text( $lang, 'err_email' );
		} elseif ( 'tel' === $field['type'] && ! preg_match( '/^\+?[0-9 ()\-.]{6,25}$/', $value ) ) {
			$errors[ $key ] = construction_request_text( $lang, 'err_phone' );
		}

		$values[ $key ] = $value;
	}

	return array(
		'values' => $values,
		'errors' => $errors,
	);
}

/**
 * Count one submission for this visitor; false once the hourly limit is reached.
 */
function construction_request_within_limit(): bool {
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? (string) wp_unslash( $_SERVER['REMOTE_ADDR'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$key = 'construction_request_' . substr( hash_hmac( 'sha256', $ip, wp_salt( 'nonce' ) ), 0, 32 );

	$count = (int) get_transient( $key );
	if ( $count >= CONSTRUCTION_REQUEST_HOURLY_MAX ) {
		return false;
	}
	set_transient( $key, $count + 1, HOUR_IN_SECONDS );
	return true;
}

/**
 * Signed page-render time, checked on submit (cannot be faked by the browser).
 */
function construction_request_token(): string {
	$time = (string) time();
	return $time . '.' . substr( hash_hmac( 'sha256', $time, wp_salt( 'nonce' ) ), 0, 20 );
}

/**
 * Whether a token is genuine and old enough for a human to have filled the form.
 */
function construction_request_token_ok( string $token ): bool {
	$parts = explode( '.', $token );
	if ( count( $parts ) !== 2 || ! ctype_digit( $parts[0] ) ) {
		return false;
	}
	$expected = substr( hash_hmac( 'sha256', $parts[0], wp_salt( 'nonce' ) ), 0, 20 );
	return hash_equals( $expected, $parts[1] ) && ( time() - (int) $parts[0] ) * 1000 >= CONSTRUCTION_REQUEST_MIN_MS;
}

/**
 * Handle a submission.
 */
function construction_request_handle( WP_REST_Request $request ): WP_REST_Response {
	$raw  = (array) $request->get_json_params();
	$lang = in_array( $raw['lang'] ?? '', construction_languages(), true ) ? (string) $raw['lang'] : 'lv';

	// Bots: honeypot filled or form sent faster than a person can type.
	// Answer like a success so they learn nothing, and send nothing.
	$honeypot = trim( (string) ( $raw['website'] ?? '' ) );
	$elapsed  = (int) ( $raw['elapsed'] ?? 0 );
	if ( $honeypot !== '' || $elapsed < CONSTRUCTION_REQUEST_MIN_MS || ! construction_request_token_ok( (string) ( $raw['token'] ?? '' ) ) ) {
		return new WP_REST_Response( array( 'ok' => true ), 200 );
	}

	$checked = construction_request_validate( $raw, $lang );
	if ( $checked['errors'] ) {
		return new WP_REST_Response(
			array(
				'ok'      => false,
				'message' => construction_request_text( $lang, 'err_form' ),
				'errors'  => $checked['errors'],
			),
			422
		);
	}

	if ( ! construction_request_within_limit() ) {
		return new WP_REST_Response(
			array(
				'ok'      => false,
				'message' => construction_request_text( $lang, 'err_rate' ),
			),
			429
		);
	}

	$type = in_array( $raw['type'] ?? '', array( 'estimate', 'assessment' ), true ) ? (string) $raw['type'] : 'general';
	$page = absint( $raw['page'] ?? 0 );
	if ( $page && 'publish' !== get_post_status( $page ) ) {
		$page = 0;
	}

	if ( ! construction_request_send( $checked['values'], $type, $lang, $page ) ) {
		return new WP_REST_Response(
			array(
				'ok'      => false,
				'message' => construction_request_text( $lang, 'err_send' ),
			),
			500
		);
	}

	return new WP_REST_Response( array( 'ok' => true ), 200 );
}

/**
 * Remove characters that could break an email header.
 */
function construction_request_header_safe( string $value ): string {
	return trim( (string) preg_replace( '/[\r\n"<>,;]+/', ' ', $value ) );
}

/**
 * Email the request to the company, then confirm to the visitor.
 *
 * @param array<string, string> $values Validated values.
 * @return bool Whether the company email was accepted by the mailer.
 */
function construction_request_send( array $values, string $type, string $lang, int $page ): bool {
	$settings  = construction_request_settings();
	$site      = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
	$labels    = $settings['lv']['labels'];
	$type_lv   = construction_request_text( 'lv', 'type_' . $type );
	$name      = $values['name'] ?? '';
	$email     = $values['email'] ?? '';
	$recipient = construction_request_recipient();

	// Company notification, in Latvian.
	$lines = array( 'Pieteikuma veids: ' . $type_lv, '' );
	foreach ( construction_request_fields() as $key => $field ) {
		if ( ! isset( $values[ $key ] ) || $values[ $key ] === '' ) {
			continue;
		}
		$label   = (string) ( $labels[ $key ] ?? $field['admin'] );
		$lines[] = 'textarea' === $field['type'] ? $label . ":\n" . $values[ $key ] . "\n" : $label . ': ' . $values[ $key ];
	}
	$lines[] = '';
	if ( $page ) {
		$lines[] = 'Lapa: ' . wp_specialchars_decode( get_the_title( $page ), ENT_QUOTES ) . ' — ' . get_permalink( $page );
	}
	$lines[] = 'Valoda: ' . strtoupper( $lang );
	$lines[] = 'Laiks: ' . wp_date( 'd.m.Y H:i' );

	$subject = sprintf( '[%s] %s: %s', $site, $type_lv, construction_request_header_safe( $name !== '' ? $name : $email ) );
	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	if ( is_email( $email ) ) {
		$reply     = construction_request_header_safe( $name );
		$headers[] = 'Reply-To: ' . ( $reply !== '' ? '"' . $reply . '" ' : '' ) . '<' . $email . '>';
	}

	if ( ! wp_mail( $recipient, $subject, implode( "\n", $lines ), $headers ) ) {
		return false;
	}

	// Visitor confirmation, in their language. It never repeats their message,
	// and a name that carries a link is not echoed either.
	if ( ! empty( $settings['confirm'] ) && is_email( $email ) ) {
		$greeting = ( $name !== '' && ! preg_match( '#https?://|www\.#i', $name ) ) ? $name : $email;
		$tokens   = array(
			'{name}'    => $greeting,
			'{request}' => construction_request_text( $lang, 'type_' . $type ),
		);
		wp_mail(
			$email,
			construction_request_header_safe( construction_request_text( $lang, 'confirm_subject', $tokens ) ),
			construction_request_text( $lang, 'confirm_body', $tokens ),
			array(
				'Content-Type: text/plain; charset=UTF-8',
				'Reply-To: <' . $recipient . '>',
			)
		);
	}

	return true;
}
