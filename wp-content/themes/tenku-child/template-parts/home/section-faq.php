<?php
/**
 * FAQ section: accordion + side car image (Figma).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Read parent-row sub-fields BEFORE iterating faq_items. Calling
// have_rows( 'faq_items' ) pushes the nested repeater onto ACF's loop
// stack, which would make subsequent get_sub_field() calls resolve
// against a faq_items row instead of this faq section row.
$heading = get_sub_field( 'heading' ) ?: __( 'Frequently Asked Questions', 'tenku-child' );
$intro   = get_sub_field( 'intro' );
$image   = get_sub_field( 'side_image' );
$items   = array();

if ( have_rows( 'faq_items' ) ) {
	while ( have_rows( 'faq_items' ) ) {
		the_row();
		$items[] = array(
			'question' => get_sub_field( 'question' ),
			'answer'   => get_sub_field( 'answer' ),
		);
	}
}

if ( ! $items ) {
	return;
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
