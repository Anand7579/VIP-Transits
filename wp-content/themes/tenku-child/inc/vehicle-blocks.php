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
 * @return array{style:string,script:string,version:string}
 */
function vip_transits_fleet_block_assets() {
	return array(
		'style'   => get_stylesheet_directory_uri() . '/assets/css/vehicle-fleet.css',
		'script'  => get_stylesheet_directory_uri() . '/assets/js/vehicle-fleet.js',
		'version' => wp_get_theme()->get( 'Version' ),
	);
}

/**
 * Register vehicle ACF blocks.
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
	if ( ! is_front_page() && ! is_post_type_archive( 'vip_vehicle' ) && ! is_singular( 'vip_vehicle' ) ) {
		return;
	}

	$assets = vip_transits_fleet_block_assets();

	wp_enqueue_style(
		'vip-vehicle-fleet',
		$assets['style'],
		array( 'chld_thm_cfg_child', 'chld_thm_cfg_parent' ),
		$assets['version']
	);

	wp_enqueue_script(
		'vip-fleet',
		$assets['script'],
		array(),
		$assets['version'],
		true
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
add_action( 'wp_enqueue_scripts', 'vip_transits_enqueue_fleet_block_assets', 20 );
