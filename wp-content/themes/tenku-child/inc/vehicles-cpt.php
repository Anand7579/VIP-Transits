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
		'vehicle_category',
		'vip_vehicle',
		array(
			'labels'            => array(
				'name'          => __( 'Categories', 'tenku-child' ),
				'singular_name' => __( 'Category', 'tenku-child' ),
				'add_new_item'  => __( 'Add category', 'tenku-child' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'vehicle-category' ),
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
	add_image_size( 'vip_vehicle_hero', 960, 540, true );
	add_image_size( 'vip_vehicle_gallery', 960, 540, false );
	add_image_size( 'vip_vehicle_gallery_thumb', 280, 160, true );
}

/**
 * Gallery images for single vehicle (featured first, then ACF gallery).
 *
 * @param int $post_id Post ID.
 * @return array<int, array{id:int,url:string,thumb:string,alt:string}>
 */
function vip_transits_vehicle_gallery_images( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$images  = array();
	$seen    = array();

	$push = static function ( array &$list, array &$seen_urls, $url, $thumb, $alt, $id = 0 ) {
		$url = (string) $url;
		if ( $url === '' || isset( $seen_urls[ $url ] ) ) {
			return;
		}
		$seen_urls[ $url ] = true;
		$list[]            = array(
			'id'    => (int) $id,
			'url'   => $url,
			'thumb' => $thumb ? (string) $thumb : $url,
			'alt'   => (string) $alt,
		);
	};

	$thumb_id = (int) get_post_thumbnail_id( $post_id );
	if ( $thumb_id ) {
		$push(
			$images,
			$seen,
			get_the_post_thumbnail_url( $post_id, 'vip_vehicle_gallery' ) ?: get_the_post_thumbnail_url( $post_id, 'large' ),
			get_the_post_thumbnail_url( $post_id, 'vip_vehicle_gallery_thumb' ) ?: get_the_post_thumbnail_url( $post_id, 'medium' ),
			get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) ?: get_the_title( $post_id ),
			$thumb_id
		);
	}

	if ( function_exists( 'get_field' ) ) {
		$gallery = get_field( 'gallery_images', $post_id );
		if ( is_array( $gallery ) ) {
			foreach ( $gallery as $img ) {
				if ( ! is_array( $img ) || empty( $img['url'] ) ) {
					continue;
				}
				$id = ! empty( $img['ID'] ) ? (int) $img['ID'] : 0;
				if ( $id && $id === $thumb_id ) {
					continue;
				}
				$push(
					$images,
					$seen,
					$img['url'],
					$id ? wp_get_attachment_image_url( $id, 'vip_vehicle_gallery_thumb' ) : $img['url'],
					! empty( $img['alt'] ) ? (string) $img['alt'] : get_the_title( $post_id ),
					$id
				);
			}
		}
	}

	return $images;
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
 * Default vehicle category terms (homepage category row + fleet filter).
 */
function vip_transits_register_default_category_terms() {
	if ( ! taxonomy_exists( 'vehicle_category' ) ) {
		return;
	}

	$defaults = array(
		'sports'      => __( 'Sports', 'tenku-child' ),
		'convertible' => __( 'Convertible', 'tenku-child' ),
		'luxury'      => __( 'Luxury', 'tenku-child' ),
		'suv'         => __( 'SUV', 'tenku-child' ),
	);

	foreach ( $defaults as $slug => $name ) {
		if ( ! term_exists( $slug, 'vehicle_category' ) ) {
			wp_insert_term( $name, 'vehicle_category', array( 'slug' => $slug ) );
		}
	}
}
add_action( 'init', 'vip_transits_register_default_category_terms', 12 );

/**
 * Slug used for fleet filtering from a homepage category card.
 *
 * @param string $title      Display title (e.g. SPORTS).
 * @param string $filter_slug Optional explicit slug from ACF.
 * @return string
 */
function vip_transits_category_filter_slug( $title, $filter_slug = '' ) {
	$filter_slug = sanitize_title( (string) $filter_slug );
	if ( $filter_slug ) {
		return $filter_slug;
	}

	return sanitize_title( (string) $title );
}

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
 * Render fleet grid template (WP core does not pass get_template_part $args into templates).
 *
 * @param array $args Keys: query (WP_Query), per_page, show_load_more, show_filters.
 */
function vip_transits_render_fleet_grid( array $args ) {
	set_query_var( 'vip_fleet_grid', $args );
	get_template_part( 'template-parts/vehicle/fleet', 'grid' );
}

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
	$brands     = wp_get_post_terms( $post_id, 'vehicle_brand', array( 'fields' => 'slugs' ) );
	$seats      = wp_get_post_terms( $post_id, 'vehicle_seat', array( 'fields' => 'slugs' ) );
	$categories = wp_get_post_terms( $post_id, 'vehicle_category', array( 'fields' => 'slugs' ) );

	if ( is_wp_error( $brands ) ) {
		$brands = array();
	}
	if ( is_wp_error( $seats ) ) {
		$seats = array();
	}
	if ( is_wp_error( $categories ) ) {
		$categories = array();
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
		'phone'        => (string) get_field( 'phone_number', $post_id ),
		'delivery'     => (bool) get_field( 'delivery_hotel_home', $post_id, false ),
		'brands'       => $brands,
		'seat_terms'   => $seats,
		'categories'   => $categories,
	);
}

