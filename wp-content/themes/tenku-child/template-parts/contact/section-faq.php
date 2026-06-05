<?php
/**
 * Contact content layout: FAQ (shared partial).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = function_exists( 'vip_transits_page_content_post_id' ) ? vip_transits_page_content_post_id() : 0;
$brand   = function_exists( 'vip_transits_contact_content_brand_name' )
	? vip_transits_contact_content_brand_name( $post_id )
	: get_bloginfo( 'name' );

$heading = function_exists( 'vip_transits_contact_content_replace_brand' )
	? vip_transits_contact_content_replace_brand( (string) get_sub_field( 'heading' ), $brand )
	: (string) get_sub_field( 'heading' );
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
			'question' => function_exists( 'vip_transits_contact_content_replace_brand' )
				? vip_transits_contact_content_replace_brand( $question, $brand )
				: $question,
			'answer'   => function_exists( 'vip_transits_contact_content_replace_brand' )
				? vip_transits_contact_content_replace_brand( $answer, $brand )
				: $answer,
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
		'id_prefix' => 'vip-contact-content-faq',
	)
);
