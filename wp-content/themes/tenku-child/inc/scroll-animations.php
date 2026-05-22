<?php
/**
 * Enqueue scroll animations + lazy section assets site-wide.
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue VIP scroll animation CSS/JS on the public site.
 */
function vip_transits_enqueue_scroll_animations() {
	if ( is_admin() ) {
		return;
	}

	$theme_dir = get_stylesheet_directory();
	$theme_uri = get_stylesheet_directory_uri();

	$css_path = $theme_dir . '/assets/css/vip-scroll-animations.css';
	$js_path  = $theme_dir . '/assets/js/vip-scroll-animations.js';

	$css_ver = file_exists( $css_path ) ? (string) filemtime( $css_path ) : wp_get_theme()->get( 'Version' );
	$js_ver  = file_exists( $js_path ) ? (string) filemtime( $js_path ) : $css_ver;

	wp_enqueue_style(
		'vip-scroll-animations',
		$theme_uri . '/assets/css/vip-scroll-animations.css',
		array( 'chld_thm_cfg_child' ),
		$css_ver
	);

	wp_enqueue_script(
		'vip-scroll-animations',
		$theme_uri . '/assets/js/vip-scroll-animations.js',
		array(),
		$js_ver,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'vip_transits_enqueue_scroll_animations', 25 );
