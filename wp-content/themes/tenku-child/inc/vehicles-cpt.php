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
		'vehicle_occasion',
		'vip_vehicle',
		array(
			'labels'            => array(
				'name'          => __( 'Occasions', 'tenku-child' ),
				'singular_name' => __( 'Occasion', 'tenku-child' ),
				'add_new_item'  => __( 'Add occasion', 'tenku-child' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'vehicle-occasion' ),
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
	
	register_taxonomy(
		'vehicle_variant',
		'vip_vehicle',
		array(
			'labels'             => array(
				'name'          => __( 'Variants', 'tenku-child' ),
				'singular_name' => __( 'Variant', 'tenku-child' ),
				'add_new_item'  => __( 'Add variant', 'tenku-child' ),
				'search_items'  => __( 'Search variants', 'tenku-child' ),
				'all_items'     => __( 'All variants', 'tenku-child' ),
			),
			'hierarchical'       => true,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_admin_column'  => true,
			'show_in_nav_menus'  => false,
			'show_in_rest'       => true,
			'rewrite'            => false,
			'query_var'          => false,
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
 * Sync vehicle_occasion taxonomy terms from published occasion pages.
 */
function vip_transits_sync_vehicle_occasion_terms() {
	if ( ! taxonomy_exists( 'vehicle_occasion' ) ) {
		return;
	}

	$occasions = get_posts(
		array(
			'post_type'              => 'vip_occasion',
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'orderby'                => 'menu_order',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	foreach ( $occasions as $occasion ) {
		$slug = (string) $occasion->post_name;
		$name = (string) $occasion->post_title;
		if ( $slug === '' || $name === '' ) {
			continue;
		}

		$existing = term_exists( $slug, 'vehicle_occasion' );
		if ( ! $existing ) {
			wp_insert_term( $name, 'vehicle_occasion', array( 'slug' => $slug ) );
			continue;
		}

		$term_id = is_array( $existing ) ? (int) $existing['term_id'] : (int) $existing;
		wp_update_term(
			$term_id,
			'vehicle_occasion',
			array(
				'name' => $name,
				'slug' => $slug,
			)
		);
	}
}
add_action( 'init', 'vip_transits_sync_vehicle_occasion_terms', 13 );

/**
 * Ordered occasion terms for fleet filters (mirrors Occasions CPT order).
 *
 * @return WP_Term[]
 */
function vip_transits_get_fleet_occasion_terms() {
	if ( ! taxonomy_exists( 'vehicle_occasion' ) ) {
		return array();
	}

	$occasions = get_posts(
		array(
			'post_type'              => 'vip_occasion',
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'orderby'                => 'menu_order',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
		)
	);

	$terms = array();
	foreach ( $occasions as $occasion ) {
		$term = get_term_by( 'slug', $occasion->post_name, 'vehicle_occasion' );
		if ( $term instanceof WP_Term ) {
			$terms[] = $term;
		}
	}

	return $terms;
}

/**
 * Default variant term (admin-only taxonomy; no front-end archive).
 */
function vip_transits_register_default_variant_terms() {
	if ( ! taxonomy_exists( 'vehicle_variant' ) ) {
		return;
	}

	if ( ! term_exists( 'variant', 'vehicle_variant' ) ) {
		wp_insert_term(
			__( 'Variant', 'tenku-child' ),
			'vehicle_variant',
			array(
				'slug' => 'variant',
			)
		);
	}
}
add_action( 'init', 'vip_transits_register_default_variant_terms', 12 );

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
 * Ordered brand slugs for fleet + hero filters.
 *
 * @return string[]
 */
function vip_transits_get_fleet_brand_order() {
	return array(
		'lamborghini',
		'ferrari',
		'rolls-royce',
		'mercedes-g63',
		'bugatti',
		'porsche',
		'bentley',
		'mclaren',
	);
}

/**
 * Brand terms for fleet sidebar and hero search.
 *
 * @return WP_Term[]
 */
function vip_transits_get_fleet_brand_terms() {
	return vip_transits_get_ordered_terms( 'vehicle_brand', vip_transits_get_fleet_brand_order() );
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
 * True when the current URL should show the vehicle fleet listing (not the blog).
 *
 * Covers CPT archive and a Page with slug "fleet" (common permalink conflict).
 *
 * @return bool
 */
function vip_transits_is_fleet_listing_view() {
	if ( is_post_type_archive( 'vip_vehicle' ) ) {
		return true;
	}

	if ( is_archive() && 'vip_vehicle' === get_query_var( 'post_type' ) ) {
		return true;
	}

	if ( is_page() ) {
		$page = get_queried_object();
		if ( $page instanceof WP_Post && in_array( $page->post_name, array( 'fleet', 'our-fleet' ), true ) ) {
			return true;
		}
	}

	// Permalink: /fleet/ when a Page and CPT both compete.
	if ( isset( $_SERVER['REQUEST_URI'] ) ) {
		$path = trim( (string) wp_parse_url( wp_unslash( $_SERVER['REQUEST_URI'] ), PHP_URL_PATH ), '/' );
		$tail = $path ? basename( $path ) : '';
		if ( 'fleet' === $tail ) {
			return true;
		}
	}

	return false;
}

/**
 * Output the fleet archive block (used when the blog block is on a fleet URL by mistake).
 */
function vip_transits_render_fleet_archive_block() {
	$template = get_stylesheet_directory() . '/blocks/vip-vehicle-archive/render.php';
	if ( is_readable( $template ) ) {
		include $template;
	}
}

/**
 * ACF options storage keys to try (default ACF options page uses "option").
 *
 * @return string[]
 */
function vip_transits_acf_option_ids() {
	$ids = array();

	// Named ACF options pages first (e.g. Theme Settings → acf-options-theme-settings).
	if ( function_exists( 'acf_get_options_pages' ) ) {
		$pages = acf_get_options_pages();
		if ( is_array( $pages ) ) {
			foreach ( $pages as $page ) {
				if ( ! empty( $page['menu_slug'] ) ) {
					$ids[] = 'acf-options-' . $page['menu_slug'];
				}
			}
		}
	}

	// Common slugs; default "option" is tried last so Theme Settings wins over stale data.
	$ids[] = 'acf-options-theme-settings';
	$ids[] = 'option';

	$ids = array_values( array_unique( array_filter( $ids ) ) );

	return apply_filters( 'vip_transits_acf_option_ids', $ids );
}

/**
 * Read one ACF field from the theme options page (first matching storage key).
 *
 * @param string $field_name ACF field name.
 * @return mixed|null Null when ACF is unavailable or the field is not set.
 */
function vip_transits_get_acf_option_field( $field_name ) {
	if ( ! function_exists( 'get_field' ) ) {
		return null;
	}

	foreach ( vip_transits_acf_option_ids() as $option_id ) {
		$value = get_field( $field_name, $option_id );
		if ( null !== $value && false !== $value && '' !== $value ) {
			return $value;
		}
	}

	// Last attempt: default "option" even when value is empty string.
	return get_field( $field_name, 'option' );
}

/**
 * Whether the fleet archive black masthead is enabled (ACF options).
 *
 * Field: feleet_archive_banner (Select: Yes / No).
 *
 * @return bool
 */
function vip_transits_fleet_archive_show_banner() {
	if ( function_exists( 'vip_transits_fleet_archive_banner_enabled' ) ) {
		return vip_transits_fleet_archive_banner_enabled();
	}

	$show = (string) vip_transits_get_acf_option_field( 'feleet_archive_banner' );
	return in_array( strtolower( $show ), array( 'yes', 'y', '1' ), true );
}

/**
 * Fleet archive masthead description from ACF options (WYSIWYG).
 *
 * Field: feet_archive_description (on the same options page as the banner toggle).
 *
 * @return string
 */
function vip_transits_get_fleet_archive_banner_description() {
	if ( function_exists( 'vip_transits_get_fleet_archive_banner_description_option' ) ) {
		return trim( vip_transits_get_fleet_archive_banner_description_option() );
	}

	$value = vip_transits_get_acf_option_field( 'feet_archive_description' );
	return trim( (string) ( null !== $value ? $value : '' ) );
}

/**
 * Fleet archive never outputs a masthead title (description-only banner).
 *
 * @return string Always empty.
 */
function vip_transits_get_fleet_archive_banner_title() {
	return '';
}

/**
 * Output fleet archive black masthead (ACF options, description only).
 *
 * Does not use template-parts/page/masthead.php because get_template_part()
 * third-argument vars are unreliable in this theme (falls back to the first
 * vehicle title from get_the_title() on the archive query).
 */
function vip_transits_render_fleet_archive_banner() {
	if ( ! vip_transits_fleet_archive_show_banner() ) {
		return;
	}

	$lead = vip_transits_get_fleet_archive_banner_description();
	if ( $lead === '' ) {
		return;
	}
	?>
	<header class="vip-bg-black-section vip-bg-black-section--masthead vip-bg-black-section--masthead-fleet-archive vip-bg-black-section--masthead-no-title">
		<div class="vip-bg-black-section__inner vip-content-container">
			<div class="vip-page__masthead-lead"><?php echo wp_kses_post( $lead ); ?></div>
		</div>
	</header>
	<?php
}

/**
 * Render fleet grid template (WP core does not pass get_template_part $args into templates).
 *
 * @param array $args Keys: query (WP_Query), per_page, show_load_more, show_filters, filter_mode (fleet|occasion).
 */
function vip_transits_render_fleet_grid( array $args ) {
	$args = wp_parse_args(
		$args,
		array(
			'filter_mode'   => 'fleet',
			'occasion_slug' => '',
		)
	);

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
		'occasion_slug'  => '',
	);

	$args = wp_parse_args( $args, $defaults );

	$occasion_slug = sanitize_title( (string) $args['occasion_slug'] );
	unset( $args['occasion_slug'] );

	if ( $occasion_slug !== '' && taxonomy_exists( 'vehicle_occasion' ) ) {
		$tax_query = isset( $args['tax_query'] ) && is_array( $args['tax_query'] ) ? $args['tax_query'] : array();
		$tax_query[] = array(
			'taxonomy' => 'vehicle_occasion',
			'field'    => 'slug',
			'terms'    => array( $occasion_slug ),
		);
		$args['tax_query'] = $tax_query;
	}

	return $args;
}

/**
 * Parsed daily price (AED) for fleet filters and cards.
 *
 * @param int $post_id Post ID.
 * @return int
 */
function vip_transits_vehicle_daily_price( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return 0;
	}

	$raw = function_exists( 'get_field' ) ? get_field( 'daily_price', $post_id ) : get_post_meta( $post_id, 'daily_price', true );
	if ( is_string( $raw ) ) {
		$raw = preg_replace( '/[^\d.]/', '', $raw );
	}

	return max( 0, (int) round( (float) $raw ) );
}

/**
 * Min/max daily price across published vehicles (for slider bounds).
 *
 * @return array{min:int,max:int}
 */
function vip_transits_get_vehicle_daily_price_range() {
	static $range = null;

	if ( null !== $range ) {
		return $range;
	}

	$range = array(
		'min' => 0,
		'max' => 0,
	);

	$ids = get_posts(
		array(
			'post_type'              => 'vip_vehicle',
			'post_status'            => 'publish',
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	foreach ( $ids as $id ) {
		$price = vip_transits_vehicle_daily_price( (int) $id );
		if ( $price <= 0 ) {
			continue;
		}
		if ( 0 === $range['min'] || $price < $range['min'] ) {
			$range['min'] = $price;
		}
		if ( $price > $range['max'] ) {
			$range['max'] = $price;
		}
	}

	return $range;
}

/**
 * Fleet price slider bounds (AED) from published vehicle daily rates.
 *
 * @return array{min:int,max:int}
 */
function vip_transits_get_fleet_price_bounds() {
	$catalog = vip_transits_get_vehicle_daily_price_range();

	if ( empty( $catalog['max'] ) || $catalog['max'] <= 0 ) {
		return array(
			'min' => 500,
			'max' => 5000,
		);
	}

	$min = max( 0, (int) ( floor( $catalog['min'] / 50 ) * 50 ) );
	$max = (int) ( ceil( $catalog['max'] / 50 ) * 50 );

	if ( $max <= $min ) {
		$max = $min + 50;
	}

	return array(
		'min' => $min,
		'max' => $max,
	);
}

/**
 * Whether a vehicle offers hotel / home delivery (ACF true_false).
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function vip_transits_vehicle_has_hotel_delivery( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return false;
	}

	$raw = get_post_meta( $post_id, 'delivery_hotel_home', true );
	if ( '' === $raw || null === $raw ) {
		$raw = function_exists( 'get_field' ) ? get_field( 'delivery_hotel_home', $post_id, false ) : '';
	}

	if ( is_bool( $raw ) ) {
		return $raw;
	}
	if ( is_numeric( $raw ) ) {
		return (int) $raw === 1;
	}
	if ( is_string( $raw ) ) {
		$raw = strtolower( trim( $raw ) );
		if ( in_array( $raw, array( '0', 'false', 'no', 'off', '' ), true ) ) {
			return false;
		}
		return in_array( $raw, array( '1', 'true', 'yes', 'on' ), true );
	}

	return false;
}

/**
 * Meta query: vehicles with hotel / home delivery enabled.
 *
 * @return array
 */
function vip_transits_vehicle_delivery_meta_query() {
	return array(
		'relation' => 'OR',
		array(
			'key'     => 'delivery_hotel_home',
			'value'   => '1',
			'compare' => '=',
		),
		array(
			'key'     => 'delivery_hotel_home',
			'value'   => 1,
			'compare' => '=',
			'type'    => 'NUMERIC',
		),
	);
}

/**
 * Query args for vehicles with hotel / home delivery enabled.
 *
 * @param array $args Overrides.
 * @return array
 */
function vip_transits_vehicle_delivery_query_args( $args = array() ) {
	$args = vip_transits_vehicle_query_args( $args );

	$args['meta_query'] = vip_transits_vehicle_delivery_meta_query();

	return $args;
}

/**
 * Count published vehicles with hotel / home delivery.
 *
 * @return int
 */
function vip_transits_count_delivery_vehicles() {
	$query = new WP_Query(
		vip_transits_vehicle_delivery_query_args(
			array(
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => false,
			)
		)
	);

	return (int) $query->found_posts;
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
	$occasions  = wp_get_post_terms( $post_id, 'vehicle_occasion', array( 'fields' => 'slugs' ) );

	if ( is_wp_error( $brands ) ) {
		$brands = array();
	}
	if ( is_wp_error( $seats ) ) {
		$seats = array();
	}
	if ( is_wp_error( $categories ) ) {
		$categories = array();
	}
	if ( is_wp_error( $occasions ) ) {
		$occasions = array();
	}

	$price = vip_transits_vehicle_daily_price( $post_id );

	$brand_terms = wp_get_post_terms( $post_id, 'vehicle_brand' );
	$brand_names = array();
	if ( ! is_wp_error( $brand_terms ) && $brand_terms ) {
		foreach ( $brand_terms as $term ) {
			$brand_names[] = $term->name;
		}
	}

	$search_text = strtolower( trim( get_the_title( $post_id ) . ' ' . implode( ' ', $brand_names ) ) );

	return array(
		'id'           => $post_id,
		'title'        => get_the_title( $post_id ),
		'search_text'  => $search_text,
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
		'delivery'     => vip_transits_vehicle_has_hotel_delivery( $post_id ),
		'brands'         => $brands,
		'seat_terms'     => $seats,
		'categories'     => $categories,
		'occasions'      => $occasions,
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
 * Cast an ACF value to string for templates.
 *
 * @param mixed $value Field value.
 * @return string
 */
function vip_transits_acf_value_to_string( $value ) {
	if ( is_string( $value ) ) {
		return $value;
	}
	if ( is_scalar( $value ) ) {
		return (string) $value;
	}
	return '';
}

/**
 * Resolve an ACF image field value to a URL.
 *
 * @param mixed  $value Attachment array, ID, or URL string.
 * @param string $size  Image size when value is an attachment ID.
 * @return string
 */
function vip_transits_acf_image_url( $value, $size = 'large' ) {
	if ( is_array( $value ) ) {
		if ( ! empty( $value['url'] ) ) {
			return (string) $value['url'];
		}
		if ( ! empty( $value['ID'] ) ) {
			$url = wp_get_attachment_image_url( (int) $value['ID'], $size );
			return $url ? (string) $url : '';
		}
		return '';
	}

	if ( is_numeric( $value ) ) {
		$url = wp_get_attachment_image_url( (int) $value, $size );
		return $url ? (string) $url : '';
	}

	if ( is_string( $value ) && $value !== '' ) {
		if ( filter_var( $value, FILTER_VALIDATE_URL ) ) {
			return $value;
		}
		if ( ctype_digit( $value ) ) {
			$url = wp_get_attachment_image_url( (int) $value, $size );
			return $url ? (string) $url : '';
		}
	}

	return '';
}

/**
 * Featured image URL for a vehicle (variant card default background).
 *
 * @param int    $post_id Post ID.
 * @param string $size    Image size.
 * @return string
 */
function vip_transits_vehicle_featured_image_url( $post_id, $size = 'large' ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return '';
	}

	$thumb_id = get_post_thumbnail_id( $post_id );
	if ( $thumb_id ) {
		foreach ( array( $size, 'vip_vehicle_hero', 'large', 'vip_fleet_card', 'full' ) as $try_size ) {
			$url = wp_get_attachment_image_url( $thumb_id, $try_size );
			if ( $url ) {
				return (string) $url;
			}
		}
	}

	foreach ( array( $size, 'vip_vehicle_hero', 'large', 'vip_fleet_card' ) as $try_size ) {
		$url = get_the_post_thumbnail_url( $post_id, $try_size );
		if ( $url ) {
			return (string) $url;
		}
	}

	return '';
}

/**
 * Image URL from one variant repeater row (handles manual ACF field keys).
 *
 * @param array<string, mixed> $row Repeater row.
 * @return string
 */
function vip_transits_variant_row_image_url( array $row ) {
	foreach ( array( 'image', 'field_vipveh_var_image' ) as $key ) {
		if ( array_key_exists( $key, $row ) ) {
			$url = vip_transits_acf_image_url( $row[ $key ] );
			if ( $url !== '' ) {
				return $url;
			}
		}
	}

	foreach ( $row as $key => $value ) {
		if ( ! is_string( $key ) || in_array( $key, array( 'name', 'note', 'field_vipveh_var_name', 'field_vipveh_var_note' ), true ) ) {
			continue;
		}
		if ( strpos( $key, 'image' ) !== false || strpos( $key, 'field_' ) === 0 ) {
			$url = vip_transits_acf_image_url( $value );
			if ( $url !== '' ) {
				return $url;
			}
		}
	}

	return '';
}

/**
 * Read one repeater sub-field from a row array (handles ACF field keys).
 *
 * Sub-field slug "name" conflicts with ACF when using get_sub_field( 'name' ).
 *
 * @param array<string, mixed> $item Row from get_field() or get_row( true ).
 * @param string               $key  Sub-field name.
 * @return string
 */
function vip_transits_acf_repeater_sub_value( array $item, $key ) {
	$key = (string) $key;
	if ( array_key_exists( $key, $item ) ) {
		return vip_transits_acf_value_to_string( $item[ $key ] );
	}

	$field_keys = array(
		'name'        => 'field_vipveh_var_name',
		'note'        => 'field_vipveh_var_note',
		'title'       => 'field_vipveh_inc_title',
		'description' => 'field_vipveh_inc_text',
		'question'    => 'field_vipveh_faq_q',
		'answer'      => 'field_vipveh_faq_a',
	);

	if ( 'title' === $key && array_key_exists( 'field_vipveh_route_title', $item ) ) {
		return vip_transits_acf_value_to_string( $item['field_vipveh_route_title'] );
	}
	if ( 'description' === $key && array_key_exists( 'field_vipveh_route_desc', $item ) ) {
		return vip_transits_acf_value_to_string( $item['field_vipveh_route_desc'] );
	}

	if ( isset( $field_keys[ $key ] ) && array_key_exists( $field_keys[ $key ], $item ) ) {
		return vip_transits_acf_value_to_string( $item[ $field_keys[ $key ] ] );
	}

	return '';
}

/**
 * Repeater rows from ACF as a simple list.
 *
 * @param string $field    Field name.
 * @param int    $post_id  Post ID.
 * @param array  $sub_keys Optional sub-field names to return per row.
 * @return array<int, array<string, string>>
 */
function vip_transits_vehicle_acf_rows( $field, $post_id, $sub_keys = array() ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return array();
	}

	$rows = array();

	if ( function_exists( 'get_field' ) ) {
		$value = get_field( $field, $post_id, false );
		if ( is_array( $value ) && $value ) {
			foreach ( $value as $item ) {
				if ( ! is_array( $item ) ) {
					continue;
				}
				if ( $sub_keys ) {
					$row = array();
					foreach ( $sub_keys as $key ) {
						$row[ $key ] = vip_transits_acf_repeater_sub_value( $item, $key );
					}
					$rows[] = $row;
				} else {
					$rows[] = $item;
				}
			}
			if ( $rows ) {
				return $rows;
			}
		}
	}

	if ( ! function_exists( 'have_rows' ) || ! have_rows( $field, $post_id ) ) {
		return array();
	}

	while ( have_rows( $field, $post_id ) ) {
		the_row();
		$acf_row = function_exists( 'get_row' ) ? get_row( true ) : array();
		if ( ! is_array( $acf_row ) ) {
			$acf_row = array();
		}

		if ( $sub_keys ) {
			$row = array();
			foreach ( $sub_keys as $key ) {
				$row[ $key ] = vip_transits_acf_repeater_sub_value( $acf_row, $key );
				if ( '' === $row[ $key ] && function_exists( 'get_sub_field' ) ) {
					$field_key_map = array(
						'name' => 'field_vipveh_var_name',
						'note' => 'field_vipveh_var_note',
					);
					if ( isset( $field_key_map[ $key ] ) ) {
						$row[ $key ] = vip_transits_acf_value_to_string( get_sub_field( $field_key_map[ $key ] ) );
					} elseif ( 'name' !== $key ) {
						$row[ $key ] = vip_transits_acf_value_to_string( get_sub_field( $key ) );
					}
				}
			}
			$rows[] = $row;
			continue;
		}

		$rows[] = $acf_row;
	}

	return $rows;
}

/**
 * Default Best routes card images (Sheikh Zayed, Palm, Marina).
 *
 * Uses local uploads when present; otherwise production media URLs.
 *
 * @return array<string, string> Keys: zayed, palm, marina.
 */
function vip_transits_driving_route_default_images() {
	static $images = null;

	if ( null !== $images ) {
		return $images;
	}

	$remote_base = 'https://viptransits.com/wp-content/uploads/';
	$files       = array(
		'zayed'  => '2026/05/pexels-nelemson-29470840-1.png',
		'palm'   => '2026/05/pexels-miketyurin-33996797-1.png',
		'marina' => '2026/05/pexels-iamllwyd-35062992-1.png',
	);

	$images = array();
	$upload = wp_upload_dir();

	foreach ( $files as $key => $relative ) {
		if ( empty( $upload['error'] ) ) {
			$local_path = $upload['basedir'] . '/' . $relative;
			if ( is_readable( $local_path ) ) {
				$images[ $key ] = $upload['baseurl'] . '/' . $relative;
				continue;
			}
		}
		$images[ $key ] = $remote_base . $relative;
	}

	return $images;
}

/**
 * Default image for a driving route card when no ACF image is set.
 *
 * @param string $title   Route title (matched to known Dubai routes).
 * @param int    $post_id Vehicle post ID (hero thumbnail fallback).
 * @return string
 */
function vip_transits_driving_route_image_url( $title, $post_id = 0 ) {
	$title_l = strtolower( (string) $title );
	$images  = vip_transits_driving_route_default_images();

	$needles = array(
		'zayed'  => 'zayed',
		'palm'   => 'palm',
		'marina' => 'marina',
	);

	foreach ( $needles as $needle => $key ) {
		if ( str_contains( $title_l, $needle ) && ! empty( $images[ $key ] ) ) {
			return $images[ $key ];
		}
	}

	$post_id = (int) $post_id;
	if ( $post_id > 0 ) {
		$hero = get_the_post_thumbnail_url( $post_id, 'large' );
		if ( $hero ) {
			return $hero;
		}
	}

	return $images['zayed'] ?? '';
}

/**
 * Built-in driving routes when none are saved in ACF.
 *
 * @param int $post_id Vehicle post ID.
 * @return array<int, array{title:string,description:string,image_url:string}>
 */
function vip_transits_vehicle_default_driving_routes( $post_id = 0 ) {
	$images   = vip_transits_driving_route_default_images();
	$defaults = array(
		array(
			'title'       => __( 'Sheikh Zayed Road', 'tenku-child' ),
			'description' => __( '10-lane highway, perfect for acceleration runs', 'tenku-child' ),
			'image_url'   => $images['zayed'] ?? '',
		),
		array(
			'title'       => __( 'Palm Jumeirah', 'tenku-child' ),
			'description' => __( 'The crescent road with sea views on both sides', 'tenku-child' ),
			'image_url'   => $images['palm'] ?? '',
		),
		array(
			'title'       => __( 'Dubai Marina Walk', 'tenku-child' ),
			'description' => __( 'Low-speed cruising at its most glamorous', 'tenku-child' ),
			'image_url'   => $images['marina'] ?? '',
		),
	);

	return $defaults;
}

/**
 * Driving routes for the vehicle detail page (ACF repeater + image URLs).
 *
 * @param int $post_id Vehicle post ID.
 * @return array<int, array{title:string,description:string,image_url:string}>
 */
function vip_transits_vehicle_driving_routes( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	if ( $post_id <= 0 ) {
		return array();
	}

	$routes = array();

	if ( function_exists( 'get_field' ) ) {
		$rows = get_field( 'driving_routes', $post_id, false );
		if ( is_array( $rows ) ) {
			foreach ( $rows as $item ) {
				if ( ! is_array( $item ) ) {
					continue;
				}

				$title = trim( vip_transits_acf_repeater_sub_value( $item, 'title' ) );
				$desc  = trim( vip_transits_acf_repeater_sub_value( $item, 'description' ) );

				$image_raw = null;
				if ( array_key_exists( 'image', $item ) ) {
					$image_raw = $item['image'];
				} elseif ( array_key_exists( 'field_vipveh_route_image', $item ) ) {
					$image_raw = $item['field_vipveh_route_image'];
				}

				$image_url = vip_transits_acf_image_url( $image_raw, 'large' );

				if ( $title === '' && $desc === '' && $image_url === '' ) {
					continue;
				}

				$routes[] = array(
					'title'       => $title,
					'description' => $desc,
					'image_url'   => $image_url !== '' ? $image_url : vip_transits_driving_route_image_url( $title, $post_id ),
				);
			}
		}
	}

	if ( $routes ) {
		return $routes;
	}

	return vip_transits_vehicle_default_driving_routes( $post_id );
}

/**
 * Related vehicles in the same Variant taxonomy (single vehicle grid).
 *
 * Latest 5 published vehicles sharing vehicle_variant with current post (excluded).
 *
 * @param int $post_id Post ID.
 * @param int $limit   Max vehicles.
 * @return array<int, array{id:int, name:string, image:string, permalink:string}>
 */
function vip_transits_get_vehicle_variants( $post_id, $limit = 5 ) {
	$post_id = (int) $post_id;
	$limit   = max( 1, (int) $limit );

	if ( $post_id <= 0 || ! taxonomy_exists( 'vehicle_variant' ) ) {
		return array();
	}

	$term_ids = wp_get_post_terms( $post_id, 'vehicle_variant', array( 'fields' => 'ids' ) );
	if ( is_wp_error( $term_ids ) || empty( $term_ids ) ) {
		return array();
	}

	$query = new WP_Query(
		array(
			'post_type'              => 'vip_vehicle',
			'post_status'            => 'publish',
			'posts_per_page'         => $limit,
			'post__not_in'           => array( $post_id ),
			'orderby'                => 'date',
			'order'                  => 'DESC',
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'tax_query'              => array(
				array(
					'taxonomy' => 'vehicle_variant',
					'field'    => 'term_id',
					'terms'    => array_map( 'intval', $term_ids ),
				),
			),
		)
	);

	if ( ! $query->have_posts() ) {
		return array();
	}

	$variants = array();

	foreach ( $query->posts as $related_post ) {
		$related_id = (int) $related_post->ID;
		if ( $related_id <= 0 || $related_id === $post_id ) {
			continue;
		}

		$image = vip_transits_vehicle_featured_image_url( $related_id );
		if ( $image === '' ) {
			$image = get_the_post_thumbnail_url( $related_id, 'vip_vehicle_hero' ) ?: get_the_post_thumbnail_url( $related_id, 'large' );
		}

		$variants[] = array(
			'id'        => $related_id,
			'name'      => get_the_title( $related_id ),
			'image'     => (string) $image,
			'permalink' => get_permalink( $related_id ),
		);
	}

	return $variants;
}

/**
 * Related vehicles (admin-picked, max 3).
 *
 * @param int $post_id Post ID.
 * @param int $limit   Max posts.
 * @return WP_Post[]
 */
function vip_transits_vehicle_related_posts( $post_id, $limit = 3 ) {
	$post_id = (int) $post_id;
	$limit   = max( 1, (int) $limit );
	$ids     = array();

	if ( function_exists( 'get_field' ) ) {
		$manual = get_field( 'related_vehicles_pick', $post_id );
		if ( is_array( $manual ) ) {
			foreach ( $manual as $item ) {
				if ( is_object( $item ) && isset( $item->ID ) ) {
					$ids[] = (int) $item->ID;
				} elseif ( is_array( $item ) && isset( $item['ID'] ) ) {
					$ids[] = (int) $item['ID'];
				} elseif ( is_numeric( $item ) ) {
					$ids[] = (int) $item;
				}
			}
		}
	}

	$ids = array_slice(
		array_values(
			array_unique(
				array_filter(
					array_map( 'intval', $ids ),
					static function ( $id ) {
						return $id > 0;
					}
				)
			)
		),
		0,
		$limit
	);

	if ( ! $ids ) {
		return array();
	}

	$posts = array();
	foreach ( $ids as $related_id ) {
		$related_post = get_post( $related_id );
		if (
			$related_post instanceof WP_Post
			&& 'vip_vehicle' === $related_post->post_type
			&& 'publish' === $related_post->post_status
		) {
			$posts[] = $related_post;
		}
	}

	return $posts;
}

/**
 * Exclude the vehicle being edited from the Also Available relationship picker.
 *
 * @param array<string, mixed> $args    Query args.
 * @param array<string, mixed> $field   Field settings.
 * @param int                  $post_id Current post ID.
 * @return array<string, mixed>
 */
function vip_transits_related_vehicles_relationship_query( $args, $field, $post_id ) {
	if ( ( $field['name'] ?? '' ) !== 'related_vehicles_pick' || $post_id <= 0 ) {
		return $args;
	}

	$args['post__not_in'] = array_merge(
		isset( $args['post__not_in'] ) && is_array( $args['post__not_in'] ) ? $args['post__not_in'] : array(),
		array( (int) $post_id )
	);

	return $args;
}
add_filter( 'acf/fields/relationship/query/name=related_vehicles_pick', 'vip_transits_related_vehicles_relationship_query', 10, 3 );

/**
 * What's included repeater rows with optional icon URLs.
 *
 * @param int $post_id Post ID.
 * @return array<int, array{title:string,description:string,icon_url:string,icon_alt:string}>
 */
function vip_transits_vehicle_included_items( $post_id ) {
	$post_id = (int) $post_id;
	$items   = array();

	if ( ! function_exists( 'get_field' ) ) {
		return $items;
	}

	$rows = get_field( 'included_items', $post_id );
	if ( ! is_array( $rows ) ) {
		return $items;
	}

	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$title = isset( $row['title'] ) ? trim( (string) $row['title'] ) : '';
		$desc  = isset( $row['description'] ) ? trim( (string) $row['description'] ) : '';
		$icon  = $row['icon'] ?? ( $row['field_vipveh_inc_icon'] ?? null );

		if ( ! $title && ! $desc && ! $icon ) {
			continue;
		}

		$icon_url = vip_transits_acf_image_url( $icon, 'thumbnail' );
		$icon_alt = '';
		if ( is_array( $icon ) ) {
			$icon_alt = isset( $icon['alt'] ) ? trim( (string) $icon['alt'] ) : '';
			if ( ! $icon_alt && $title ) {
				$icon_alt = $title;
			}
		}

		$items[] = array(
			'title'       => $title,
			'description' => $desc,
			'icon_url'    => $icon_url,
			'icon_alt'    => $icon_alt,
		);
	}

	return vip_transits_vehicle_filter_displayable_included( $items );
}

/**
 * Keep only included rows that have title, description, or icon.
 *
 * @param array<int, array<string, string>> $items Included rows.
 * @return array<int, array<string, string>>
 */
function vip_transits_vehicle_filter_displayable_included( $items ) {
	$visible = array();

	foreach ( (array) $items as $item ) {
		if ( ! is_array( $item ) ) {
			continue;
		}

		$title = isset( $item['title'] ) ? trim( (string) $item['title'] ) : '';
		$desc  = isset( $item['description'] ) ? trim( (string) $item['description'] ) : '';
		$icon  = isset( $item['icon_url'] ) ? trim( (string) $item['icon_url'] ) : '';

		if ( $title || $desc || $icon ) {
			$visible[] = $item;
		}
	}

	return $visible;
}

/**
 * Header deposit line (black bar under price).
 *
 * @param array<string, mixed> $d Vehicle single data.
 * @return string
 */
function vip_transits_vehicle_header_deposit_text( $d ) {
	$line = '';
	if ( ! empty( $d['header_deposit_line'] ) ) {
		$line = (string) $d['header_deposit_line'];
	} elseif ( ! empty( $d['masthead_deposit_line'] ) ) {
		$line = (string) $d['masthead_deposit_line'];
	}
	if ( $line !== '' ) {
		return $line;
	}

	$deposit = isset( $d['security_deposit'] ) ? (int) $d['security_deposit'] : 0;
	if ( $deposit <= 0 ) {
		return __( 'No Deposit', 'tenku-child' );
	}

	return sprintf(
		/* translators: %s: formatted deposit amount */
		__( 'Refundable deposit: AED %s', 'tenku-child' ),
		number_format_i18n( $deposit )
	);
}

/**
 * Pricing card deposit value (header + security deposit row).
 *
 * @param array<string, mixed> $d Vehicle single data.
 * @return string
 */
function vip_transits_vehicle_pricing_deposit_text( $d ) {
	if ( ! empty( $d['pricing_deposit_value'] ) ) {
		return (string) $d['pricing_deposit_value'];
	}

	$deposit = isset( $d['security_deposit'] ) ? (int) $d['security_deposit'] : 0;
	if ( $deposit <= 0 ) {
		return __( 'No deposit required', 'tenku-child' );
	}

	return sprintf(
		/* translators: %s: formatted deposit amount */
		__( 'AED %s (refundable)', 'tenku-child' ),
		number_format_i18n( $deposit )
	);
}

/**
 * Pricing card deposit label (header).
 *
 * @param array<string, mixed> $d Vehicle single data.
 * @return string
 */
function vip_transits_vehicle_pricing_deposit_heading( $d ) {
	if ( ! empty( $d['pricing_deposit_heading'] ) ) {
		return (string) $d['pricing_deposit_heading'];
	}

	return __( 'Deposit:', 'tenku-child' );
}

/**
 * Related vehicles section heading (ACF or default with brand name).
 *
 * @param array<string, mixed> $d Vehicle single data.
 * @return string
 */
function vip_transits_vehicle_related_section_heading( $d ) {
	$custom = isset( $d['related_vehicles_section_heading'] ) ? trim( (string) $d['related_vehicles_section_heading'] ) : '';
	if ( $custom !== '' ) {
		if ( strpos( $custom, '%s' ) !== false ) {
			$brand = isset( $d['brand_name'] ) ? (string) $d['brand_name'] : '';
			return sprintf( $custom, $brand );
		}
		return $custom;
	}

	if ( ! empty( $d['brand_name'] ) ) {
		return sprintf(
			/* translators: %s: brand name */
			__( 'Also Available from %s', 'tenku-child' ),
			(string) $d['brand_name']
		);
	}

	return __( 'Also Available', 'tenku-child' );
}

/**
 * Included section heading (ACF or default with vehicle short name).
 *
 * @param array<string, mixed> $d Vehicle single data.
 * @return string
 */
function vip_transits_vehicle_included_section_heading( $d ) {
	$custom = isset( $d['included_section_heading'] ) ? trim( (string) $d['included_section_heading'] ) : '';
	if ( $custom !== '' ) {
		if ( strpos( $custom, '%s' ) !== false ) {
			$short = isset( $d['short_name'] ) ? (string) $d['short_name'] : '';
			return sprintf( $custom, $short );
		}
		return $custom;
	}

	$short = isset( $d['short_name'] ) ? (string) $d['short_name'] : '';
	return sprintf(
		/* translators: %s: vehicle short name */
		__( "What's Included With Your %s Rental", 'tenku-child' ),
		$short
	);
}

/**
 * Pricing card — security deposit row label.
 *
 * @param array<string, mixed> $d Vehicle single data.
 * @return string
 */
function vip_transits_vehicle_pricing_security_deposit_label( $d ) {
	if ( ! empty( $d['pricing_security_deposit_label'] ) ) {
		return (string) $d['pricing_security_deposit_label'];
	}

	return __( 'Security deposit', 'tenku-child' );
}

/**
 * Pricing card — insurance row label.
 *
 * @param array<string, mixed> $d Vehicle single data.
 * @return string
 */
function vip_transits_vehicle_pricing_insurance_label( $d ) {
	if ( ! empty( $d['pricing_insurance_label'] ) ) {
		return (string) $d['pricing_insurance_label'];
	}

	return __( 'Insurance', 'tenku-child' );
}

/**
 * Pricing card — insurance row value.
 *
 * @param array<string, mixed> $d Vehicle single data.
 * @return string
 */
function vip_transits_vehicle_pricing_insurance_value( $d ) {
	if ( ! empty( $d['pricing_insurance_value'] ) ) {
		return (string) $d['pricing_insurance_value'];
	}

	return __( 'Included', 'tenku-child' );
}

/**
 * Pricing card — delivery row label.
 *
 * @param array<string, mixed> $d Vehicle single data.
 * @return string
 */
function vip_transits_vehicle_pricing_delivery_label( $d ) {
	if ( ! empty( $d['pricing_delivery_label'] ) ) {
		return (string) $d['pricing_delivery_label'];
	}

	return __( 'Delivery', 'tenku-child' );
}

/**
 * Pricing card — delivery row value.
 *
 * @param array<string, mixed> $d Vehicle single data.
 * @return string
 */
function vip_transits_vehicle_pricing_delivery_value( $d ) {
	if ( ! empty( $d['pricing_delivery_value'] ) ) {
		return (string) $d['pricing_delivery_value'];
	}

	if ( ! empty( $d['delivery'] ) ) {
		return __( 'Free - Anywhere Dubai', 'tenku-child' );
	}

	return __( 'Ask on WhatsApp', 'tenku-child' );
}

/**
 * Daily rate line for the pricing card (optional price suffix / VAT).
 *
 * @param array<string, mixed> $d          Vehicle single data.
 * @param string               $price_fmt Formatted daily price (no currency).
 * @return string
 */
function vip_transits_vehicle_pricing_daily_rate_text( $d, $price_fmt ) {
	$price_fmt = trim( (string) $price_fmt );
	if ( $price_fmt === '' ) {
		return '—';
	}

	$text = sprintf(
		/* translators: %s: formatted price */
		__( 'AED %s', 'tenku-child' ),
		$price_fmt
	);

	$suffix = isset( $d['price_suffix'] ) ? trim( (string) $d['price_suffix'] ) : '';
	if ( $suffix !== '' ) {
		$text .= ' ' . $suffix;
	}

	return $text;
}

/**
 * @deprecated Use vip_transits_vehicle_header_deposit_text().
 * @param array<string, mixed> $d Vehicle single data.
 * @return string
 */
function vip_transits_vehicle_masthead_deposit_text( $d ) {
	return vip_transits_vehicle_header_deposit_text( $d );
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

	$deposit_raw = get_field( 'security_deposit', $post_id );
	if ( $deposit_raw === null || $deposit_raw === '' ) {
		$deposit = 5000;
	} else {
		$deposit = max( 0, (int) $deposit_raw );
	}

	$price_suffix            = trim( (string) get_field( 'price_suffix', $post_id ) );
	$header_deposit_line = trim( (string) get_field( 'header_deposit_line', $post_id ) );
	if ( $header_deposit_line === '' ) {
		$header_deposit_line = trim( (string) get_field( 'masthead_deposit_line', $post_id ) );
	}
	$pricing_deposit_heading       = trim( (string) get_field( 'pricing_deposit_heading', $post_id ) );
	$pricing_deposit_value         = trim( (string) get_field( 'pricing_deposit_value', $post_id ) );
	$pricing_security_deposit_label = trim( (string) get_field( 'pricing_security_deposit_label', $post_id ) );
	$pricing_insurance_label       = trim( (string) get_field( 'pricing_insurance_label', $post_id ) );
	$pricing_insurance_value       = trim( (string) get_field( 'pricing_insurance_value', $post_id ) );
	$pricing_delivery_label        = trim( (string) get_field( 'pricing_delivery_label', $post_id ) );
	$pricing_delivery_value        = trim( (string) get_field( 'pricing_delivery_value', $post_id ) );
	$included_section_heading       = trim( (string) get_field( 'included_section_heading', $post_id ) );
	$related_vehicles_section_heading = trim( (string) get_field( 'related_vehicles_section_heading', $post_id ) );

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

	$included = vip_transits_vehicle_included_items( $post_id );

	$variants = vip_transits_get_vehicle_variants( $post_id );
	$routes   = vip_transits_vehicle_driving_routes( $post_id );
	$faq      = vip_transits_vehicle_acf_rows( 'vehicle_faq', $post_id, array( 'question', 'answer' ) );

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
			'security_deposit'      => $deposit,
			'price_suffix'          => $price_suffix,
			'header_deposit_line'   => $header_deposit_line,
			'masthead_deposit_line' => $header_deposit_line,
			'pricing_deposit_heading'        => $pricing_deposit_heading,
			'pricing_deposit_value'          => $pricing_deposit_value,
			'pricing_security_deposit_label' => $pricing_security_deposit_label,
			'pricing_insurance_label'        => $pricing_insurance_label,
			'pricing_insurance_value'        => $pricing_insurance_value,
			'pricing_delivery_label'         => $pricing_delivery_label,
			'pricing_delivery_value'         => $pricing_delivery_value,
			'included_section_heading'         => $included_section_heading,
			'related_vehicles_section_heading' => $related_vehicles_section_heading,
			'weekly_rate'                      => $weekly,
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
			'related'          => vip_transits_vehicle_related_posts( $post_id, 3 ),
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

	$page           = max( 1, (int) ( $_POST['page'] ?? 1 ) );
	$per_page       = max( 1, min( 24, (int) ( $_POST['per_page'] ?? 9 ) ) );
	$delivery_only  = ! empty( $_POST['delivery_only'] );
	$occasion_slug  = sanitize_title( (string) ( $_POST['occasion_slug'] ?? '' ) );
	$query_arg_fn   = $delivery_only ? 'vip_transits_vehicle_delivery_query_args' : 'vip_transits_vehicle_query_args';

	$query_args = array(
		'paged'          => $page,
		'posts_per_page' => $per_page,
	);
	if ( $occasion_slug !== '' ) {
		$query_args['occasion_slug'] = $occasion_slug;
	}

	$query = new WP_Query( $query_arg_fn( $query_args ) );

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

/**
 * Theme URL for vehicle gallery slider arrow icons.
 *
 * @param string $direction `prev` or `next`.
 * @return string
 */
function vip_transits_vehicle_gallery_arrow_url( $direction ) {
	$file = 'prev' === $direction ? 'gallery-arrow-left.svg' : 'gallery-arrow-right.svg';

	return get_stylesheet_directory_uri() . '/assets/images/' . $file;
}
