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
		'style'        => get_stylesheet_directory_uri() . '/assets/css/vehicle-fleet.css',
		'script'       => get_stylesheet_directory_uri() . '/assets/js/vehicle-fleet.js',
		'version'      => $style_ver,
		'style_version'  => $style_ver,
		'script_version' => $script_ver,
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
			'enqueue_style'   => get_stylesheet_directory_uri() . '/assets/css/vehicle-single.css',
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
 * Fleet CSS/JS on archive, single vehicle, and homepage (fleet section in vip-home).
 */
function vip_transits_enqueue_fleet_block_assets() {
	$is_fleet_page = is_front_page() || is_post_type_archive( 'vip_vehicle' );
	$is_single     = is_singular( 'vip_vehicle' );

	if ( ! $is_fleet_page && ! $is_single ) {
		return;
	}

	$theme_dir = get_stylesheet_directory();
	$theme_uri = get_stylesheet_directory_uri();
	$assets    = vip_transits_fleet_block_assets();

	if ( $is_fleet_page ) {
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

		if ( is_front_page() ) {
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
		}

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

	if ( $is_single ) {
		$single_css   = $theme_dir . '/assets/css/vehicle-single.css';
		$gallery_js   = $theme_dir . '/assets/js/vehicle-single-gallery.js';
		$faq_js       = $theme_dir . '/blocks/vip-home/faq.js';
		$single_ver   = file_exists( $single_css ) ? (string) filemtime( $single_css ) : $assets['version'];
		$gallery_ver  = file_exists( $gallery_js ) ? (string) filemtime( $gallery_js ) : $single_ver;
		$faq_ver      = file_exists( $faq_js ) ? (string) filemtime( $faq_js ) : $single_ver;

		wp_enqueue_style(
			'vip-vehicle-single',
			$theme_uri . '/assets/css/vehicle-single.css',
			array( 'chld_thm_cfg_child', 'chld_thm_cfg_parent' ),
			$single_ver
		);

		wp_enqueue_script(
			'vip-vehicle-single-gallery',
			$theme_uri . '/assets/js/vehicle-single-gallery.js',
			array(),
			$gallery_ver,
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);

		wp_enqueue_script(
			'vip-vehicle-single-faq',
			$theme_uri . '/blocks/vip-home/faq.js',
			array(),
			$faq_ver,
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'vip_transits_enqueue_fleet_block_assets', 20 );
