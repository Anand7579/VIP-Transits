<?php
/**
 * About flexible layout: FAQ (shared partial).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = (string) get_sub_field( 'heading' );
$intro   = (string) get_sub_field( 'intro' );
$image   = get_sub_field( 'image' );

$items = array();
if ( have_rows( 'faqs' ) ) {
	while ( have_rows( 'faqs' ) ) {
		the_row();
		$question = trim( (string) get_sub_field( 'question' ) );
		$answer   = (string) get_sub_field( 'answer' );
		if ( $question === '' ) {
			continue;
		}
		$items[] = array(
			'question' => $question,
			'answer'   => $answer,
		);
	}
}

if ( ! $items ) {
	return;
}

get_template_part(
	'template-parts/shared/faq-section',
	null,
	array(
		'heading'   => $heading,
		'intro'     => $intro,
		'items'     => $items,
		'image'     => $image,
		'id_prefix' => 'vip-about-faq',
	)
);
