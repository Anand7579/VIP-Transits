<?php
/**
 * Allow SVG uploads in the Media Library (admin) with basic sanitization.
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register SVG mime types for uploads.
 *
 * @param array $mimes Allowed mime types.
 * @return array
 */
function vip_transits_allow_svg_mime_types( $mimes ) {
	$mimes['svg']  = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';
	return $mimes;
}
add_filter( 'upload_mimes', 'vip_transits_allow_svg_mime_types' );

/**
 * Fix filetype detection for .svg (WordPress can reject valid SVGs).
 *
 * @param array  $data     File data.
 * @param string $file     Full path.
 * @param string $filename File name.
 * @param array  $mimes    Mime types.
 * @return array
 */
function vip_transits_fix_svg_filetype( $data, $file, $filename, $mimes ) {
	if ( ! empty( $data['ext'] ) && ! empty( $data['type'] ) ) {
		return $data;
	}

	$ext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
	if ( 'svg' === $ext || 'svgz' === $ext ) {
		$data['ext']  = $ext;
		$data['type'] = 'image/svg+xml';
	}

	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'vip_transits_fix_svg_filetype', 10, 4 );

/**
 * Whether an attachment is SVG.
 *
 * @param int $attachment_id Attachment ID.
 * @return bool
 */
function vip_transits_attachment_is_svg( $attachment_id ) {
	return 'image/svg+xml' === get_post_mime_type( (int) $attachment_id );
}

/**
 * Strip risky content from SVG before it is stored.
 *
 * @param string $svg Raw SVG markup.
 * @return string
 */
function vip_transits_sanitize_svg_markup( $svg ) {
	$svg = preg_replace( '/<script\b[^>]*>[\s\S]*?<\/script>/mi', '', $svg );
	$svg = preg_replace( '/<foreignObject\b[^>]*>[\s\S]*?<\/foreignObject>/mi', '', $svg );
	$svg = preg_replace( '/\s(on\w+|xmlns:xlink)\s*=\s*("|\').*?\2/i', '', $svg );
	$svg = preg_replace( '/javascript:/i', '', $svg );

	return $svg;
}

/**
 * Sanitize SVG files on upload (admins / users with upload_files only).
 *
 * @param array $file Upload file data.
 * @return array
 */
function vip_transits_sanitize_svg_on_upload( $file ) {
	if ( empty( $file['type'] ) || 'image/svg+xml' !== $file['type'] ) {
		return $file;
	}

	if ( ! current_user_can( 'upload_files' ) ) {
		$file['error'] = __( 'You are not allowed to upload SVG files.', 'tenku-child' );
		return $file;
	}

	if ( empty( $file['tmp_name'] ) || ! is_readable( $file['tmp_name'] ) ) {
		return $file;
	}

	$contents = file_get_contents( $file['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( false === $contents || '' === $contents ) {
		$file['error'] = __( 'The SVG file could not be read.', 'tenku-child' );
		return $file;
	}

	if ( false === stripos( $contents, '<svg' ) ) {
		$file['error'] = __( 'This file does not look like a valid SVG.', 'tenku-child' );
		return $file;
	}

	$sanitized = vip_transits_sanitize_svg_markup( $contents );
	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	file_put_contents( $file['tmp_name'], $sanitized );

	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'vip_transits_sanitize_svg_on_upload', 15 );

/**
 * Show SVG preview in the Media Library grid/modal.
 *
 * @param array   $response   Attachment JS data.
 * @param WP_Post $attachment Attachment post.
 * @return array
 */
function vip_transits_svg_attachment_for_js( $response, $attachment ) {
	if ( ! vip_transits_attachment_is_svg( $attachment->ID ) ) {
		return $response;
	}

	$url = wp_get_attachment_url( $attachment->ID );
	if ( ! $url ) {
		return $response;
	}

	$response['type']    = 'image';
	$response['subtype'] = 'svg+xml';
	$response['icon']    = $url;
	$response['url']     = $url;
	$response['sizes']   = array(
		'full'      => array(
			'url'         => $url,
			'width'       => 300,
			'height'      => 300,
			'orientation' => 'portrait',
		),
		'thumbnail' => array(
			'url'         => $url,
			'width'       => 150,
			'height'      => 150,
			'orientation' => 'portrait',
		),
	);
	$response['image']   = array(
		'src'    => $url,
		'width'  => 300,
		'height' => 300,
	);

	return $response;
}
add_filter( 'wp_prepare_attachment_for_js', 'vip_transits_svg_attachment_for_js', 10, 2 );

/**
 * Output a plain <img> for SVG attachments (wp_get_attachment_image has no sizes).
 *
 * @param string $html          Default HTML.
 * @param int    $attachment_id Attachment ID.
 * @param string $size          Requested size (ignored for SVG).
 * @param bool   $icon          Icon flag.
 * @param array  $attr          Attributes.
 * @return string
 */
function vip_transits_svg_attachment_image_html( $html, $attachment_id, $size, $icon, $attr ) {
	if ( $icon || ! vip_transits_attachment_is_svg( $attachment_id ) ) {
		return $html;
	}

	$url = wp_get_attachment_url( $attachment_id );
	if ( ! $url ) {
		return $html;
	}

	$attr = wp_parse_args(
		$attr,
		array(
			'class'    => 'attachment-svg',
			'alt'      => trim( (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ),
			'loading'  => 'lazy',
			'decoding' => 'async',
		)
	);

	$attr['src'] = $url;
	unset( $attr['srcset'], $attr['sizes'] );

	$html_attrs = '';
	foreach ( $attr as $name => $value ) {
		if ( '' === $value && 'alt' !== $name ) {
			continue;
		}
		$html_attrs .= sprintf( ' %s="%s"', esc_attr( $name ), esc_attr( (string) $value ) );
	}

	return '<img' . $html_attrs . ' />';
}
add_filter( 'wp_get_attachment_image', 'vip_transits_svg_attachment_image_html', 10, 5 );
