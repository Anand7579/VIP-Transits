<?php
/**
 * WhatsApp enquiry tracking (CPT + AJAX).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'VIP_TRANSITS_WA_TRACKING_OPTION', 'vip_transits_whatsapp_tracking_enabled' );
define( 'VIP_TRANSITS_WA_REF_OPTION', 'vip_transits_whatsapp_append_reference' );

/**
 * Register enquiry post type.
 */
function vip_transits_register_enquiry_cpt() {
	register_post_type(
		'vip_enquiry',
		array(
			'labels'              => array(
				'name'               => __( 'Enquiries', 'tenku-child' ),
				'singular_name'      => __( 'Enquiry', 'tenku-child' ),
				'add_new'            => __( 'Add enquiry', 'tenku-child' ),
				'add_new_item'       => __( 'Add new enquiry', 'tenku-child' ),
				'edit_item'          => __( 'Edit enquiry', 'tenku-child' ),
				'view_item'          => __( 'View enquiry', 'tenku-child' ),
				'search_items'       => __( 'Search enquiries', 'tenku-child' ),
				'not_found'          => __( 'No enquiries found.', 'tenku-child' ),
				'not_found_in_trash' => __( 'No enquiries found in Trash.', 'tenku-child' ),
				'all_items'          => __( 'All enquiries', 'tenku-child' ),
				'menu_name'          => __( 'Enquiries', 'tenku-child' ),
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_icon'           => 'dashicons-whatsapp',
			'menu_position'       => 26,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'hierarchical'        => false,
			'supports'            => array( 'title' ),
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
			'show_in_rest'        => false,
		)
	);
}
add_action( 'init', 'vip_transits_register_enquiry_cpt' );

/**
 * @return bool
 */
function vip_transits_whatsapp_tracking_enabled() {
	return get_option( VIP_TRANSITS_WA_TRACKING_OPTION, '1' ) === '1';
}

/**
 * @return bool
 */
function vip_transits_whatsapp_append_reference_enabled() {
	return get_option( VIP_TRANSITS_WA_REF_OPTION, '1' ) === '1';
}

/**
 * Register tracking settings (hooked from whatsapp-settings display section).
 */
function vip_transits_register_enquiry_settings() {
	register_setting(
		'vip_transits_settings',
		VIP_TRANSITS_WA_TRACKING_OPTION,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'vip_transits_sanitize_checkbox_option',
			'default'           => '1',
		)
	);

	register_setting(
		'vip_transits_settings',
		VIP_TRANSITS_WA_REF_OPTION,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'vip_transits_sanitize_checkbox_option',
			'default'           => '1',
		)
	);

	add_settings_field(
		VIP_TRANSITS_WA_TRACKING_OPTION,
		__( 'Track WhatsApp enquiries', 'tenku-child' ),
		'vip_transits_whatsapp_tracking_field_cb',
		'vip-transits-settings',
		'vip_transits_whatsapp_section'
	);

	add_settings_field(
		VIP_TRANSITS_WA_REF_OPTION,
		__( 'Append reference ID to WhatsApp message', 'tenku-child' ),
		'vip_transits_whatsapp_reference_field_cb',
		'vip-transits-settings',
		'vip_transits_whatsapp_section'
	);
}
add_action( 'admin_init', 'vip_transits_register_enquiry_settings', 20 );

/**
 * @param mixed $value Raw option.
 * @return string
 */
function vip_transits_sanitize_checkbox_option( $value ) {
	return ! empty( $value ) ? '1' : '0';
}

/**
 * Track enquiries field.
 */
function vip_transits_whatsapp_tracking_field_cb() {
	$checked = vip_transits_whatsapp_tracking_enabled() ? 'checked' : '';
	?>
	<label>
		<input type="checkbox" name="<?php echo esc_attr( VIP_TRANSITS_WA_TRACKING_OPTION ); ?>" value="1" <?php echo esc_attr( $checked ); ?> />
		<?php esc_html_e( 'Save a record in Enquiries when someone clicks a WhatsApp booking button.', 'tenku-child' ); ?>
	</label>
	<?php
}

