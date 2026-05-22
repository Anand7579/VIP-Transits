<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

if ( !function_exists( 'chld_thm_cfg_parent_css' ) ):
    function chld_thm_cfg_parent_css() {
        wp_enqueue_style( 'chld_thm_cfg_parent', trailingslashit( get_template_directory_uri() ) . 'style.css', array(  ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'chld_thm_cfg_parent_css', 10 );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_child', trailingslashit( get_stylesheet_directory_uri() ) . 'style.css', array( 'chld_thm_cfg_parent' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 10 );

// END ENQUEUE PARENT ACTION
// Parent + child styles are enqueued above (chld_thm_cfg_*). Do not enqueue parent style.css again.

$vip_whatsapp = get_stylesheet_directory() . '/inc/whatsapp-settings.php';
if ( file_exists( $vip_whatsapp ) ) {
	require_once $vip_whatsapp;
}

$vip_home_acf = get_stylesheet_directory() . '/inc/homepage-acf.php';
if ( file_exists( $vip_home_acf ) ) {
	require_once $vip_home_acf;
}

$vip_vehicles = get_stylesheet_directory() . '/inc/vehicles-cpt.php';
if ( file_exists( $vip_vehicles ) ) {
	require_once $vip_vehicles;
}

$vip_vehicle_blocks = get_stylesheet_directory() . '/inc/vehicle-blocks.php';
if ( file_exists( $vip_vehicle_blocks ) ) {
	require_once $vip_vehicle_blocks;
}

$vip_theme_icons = get_stylesheet_directory() . '/inc/theme-icons.php';
if ( file_exists( $vip_theme_icons ) ) {
	require_once $vip_theme_icons;
}

/**
 * WordPress Studio / large Figma PNG uploads: avoid thumbnail generation failures.
 */
add_filter( 'big_image_size_threshold', '__return_false' );

/**
 * Sanitize upload filenames (spaces in names like "Group 48472.png" can break uploads).
 *
 * @param array $file Upload file array.
 * @return array
 */
function vip_transits_sanitize_upload_filename( $file ) {
	if ( ! empty( $file['name'] ) ) {
		$file['name'] = sanitize_file_name( $file['name'] );
	}
	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'vip_transits_sanitize_upload_filename' );
