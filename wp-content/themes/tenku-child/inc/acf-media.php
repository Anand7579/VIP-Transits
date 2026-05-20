<?php
/**
 * Helpers for ACF image/file fields (ID, array, URL) — fixes broken URLs after Studio URL changes.
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Attachment ID from an ACF image/file field value.
 *
 * @param mixed $field ACF value.
 * @return int
 */
function vip_transits_acf_attachment_id( $field ) {
	if ( empty( $field ) ) {
		return 0;
	}

	if ( is_numeric( $field ) ) {
		return (int) $field;
	}

	if ( is_array( $field ) ) {
		if ( ! empty( $field['ID'] ) ) {
			return (int) $field['ID'];
		}
		if ( ! empty( $field['id'] ) ) {
			return (int) $field['id'];
		}
	}

	return 0;
}

/**
 * Normalize a media URL to the current site (fixes stale localhost ports / domains in saved ACF data).
 *
 * @param string $url Media URL.
 * @return string
 */
function vip_transits_normalize_media_url( $url ) {
	$url = trim( (string) $url );
	if ( $url === '' ) {
		return '';
	}

	if ( str_starts_with( $url, '//' ) ) {
		return ( is_ssl() ? 'https:' : 'http:' ) . $url;
	}

	if ( str_starts_with( $url, '/' ) ) {
		return home_url( $url );
	}

	$path = wp_parse_url( $url, PHP_URL_PATH );
	if ( is_string( $path ) && str_contains( $path, '/wp-content/uploads/' ) ) {
		return home_url( $path );
	}

	return $url;
}

/**
 * Image URL from an ACF image field.
 *
 * @param mixed  $field ACF image value.
 * @param string $size  WordPress image size.
 * @return string
 */
function vip_transits_acf_image_url( $field, $size = 'large' ) {
	$id = vip_transits_acf_attachment_id( $field );
	if ( $id ) {
		$url = wp_get_attachment_image_url( $id, $size );
		if ( $url ) {
			return vip_transits_normalize_media_url( $url );
		}
	}

	if ( is_array( $field ) && ! empty( $field['url'] ) ) {
		return vip_transits_normalize_media_url( (string) $field['url'] );
	}

	if ( is_string( $field ) && $field !== '' ) {
		return vip_transits_normalize_media_url( $field );
	}

	return '';
}

/**
 * Alt text for an ACF image field.
 *
 * @param mixed  $field    ACF image value.
 * @param string $fallback Fallback alt.
 * @return string
 */
function vip_transits_acf_image_alt( $field, $fallback = '' ) {
	$id = vip_transits_acf_attachment_id( $field );
	if ( $id ) {
		$alt = (string) get_post_meta( $id, '_wp_attachment_image_alt', true );
		if ( $alt !== '' ) {
			return $alt;
		}
	}

	if ( is_array( $field ) && ! empty( $field['alt'] ) ) {
		return (string) $field['alt'];
	}

	return (string) $fallback;
}

/**
 * File URL from an ACF file field (e.g. hero video).
 *
 * @param mixed $field ACF file value.
 * @return string
 */
function vip_transits_acf_file_url( $field ) {
	$id = vip_transits_acf_attachment_id( $field );
	if ( $id ) {
		$url = wp_get_attachment_url( $id );
		if ( $url ) {
			return vip_transits_normalize_media_url( $url );
		}
	}

	if ( is_array( $field ) && ! empty( $field['url'] ) ) {
		return vip_transits_normalize_media_url( (string) $field['url'] );
	}

	if ( is_string( $field ) && $field !== '' ) {
		return vip_transits_normalize_media_url( $field );
	}

	return '';
}

/**
 * Echo an ACF image using the attachment API when possible.
 *
 * @param mixed  $field ACF image value.
 * @param string $size  Image size.
 * @param array  $attrs Extra attributes for wp_get_attachment_image / img.
 */
function vip_transits_the_acf_image( $field, $size = 'large', $attrs = array() ) {
	$id = vip_transits_acf_attachment_id( $field );

	if ( $id ) {
		$defaults = array(
			'loading'  => 'lazy',
			'decoding' => 'async',
		);
		echo wp_get_attachment_image( $id, $size, false, array_merge( $defaults, $attrs ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		return;
	}

	$url = vip_transits_acf_image_url( $field, $size );
	if ( ! $url ) {
		return;
	}

	$class = isset( $attrs['class'] ) ? $attrs['class'] : '';
	$alt   = isset( $attrs['alt'] ) ? $attrs['alt'] : vip_transits_acf_image_alt( $field, '' );
	unset( $attrs['class'], $attrs['alt'] );

	$attr_string = '';
	foreach ( $attrs as $key => $value ) {
		if ( $value === '' || $value === null ) {
			continue;
		}
		$attr_string .= sprintf( ' %s="%s"', esc_attr( $key ), esc_attr( (string) $value ) );
	}

	printf(
		'<img src="%s" alt="%s"%s%s />',
		esc_url( $url ),
		esc_attr( $alt ),
		$class ? ' class="' . esc_attr( $class ) . '"' : '',
		$attr_string
	);
}