/**
 * Short display name (strip trailing "Rental Dubai" etc.).
 *
 * @param string $title Post title.
 * @return string
 */
function vip_transits_vehicle_short_name( $title ) {
	$title = trim( (string) $title );
	$title = preg_replace( '/\s+rental\s+dubai\s*$/i', '', $title );
	return $title ? $title : __( 'Vehicle', 'tenku-child' );
}

/**
 * Primary brand term for a vehicle.
 *
 * @param int $post_id Post ID.
 * @return WP_Term|null
 */
function vip_transits_vehicle_primary_brand( $post_id ) {
	$terms = wp_get_post_terms( $post_id, 'vehicle_brand' );
	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return null;
	}
	return $terms[0];
}

/**
 * Repeater rows from ACF as a simple list.
 *
 * @param string $field   Field name.
 * @param int    $post_id Post ID.
 * @return array<int, array<string, string>>
 */
function vip_transits_vehicle_acf_rows( $field, $post_id, $sub_keys = array() ) {
	$rows = array();
	if ( ! function_exists( 'have_rows' ) || ! have_rows( $field, $post_id ) ) {
		return $rows;
	}
	while ( have_rows( $field, $post_id ) ) {
		the_row();
		if ( $sub_keys ) {
			$row = array();
			foreach ( $sub_keys as $key ) {
				$val = get_sub_field( $key );
				$row[ $key ] = is_string( $val ) ? $val : ( is_scalar( $val ) ? (string) $val : '' );
			}
			$rows[] = $row;
			continue;
		}
		$raw = function_exists( 'get_row' ) ? get_row( true ) : array();
		$rows[] = is_array( $raw ) ? $raw : array();
	}
	return $rows;
}

/**
 * Related vehicles (same brand).
 *
 * @param int $post_id Post ID.
 * @param int $limit   Max posts.
 * @return WP_Post[]
 */
function vip_transits_vehicle_related_posts( $post_id, $limit = 2 ) {
	$brand = vip_transits_vehicle_primary_brand( $post_id );
	if ( ! $brand ) {
		return array();
	}
	$query = new WP_Query(
		array(
			'post_type'      => 'vip_vehicle',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
			'post__not_in'   => array( (int) $post_id ),
			'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'vehicle_brand',
					'field'    => 'term_id',
					'terms'    => array( (int) $brand->term_id ),
				),
			),
		)
	);
	return $query->posts;
}

/**
 * Full single-vehicle page data (Figma car detail).
 *
 * @param int $post_id Post ID.
 * @return array
 */
