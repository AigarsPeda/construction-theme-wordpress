<?php
/**
 * Plugin Name: Construction Site Kit consent guard
 * Description: Keeps Site Kit reports available while the site's consent controls load Analytics.
 * Version: 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'googlesitekit_analytics-4_tag_blocked', '__return_true', PHP_INT_MAX );
add_filter( 'googlesitekit_analytics-4_tag_amp_blocked', '__return_true', PHP_INT_MAX );
add_filter( 'googlesitekit_tagmanager_tag_blocked', '__return_true', PHP_INT_MAX );
add_filter( 'googlesitekit_tagmanager_tag_amp_blocked', '__return_true', PHP_INT_MAX );

add_action( 'admin_notices', function () {
	$screen = get_current_screen();
	if ( ! current_user_can( 'manage_options' ) || ! $screen || ! str_contains( $screen->id, 'googlesitekit' ) ) {
		return;
	}
	echo '<div class="notice notice-info"><p>OAKS Build loads Google Analytics only after visitor consent. Site Kit can connect Google services and display reports; its automatic Analytics and Tag Manager snippets are blocked to prevent duplicate tags and collection before consent. Configure collection under Settings → Privacy &amp; Analytics. Local visits are excluded. Complete Google account setup on the public oaksbuild.lv website when these changes are released.</p></div>';
} );
