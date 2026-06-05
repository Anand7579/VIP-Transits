<?php
/**
 * Contact page footer diagnostics (HTML comments in page source).
 *
 * Enable: /contact-us/?vip_debug_footer=1 while logged in as Administrator.
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether footer debug comments should be emitted.
 *
 * @return bool
 */
function vip_transits_footer_debug_enabled() {
	if ( is_admin() ) {
		return false;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( empty( $_GET['vip_debug_footer'] ) || '1' !== (string) $_GET['vip_debug_footer'] ) {
		return false;
	}

	return current_user_can( 'manage_options' );
}

/**
 * @param string               $phase  Short label.
 * @param array<string, mixed> $data   Key/value pairs (scalar values only).
 */
function vip_transits_footer_debug_log( $phase, $data = array() ) {
	if ( ! vip_transits_footer_debug_enabled() ) {
		return;
	}

	if ( empty( $GLOBALS['vip_footer_debug_log'] ) || ! is_array( $GLOBALS['vip_footer_debug_log'] ) ) {
		$GLOBALS['vip_footer_debug_log'] = array();
	}

	$GLOBALS['vip_footer_debug_log'][] = array(
		'phase' => (string) $phase,
		'time'  => microtime( true ),
		'data'  => $data,
	);
}

/**
 * @param string               $phase Short label.
 * @param array<string, mixed> $data  Optional scalar context.
 */
function vip_transits_footer_debug_comment( $phase, $data = array() ) {
	if ( ! vip_transits_footer_debug_enabled() ) {
		return;
	}

	vip_transits_footer_debug_log( $phase, $data );

	$parts = array( 'phase=' . rawurlencode( (string) $phase ) );
	foreach ( $data as $key => $value ) {
		if ( is_bool( $value ) ) {
			$value = $value ? '1' : '0';
		} elseif ( is_array( $value ) ) {
			$value = wp_json_encode( $value );
		} else {
			$value = (string) $value;
		}
		$parts[] = rawurlencode( (string) $key ) . '=' . rawurlencode( $value );
	}

	echo "\n<!-- vip-footer-debug " . esc_html( implode( ' ', $parts ) ) . " -->\n";
}

/**
 * Template / DB diagnostics for the active contact page.
 *
 * @return array<string, mixed>
 */
function vip_transits_footer_debug_template_snapshot() {
	$snapshot = array(
		'queried_id'       => (int) get_queried_object_id(),
		'is_page'          => is_page() ? 1 : 0,
		'page_template'    => '',
		'db_has_footer'    => 0,
		'db_template_slug' => '',
		'footer_part_len'  => 0,
	);

	$post_id = (int) get_queried_object_id();
	if ( $post_id > 0 ) {
		$snapshot['page_template'] = (string) get_page_template_slug( $post_id );
	}

	if ( ! function_exists( 'get_block_template' ) ) {
		return $snapshot;
	}

	$theme_slug = get_stylesheet();
	$slug       = $snapshot['page_template'];

	// Normalize block-theme slug (e.g. page-contact vs templates/page-contact.html).
	if ( $slug === '' || $slug === 'default' ) {
		$slug = 'page-contact';
	}
	$slug = preg_replace( '#^templates/#', '', $slug );
	$slug = preg_replace( '#\.html$#', '', (string) $slug );

	$snapshot['db_template_slug'] = $slug;

	$template = get_block_template( $theme_slug . '//' . $slug, 'wp_template' );
	if ( $template && ! empty( $template->wp_id ) ) {
		$content = (string) get_post_field( 'post_content', (int) $template->wp_id );
		$snapshot['db_has_footer'] = str_contains( $content, '"slug":"footer"' ) ? 1 : 0;
	} else {
		// No DB override — theme file is used.
		$snapshot['db_has_footer'] = -1;
	}

	$footer_part = get_block_template( $theme_slug . '//footer', 'wp_template_part' );
	if ( $footer_part && ! empty( $footer_part->wp_id ) ) {
		$snapshot['footer_part_len'] = strlen( (string) get_post_field( 'post_content', (int) $footer_part->wp_id ) );
	}

	return $snapshot;
}

/**
 * @return array<string, mixed>
 */
function vip_transits_footer_debug_markup_lengths() {
	$static = function_exists( 'vip_transits_footer_static_html' )
		? (string) vip_transits_footer_static_html()
		: '';
	$full   = function_exists( 'vip_transits_get_footer_markup' )
		? (string) vip_transits_get_footer_markup()
		: '';

	return array(
		'static_len' => strlen( $static ),
		'full_len'   => strlen( $full ),
		'static_has_class' => str_contains( $static, 'vip-site-footer' ) ? 1 : 0,
		'full_has_class'   => str_contains( $full, 'vip-site-footer' ) ? 1 : 0,
	);
}

/**
 * Print opening diagnostic block early on contact page.
 */
function vip_transits_footer_debug_bootstrap() {
	if ( ! vip_transits_footer_debug_enabled() ) {
		return;
	}

	if ( ! function_exists( 'vip_transits_is_contact_page' ) || ! vip_transits_is_contact_page() ) {
		vip_transits_footer_debug_comment(
			'bootstrap-skip',
			array( 'reason' => 'not_contact_page' )
		);
		return;
	}

	$lengths = vip_transits_footer_debug_markup_lengths();

	vip_transits_footer_debug_comment(
		'bootstrap',
		array_merge(
			vip_transits_footer_debug_template_snapshot(),
			$lengths,
			array(
				'footer_rendered' => ! empty( $GLOBALS['vip_transits_footer_rendered'] ) ? 1 : 0,
				'theme'           => get_stylesheet(),
			)
		)
	);
}
add_action( 'template_redirect', 'vip_transits_footer_debug_bootstrap', 1 );

/**
 * Log when wp_footer runs (proves request completed past main content).
 */
function vip_transits_footer_debug_wp_footer() {
	if ( ! vip_transits_footer_debug_enabled() ) {
		return;
	}

	vip_transits_footer_debug_comment(
		'wp_footer',
		array(
			'footer_rendered' => ! empty( $GLOBALS['vip_transits_footer_rendered'] ) ? 1 : 0,
			'ran'             => 1,
		)
	);
}
add_action( 'wp_footer', 'vip_transits_footer_debug_wp_footer', 999 );

/**
 * Final summary at end of request.
 */
function vip_transits_footer_debug_shutdown_summary() {
	if ( ! vip_transits_footer_debug_enabled() ) {
		return;
	}

	$log = isset( $GLOBALS['vip_footer_debug_log'] ) && is_array( $GLOBALS['vip_footer_debug_log'] )
		? $GLOBALS['vip_footer_debug_log']
		: array();

	$phases = array();
	foreach ( $log as $row ) {
		if ( ! empty( $row['phase'] ) ) {
			$phases[] = (string) $row['phase'];
		}
	}

	$error = error_get_last();

	vip_transits_footer_debug_comment(
		'shutdown',
		array(
			'footer_rendered' => ! empty( $GLOBALS['vip_transits_footer_rendered'] ) ? 1 : 0,
			'phase_count'     => count( $phases ),
			'phases'          => implode( '>', $phases ),
			'fatal'           => $error ? (string) ( $error['message'] ?? '' ) : '',
		)
	);
}
register_shutdown_function( 'vip_transits_footer_debug_shutdown_summary' );
