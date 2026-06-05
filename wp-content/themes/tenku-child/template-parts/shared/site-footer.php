<?php
/**
 * Shared site footer markup (plain HTML — safe for contact page inline output).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( function_exists( 'vip_transits_footer_static_html' ) ) {
	echo vip_transits_footer_static_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
