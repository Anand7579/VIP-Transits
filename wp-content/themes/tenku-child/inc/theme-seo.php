<?php
/**
 * Title tag, meta description, and single H1 guidance for VIP templates.
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Core theme supports for SEO-friendly markup.
 */
function vip_transits_theme_seo_setup() {
	add_theme_support( 'title-tag' );
}
add_action( 'after_setup_theme', 'vip_transits_theme_seo_setup' );

/**
 * Whether a dedicated SEO plugin manages meta tags.
 *
 * @return bool
 */
function vip_transits_has_seo_plugin() {
	return defined( 'WPSEO_VERSION' )
		|| defined( 'RANK_MATH_VERSION' )
		|| defined( 'AIOSEO_VERSION' )
		|| defined( 'SEOPRESS_VERSION' )
		|| defined( 'THE_SEO_FRAMEWORK_VERSION' );
}

/**
 * Meta description from excerpt (fallback when no SEO plugin).
 */
function vip_transits_output_meta_description() {
	if ( is_admin() || vip_transits_has_seo_plugin() || ! is_singular() ) {
		return;
	}

	$post_id = (int) get_queried_object_id();
	if ( $post_id <= 0 ) {
		return;
	}

	$description = trim( (string) get_post_field( 'post_excerpt', $post_id ) );
	if ( $description === '' ) {
		$raw = (string) get_post_field( 'post_content', $post_id );
		$description = wp_trim_words( wp_strip_all_tags( $raw ), 28, '…' );
	}

	$description = trim( preg_replace( '/\s+/', ' ', $description ) );
	if ( $description === '' ) {
		return;
	}

	printf(
		'<meta name="description" content="%s" />' . "\n",
		esc_attr( $description )
	);
}
add_action( 'wp_head', 'vip_transits_output_meta_description', 2 );

/**
 * Output stored footer markup once when the template did not render it.
 */
function vip_transits_output_page_footer_fallback() {
	if ( is_admin() ) {
		return;
	}

	$markup = function_exists( 'vip_transits_take_footer_markup' )
		? vip_transits_take_footer_markup()
		: '';

	if ( $markup === '' ) {
		return;
	}

	echo $markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
