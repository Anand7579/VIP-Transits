<?php
/**
 * Occasions CPT (Wedding, Birthday, etc.) — detail pages with fleet + ACF.
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register vip_occasion post type.
 */
function vip_transits_register_occasion_cpt() {
	register_post_type(
		'vip_occasion',
		array(
			'labels'              => array(
				'name'               => __( 'Occasions', 'tenku-child' ),
				'singular_name'      => __( 'Occasion', 'tenku-child' ),
				'add_new'            => __( 'Add occasion', 'tenku-child' ),
				'add_new_item'       => __( 'Add new occasion', 'tenku-child' ),
				'edit_item'          => __( 'Edit occasion', 'tenku-child' ),
				'new_item'           => __( 'New occasion', 'tenku-child' ),
				'view_item'          => __( 'View occasion', 'tenku-child' ),
				'search_items'       => __( 'Search occasions', 'tenku-child' ),
				'not_found'          => __( 'No occasions found.', 'tenku-child' ),
				'not_found_in_trash' => __( 'No occasions found in Trash.', 'tenku-child' ),
				'all_items'          => __( 'All occasions', 'tenku-child' ),
				'menu_name'          => __( 'Occasions', 'tenku-child' ),
			),
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => true,
			'has_archive'         => false,
			'rewrite'             => array(
				'slug'       => 'occasions',
				'with_front' => false,
			),
			'menu_icon'           => 'dashicons-star-filled',
			'menu_position'       => 26,
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ),
			'capability_type'     => 'post',
			'exclude_from_search' => false,
		)
	);
}
add_action( 'init', 'vip_transits_register_occasion_cpt', 5 );

/**
 * Flush rewrite rules once after CPT registration.
 */
function vip_transits_occasion_cpt_flush_rewrites() {
	if ( get_option( 'vip_transits_occasion_cpt_flushed' ) ) {
		return;
	}

	flush_rewrite_rules( false );
	update_option( 'vip_transits_occasion_cpt_flushed', 1, false );
}
add_action( 'init', 'vip_transits_occasion_cpt_flush_rewrites', 99 );

/**
 * One-time flush after disabling the occasions archive.
 */
function vip_transits_occasion_cpt_flush_no_archive() {
	if ( get_option( 'vip_transits_occasion_no_archive_flushed' ) ) {
		return;
	}

	flush_rewrite_rules( false );
	update_option( 'vip_transits_occasion_no_archive_flushed', 1, false );
}
add_action( 'init', 'vip_transits_occasion_cpt_flush_no_archive', 98 );

/**
 * Link for breadcrumb "Occasions" (homepage Rent by occasion section).
 *
 * @return string
 */
function vip_transits_get_occasions_archive_url() {
	return home_url( '/#vip-occasions' );
}

/**
 * Redirect legacy CPT archive to homepage occasions section.
 */
function vip_transits_redirect_occasion_archive() {
	if ( is_post_type_archive( 'vip_occasion' ) ) {
		wp_safe_redirect( vip_transits_get_occasions_archive_url(), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'vip_transits_redirect_occasion_archive' );

/**
 * Current occasion post ID (CPT single or legacy page).
 *
 * @return int
 */
function vip_transits_occasion_post_id() {
	if ( is_singular( 'vip_occasion' ) ) {
		return (int) get_queried_object_id();
	}

	if ( function_exists( 'vip_transits_page_content_post_id' ) ) {
		$page_id = vip_transits_page_content_post_id();
		if ( $page_id > 0 && is_page( $page_id ) && function_exists( 'vip_transits_page_uses_vip_template' ) && vip_transits_page_uses_vip_template( $page_id, 'occasion' ) ) {
			return $page_id;
		}
	}

	return 0;
}

/**
 * Published occasions for archives / homepage queries.
 *
 * @param array $args Optional WP_Query-style overrides.
 * @return WP_Post[]
 */
function vip_transits_get_published_occasions( $args = array() ) {
	$query = new WP_Query(
		wp_parse_args(
			$args,
			array(
				'post_type'              => 'vip_occasion',
				'post_status'            => 'publish',
				'posts_per_page'         => -1,
				'orderby'                => 'menu_order title',
				'order'                  => 'ASC',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
			)
		)
	);

	return $query->posts;
}

/**
 * Whether the request is an occasion detail view (CPT or legacy page).
 *
 * @param int $post_id Optional post ID.
 * @return bool
 */
function vip_transits_is_occasion_detail( $post_id = 0 ) {
	if ( is_singular( 'vip_occasion' ) ) {
		return true;
	}

	$post_id = $post_id ? (int) $post_id : (int) get_queried_object_id();
	if ( $post_id <= 0 || ! is_page( $post_id ) ) {
		return false;
	}

	return function_exists( 'vip_transits_page_uses_vip_template' )
		&& vip_transits_page_uses_vip_template( $post_id, 'occasion' );
}

/**
 * Short occasion name for filters (e.g. "wedding" from slug wedding-car-rental).
 *
 * @param int $post_id Occasion post ID.
 * @return string
 */
function vip_transits_get_occasion_short_name( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : vip_transits_occasion_post_id();
	if ( $post_id <= 0 ) {
		return '';
	}

	$slug = (string) get_post_field( 'post_name', $post_id );
	if ( $slug === '' ) {
		return strtolower( wp_strip_all_tags( get_the_title( $post_id ) ) );
	}

	$first = strtok( $slug, '-' );
	return strtolower( $first ? $first : $slug );
}

/**
 * Label for the occasion-only fleet role filter group.
 *
 * @param int $post_id Occasion post ID.
 * @return string
 */
function vip_transits_get_occasion_role_filter_label( $post_id = 0 ) {
	$short = vip_transits_get_occasion_short_name( $post_id );
	if ( $short === '' ) {
		return __( 'Car role for occasion', 'tenku-child' );
	}

	return sprintf(
		/* translators: %s: occasion short name, e.g. wedding */
		__( 'Car role for %s', 'tenku-child' ),
		$short
	);
}

/**
 * ACF field helper for the current occasion (CPT or legacy page).
 *
 * @param string $field   Field name.
 * @param mixed  $default Default value.
 * @return mixed
 */
function vip_transits_get_occasion_field( $field, $default = null ) {
	$post_id = vip_transits_occasion_post_id();
	if ( ! $post_id || ! function_exists( 'get_field' ) ) {
		return $default;
	}

	$value = get_field( $field, $post_id );
	return null !== $value && '' !== $value && array() !== $value ? $value : $default;
}

