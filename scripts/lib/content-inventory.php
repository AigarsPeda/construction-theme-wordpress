<?php
/**
 * Print one line per post: ID, type, slug, status, last modified (GMT).
 * Used by the database scripts to spot work that a copy would overwrite.
 *
 *   wp --skip-plugins --skip-themes eval-file content-inventory.php
 */

// phpcs:disable WordPress.DB.DirectDatabaseQuery

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit( 1 );
}

global $wpdb;

$rows = $wpdb->get_results(
	"SELECT ID, post_type, post_name, post_status, post_modified_gmt FROM {$wpdb->posts}
	WHERE post_type NOT IN ('revision', 'customize_changeset', 'oembed_cache') AND post_status <> 'auto-draft'
	ORDER BY ID",
	ARRAY_N
);

foreach ( $rows as $row ) {
	echo implode( "\t", $row ), "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
