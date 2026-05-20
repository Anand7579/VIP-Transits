<?php
/**
 * Luxury vehicle CPT, taxonomies, assets, and load-more AJAX.
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register vehicle post type and taxonomies.
 */
function vip_transits_register_vehicle_cpt() {
	register_post_type(
		'vip_vehicle',
		array(
			'labels'              => array(
				'name'               => __( 'Vehicles', 'tenku-child' ),
				'singular_name'      => __( 'Vehicle', 'tenku-child' ),
				'add_new'            => __( 'Add vehicle', 'tenku-child' ),
				'add_new_item'       => __( 'Add new vehicle', 'tenku-child' ),
				'edit_item'          => __( 'Edit vehicle', 'tenku-child' ),
				'new_item'           => __( 'New vehicle', 'tenku-child' ),
				'view_item'          => __( 'View vehicle', 'tenku-child' ),
				'search_items'       => __( 'Search vehicles', 'tenku-child' ),
				'not_found'          => __( 'No vehicles found.', 'tenku-child' ),
				'not_found_in_trash' => __( 'No vehicles found in Trash.', 'tenku-child' ),
				'all_items'          => __( 'All vehicles', 'tenku-child' ),
			),
			'public'              => true,
			'has_archive'         => true,
			'rewrite'             => array(
				'slug'       => 'fleet',
				'with_front' => false,
			),
			'menu_icon'           => 'dashicons-car',
			'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
			'show_in_rest'        => true,
			'exclude_from_search' => false,
		)
	);

	register_taxonomy(
		'vehicle_brand',
		'vip_vehicle',
		array(
			'labels'            => array(
				'name'          => __( 'Brands', 'tenku-child' ),
				'singular_name' => __( 'Brand', 'tenku-child' ),
				'add_new_item'  => __( 'Add brand', 'tenku-child' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'vehicle-brand' ),
			'show_in_rest'      => true,
		)
	);

	register_taxonomy(
		'vehicle_seat',
		'vip_vehicle',
		array(
			'labels'            => array(
				'name'          => __( 'Seats', 'tenku-child' ),
				'singular_name' => __( 'Seat count', 'tenku-child' ),
				'add_new_item'  => __( 'Add seat option', 'tenku-child' ),
			),
			'hierarchical'      => false,
			'public'            => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'vehicle-seats' ),
			'show_in_rest'      => true,
		)
	);
}
add_action( 'init', 'vip_transits_register_vehicle_cpt' );

/**
 * Fleet card thumbnail (Figma 333×170).
 */
function vip_transits_register_fleet_image_size() {
	add_image_size( 'vip_fleet_card', 333, 170, true );
}
add_action( 'after_setup_theme', 'vip_transits_register_fleet_image_size' );

/**
 * Default seat taxonomy terms for the vehicle select field.
 */
function vip_transits_register_default_seat_terms() {
	if ( ! taxonomy_exists( 'vehicle_seat' ) ) {
		return;
	}

	$defaults = array(
		'2-seats' => __( '2 Seats', 'tenku-child' ),
		'4-seats' => __( '4 Seats', 'tenku-child' ),
		'5-seats' => __( '5 Seats', 'tenku-child' ),
		'7-seats' => __( '7 Seats', 'tenku-child' ),
	);

	foreach ( $defaults as $slug => $name ) {
		if ( ! term_exists( $slug, 'vehicle_seat' ) ) {
			wp_insert_term( $name, 'vehicle_seat', array( 'slug' => $slug ) );
		}
	}
}
add_action( 'init', 'vip_transits_register_default_seat_terms', 12 );

/**
 * Get taxonomy terms in a fixed slug order (for fleet filters).
 *
 * @param string $taxonomy Taxonomy name.
 * @param array  $slugs    Ordered slugs.
 * @return WP_Term[]
 */
function vip_transits_get_ordered_terms( $taxonomy, $slugs ) {
	$ordered = array();

	foreach ( $slugs as $slug ) {
		$term = get_term_by( 'slug', $slug, $taxonomy );
		if ( $term instanceof WP_Term ) {
			$ordered[] = $term;
		}
	}

	return $ordered;
}

/**
 * Default brand taxonomy terms (Figma fleet filter list).
 */
function vip_transits_register_default_brand_terms() {
	if ( ! taxonomy_exists( 'vehicle_brand' ) ) {
		return;
	}

	$defaults = array(
		'lamborghini'  => __( 'Lamborghini', 'tenku-child' ),
		'ferrari'      => __( 'Ferrari', 'tenku-child' ),
		'rolls-royce'  => __( 'Rolls Royce', 'tenku-child' ),
		'mercedes-g63' => __( 'Mercedes G63', 'tenku-child' ),
		'bugatti'      => __( 'Bugatti', 'tenku-child' ),
		'porsche'      => __( 'Porsche', 'tenku-child' ),
		'bentley'      => __( 'Bentley', 'tenku-child' ),
		'mclaren'      => __( 'McLaren', 'tenku-child' ),
	);

	foreach ( $defaults as $slug => $name ) {
		if ( ! term_exists( $slug, 'vehicle_brand' ) ) {
			wp_insert_term( $name, 'vehicle_brand', array( 'slug' => $slug ) );
		}
	}
}
add_action( 'init', 'vip_transits_register_default_brand_terms', 12 );

/**
 * Seats are chosen via ACF; hide the duplicate taxonomy metabox on the vehicle edit screen.
 */