/**
 * Reference ID field.
 */
function vip_transits_whatsapp_reference_field_cb() {
	$checked = vip_transits_whatsapp_append_reference_enabled() ? 'checked' : '';
	?>
	<label>
		<input type="checkbox" name="<?php echo esc_attr( VIP_TRANSITS_WA_REF_OPTION ); ?>" value="1" <?php echo esc_attr( $checked ); ?> />
		<?php esc_html_e( 'Add a unique reference (e.g. VIP-20260603-A1B2) to the WhatsApp message so chats can be matched to admin records.', 'tenku-child' ); ?>
	</label>
	<?php
}

/**
 * @return string
 */
function vip_transits_generate_enquiry_reference() {
	return 'VIP-' . gmdate( 'Ymd' ) . '-' . strtoupper( wp_generate_password( 4, false, false ) );
}

/**
 * @param array<string, mixed> $data Enquiry payload.
 * @return int Post ID or 0.
 */
function vip_transits_create_enquiry_record( array $data ) {
	$vehicle_name = isset( $data['vehicle_name'] ) ? sanitize_text_field( (string) $data['vehicle_name'] ) : '';
	$reference    = isset( $data['reference'] ) ? sanitize_text_field( (string) $data['reference'] ) : vip_transits_generate_enquiry_reference();
	$source       = isset( $data['source'] ) ? sanitize_text_field( (string) $data['source'] ) : '';
	$page_url     = isset( $data['page_url'] ) ? esc_url_raw( (string) $data['page_url'] ) : '';
	$vehicle_url  = isset( $data['vehicle_url'] ) ? esc_url_raw( (string) $data['vehicle_url'] ) : '';
	$message      = isset( $data['message'] ) ? sanitize_textarea_field( (string) $data['message'] ) : '';
	$vehicle_id   = isset( $data['vehicle_id'] ) ? max( 0, (int) $data['vehicle_id'] ) : 0;

	$title_bits = array_filter(
		array(
			$vehicle_name !== '' ? $vehicle_name : __( 'General enquiry', 'tenku-child' ),
			wp_date( 'Y-m-d H:i' ),
		)
	);

	$post_id = wp_insert_post(
		array(
			'post_type'   => 'vip_enquiry',
			'post_status' => 'publish',
			'post_title'  => implode( ' — ', $title_bits ),
		),
		true
	);

	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return 0;
	}

	update_post_meta( $post_id, 'enquiry_reference', $reference );
	update_post_meta( $post_id, 'enquiry_vehicle_id', $vehicle_id );
	update_post_meta( $post_id, 'enquiry_vehicle_name', $vehicle_name );
	update_post_meta( $post_id, 'enquiry_vehicle_url', $vehicle_url );
	update_post_meta( $post_id, 'enquiry_message', $message );
	update_post_meta( $post_id, 'enquiry_source', $source );
	update_post_meta( $post_id, 'enquiry_page_url', $page_url );
	update_post_meta( $post_id, 'enquiry_created_at', current_time( 'mysql' ) );

	return (int) $post_id;
}

/**
 * AJAX: create enquiry before WhatsApp redirect.
 */
