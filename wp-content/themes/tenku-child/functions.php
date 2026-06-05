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

/**
 * Cache-bust the base parent/child stylesheets with filemtime.
 *
 * The Child Theme Configurator enqueues (above) carry no version, so browsers
 * keep serving cached style.css after edits. Overriding the registered version
 * with the file modification time forces a fresh fetch whenever style.css
 * changes, without touching the auto-generated enqueue block.
 */
function vip_transits_version_base_styles() {
	$styles = wp_styles();
	if ( ! $styles ) {
		return;
	}

	$map = array(
		'chld_thm_cfg_parent' => get_template_directory() . '/style.css',
		'chld_thm_cfg_child'  => get_stylesheet_directory() . '/style.css',
	);

	foreach ( $map as $handle => $path ) {
		if ( isset( $styles->registered[ $handle ] ) && file_exists( $path ) ) {
			$styles->registered[ $handle ]->ver = (string) filemtime( $path );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'vip_transits_version_base_styles', 11 );

$vip_whatsapp = get_stylesheet_directory() . '/inc/whatsapp-settings.php';
if ( file_exists( $vip_whatsapp ) ) {
	require_once $vip_whatsapp;
}

$vip_whatsapp_sticky = get_stylesheet_directory() . '/inc/whatsapp-sticky-widget.php';
if ( file_exists( $vip_whatsapp_sticky ) ) {
	require_once $vip_whatsapp_sticky;
}

$vip_home_acf = get_stylesheet_directory() . '/inc/homepage-acf.php';
if ( file_exists( $vip_home_acf ) ) {
	require_once $vip_home_acf;
}

$vip_vehicles = get_stylesheet_directory() . '/inc/vehicles-cpt.php';
if ( file_exists( $vip_vehicles ) ) {
	require_once $vip_vehicles;
}

$vip_enquiries = get_stylesheet_directory() . '/inc/enquiries-cpt.php';
if ( file_exists( $vip_enquiries ) ) {
	require_once $vip_enquiries;
}

$vip_occasions = get_stylesheet_directory() . '/inc/occasions-cpt.php';
if ( file_exists( $vip_occasions ) ) {
	require_once $vip_occasions;
}

$vip_vehicle_blocks = get_stylesheet_directory() . '/inc/vehicle-blocks.php';
if ( file_exists( $vip_vehicle_blocks ) ) {
	require_once $vip_vehicle_blocks;
}

$vip_articles = get_stylesheet_directory() . '/inc/articles.php';
if ( file_exists( $vip_articles ) ) {
	require_once $vip_articles;
}

$vip_article_blocks = get_stylesheet_directory() . '/inc/article-blocks.php';
if ( file_exists( $vip_article_blocks ) ) {
	require_once $vip_article_blocks;
}

$vip_page_content = get_stylesheet_directory() . '/inc/page-content-acf.php';
if ( file_exists( $vip_page_content ) ) {
	require_once $vip_page_content;
}

$vip_editor_blocks = get_stylesheet_directory() . '/inc/editor-blocks.php';
if ( file_exists( $vip_editor_blocks ) ) {
	require_once $vip_editor_blocks;
}

$vip_template_parts = get_stylesheet_directory() . '/inc/theme-template-parts.php';
if ( file_exists( $vip_template_parts ) ) {
	require_once $vip_template_parts;
}

$vip_footer_debug = get_stylesheet_directory() . '/inc/contact-footer-debug.php';
if ( file_exists( $vip_footer_debug ) ) {
	require_once $vip_footer_debug;
}

$vip_theme_seo = get_stylesheet_directory() . '/inc/theme-seo.php';
if ( file_exists( $vip_theme_seo ) ) {
	require_once $vip_theme_seo;
}

$vip_theme_icons = get_stylesheet_directory() . '/inc/theme-icons.php';
if ( file_exists( $vip_theme_icons ) ) {
	require_once $vip_theme_icons;
}

$vip_scroll_animations = get_stylesheet_directory() . '/inc/scroll-animations.php';
if ( file_exists( $vip_scroll_animations ) ) {
	require_once $vip_scroll_animations;
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
