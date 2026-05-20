<?php
/**
 * Block: vip-vehicle-single
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_singular( 'vip_vehicle' ) ) {
	if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		echo '<p class="vip-vehicle-single vip-vehicle-single--placeholder">' . esc_html__( 'Vehicle detail (preview on single vehicle pages).', 'tenku-child' ) . '</p>';
	}
	return;
}

get_template_part( 'template-parts/vehicle/single', 'content' );