function vip_transits_get_vehicle_single_data( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$card    = vip_transits_get_vehicle_card_data( $post_id );
	$brand   = vip_transits_vehicle_primary_brand( $post_id );
	$short   = vip_transits_vehicle_short_name( $card['title'] );

	$deposit = (int) get_field( 'security_deposit', $post_id );
	if ( $deposit <= 0 ) {
		$deposit = 5000;
	}

	$intro = (string) get_field( 'intro_lead', $post_id );
	if ( $intro === '' ) {
		$intro = $card['excerpt'];
	}

	$engine_label = $card['engine_type'];
	if ( $engine_label && stripos( $engine_label, 'v' ) === 0 ) {
		$engine_label = strtoupper( $engine_label );
	}

	$stats = array();
	if ( $card['acceleration'] ) {
		$stats[] = array(
			'value' => $card['acceleration'],
			'label' => __( '0–100 km/h', 'tenku-child' ),
		);
	}
	if ( (string) get_field( 'power_hp', $post_id ) ) {
		$stats[] = array(
			'value' => (string) get_field( 'power_hp', $post_id ),
			'label' => __( 'Power', 'tenku-child' ),
		);
	}
	if ( (string) get_field( 'top_speed', $post_id ) ) {
		$stats[] = array(
			'value' => (string) get_field( 'top_speed', $post_id ),
			'label' => __( 'Top Speed', 'tenku-child' ),
		);
	}
	if ( $engine_label ) {
		$stats[] = array(
			'value' => $engine_label,
			'label' => __( 'Engine Config', 'tenku-child' ),
		);
	}

	$specs = array();
	$spec_map = array(
		__( 'Engine', 'tenku-child' )       => $card['engine_type'],
		__( 'Power', 'tenku-child' )        => (string) get_field( 'power_hp', $post_id ),
		__( 'Torque', 'tenku-child' )       => (string) get_field( 'torque', $post_id ),
		__( '0–100 km/h', 'tenku-child' )   => $card['acceleration'],
		__( 'Top Speed', 'tenku-child' )     => (string) get_field( 'top_speed', $post_id ),
		__( 'Transmission', 'tenku-child' ) => (string) get_field( 'transmission', $post_id ),
		__( 'Drive', 'tenku-child' )         => (string) get_field( 'drive_type', $post_id ),
		__( 'Doors', 'tenku-child' )         => $card['doors'],
		__( 'Seats', 'tenku-child' )         => $card['seats'],
	);
	foreach ( $spec_map as $label => $value ) {
		$value = trim( (string) $value );
		if ( $value !== '' ) {
			$specs[] = array(
				'label' => $label,
				'value' => $value,
			);
		}
	}

	$included = vip_transits_vehicle_acf_rows( 'included_items', $post_id, array( 'title', 'description' ) );
	if ( empty( $included ) ) {
		$included = array(
			array(
				'title'       => __( 'Full comprehensive insurance', 'tenku-child' ),
				'description' => '',
			),
			array(
				'title'       => __( 'Free delivery to any Dubai address', 'tenku-child' ),
				'description' => '',
			),
		);
	}

	$variants = vip_transits_vehicle_acf_rows( 'vehicle_variants', $post_id, array( 'name', 'note' ) );
	$routes   = vip_transits_vehicle_acf_rows( 'driving_routes', $post_id, array( 'title', 'description' ) );
	$faq      = vip_transits_vehicle_acf_rows( 'vehicle_faq', $post_id, array( 'question', 'answer' ) );

	if ( empty( $routes ) ) {
		$routes = array(
			array(
				'title'       => __( 'Sheikh Zayed Road', 'tenku-child' ),
				'description' => __( '10-lane highway, perfect for acceleration runs', 'tenku-child' ),
			),
			array(
				'title'       => __( 'Palm Jumeirah', 'tenku-child' ),
				'description' => __( 'The crescent road with sea views on both sides', 'tenku-child' ),
			),
			array(
				'title'       => __( 'Dubai Marina Walk', 'tenku-child' ),
				'description' => __( 'Low-speed cruising at its most glamorous', 'tenku-child' ),
			),
		);
	}

	$weekly = (string) get_field( 'weekly_rate_label', $post_id );
	if ( $weekly === '' ) {
		$weekly = __( 'Ask on WhatsApp', 'tenku-child' );
	}

	$display_title = $card['title'];
	if ( stripos( $display_title, 'rental' ) === false ) {
		$display_title = sprintf(
			/* translators: %s: vehicle name */
			__( '%s Rental Dubai', 'tenku-child' ),
			$short
		);
	}

	$wa_href_attr = function_exists( 'vip_transits_vehicle_whatsapp_href_attr' )
		? vip_transits_vehicle_whatsapp_href_attr( $post_id )
		: '';

	$phone = $card['phone'];
	if ( $phone === '' && function_exists( 'vip_transits_get_whatsapp_number' ) ) {
		$phone = vip_transits_get_whatsapp_number();
	}

	return array_merge(
		$card,
		array(
			'display_title'    => $display_title,
			'short_name'       => $short,
			'brand_name'       => $brand ? $brand->name : '',
			'hero_image'       => get_the_post_thumbnail_url( $post_id, 'vip_vehicle_hero' ) ?: get_the_post_thumbnail_url( $post_id, 'large' ),
			'gallery'          => vip_transits_vehicle_gallery_images( $post_id ),
			'intro'            => $intro,
			'security_deposit' => $deposit,
			'weekly_rate'      => $weekly,
			'minimum_age'      => (string) get_field( 'minimum_age', $post_id ) ?: '25',
			'stats'            => $stats,
			'specs'            => $specs,
			'included'         => $included,
			'variants'         => $variants,
			'routes'           => $routes,
			'faq'              => $faq,
			'seo_content'      => (string) get_field( 'seo_content', $post_id ),
			'transmission'     => (string) get_field( 'transmission', $post_id ),
			'wa_href_attr'     => $wa_href_attr,
			'phone_display'    => $phone,
			'tel_href'         => $phone ? 'tel:' . preg_replace( '/[^\d+]/', '', $phone ) : '',
			'related'          => vip_transits_vehicle_related_posts( $post_id, 2 ),
			'booking_steps'    => array(
				array(
					'title' => __( 'Check availability', 'tenku-child' ),
					'text'  => sprintf(
						/* translators: %s: vehicle short name */
						__( 'WhatsApp us with your preferred %s variant, rental dates, and Dubai delivery address.', 'tenku-child' ),
						$short
					),
				),
				array(
					'title' => __( 'Confirm and arrange', 'tenku-child' ),
					'text'  => __( 'We confirm availability within 15 minutes and provide you with pricing, deposit details, and delivery time.', 'tenku-child' ),
				),
				array(
					'title' => __( 'We deliver to you', 'tenku-child' ),
					'text'  => __( 'Your car arrives at your chosen address. Security deposit is held, keys handed over. Drive away.', 'tenku-child' ),
				),
			),
		)
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
