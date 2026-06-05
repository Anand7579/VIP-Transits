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
 * Keep vehicle_occasion taxonomy terms in sync when an occasion is saved.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post object.
 */
function vip_transits_sync_vehicle_occasion_term_on_save( $post_id, $post ) {
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}

	if ( ! $post instanceof WP_Post || 'vip_occasion' !== $post->post_type || 'publish' !== $post->post_status ) {
		return;
	}

	if ( function_exists( 'vip_transits_sync_vehicle_occasion_terms' ) ) {
		vip_transits_sync_vehicle_occasion_terms();
	}
}
add_action( 'save_post_vip_occasion', 'vip_transits_sync_vehicle_occasion_term_on_save', 20, 2 );

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
 * Homepage “Rent by occasion” anchor (no public CPT archive).
 *
 * @return string
 */
function vip_transits_get_occasions_archive_url() {
	return home_url( '/#vip-occasions' );
}

/**
 * Redirect legacy /occasions/ archive URLs to the homepage section.
 */
function vip_transits_redirect_occasion_archive() {
	if ( is_post_type_archive( 'vip_occasion' ) ) {
		wp_safe_redirect( vip_transits_get_occasions_archive_url(), 301 );
		exit;
	}

	global $wp;
	if ( is_404() && isset( $wp->request ) && trim( (string) $wp->request, '/' ) === 'occasions' ) {
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
 * Occasions published and marked visible on the homepage section.
 *
 * @return WP_Post[]
 */
function vip_transits_get_homepage_occasion_posts() {
	$posts = vip_transits_get_published_occasions();
	$out   = array();

	foreach ( $posts as $post ) {
		if ( function_exists( 'get_field' ) ) {
			$show = get_field( 'show_on_homepage', $post->ID );
			if ( false === $show || 0 === $show || '0' === $show ) {
				continue;
			}
		}
		$out[] = $post;
	}

	return $out;
}

/**
 * Intro text for a homepage occasion card (excerpt, else hero intro).
 *
 * @param int $post_id Occasion post ID.
 * @return string
 */
function vip_transits_occasion_homepage_intro( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return '';
	}

	$excerpt = trim( (string) get_post_field( 'post_excerpt', $post_id ) );
	if ( $excerpt !== '' ) {
		return $excerpt;
	}

	if ( function_exists( 'get_field' ) ) {
		$hero = get_field( 'text_image_section', $post_id );
		if ( is_array( $hero ) && ! empty( $hero['content'] ) ) {
			return wp_trim_words( wp_strip_all_tags( (string) $hero['content'] ), 40, '…' );
		}
	}

	return '';
}

/**
 * Image array for homepage card media (featured image, else hero image).
 *
 * @param int $post_id Occasion post ID.
 * @return array|null ACF-style image array with ID and/or url.
 */
function vip_transits_occasion_homepage_image( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return null;
	}

	$thumb_id = (int) get_post_thumbnail_id( $post_id );
	if ( $thumb_id > 0 ) {
		$alt = (string) get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
		return array(
			'ID'  => $thumb_id,
			'alt' => $alt !== '' ? $alt : get_the_title( $post_id ),
		);
	}

	if ( function_exists( 'get_field' ) ) {
		$hero = get_field( 'text_image_section', $post_id );
		if ( is_array( $hero ) && ! empty( $hero['image'] ) && is_array( $hero['image'] ) ) {
			return $hero['image'];
		}
	}

	return null;
}

/**
 * Homepage card button label (ACF field, else generated default).
 *
 * @param int $post_id Occasion post ID.
 * @return string
 */
function vip_transits_occasion_homepage_button_label( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return '';
	}

	if ( function_exists( 'get_field' ) ) {
		$label = trim( (string) get_field( 'homepage_button_label', $post_id ) );
		if ( $label !== '' ) {
			return $label;
		}
	}

	$short = vip_transits_get_occasion_short_name( $post_id );
	if ( $short !== '' ) {
		return sprintf(
			/* translators: %s: occasion short name, e.g. wedding */
			__( 'Book %s', 'tenku-child' ),
			ucfirst( $short )
		);
	}

	return __( 'Learn more', 'tenku-child' );
}

/**
 * One occasion as a homepage card row (matches legacy ACF card shape).
 *
 * @param int $post_id Occasion post ID.
 * @return array{image:array|null,title:string,description:string,button_label:string,permalink:string}
 */
