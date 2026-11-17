<?php
/**
 * Keep environment-specific settings when a database is copied between Local
 * and production: mail delivery (WP Mail SMTP) and Google Site Kit
 * credentials. Production must keep its Gmail connection; Local must never
 * receive it.
 *
 *   wp --skip-plugins --skip-themes eval-file environment-settings.php save <file.json>
 *   wp --skip-plugins --skip-themes eval-file environment-settings.php restore <file.json>
 *
 * `save` runs on the database that is about to be replaced. `restore` runs on
 * it after the import: matching rows that arrived with the import are deleted,
 * then the saved rows are written back unchanged (raw serialized values).
 * WP Mail SMTP log tables are handled by the shell scripts.
 */

// phpcs:disable WordPress.DB.DirectDatabaseQuery

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit( 1 );
}

global $wpdb;

$option_prefixes = array( 'wp_mail_smtp', 'wpmailsmtp', 'googlesitekit' );
$meta_contains   = array( 'googlesitekit', 'mail_smtp' );

$action = (string) ( $args[0] ?? '' );
$file   = (string) ( $args[1] ?? '' );
if ( ! in_array( $action, array( 'save', 'restore' ), true ) || $file === '' ) {
	WP_CLI::error( 'Usage: environment-settings.php save|restore <file.json>' );
}

$option_where = implode(
	' OR ',
	array_map(
		static function ( string $prefix ) use ( $wpdb ): string {
			return $wpdb->prepare( 'option_name LIKE %s', $wpdb->esc_like( $prefix ) . '%' );
		},
		$option_prefixes
	)
);
$meta_where   = implode(
	' OR ',
	array_map(
		static function ( string $needle ) use ( $wpdb ): string {
			return $wpdb->prepare( 'm.meta_key LIKE %s', '%' . $wpdb->esc_like( $needle ) . '%' );
		},
		$meta_contains
	)
);

if ( 'save' === $action ) {
	$data = array(
		'options'  => $wpdb->get_results( "SELECT option_name, option_value, autoload FROM {$wpdb->options} WHERE {$option_where}", ARRAY_A ),
		'usermeta' => $wpdb->get_results( "SELECT u.user_login, m.meta_key, m.meta_value FROM {$wpdb->usermeta} m JOIN {$wpdb->users} u ON u.ID = m.user_id WHERE {$meta_where}", ARRAY_A ),
	);
	if ( false === file_put_contents( $file, wp_json_encode( $data ) ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
		WP_CLI::error( "Could not write $file" );
	}
	WP_CLI::success( sprintf( 'Saved %d environment options and %d user settings.', count( $data['options'] ), count( $data['usermeta'] ) ) );
	return;
}

$data = json_decode( (string) file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
if ( ! is_array( $data ) || ! isset( $data['options'], $data['usermeta'] ) ) {
	WP_CLI::error( "Unreadable environment file: $file" );
}

// Drop what came with the imported database, then put this environment's rows back.
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE {$option_where}" );
foreach ( $data['options'] as $row ) {
	$wpdb->insert( $wpdb->options, $row );
}

$wpdb->query( "DELETE m FROM {$wpdb->usermeta} m WHERE {$meta_where}" );
$restored = 0;
foreach ( $data['usermeta'] as $row ) {
	$user = get_user_by( 'login', $row['user_login'] );
	if ( ! $user ) {
		WP_CLI::warning( "User {$row['user_login']} no longer exists; skipped {$row['meta_key']}." );
		continue;
	}
	$wpdb->insert(
		$wpdb->usermeta,
		array(
			'user_id'    => $user->ID,
			'meta_key'   => $row['meta_key'], // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value' => $row['meta_value'], // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	++$restored;
}

wp_cache_flush();
WP_CLI::success( sprintf( 'Restored %d environment options and %d user settings.', count( $data['options'] ), $restored ) );
