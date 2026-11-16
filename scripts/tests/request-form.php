<?php
/**
 * Run with WP-CLI eval-file on construction.local.
 *
 * Exercises the request form endpoint without sending anything: every email is
 * captured at `pre_wp_mail` (last priority) and never reaches a mailer.
 * Settings changes are simulated with `pre_option_*`; nothing is saved.
 */

if ( get_option( 'home' ) !== 'http://construction.local' ) {
	WP_CLI::error( 'Run this regression check on construction.local only.' );
}

$failures = array();
$checks   = 0;
$check    = static function ( bool $passed, string $message ) use ( &$failures, &$checks ): void {
	$checks++;
	if ( ! $passed ) {
		$failures[] = $message;
	}
};

// Capture mail; returning a value from pre_wp_mail short-circuits wp_mail().
$mails = array();
$capture = static function ( $return, array $atts ) use ( &$mails ) {
	$mails[] = $atts;
	return true;
};
add_filter( 'pre_wp_mail', $capture, PHP_INT_MAX, 2 );

$settings_override = null;
add_filter(
	'pre_option_' . CONSTRUCTION_REQUEST_OPTION,
	static function ( $value ) use ( &$settings_override ) {
		return null === $settings_override ? $value : $settings_override;
	}
);

$ips = array();
$token = static function ( int $age = 10 ): string {
	$time = (string) ( time() - $age );
	return $time . '.' . substr( hash_hmac( 'sha256', $time, wp_salt( 'nonce' ) ), 0, 20 );
};
$send = static function ( array $body, string $ip = '' ) use ( &$ips, $token ) {
	$ip                     = $ip !== '' ? $ip : '203.0.113.' . wp_rand( 1, 250 );
	$ips[]                  = $ip;
	$_SERVER['REMOTE_ADDR'] = $ip;
	$request                = new WP_REST_Request( 'POST', '/construction/v1/request' );
	$request->set_header( 'Content-Type', 'application/json' );
	$request->set_body( wp_json_encode( array_merge( array( 'elapsed' => 9000, 'token' => $token() ), $body ) ) );
	return rest_do_request( $request );
};
$valid = array(
	'lang'    => 'lv',
	'type'    => 'estimate',
	'name'    => 'Jānis Bērziņš',
	'email'   => 'janis@example.com',
	'phone'   => '+371 20000000',
	'message' => 'Vienģimenes māja, 160 m², projekts gatavs.',
	'page'    => (int) ( construction_get_estimate_page_ids()['lv'] ?? 0 ),
);

