<?php
/**
 * Vehicle categories: image on top, title below (Figma category row).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$categories = array();

if ( have_rows( 'categories' ) ) {
	while ( have_rows( 'categories' ) ) {
		the_row();
		$normalized = vip_transits_normalize_vehicle_category_row(
			array(
				'title'       => get_sub_field( 'title' ),
				'image'       => get_sub_field( 'image' ),
				'filter_slug' => get_sub_field( 'filter_slug' ),
			)
		);
		if ( $normalized ) {
			$categories[] = $normalized;
		}
	}
}

if ( ! $categories ) {
	return;
}

get_template_part(
	'template-parts/vehicle/category',
	'row',
	array(
		'categories' => $categories,
	)
);