function vip_transits_ajax_track_whatsapp_enquiry() {
	check_ajax_referer( 'vip_whatsapp_enquiry', 'nonce' );

	if ( ! vip_transits_whatsapp_tracking_enabled() ) {
		wp_send_json_error( array( 'message' => 'disabled' ), 403 );
	}

	$vehicle_id = max( 0, (int) ( $_POST['vehicle_id'] ?? 0 ) );
	$data       = array(
		'vehicle_id'   => $vehicle_id,
		'vehicle_name' => sanitize_text_field( wp_unslash( (string) ( $_POST['vehicle_name'] ?? '' ) ) ),
		'vehicle_url'  => esc_url_raw( wp_unslash( (string) ( $_POST['vehicle_url'] ?? '' ) ) ),
		'message'      => sanitize_textarea_field( wp_unslash( (string) ( $_POST['message'] ?? '' ) ) ),
		'source'       => sanitize_text_field( wp_unslash( (string) ( $_POST['source'] ?? '' ) ) ),
		'page_url'     => esc_url_raw( wp_unslash( (string) ( $_POST['page_url'] ?? '' ) ) ),
	);

	if ( $vehicle_id > 0 && $data['vehicle_name'] === '' ) {
		$data['vehicle_name'] = get_the_title( $vehicle_id );
	}
	if ( $vehicle_id > 0 && $data['vehicle_url'] === '' ) {
		$data['vehicle_url'] = get_permalink( $vehicle_id );
	}

	$reference = vip_transits_generate_enquiry_reference();
	$data['reference'] = $reference;

	$post_id = vip_transits_create_enquiry_record( $data );
	if ( $post_id <= 0 ) {
		wp_send_json_error( array( 'message' => 'save_failed' ), 500 );
	}

	$append_ref = vip_transits_whatsapp_append_reference_enabled();
	$wa_url     = '';

	if ( ! empty( $_POST['wa_url'] ) ) {
		$wa_url = esc_url_raw( wp_unslash( (string) $_POST['wa_url'] ) );
	}

	if ( $append_ref && $wa_url !== '' ) {
		$wa_url = vip_transits_append_reference_to_whatsapp_url( $wa_url, $reference );
	}

	wp_send_json_success(
		array(
			'id'        => $post_id,
			'reference' => $reference,
			'wa_url'    => $wa_url,
			'append_ref'=> $append_ref,
		)
	);
}
add_action( 'wp_ajax_vip_track_whatsapp_enquiry', 'vip_transits_ajax_track_whatsapp_enquiry' );
add_action( 'wp_ajax_nopriv_vip_track_whatsapp_enquiry', 'vip_transits_ajax_track_whatsapp_enquiry' );

/**
 * Append reference line to a WhatsApp click-to-chat URL.
 *
 * @param string $url       WhatsApp URL.
 * @param string $reference Reference ID.
 * @return string
 */
function vip_transits_append_reference_to_whatsapp_url( $url, $reference ) {
	$url       = (string) $url;
	$reference = trim( (string) $reference );
	if ( $url === '' || $reference === '' ) {
		return $url;
	}

	$line = 'Ref: ' . $reference;

	if ( preg_match( '/[?&]text=([^&]+)/i', $url, $match ) ) {
		$decoded = rawurldecode( $match[1] );
		$decoded = rtrim( $decoded ) . "\n\n" . $line;
		return preg_replace(
			'/([?&]text=)([^&]+)/i',
			'$1' . rawurlencode( $decoded ),
			$url,
			1
		);
	}

	$sep = str_contains( $url, '?' ) ? '&' : '?';
	return $url . $sep . 'text=' . rawurlencode( $line );
}

/**
 * Enqueue front-end tracker.
 */