function vip_transits_get_occasion_homepage_card_data( $post_id ) {
	$post_id = (int) $post_id;

	return array(
		'image'         => vip_transits_occasion_homepage_image( $post_id ),
		'title'         => get_the_title( $post_id ),
		'description'   => vip_transits_occasion_homepage_intro( $post_id ),
		'button_label'  => vip_transits_occasion_homepage_button_label( $post_id ),
		'permalink'     => get_permalink( $post_id ),
	);
}

/**
 * Whether a post ID is a published occasion.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function vip_transits_is_published_occasion( $post_id ) {
	$post_id = (int) $post_id;

	return $post_id > 0
		&& get_post_type( $post_id ) === 'vip_occasion'
		&& get_post_status( $post_id ) === 'publish';
}

/**
 * Normalize ACF post object / ID to a single occasion post ID.
 *
 * @param mixed $value Post ID, post object, or array.
 * @return int
 */
function vip_transits_normalize_occasion_post_id( $value ) {
	if ( is_numeric( $value ) ) {
		return (int) $value;
	}

	if ( $value instanceof WP_Post ) {
		return (int) $value->ID;
	}

	if ( is_array( $value ) ) {
		if ( isset( $value['ID'] ) ) {
			return (int) $value['ID'];
		}
		if ( isset( $value[0] ) ) {
			return vip_transits_normalize_occasion_post_id( $value[0] );
		}
	}

	return 0;
}

/**
 * Normalize ACF relationship value to ordered occasion post IDs.
 *
 * @param mixed $value Post IDs, post objects, or mixed array.
 * @return int[]
 */
function vip_transits_normalize_occasion_post_ids( $value ) {
	if ( ! $value ) {
		return array();
	}

	$items = is_array( $value ) && ! isset( $value['ID'] ) ? $value : array( $value );
	$ids   = array();

	foreach ( $items as $item ) {
		$id = vip_transits_normalize_occasion_post_id( $item );
		if ( $id > 0 && ! in_array( $id, $ids, true ) ) {
			$ids[] = $id;
		}
	}

	return $ids;
}

/**
 * Homepage layout from explicit picks on the Rent by occasion section.
 *
 * @param mixed $featured_pick ACF featured_occasion (post ID or object).
 * @param mixed $grid_picks    ACF grid_occasions (IDs or posts).
 * @return array{featured:array|null,cards:array<int,array>}
 */
function vip_transits_get_homepage_occasions_layout_for_section( $featured_pick = null, $grid_picks = null ) {
	$featured_id = vip_transits_normalize_occasion_post_id( $featured_pick );
	$grid_ids    = vip_transits_normalize_occasion_post_ids( $grid_picks );

	if ( $featured_id <= 0 && empty( $grid_ids ) ) {
		return vip_transits_get_homepage_occasions_layout();
	}

	$featured_card = null;
	if ( $featured_id > 0 && vip_transits_is_published_occasion( $featured_id ) ) {
		$featured_card = vip_transits_get_occasion_homepage_card_data( $featured_id );
	}

	$cards = array();
	foreach ( $grid_ids as $id ) {
		if ( count( $cards ) >= 4 ) {
			break;
		}
		if ( $id === $featured_id ) {
			continue;
		}
		if ( ! vip_transits_is_published_occasion( $id ) ) {
			continue;
		}
		$cards[] = vip_transits_get_occasion_homepage_card_data( $id );
	}

	return array(
		'featured' => $featured_card,
		'cards'    => $cards,
	);
}

/**
 * Featured + grid cards for the homepage Rent by occasion section (automatic).
 *
 * @return array{featured:array|null,cards:array<int,array>}
 */
