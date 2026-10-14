<?php
/** Local-only, read-only verification of the Site Kit consent integration. */
if ( 'construction.local' !== wp_parse_url( home_url(), PHP_URL_HOST ) ) { WP_CLI::error( 'Local only.' ); }
$fixture = array( 'enabled' => true, 'measurement_id' => 'G-OVERRIDE123' );
add_filter( 'pre_option_construction_privacy', function () use ( $fixture ) { return $fixture; } );
add_filter( 'pre_option_googlesitekit_analytics-4_settings', function () { return array( 'measurementID' => 'G-SITEKIT123' ); } );
if ( ! apply_filters( 'googlesitekit_analytics-4_tag_blocked', false ) || ! apply_filters( 'googlesitekit_tagmanager_tag_blocked', false ) ) {
    WP_CLI::error( 'Automatic Site Kit tags are not blocked.' );
}
do_action( 'wp_enqueue_scripts' );
$data = wp_scripts()->get_data( 'construction-privacy', 'before' );
if ( ! str_contains( implode( '', $data ), '"measurementId":""' ) ) { WP_CLI::error( 'Local statistics are not excluded.' ); }
add_filter( 'home_url', function ( $url ) { return str_replace( 'http://construction.local', 'https://oaksbuild.lv', $url ); } );
do_action( 'wp_enqueue_scripts' );
$data = implode( '', wp_scripts()->get_data( 'construction-privacy', 'before' ) );
if ( str_contains( $data, '"measurementId":"G-SITEKIT123"' ) ) { WP_CLI::error( 'Local environment exclusion was bypassed.' ); }
WP_CLI::success( 'Automatic tags blocked; Local excluded even when enabled or the domain changes. No settings changed and no tags loaded.' );