function vip_transits_enqueue_whatsapp_enquiry_tracker() {
	if ( is_admin() || ! vip_transits_whatsapp_tracking_enabled() ) {
		return;
	}

	if ( ! function_exists( 'vip_transits_get_whatsapp_number' ) || vip_transits_get_whatsapp_number() === '' ) {
		return;
	}

	$path = get_stylesheet_directory() . '/assets/js/whatsapp-enquiry-track.js';
	if ( ! file_exists( $path ) ) {
		return;
	}

	wp_enqueue_script(
		'vip-whatsapp-enquiry-track',
		get_stylesheet_directory_uri() . '/assets/js/whatsapp-enquiry-track.js',
		array(),
		(string) filemtime( $path ),
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);

	wp_localize_script(
		'vip-whatsapp-enquiry-track',
		'vipWhatsAppTrack',
		array(
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
			'nonce'     => wp_create_nonce( 'vip_whatsapp_enquiry' ),
			'enabled'   => true,
			'appendRef' => vip_transits_whatsapp_append_reference_enabled(),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'vip_transits_enqueue_whatsapp_enquiry_tracker', 25 );

/**
 * Admin list columns.
 *
 * @param array $columns Columns.
 * @return array
 */
function vip_transits_enquiry_admin_columns( $columns ) {
	$new = array();
	foreach ( $columns as $key => $label ) {
		$new[ $key ] = $label;
		if ( 'title' === $key ) {
			$new['enquiry_reference'] = __( 'Reference', 'tenku-child' );
			$new['enquiry_vehicle']   = __( 'Vehicle', 'tenku-child' );
			$new['enquiry_source']    = __( 'Source', 'tenku-child' );
			$new['enquiry_date']      = __( 'Date', 'tenku-child' );
		}
	}
	return $new;
}
add_filter( 'manage_vip_enquiry_posts_columns', 'vip_transits_enquiry_admin_columns' );

/**
 * @param string $column  Column key.
 * @param int    $post_id Post ID.
 */
function vip_transits_enquiry_admin_column_content( $column, $post_id ) {
	switch ( $column ) {
		case 'enquiry_reference':
			echo esc_html( (string) get_post_meta( $post_id, 'enquiry_reference', true ) );
			break;
		case 'enquiry_vehicle':
			$name = (string) get_post_meta( $post_id, 'enquiry_vehicle_name', true );
			$url  = (string) get_post_meta( $post_id, 'enquiry_vehicle_url', true );
			if ( $url ) {
				printf( '<a href="%1$s">%2$s</a>', esc_url( $url ), esc_html( $name ? $name : $url ) );
			} else {
				echo esc_html( $name ? $name : '—' );
			}
			break;
		case 'enquiry_source':
			echo esc_html( (string) get_post_meta( $post_id, 'enquiry_source', true ) );
			break;
		case 'enquiry_date':
			echo esc_html( (string) get_post_meta( $post_id, 'enquiry_created_at', true ) );
			break;
	}
}
add_action( 'manage_vip_enquiry_posts_custom_column', 'vip_transits_enquiry_admin_column_content', 10, 2 );

/**
 * Enquiry meta box in admin.
 */
function vip_transits_enquiry_meta_boxes() {
	add_meta_box(
		'vip-enquiry-details',
		__( 'Enquiry details', 'tenku-child' ),
		'vip_transits_render_enquiry_meta_box',
		'vip_enquiry',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'vip_transits_enquiry_meta_boxes' );

/**
 * @param WP_Post $post Post.
 */
function vip_transits_render_enquiry_meta_box( $post ) {
	$fields = array(
		'enquiry_reference'    => __( 'Reference', 'tenku-child' ),
		'enquiry_vehicle_name' => __( 'Vehicle name', 'tenku-child' ),
		'enquiry_vehicle_url'  => __( 'Vehicle URL', 'tenku-child' ),
		'enquiry_message'      => __( 'WhatsApp message', 'tenku-child' ),
		'enquiry_source'       => __( 'Source', 'tenku-child' ),
		'enquiry_page_url'     => __( 'Page URL', 'tenku-child' ),
		'enquiry_created_at'   => __( 'Date & time', 'tenku-child' ),
	);

	echo '<table class="form-table"><tbody>';
	foreach ( $fields as $key => $label ) {
		$value = (string) get_post_meta( $post->ID, $key, true );
		echo '<tr><th scope="row">' . esc_html( $label ) . '</th><td>';
		if ( str_contains( $key, '_url' ) && $value ) {
			printf( '<a href="%1$s">%2$s</a>', esc_url( $value ), esc_html( $value ) );
		} elseif ( 'enquiry_message' === $key ) {
			echo '<textarea readonly rows="6" class="large-text">' . esc_textarea( $value ) . '</textarea>';
		} else {
			echo esc_html( $value ? $value : '—' );
		}
		echo '</td></tr>';
	}
	echo '</tbody></table>';
}