try {
	// Localised validation errors.
	$response = $send( array( 'lang' => 'lv' ) );
	$data     = $response->get_data();
	$check( 422 === $response->get_status(), 'Empty LV form must return 422.' );
	$check( ( $data['errors']['name'] ?? '' ) === 'Lūdzu, aizpildiet šo lauku.', 'LV required error must be Latvian.' );
	$check( isset( $data['errors']['email'], $data['errors']['message'] ) && ! isset( $data['errors']['phone'] ), 'Only required fields may error when empty.' );

	$data = $send( array_merge( $valid, array( 'lang' => 'ru', 'email' => 'nope', 'phone' => 'abc' ) ) )->get_data();
	$check( ( $data['errors']['email'] ?? '' ) === 'Пожалуйста, введите действительный адрес электронной почты.', 'RU email error must be Russian.' );
	$check( ( $data['errors']['phone'] ?? '' ) === 'Пожалуйста, введите действительный номер телефона.', 'RU phone error must be Russian.' );
	$check( ( $data['message'] ?? '' ) === 'Пожалуйста, проверьте отмеченные поля.', 'RU summary must be Russian.' );
	$check( array() === $mails, 'Invalid submissions must not send mail.' );

	// Bots: honeypot, too fast, forged or fresh token — silent success, no mail.
	foreach (
		array(
			'honeypot'     => array( 'website' => 'http://spam.example' ),
			'client speed' => array( 'elapsed' => 400 ),
			'fresh token'  => array( 'token' => $token( 0 ) ),
			'forged token' => array( 'token' => time() - 60 . '.0000000000aaaaaaaaaa' ),
			'no token'     => array( 'token' => '' ),
		) as $label => $override
	) {
		$response = $send( array_merge( $valid, $override ) );
		$check( 200 === $response->get_status() && ! empty( $response->get_data()['ok'] ), "Bot check ($label) must look like success." );
	}
	$check( array() === $mails, 'Bot submissions must never send mail.' );

	// Valid LV request: company email + Latvian confirmation.
	$response = $send( $valid );
	$check( 200 === $response->get_status() && ! empty( $response->get_data()['ok'] ), 'Valid LV request must succeed.' );
	$check( 2 === count( $mails ), 'Valid request must produce the company email and the confirmation.' );
	$company = $mails[0] ?? array();
	$confirm = $mails[1] ?? array();
	$check( ( $company['to'] ?? '' ) === construction_request_recipient(), 'Company email must go to the configured recipient.' );
	$check( str_contains( (string) ( $company['subject'] ?? '' ), 'Tāmes izstrāde' ), 'Company subject must name the request type.' );
	$check( in_array( 'Reply-To: "Jānis Bērziņš" <janis@example.com>', (array) ( $company['headers'] ?? array() ), true ), 'Company email must reply to the visitor.' );
	$check( str_contains( (string) ( $company['message'] ?? '' ), 'Vienģimenes māja, 160 m²' ) && str_contains( (string) $company['message'], 'Lapa: ' ), 'Company email must contain the message and source page.' );
	$check( ( $confirm['to'] ?? '' ) === 'janis@example.com', 'Confirmation must go to the visitor.' );
	$check( str_starts_with( (string) ( $confirm['subject'] ?? '' ), 'Jūsu pieteikums ir saņemts' ), 'LV confirmation subject must be Latvian.' );
	$check( str_contains( (string) ( $confirm['message'] ?? '' ), 'Labdien, Jānis Bērziņš!' ) && str_contains( (string) $confirm['message'], 'Tāmes izstrāde' ), 'LV confirmation must greet and name the request type.' );
	$check( ! str_contains( (string) ( $confirm['message'] ?? '' ), 'Vienģimenes' ), 'Confirmation must not repeat the visitor message.' );

	// EN confirmation; a name carrying a link is not echoed; header injection is neutralised.
	$mails = array();
	$send( array_merge( $valid, array( 'lang' => 'en', 'type' => 'assessment', 'name' => "Visit www.spam.example\r\nBcc: victim@example.com", 'email' => 'a@example.com' ) ) );
	$check( 2 === count( $mails ), 'EN request must send two emails.' );
	$check( str_contains( (string) ( $mails[1]['message'] ?? '' ), 'Hello a@example.com,' ), 'A name with a link must not appear in the confirmation.' );
	$check( str_contains( (string) ( $mails[1]['message'] ?? '' ), 'Project assessment' ), 'EN confirmation must name the request type in English.' );
	foreach ( (array) ( $mails[0]['headers'] ?? array() ) as $header ) {
		$check( ! preg_match( '/[\r\n]/', $header ), 'Headers must not contain line breaks.' );
	}
	$check( ! preg_match( '/[\r\n]/', (string) ( $mails[0]['subject'] ?? '' ) ), 'Subject must not contain line breaks.' );

	// Editor settings: phone required, message hidden, confirmation off.
	$settings_override = array(
		'confirm' => false,
		'fields'  => array(
			'phone'   => array( 'enabled' => true, 'required' => true ),
			'message' => array( 'enabled' => false, 'required' => true ),
		),
	);
	$data = $send( array( 'lang' => 'en', 'name' => 'Anna', 'email' => 'anna@example.com' ) )->get_data();
	$check( ( $data['errors']['phone'] ?? '' ) === 'Please fill in this field.', 'A field marked required must be enforced (EN message).' );
	$check( ! isset( $data['errors']['message'] ), 'A hidden field must not be required.' );
	$mails = array();
	$send( array( 'lang' => 'en', 'name' => 'Anna', 'email' => 'anna@example.com', 'phone' => '+371 20000000' ) );
	$check( 1 === count( $mails ), 'With confirmation off only the company email is sent.' );
	$settings_override = array( 'fields' => array( 'email' => array( 'enabled' => false, 'required' => false ) ) );
	$check( construction_request_settings()['fields']['email'] === array( 'enabled' => true, 'required' => true ), 'Email must stay shown and required.' );
	$settings_override = null;

	// Hourly limit per visitor, with a localised message.
	$ip = '198.51.100.77';
	for ( $i = 0; $i < CONSTRUCTION_REQUEST_HOURLY_MAX; $i++ ) {
		$send( $valid, $ip );
	}
	$response = $send( array_merge( $valid, array( 'lang' => 'ru' ) ), $ip );
	$check( 429 === $response->get_status(), 'Requests over the hourly limit must be refused.' );
	$check( ( $response->get_data()['message'] ?? '' ) === 'Слишком много заявок за короткое время. Пожалуйста, попробуйте позже.', 'Limit message must be localised.' );
} finally {
	remove_filter( 'pre_wp_mail', $capture, PHP_INT_MAX );
	foreach ( array_unique( $ips ) as $ip ) {
		delete_transient( 'construction_request_' . substr( hash_hmac( 'sha256', $ip, wp_salt( 'nonce' ) ), 0, 32 ) );
	}
}

if ( $failures ) {
	WP_CLI::error( implode( "\n", $failures ) );
}
WP_CLI::success( $checks . ' request form checks passed (no mail sent): validation, languages, spam guards, emails, settings and limits.' );
