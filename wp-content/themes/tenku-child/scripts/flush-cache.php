<?php
/**
 * Flush WordPress caches (run via Studio CLI):
 *
 *   studio wp eval-file wp-content/themes/tenku-child/scripts/flush-cache.php
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wp_cache_flush();

if ( function_exists( 'acf_reset_local' ) ) {
	acf_reset_local();
}

$deleted = 0;
if ( function_exists( 'delete_expired_transients' ) ) {
	delete_expired_transients( true );
}

global $wpdb;
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
$rows = $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_%' OR option_name LIKE '_site_transient_%'" );
foreach ( $rows as $name ) {
	if ( delete_option( $name ) ) {
		++$deleted;
	}
}

$page_optimize_dir = WP_CONTENT_DIR . '/cache/page_optimize';
if ( is_dir( $page_optimize_dir ) && function_exists( 'page_optimize_cache_cleanup' ) ) {
	page_optimize_cache_cleanup( $page_optimize_dir, 0 );
}

echo "Cache flushed. Transients removed: {$deleted}.\n";
