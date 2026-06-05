<?php
/**
 * ACF blocks for vehicle CPT templates (block theme).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fleet block asset URLs.
 *
 * @return array{style:string,script:string,version:string,style_version:string,script_version:string}
 */
function vip_transits_fleet_block_assets() {
	$theme_dir = get_stylesheet_directory();
	$theme_ver = wp_get_theme()->get( 'Version' );

	$style_path = $theme_dir . '/assets/css/vehicle-fleet.css';
	$script_path = $theme_dir . '/assets/js/vehicle-fleet.js';

	$style_ver  = file_exists( $style_path ) ? (string) filemtime( $style_path ) : $theme_ver;
	$script_ver = file_exists( $script_path ) ? (string) filemtime( $script_path ) : $theme_ver;

	return array(
		'style'          => get_stylesheet_directory_uri() . '/assets/css/vehicle-fleet.css',
		'script'         => get_stylesheet_directory_uri() . '/assets/js/vehicle-fleet.js',
		'version'        => $style_ver,
		'style_version'  => $style_ver,
		'script_version' => $script_ver,
	);
}

/**
 * Single vehicle page asset URLs and filemtime versions.
 *
 * @return array{style:string,gallery_script:string,faq_script:string,version:string,gallery_version:string,faq_version:string}
 */
function vip_transits_vehicle_single_assets() {
	$theme_dir = get_stylesheet_directory();
	$theme_uri = get_stylesheet_directory_uri();
	$theme_ver = wp_get_theme()->get( 'Version' );

	$style_path   = $theme_dir . '/assets/css/vehicle-single.css';
	$gallery_path = $theme_dir . '/assets/js/vehicle-single-gallery.js';
	$faq_path     = $theme_dir . '/blocks/vip-home/faq.js';

	$style_ver   = file_exists( $style_path ) ? (string) filemtime( $style_path ) : $theme_ver;
	$gallery_ver = file_exists( $gallery_path ) ? (string) filemtime( $gallery_path ) : $style_ver;
	$faq_ver     = file_exists( $faq_path ) ? (string) filemtime( $faq_path ) : $style_ver;

	return array(
		'style'           => $theme_uri . '/assets/css/vehicle-single.css',
		'gallery_script'  => $theme_uri . '/assets/js/vehicle-single-gallery.js',
		'faq_script'      => $theme_uri . '/blocks/vip-home/faq.js',
		'version'         => $style_ver,
		'gallery_version' => $gallery_ver,
		'faq_version'     => $faq_ver,
	);
}

/**
 * Enqueue vehicle single CSS/JS (shared by front end, block editor, and ACF block).
 */
function vip_transits_enqueue_vehicle_single_assets() {
	$assets = vip_transits_vehicle_single_assets();
	$deps   = array( 'chld_thm_cfg_child', 'chld_thm_cfg_parent' );

	wp_enqueue_style(
		'vip-vehicle-single',
		$assets['style'],
		$deps,
		$assets['version']
	);

	wp_enqueue_script(
		'vip-vehicle-single-gallery',
		$assets['gallery_script'],
		array(),
		$assets['gallery_version'],
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	wp_enqueue_script(
		'vip-vehicle-single-faq',
		$assets['faq_script'],
		array(),
		$assets['faq_version'],
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}

/**
 * Register vehicle ACF blocks (PHP only — block.json removed to avoid duplicate registration).
 */
function vip_transits_register_vehicle_blocks() {
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	$dir = get_stylesheet_directory();

	acf_register_block_type(
		array(
			'name'            => 'vip-vehicle-single',
			'title'           => __( 'Vehicle detail', 'tenku-child' ),
			'description'     => __( 'Single vehicle layout for the fleet CPT.', 'tenku-child' ),
			'category'        => 'layout',
			'icon'            => 'car',
			'keywords'        => array( 'vehicle', 'fleet', 'vip' ),
			'render_template' => $dir . '/blocks/vip-vehicle-single/render.php',
			'enqueue_assets'  => 'vip_transits_enqueue_vehicle_single_block_assets',
			'mode'            => 'preview',
			'supports'        => array(
				'align'  => array( 'wide', 'full' ),
				'anchor' => true,
				'mode'   => false,
			),
		)
	);

	acf_register_block_type(
		array(
			'name'            => 'vip-vehicle-archive',
			'title'           => __( 'Vehicle fleet archive', 'tenku-child' ),
			'description'     => __( 'Fleet grid with filters for the vehicle archive.', 'tenku-child' ),
			'category'        => 'layout',
			'icon'            => 'grid-view',
			'keywords'        => array( 'fleet', 'vehicles', 'archive' ),
			'render_template' => $dir . '/blocks/vip-vehicle-archive/render.php',
			'mode'            => 'preview',
			'supports'        => array(
				'align'  => array( 'wide', 'full' ),
				'anchor' => true,
				'mode'   => false,
			),
		)
	);
}
add_action( 'acf/init', 'vip_transits_register_vehicle_blocks', 5 );

/**
 * ACF block preview: vehicle-single.css with filemtime version.
 *
 * @param array $block_type Block type settings.
 */
function vip_transits_enqueue_vehicle_single_block_assets( $block_type ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
	unset( $block_type );
	vip_transits_enqueue_vehicle_single_assets();
}

/**
 * Block editor: same dynamic version for vehicle single preview.
 */
function vip_transits_enqueue_vehicle_single_editor_assets() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen ) {
		return;
	}

	$load = false;
	if ( 'vip_vehicle' === $screen->post_type || 'site-editor' === $screen->id ) {
		$load = true;
	}

	if ( ! $load ) {
		return;
	}

	vip_transits_enqueue_vehicle_single_assets();
}
add_action( 'enqueue_block_editor_assets', 'vip_transits_enqueue_vehicle_single_editor_assets' );

