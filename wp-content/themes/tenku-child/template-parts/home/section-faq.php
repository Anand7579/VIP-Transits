<?php
/**
 * FAQ section: accordion + side car image (Figma).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! have_rows( 'faq_items' ) ) {
	return;
}

$heading = get_sub_field( 'heading' ) ?: __( 'Frequently Asked Questions', 'tenku-child' );
$intro   = get_sub_field( 'intro' );
$image   = get_sub_field( 'side_image' );
$items   = array();

while ( have_rows( 'faq_items' ) ) {
	the_row();
	$items[] = array(
		'question' => get_sub_field( 'question' ),
		'answer'   => get_sub_field( 'answer' ),
	);
}

get_template_part(
	'template-parts/shared/faq',
	'section',
	array(
		'heading'   => $heading,
		'intro'     => $intro,
		'items'     => $items,
		'image'     => $image,
		'id_prefix' => 'vip-faq',
	)
);