function vip_transits_get_homepage_occasions_layout() {
	$posts = vip_transits_get_homepage_occasion_posts();
	if ( ! $posts ) {
		return array(
			'featured' => null,
			'cards'    => array(),
		);
	}

	$featured_id = 0;
	foreach ( $posts as $post ) {
		if ( function_exists( 'get_field' ) && get_field( 'homepage_featured', $post->ID ) ) {
			$featured_id = (int) $post->ID;
			break;
		}
	}
	if ( $featured_id <= 0 ) {
		$featured_id = (int) $posts[0]->ID;
	}

	$featured_card = null;
	$cards         = array();

	foreach ( $posts as $post ) {
		$id = (int) $post->ID;
		if ( $id === $featured_id ) {
			$featured_card = vip_transits_get_occasion_homepage_card_data( $id );
			continue;
		}
		if ( count( $cards ) < 4 ) {
			$cards[] = vip_transits_get_occasion_homepage_card_data( $id );
		}
	}

	return array(
		'featured' => $featured_card,
		'cards'    => $cards,
	);
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
 * @param int    $post_id Optional occasion post ID.
 * @return mixed
 */
function vip_transits_get_occasion_field( $field, $default = null, $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : vip_transits_occasion_post_id();
	if ( ! $post_id || ! function_exists( 'get_field' ) ) {
		return $default;
	}

	$value = get_field( $field, $post_id );
	return null !== $value && '' !== $value && array() !== $value ? $value : $default;
}

/**
 * Whether the occasion Page Banner fields should output (show_banner = Yes).
 *
 * @param int $post_id Occasion post ID.
 * @return bool
 */
function vip_transits_occasion_show_banner( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : vip_transits_occasion_post_id();
	if ( $post_id <= 0 ) {
		return false;
	}

	$show = (string) vip_transits_get_occasion_field( 'show_banner', 'No', $post_id );
	return in_array( strtolower( $show ), array( 'yes', 'y', '1' ), true );
}

/**
 * Occasion banner WYSIWYG (field: description).
 *
 * @param int $post_id Occasion post ID.
 * @return string
 */
function vip_transits_get_occasion_banner_description( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : vip_transits_occasion_post_id();
	if ( $post_id <= 0 ) {
		return '';
	}

	return trim( (string) vip_transits_get_occasion_field( 'description', '', $post_id ) );
}

/**
 * Black masthead from occasion ACF (description field, full WYSIWYG).
 *
 * @param int $post_id Occasion post ID.
 * @return bool True when banner markup was output.
 */
function vip_transits_render_occasion_banner( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : vip_transits_occasion_post_id();
	if ( $post_id <= 0 || ! vip_transits_occasion_show_banner( $post_id ) ) {
		return false;
	}

	$title = get_the_title( $post_id );
	$lead  = vip_transits_get_occasion_banner_description( $post_id );
	if ( $title === '' && $lead === '' ) {
		return false;
	}

	if ( function_exists( 'vip_transits_render_black_banner_lead' ) ) {
		vip_transits_render_black_banner_lead( $lead, 'vip-bg-black-section--masthead-occasion', $title );
		return true;
	}
	?>
	<header class="vip-bg-black-section vip-bg-black-section--masthead vip-bg-black-section--masthead-occasion">
		<div class="vip-bg-black-section__inner vip-content-container">
			<?php if ( $title !== '' ) : ?>
				<h1 class="vip-page__masthead-title"><?php echo esc_html( $title ); ?></h1>
			<?php endif; ?>
			<?php if ( $lead !== '' ) : ?>
				<div class="vip-page__masthead-lead"><?php echo wp_kses_post( $lead ); ?></div>
			<?php endif; ?>
		</div>
	</header>
	<?php
	return true;
}

/**
 * Enqueue banner styles on occasion singles when Page Banner is enabled.
 */
function vip_transits_enqueue_occasion_banner_assets() {
	if ( is_admin() || ! is_singular( 'vip_occasion' ) ) {
		return;
	}

	$post_id = (int) get_queried_object_id();
	if ( $post_id <= 0 || ! vip_transits_occasion_show_banner( $post_id ) ) {
		return;
	}

	$fleet_css = get_stylesheet_directory() . '/assets/css/vehicle-fleet.css';
	if ( file_exists( $fleet_css ) ) {
		wp_enqueue_style(
			'vip-fleet',
			get_stylesheet_directory_uri() . '/assets/css/vehicle-fleet.css',
			array( 'chld_thm_cfg_child' ),
			(string) filemtime( $fleet_css )
		);
	}

	if ( function_exists( 'vip_transits_page_content_assets' ) && file_exists( get_stylesheet_directory() . '/assets/css/vip-pages.css' ) ) {
		$assets = vip_transits_page_content_assets();
		wp_enqueue_style(
			'vip-pages',
			$assets['style'],
			array( 'chld_thm_cfg_child', 'vip-fleet' ),
			$assets['version']
		);
	}
}
add_action( 'wp_enqueue_scripts', 'vip_transits_enqueue_occasion_banner_assets', 25 );