/**
 * Fleet CSS/JS on archive, single vehicle, and homepage (fleet section in vip-home).
 */
/**
 * Whether the current request is an occasion listing page.
 *
 * @param int $post_id Optional page ID.
 * @return bool
 */
function vip_transits_is_occasion_listing_page( $post_id = 0 ) {
	if ( function_exists( 'vip_transits_is_occasion_detail' ) ) {
		return vip_transits_is_occasion_detail( $post_id );
	}

	if ( is_singular( 'vip_occasion' ) ) {
		return true;
	}

	if ( ! function_exists( 'vip_transits_page_uses_vip_template' ) ) {
		return false;
	}

	$post_id = $post_id ? (int) $post_id : (int) get_queried_object_id();
	if ( $post_id <= 0 ) {
		return false;
	}

	return is_page( $post_id ) && vip_transits_page_uses_vip_template( $post_id, 'occasion' );
}

/**
 * Enqueue fleet assets (CSS, JS, AJAX) for a listing view.
 */
function vip_transits_enqueue_fleet_listing_assets() {
	$theme_dir = get_stylesheet_directory();
	$theme_uri = get_stylesheet_directory_uri();
	$assets    = vip_transits_fleet_block_assets();

	$bridge_js  = $theme_dir . '/assets/js/category-fleet-bridge.js';
	$bridge_ver = file_exists( $bridge_js ) ? (string) filemtime( $bridge_js ) : $assets['script_version'];

	wp_enqueue_style(
		'vip-vehicle-fleet',
		$assets['style'],
		array( 'chld_thm_cfg_child', 'chld_thm_cfg_parent' ),
		$assets['style_version']
	);

	wp_enqueue_script(
		'vip-fleet',
		$assets['script'],
		array(),
		$assets['script_version'],
		true
	);

	wp_enqueue_script(
		'vip-category-fleet-bridge',
		$theme_uri . '/assets/js/category-fleet-bridge.js',
		array( 'vip-fleet' ),
		$bridge_ver,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	wp_localize_script(
		'vip-fleet',
		'vipFleet',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'vip_fleet_load_more' ),
			'i18n'    => array(
				'showing'  => __( 'Showing %1$s vehicles', 'tenku-child' ),
				'loadMore' => __( 'Load more', 'tenku-child' ),
			),
		)
	);
}

function vip_transits_enqueue_fleet_block_assets() {
	$is_fleet_page = is_front_page() || is_post_type_archive( 'vip_vehicle' ) || vip_transits_is_occasion_listing_page();
	$is_single     = is_singular( 'vip_vehicle' );

	if ( ! $is_fleet_page && ! $is_single ) {
		return;
	}

	$theme_dir = get_stylesheet_directory();
	$theme_uri = get_stylesheet_directory_uri();
	$assets    = vip_transits_fleet_block_assets();

	if ( $is_fleet_page ) {
		vip_transits_enqueue_fleet_listing_assets();

		if (
			is_post_type_archive( 'vip_vehicle' )
			&& function_exists( 'vip_transits_fleet_archive_show_banner' )
			&& vip_transits_fleet_archive_show_banner()
			&& function_exists( 'vip_transits_page_content_assets' )
		) {
			$page_assets = vip_transits_page_content_assets();
			wp_enqueue_style(
				'vip-pages',
				$page_assets['style'],
				array( 'chld_thm_cfg_child', 'vip-fleet' ),
				$page_assets['version']
			);
		}

		if ( is_front_page() ) {
			$hero_bridge_js  = $theme_dir . '/assets/js/hero-fleet-bridge.js';
			$hero_bridge_ver = file_exists( $hero_bridge_js ) ? (string) filemtime( $hero_bridge_js ) : $assets['script_version'];

			wp_enqueue_script(
				'vip-hero-fleet-bridge',
				$theme_uri . '/assets/js/hero-fleet-bridge.js',
				array( 'vip-fleet' ),
				$hero_bridge_ver,
				array(
					'in_footer' => true,
					'strategy'  => 'defer',
				)
			);
		}
	}

	if ( $is_single ) {
		vip_transits_enqueue_vehicle_single_assets();
	}
}
add_action( 'wp_enqueue_scripts', 'vip_transits_enqueue_fleet_block_assets', 20 );
