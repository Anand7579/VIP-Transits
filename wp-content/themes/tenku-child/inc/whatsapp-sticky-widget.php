<?php
/**
 * Sticky WhatsApp concierge widget (all public pages).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default prefilled WhatsApp message for the sticky concierge widget.
 *
 * @return string
 */
function vip_transits_concierge_whatsapp_message() {
	$message = __( 'Hi, I would like to speak to the concierge.', 'tenku-child' );

	/**
	 * Filter sticky widget WhatsApp prefilled message.
	 *
	 * @param string $message Plain text message.
	 */
	return (string) apply_filters( 'vip_transits_concierge_whatsapp_message', $message );
}

/**
 * Enqueue sticky widget styles.
 */
function vip_transits_enqueue_whatsapp_sticky_widget() {
	if ( is_admin() || ! function_exists( 'vip_transits_get_whatsapp_number' ) ) {
		return;
	}

	if ( vip_transits_get_whatsapp_number() === '' ) {
		return;
	}

	$path = get_stylesheet_directory() . '/assets/css/whatsapp-sticky.css';
	$uri  = get_stylesheet_directory_uri() . '/assets/css/whatsapp-sticky.css';
	$ver  = file_exists( $path ) ? (string) filemtime( $path ) : wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'vip-whatsapp-sticky',
		$uri,
		array( 'chld_thm_cfg_child' ),
		$ver
	);
}
add_action( 'wp_enqueue_scripts', 'vip_transits_enqueue_whatsapp_sticky_widget', 20 );

/**
 * Output sticky WhatsApp widget in footer.
 */
function vip_transits_render_whatsapp_sticky_widget() {
	if ( is_admin() || ! function_exists( 'vip_transits_whatsapp_href_attr' ) ) {
		return;
	}

	if ( vip_transits_get_whatsapp_number() === '' ) {
		return;
	}

	get_template_part( 'template-parts/global/whatsapp', 'sticky' );
}
add_action( 'wp_footer', 'vip_transits_render_whatsapp_sticky_widget', 50 );
