<?php
/**
 * Register images from wp-content/uploads when Studio's uploader fails.
 *
 * Tools → VIP Import Media
 *
 * @package VIP_Transits
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin menu.
 */
function vip_media_import_menu() {
	add_management_page(
		__( 'VIP Import Media', 'tenku-child' ),
		__( 'VIP Import Media', 'tenku-child' ),
		'upload_files',
		'vip-media-import',
		'vip_media_import_render_page'
	);
}
add_action( 'admin_menu', 'vip_media_import_menu' );

/**
 * Register a file already on disk as a Media Library item.
 *
 * @param string $relative_path Path under uploads, e.g. 2026/05/hero.png.
 * @return int|WP_Error Attachment ID or error.
 */
function vip_media_import_from_uploads( $relative_path ) {
	$relative_path = ltrim( str_replace( '\\', '/', $relative_path ), '/' );
	$relative_path = sanitize_text_field( $relative_path );

	if ( ! $relative_path || preg_match( '#\.\.#', $relative_path ) ) {
		return new WP_Error( 'invalid_path', __( 'Invalid file path.', 'tenku-child' ) );
	}

	$upload_dir = wp_upload_dir();
	if ( ! empty( $upload_dir['error'] ) ) {
		return new WP_Error( 'upload_dir', $upload_dir['error'] );
	}

	$absolute = trailingslashit( $upload_dir['basedir'] ) . $relative_path;

	if ( ! file_exists( $absolute ) || ! is_readable( $absolute ) ) {
		return new WP_Error(
			'not_found',
			sprintf(
				/* translators: %s: full file path */
				__( 'File not found: %s', 'tenku-child' ),
				$absolute
			)
		);
	}

	$filename  = basename( $absolute );
	$filetype  = wp_check_filetype( $filename, null );
	$mime_type = $filetype['type'] ? $filetype['type'] : 'image/png';

	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => '_wp_attached_file',
					'value'   => $relative_path,
					'compare' => '=',
				),
			),
			'fields'         => 'ids',
		)
	);

	if ( ! empty( $existing[0] ) ) {
		return (int) $existing[0];
	}

	$attachment = array(
		'post_mime_type' => $mime_type,
		'post_title'     => preg_replace( '/\.[^.]+$/', '', $filename ),
		'post_content'   => '',
		'post_status'    => 'inherit',
		'guid'           => trailingslashit( $upload_dir['baseurl'] ) . $relative_path,
	);

	$attach_id = wp_insert_attachment( $attachment, $absolute );
	if ( is_wp_error( $attach_id ) ) {
		return $attach_id;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';

	$metadata = wp_generate_attachment_metadata( $attach_id, $absolute );
	if ( is_array( $metadata ) ) {
		wp_update_attachment_metadata( $attach_id, $metadata );
	}

	return (int) $attach_id;
}

/**
 * Admin page output.
 */
function vip_media_import_render_page() {
	if ( ! current_user_can( 'upload_files' ) ) {
		wp_die( esc_html__( 'You do not have permission to import media.', 'tenku-child' ) );
	}

	$message = '';
	$type    = 'info';
	$attach  = 0;

	if ( isset( $_POST['vip_media_import_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['vip_media_import_nonce'] ) ), 'vip_media_import' ) ) {
		$path   = isset( $_POST['vip_media_path'] ) ? wp_unslash( $_POST['vip_media_path'] ) : '';
		$result = vip_media_import_from_uploads( $path );

		if ( is_wp_error( $result ) ) {
			$message = $result->get_error_message();
			$type    = 'error';
		} else {
			$attach  = $result;
			$message = sprintf(
				/* translators: %d: attachment ID */
				__( 'Success. Media item #%d is in the library. Use it in ACF via "Select from library".', 'tenku-child' ),
				$attach
			);
			$type = 'success';
		}
	}

	$upload_dir = wp_upload_dir();
	$month      = gmdate( 'Y/m' );
	$example    = $month . '/group-48472.png';
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'VIP Import Media', 'tenku-child' ); ?></h1>

		<p><?php esc_html_e( 'WordPress Studio often cannot upload files through the browser. Copy your image into the uploads folder on disk, then register it here.', 'tenku-child' ); ?></p>

		<ol>
			<li>
				<?php
				printf(
					/* translators: %s: uploads folder path */
					esc_html__( 'Copy your image file into: %s', 'tenku-child' ),
					'<code>' . esc_html( wp_normalize_path( $upload_dir['basedir'] ) ) . '</code>'
				);
				?>
			</li>
			<li><?php esc_html_e( 'Use a simple name (no spaces), e.g. group-48472.png', 'tenku-child' ); ?></li>
			<li><?php esc_html_e( 'Enter the path relative to uploads below (year/month/filename).', 'tenku-child' ); ?></li>
			<li><?php esc_html_e( 'Click Import, then pick the image in ACF from the Media Library.', 'tenku-child' ); ?></li>
		</ol>

		<?php if ( $message ) : ?>
			<div class="notice notice-<?php echo esc_attr( $type ); ?> is-dismissible"><p><?php echo esc_html( $message ); ?></p></div>
			<?php if ( $attach ) : ?>
				<p>
					<a class="button" href="<?php echo esc_url( get_edit_post_link( $attach, 'raw' ) ); ?>"><?php esc_html_e( 'Edit attachment', 'tenku-child' ); ?></a>
					<a class="button" href="<?php echo esc_url( admin_url( 'upload.php' ) ); ?>"><?php esc_html_e( 'Media Library', 'tenku-child' ); ?></a>
				</p>
			<?php endif; ?>
		<?php endif; ?>

		<form method="post">
			<?php wp_nonce_field( 'vip_media_import', 'vip_media_import_nonce' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="vip_media_path"><?php esc_html_e( 'Path under uploads', 'tenku-child' ); ?></label></th>
					<td>
						<input name="vip_media_path" id="vip_media_path" type="text" class="regular-text" value="<?php echo esc_attr( $example ); ?>" placeholder="2026/05/hero.png" />
						<p class="description"><?php esc_html_e( 'Example: 2026/05/group-48472.png', 'tenku-child' ); ?></p>
					</td>
				</tr>
			</table>
			<?php submit_button( __( 'Import into Media Library', 'tenku-child' ) ); ?>
		</form>
	</div>
	<?php
}