function vip_transits_hide_vehicle_seat_metabox() {
	remove_meta_box( 'tagsdiv-vehicle_seat', 'vip_vehicle', 'side' );
}
add_action( 'add_meta_boxes', 'vip_transits_hide_vehicle_seat_metabox', 20 );

/**
 * Card description (excerpt or Figma default copy).
 *
 * @param int $post_id Post ID.
 * @return string
 */
function vip_transits_vehicle_card_excerpt( $post_id ) {
	if ( has_excerpt( $post_id ) ) {
		return get_the_excerpt( $post_id );
	}

	$content = trim( wp_strip_all_tags( (string) get_post_field( 'post_content', $post_id ) ) );
	if ( $content ) {
		return wp_trim_words( $content, 28 );
	}

	return __( 'Message us anyway - we source any luxury car in Dubai on demand. If it exists in Dubai, we can get it.', 'tenku-child' );
}

/**
 * Display label for seats on cards (from Seats taxonomy).
 *
 * @param int $post_id Post ID.
 * @return string
 */
function vip_transits_vehicle_seats_label( $post_id ) {
	$terms = wp_get_post_terms( $post_id, 'vehicle_seat' );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		$acf_term = get_field( 'vehicle_seat', $post_id );
		if ( $acf_term instanceof WP_Term ) {
			return $acf_term->name;
		}
		return '';
	}

	$term = $terms[0];
	// Show compact "02" when term is like "2 Seats".
	if ( preg_match( '/^(\d+)/', $term->name, $m ) ) {
		return str_pad( $m[1], 2, '0', STR_PAD_LEFT );
	}

	return $term->name;
}

/**
 * Flush rewrite rules once after CPT registration.
 */
function vip_transits_vehicle_rewrite_flush() {
	if ( get_option( 'vip_vehicle_rewrite_flushed' ) ) {
		return;
	}
	flush_rewrite_rules();
	update_option( 'vip_vehicle_rewrite_flushed', 1 );
}
add_action( 'init', 'vip_transits_vehicle_rewrite_flush', 20 );

/**
 * Build vehicle query args.
 *
 * @param array $args Overrides.
 * @return array
 */
function vip_transits_vehicle_query_args( $args = array() ) {
	$defaults = array(
		'post_type'      => 'vip_vehicle',
		'post_status'    => 'publish',
		'posts_per_page' => 9,
		'paged'          => 1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	);

	return wp_parse_args( $args, $defaults );
}

/**
 * Get vehicle card data for templates / JSON.
 *
 * @param int $post_id Post ID.
 * @return array
 */
function vip_transits_get_vehicle_card_data( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$brands  = wp_get_post_terms( $post_id, 'vehicle_brand', array( 'fields' => 'slugs' ) );
	$seats   = wp_get_post_terms( $post_id, 'vehicle_seat', array( 'fields' => 'slugs' ) );

	if ( is_wp_error( $brands ) ) {
		$brands = array();
	}
	if ( is_wp_error( $seats ) ) {
		$seats = array();
	}

	$price = (int) get_field( 'daily_price', $post_id );

	return array(
		'id'           => $post_id,
		'title'        => get_the_title( $post_id ),
		'permalink'    => get_permalink( $post_id ),
		'excerpt'      => vip_transits_vehicle_card_excerpt( $post_id ),
		'thumbnail'    => get_the_post_thumbnail_url( $post_id, 'vip_fleet_card' ) ?: get_the_post_thumbnail_url( $post_id, 'large' ),
		'color_name'   => (string) get_field( 'color_name', $post_id ),
		'color_hex'    => (string) get_field( 'color_hex', $post_id ),
		'engine_type'  => (string) get_field( 'engine_type', $post_id ),
		'acceleration' => (string) get_field( 'acceleration_0_100', $post_id ),
		'doors'        => (string) get_field( 'doors', $post_id ),
		'seats'        => vip_transits_vehicle_seats_label( $post_id ),
		'daily_price'  => $price,
		'whatsapp'     => (string) get_field( 'whatsapp_url', $post_id ),
		'phone'        => (string) get_field( 'phone_number', $post_id ),
		'delivery'     => (bool) get_field( 'delivery_hotel_home', $post_id ),
		'brands'       => $brands,
		'seat_terms'   => $seats,
	);
}

/**
 * AJAX: load more vehicles.
 */
function vip_transits_ajax_fleet_load_more() {
	check_ajax_referer( 'vip_fleet_load_more', 'nonce' );

	$page     = max( 1, (int) ( $_POST['page'] ?? 1 ) );
	$per_page = max( 1, min( 24, (int) ( $_POST['per_page'] ?? 9 ) ) );

	$query = new WP_Query(
		vip_transits_vehicle_query_args(
			array(
				'paged'          => $page,
				'posts_per_page' => $per_page,
			)
		)
	);

	$html = '';
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			ob_start();
			get_template_part( 'template-parts/vehicle/card' );
			$html .= ob_get_clean();
		}
		wp_reset_postdata();
	}

	wp_send_json_success(
		array(
			'html'     => $html,
			'page'     => $page,
			'maxPages' => (int) $query->max_num_pages,
			'found'    => (int) $query->found_posts,
		)
	);
}
add_action( 'wp_ajax_vip_fleet_load_more', 'vip_transits_ajax_fleet_load_more' );
add_action( 'wp_ajax_nopriv_vip_fleet_load_more', 'vip_transits_ajax_fleet_load_more' );
