<?php
/**
 * Mitigate media upload failures on WordPress Studio (Playground / PHP WASM).
 *
 * @package VIP_Transits
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Skip extra thumbnails — image processing often fails in Studio WASM.
 *
 * @param array $sizes    Sizes.
 * @param array $metadata Metadata.
 * @return array
 */
function vip_transits_skip_intermediate_sizes( $sizes, $metadata ) {
	unset( $metadata );
	return array();
}
add_filter( 'intermediate_image_sizes_advanced', 'vip_transits_skip_intermediate_sizes', 10, 2 );

/**
 * If thumbnail generation fails, still save basic attachment metadata.
 *
 * @param array|false $metadata      Metadata.
 * @param int         $attachment_id Attachment ID.
 * @return array
 */
function vip_transits_safe_attachment_metadata( $metadata, $attachment_id ) {
	if ( is_array( $metadata ) && ! empty( $metadata ) ) {
		return $metadata;
	}

	$file = get_attached_file( $attachment_id );
	if ( ! $file || ! file_exists( $file ) ) {
		return array();
	}

	$size = @getimagesize( $file );
	$data = array(
		'file' => _wp_relative_upload_path( $file ),
	);

	if ( is_array( $size ) ) {
		$data['width']  = (int) $size[0];
		$data['height'] = (int) $size[1];
	}

	return $data;
}
add_filter( 'wp_generate_attachment_metadata', 'vip_transits_safe_attachment_metadata', 99, 2 );

/**
 * Log upload errors when debug logging is enabled.
 *
 * @param array $upload Upload data.
 * @return array
 */
function vip_transits_log_upload_errors( $upload ) {
	if ( ! empty( $upload['error'] ) && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( 'VIP upload error: ' . $upload['error'] );
	}
	return $upload;
}
add_filter( 'wp_handle_upload', 'vip_transits_log_upload_errors' );
