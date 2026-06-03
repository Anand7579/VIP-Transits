<?php
/**
 * ACF Pro: register VIP home block + load field groups from acf-json.
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Front page post ID (static homepage).
 *
 * @return int
 */
function vip_transits_home_page_id() {
	$page_on_front = (int) get_option( 'page_on_front' );
	if ( $page_on_front > 0 ) {
		return $page_on_front;
	}

	$home = get_page_by_path( 'home' );
	if ( $home instanceof WP_Post ) {
		return (int) $home->ID;
	}

	return (int) get_queried_object_id();
}

/**
 * Normalize one vehicle category card from ACF row data.
 *
 * @param array<string, mixed> $row ACF repeater row (title, image, filter_slug).
 * @return array<string, string>|null
 */
function vip_transits_normalize_vehicle_category_row( $row ) {
	if ( ! is_array( $row ) ) {
		return null;
	}

	$title       = isset( $row['title'] ) ? (string) $row['title'] : '';
	$image       = isset( $row['image'] ) ? $row['image'] : null;
	$filter_slug = isset( $row['filter_slug'] ) ? (string) $row['filter_slug'] : '';

	if ( ! $title && ( ! is_array( $image ) || empty( $image['url'] ) ) ) {
		return null;
	}

	$slug = function_exists( 'vip_transits_category_filter_slug' )
		? vip_transits_category_filter_slug( $title, $filter_slug )
		: sanitize_title( $title );

	return array(
		'title'     => $title,
		'slug'      => $slug,
		'image_url' => is_array( $image ) && ! empty( $image['url'] ) ? (string) $image['url'] : '',
		'image_alt' => is_array( $image ) && ! empty( $image['alt'] ) ? (string) $image['alt'] : $title,
	);
}

/**
 * Vehicle category cards from the homepage ACF flexible content (shared with fleet archive).
 *
 * @return array<int, array<string, string>>
 */
function vip_transits_get_homepage_vehicle_categories() {
	$categories = array();
	$home_id    = vip_transits_home_page_id();

	if ( ! $home_id || ! function_exists( 'get_field' ) ) {
		return $categories;
	}

	$sections = get_field( 'sections', $home_id );
	if ( ! is_array( $sections ) ) {
		return $categories;
	}

	foreach ( $sections as $section ) {
		if ( empty( $section['acf_fc_layout'] ) || 'vehicle_categories' !== $section['acf_fc_layout'] ) {
			continue;
		}

		if ( empty( $section['categories'] ) || ! is_array( $section['categories'] ) ) {
			break;
		}

		foreach ( $section['categories'] as $row ) {
			$normalized = vip_transits_normalize_vehicle_category_row( $row );
			if ( $normalized ) {
				$categories[] = $normalized;
			}
		}

		break;
	}

	return $categories;
}

/**
 * Child theme folder for ACF local JSON (requires tenku-child active).
 *
 * @return string Absolute path with trailing slash.
 */
function vip_transits_acf_json_dir() {
	return trailingslashit( get_stylesheet_directory() ) . 'acf-json';
}

/**
 * Register load/save paths early (ACF 5 + 6).
 */
function vip_transits_acf_json_setup() {
	if ( ! function_exists( 'acf_update_setting' ) ) {
		return;
	}

	$dir = vip_transits_acf_json_dir();
	acf_update_setting( 'save_json', $dir );
	acf_append_setting( 'load_json', $dir );
}
add_action( 'acf/init', 'vip_transits_acf_json_setup', 1 );

/**
 * @param array $paths Load paths.
 * @return array
 */
function vip_transits_acf_json_load_paths( $paths ) {
	$paths[] = vip_transits_acf_json_dir();
	return array_values( array_unique( array_filter( (array) $paths ) ) );
}
add_filter( 'acf/settings/load_json', 'vip_transits_acf_json_load_paths' );
add_filter( 'acf/json/load_paths', 'vip_transits_acf_json_load_paths' );

/**
 * @param string $path Save path (legacy filter).
 * @return string
 */
function vip_transits_acf_json_save_path( $path ) {
	return vip_transits_acf_json_dir();
}
add_filter( 'acf/settings/save_json', 'vip_transits_acf_json_save_path' );

/**
 * @param array $paths Save paths (ACF 6+).
 * @param array $post  Field group array.
 * @return array
 */
function vip_transits_acf_json_save_paths( $paths, $post = array() ) {
	unset( $post );
	$paths[] = vip_transits_acf_json_dir();
	return array_values( array_unique( array_filter( (array) $paths ) ) );
}
add_filter( 'acf/json/save_paths', 'vip_transits_acf_json_save_paths', 10, 2 );

/**
 * All database post IDs for an ACF field group key (post_name).
 *
 * @param string $key Field group key, e.g. group_vip_vehicle.
 * @return int[]
 */
function vip_transits_get_field_group_post_ids_by_key( $key ) {
	$key = sanitize_key( (string) $key );
	if ( $key === '' ) {
		return array();
	}

	$ids = get_posts(
		array(
			'post_type'              => 'acf-field-group',
			'posts_per_page'         => -1,
			'post_status'            => array( 'publish', 'acf-disabled', 'trash', 'draft' ),
			'name'                   => $key,
			'fields'                 => 'ids',
			'orderby'                => 'ID',
			'order'                  => 'ASC',
			'suppress_filters'       => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		)
	);

	return array_values( array_filter( array_map( 'intval', (array) $ids ) ) );
}

/**
 * Delete extra database copies of a field group, keeping one post ID.
 *
 * @param string $key     Field group key.
 * @param int    $keep_id Post ID to keep (0 = keep highest ID).
 * @return int Number of posts removed.
 */
function vip_transits_remove_duplicate_acf_field_groups( $key, $keep_id = 0 ) {
	$ids = vip_transits_get_field_group_post_ids_by_key( $key );
	if ( count( $ids ) <= 1 ) {
		return 0;
	}

	$keep_id = (int) $keep_id;
	if ( $keep_id > 0 && in_array( $keep_id, $ids, true ) ) {
		$keep = $keep_id;
	} else {
		$keep = max( $ids );
	}

	$removed = 0;
	foreach ( $ids as $id ) {
		if ( (int) $id === (int) $keep ) {
			continue;
		}
		if ( function_exists( 'acf_delete_field_group' ) ) {
			acf_delete_field_group( $id );
			++$removed;
		}
	}

	vip_transits_flush_acf_field_group_cache();

	return $removed;
}

/**
 * Clear ACF field group caches after import/dedupe.
 */
function vip_transits_flush_acf_field_group_cache() {
	if ( function_exists( 'acf_reset_local' ) ) {
		acf_reset_local();
	}

	if ( function_exists( 'acf_get_store' ) ) {
		$store = acf_get_store( 'field-groups' );
		if ( $store ) {
			$store->reset();
		}
	}

	if ( function_exists( 'acf_cache_key' ) ) {
		wp_cache_delete( acf_cache_key( 'acf_get_field_group_posts' ), 'acf' );
	}
}

/**
 * Match database modified time to JSON so ACF stops showing "Sync available".
 *
 * @param int $post_id  Field group post ID.
 * @param int $modified Unix timestamp from JSON.
 */
function vip_transits_touch_field_group_modified( $post_id, $modified ) {
	$post_id  = (int) $post_id;
	$modified = (int) $modified;
	if ( $post_id <= 0 || $modified <= 0 ) {
		return;
	}

	$date = gmdate( 'Y-m-d H:i:s', $modified );
	wp_update_post(
		array(
			'ID'                => $post_id,
			'post_modified'     => $date,
			'post_modified_gmt' => $date,
		)
	);
}

/**
 * Import one field group array into the database (ACF 6+ or legacy API).
 *
 * @param array<string, mixed> $json Field group export array.
 * @return array<string, mixed>|false
 */
function vip_transits_import_acf_field_group_array( $json ) {
	if ( function_exists( 'acf_import_internal_post_type' ) ) {
		return acf_import_internal_post_type( $json, 'acf-field-group' );
	}

	if ( function_exists( 'acf_import_field_group' ) ) {
		return acf_import_field_group( $json );
	}

	return false;
}

/**
 * Import all field-group JSON files from the child theme into the database.
 *
 * Removes duplicate DB posts per key, imports from JSON, and aligns modified times.
 *
 * @return array{ok:bool,message:string,titles:string[],removed:int}
 */
function vip_transits_import_acf_json_field_groups() {
	if ( ! function_exists( 'acf_import_field_group' ) ) {
		return array(
			'ok'      => false,
			'message' => __( 'ACF Pro is not active.', 'tenku-child' ),
			'titles'  => array(),
			'removed' => 0,
		);
	}

	$dir = vip_transits_acf_json_dir();
	if ( ! is_dir( $dir ) || ! is_readable( $dir ) ) {
		return array(
			'ok'      => false,
			'message' => __( 'acf-json folder is missing or not readable in the child theme.', 'tenku-child' ),
			'titles'  => array(),
			'removed' => 0,
		);
	}

	// Prevent JSON files being overwritten while importing.
	acf_update_setting( 'json', false );

	$titles  = array();
	$removed = 0;
	$files   = glob( $dir . '/*.json' );
	if ( ! $files ) {
		acf_update_setting( 'json', true );
		return array(
			'ok'      => false,
			'message' => __( 'No JSON field group files found.', 'tenku-child' ),
			'titles'  => array(),
			'removed' => 0,
		);
	}

	foreach ( $files as $file ) {
		$json = json_decode( (string) file_get_contents( $file ), true );
		if ( ! is_array( $json ) || empty( $json['key'] ) || empty( $json['fields'] ) ) {
			continue;
		}

		$key = (string) $json['key'];

		// Drop stray duplicate database posts before import (same post_name / key).
		$removed += vip_transits_remove_duplicate_acf_field_groups( $key );

		$existing_ids = vip_transits_get_field_group_post_ids_by_key( $key );
		$keep_id      = $existing_ids ? (int) max( $existing_ids ) : 0;
		if ( $keep_id > 0 ) {
			$json['ID'] = $keep_id;
		} else {
			unset( $json['ID'] );
		}

		$result = vip_transits_import_acf_field_group_array( $json );
		if ( is_array( $result ) && ! empty( $result['title'] ) ) {
			$titles[] = (string) $result['title'];

			if ( ! empty( $result['ID'] ) ) {
				$modified = isset( $json['modified'] ) ? (int) $json['modified'] : time();
				vip_transits_touch_field_group_modified( (int) $result['ID'], $modified );
			}
		}
	}

	acf_update_setting( 'json', true );
	vip_transits_flush_acf_field_group_cache();

	if ( empty( $titles ) ) {
		return array(
			'ok'      => false,
			'message' => __( 'Import failed — check JSON files in acf-json.', 'tenku-child' ),
			'titles'  => array(),
			'removed' => $removed,
		);
	}

	$message = sprintf(
		/* translators: %s: comma-separated field group titles */
		__( 'Imported: %s', 'tenku-child' ),
		implode( ', ', $titles )
	);

	if ( $removed > 0 ) {
		$message .= ' ' . sprintf(
			/* translators: %d: number of duplicate field groups removed */
			_n(
				'Removed %d duplicate field group from the database.',
				'Removed %d duplicate field groups from the database.',
				$removed,
				'tenku-child'
			),
			$removed
		);
	}

	return array(
		'ok'      => true,
		'message' => $message,
		'titles'  => $titles,
		'removed' => $removed,
	);
}

/**
 * Handle one-click ACF import from Settings → VIP Transits.
 */
function vip_transits_handle_acf_json_sync_request() {
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( empty( $_POST['vip_acf_sync_json'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}

	check_admin_referer( 'vip_acf_sync_json' );

	$result = vip_transits_import_acf_json_field_groups();
	set_transient( 'vip_acf_sync_notice', $result, 60 );

	wp_safe_redirect( admin_url( 'options-general.php?page=vip-transits-settings&acf_synced=1' ) );
	exit;
}
add_action( 'admin_init', 'vip_transits_handle_acf_json_sync_request' );

/**
 * Show result after manual ACF JSON import.
 */
function vip_transits_acf_sync_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) || empty( $_GET['acf_synced'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	$result = get_transient( 'vip_acf_sync_notice' );
	if ( ! is_array( $result ) ) {
		return;
	}

	delete_transient( 'vip_acf_sync_notice' );
	$class = ! empty( $result['ok'] ) ? 'notice-success' : 'notice-error';
	echo '<div class="notice ' . esc_attr( $class ) . ' is-dismissible"><p>' . esc_html( (string) $result['message'] ) . '</p></div>';
}
add_action( 'admin_notices', 'vip_transits_acf_sync_admin_notice' );

/**
 * Register the VIP home ACF block (PHP only — no block.json).
 */
function vip_transits_register_home_block() {
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	$block_dir = get_stylesheet_directory() . '/blocks/vip-home';

	$style_path = $block_dir . '/style.css';
	$style_uri  = get_stylesheet_directory_uri() . '/blocks/vip-home/style.css';
	$style_ver  = file_exists( $style_path ) ? (string) filemtime( $style_path ) : wp_get_theme()->get( 'Version' );

	acf_register_block_type(
		array(
			'name'            => 'vip-home',
			'title'           => __( 'VIP Home sections', 'tenku-child' ),
			'description'     => __( 'Homepage sections from ACF on the static front page.', 'tenku-child' ),
			'category'        => 'layout',
			'icon'            => 'admin-site',
			'keywords'        => array( 'home', 'vip', 'acf' ),
			'render_template' => $block_dir . '/render.php',
			'enqueue_style'   => $style_uri . '?ver=' . rawurlencode( $style_ver ),
			'mode'            => 'preview',
			'supports'        => array(
				'align'  => array( 'wide', 'full' ),
				'anchor' => true,
			),
		)
	);
}
add_action( 'acf/init', 'vip_transits_register_home_block', 5 );

/**
 * Whether VIP home block CSS/JS should load on this request.
 *
 * @return bool
 */
function vip_transits_should_enqueue_vip_home_assets() {
	if ( is_admin() ) {
		return false;
	}

	if ( is_front_page() ) {
		return true;
	}

	$post_id = get_queried_object_id();
	if ( $post_id && is_singular() && function_exists( 'has_block' ) && has_block( 'acf/vip-home', $post_id ) ) {
		return true;
	}

	return false;
}

/**
 * FAQ accordion assets (homepage + occasion detail).
 */
function vip_transits_enqueue_faq_section_assets() {
	$dir  = get_stylesheet_directory() . '/blocks/vip-home';
	$uri  = get_stylesheet_directory_uri() . '/blocks/vip-home';
	$path = $dir . '/style.css';
	$ver  = file_exists( $path ) ? (string) filemtime( $path ) : wp_get_theme()->get( 'Version' );

	wp_enqueue_style(
		'vip-home-block',
		$uri . '/style.css',
		array( 'chld_thm_cfg_child' ),
		$ver
	);

	wp_enqueue_script(
		'vip-home-faq',
		$uri . '/faq.js',
		array(),
		$ver,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}

/**
 * Homepage section CSS (wp_enqueue_scripts — not enqueue_assets, which runs too late).
 */
function vip_transits_enqueue_vip_home_assets() {
	if ( ! vip_transits_should_enqueue_vip_home_assets() ) {
		return;
	}

	vip_transits_enqueue_faq_section_assets();
}
add_action( 'wp_enqueue_scripts', 'vip_transits_enqueue_vip_home_assets', 20 );

/**
 * VIP home styles in the block/site editor so sections match the front end.
 */
function vip_transits_enqueue_vip_home_editor_assets() {
	$path = get_stylesheet_directory() . '/blocks/vip-home/style.css';
	if ( ! file_exists( $path ) ) {
		return;
	}

	wp_enqueue_style(
		'vip-home-block-editor',
		get_stylesheet_directory_uri() . '/blocks/vip-home/style.css',
		array(),
		(string) filemtime( $path )
	);
}
add_action( 'enqueue_block_editor_assets', 'vip_transits_enqueue_vip_home_editor_assets' );

/**
 * Show homepage fields when editing the static front page OR the page with slug "home".
 *
 * @param bool  $match  Whether the rule matched.
 * @param array $rule   Location rule.
 * @param array $screen Screen args.
 * @return bool
 */
function vip_transits_acf_match_front_page_type( $match, $rule, $screen ) {
	if ( 'page_type' !== $rule['param'] || 'front_page' !== $rule['value'] || '==' !== $rule['operator'] ) {
		return $match;
	}

	if ( empty( $screen['post_id'] ) ) {
		return $match;
	}

	$post_id = (int) $screen['post_id'];
	$front   = (int) get_option( 'page_on_front' );

	if ( $front && $post_id === $front ) {
		return true;
	}

	$post = get_post( $post_id );
	if ( $post && 'page' === $post->post_type && 'home' === $post->post_name ) {
		return true;
	}

	return $match;
}
add_filter( 'acf/location/rule_match/page_type', 'vip_transits_acf_match_front_page_type', 10, 3 );

/**
 * Remind editors where homepage content is managed.
 */
function vip_transits_home_edit_notice() {
	if ( ! function_exists( 'acf' ) ) {
		return;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'page' !== $screen->id || ! isset( $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	$post_id = (int) $_GET['post']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$post    = get_post( $post_id );
	if ( ! $post || 'home' !== $post->post_name ) {
		return;
	}

	$front = (int) get_option( 'page_on_front' );
	?>
	<div class="notice notice-info">
		<p>
			<strong><?php esc_html_e( 'VIP homepage content', 'tenku-child' ); ?></strong>
			<?php esc_html_e( 'Scroll below the block editor to the "VIP Homepage" box and use Sections > Add section. The live site uses that data (not the blocks above).', 'tenku-child' ); ?>
		</p>
		<?php if ( ! $front ) : ?>
			<p>
				<?php
				printf(
					/* translators: %s: settings URL */
					esc_html__( 'Also set Settings > Reading > "Your homepage displays" to this page: %s', 'tenku-child' ),
					'<a href="' . esc_url( admin_url( 'options-reading.php' ) ) . '">' . esc_html__( 'Reading settings', 'tenku-child' ) . '</a>'
				);
				?>
			</p>
		<?php elseif ( $front !== $post_id ) : ?>
			<p>
				<?php esc_html_e( 'This page is not set as the site homepage yet. Choose it under Settings > Reading.', 'tenku-child' ); ?>
			</p>
		<?php endif; ?>
	</div>
	<?php
}
add_action( 'admin_notices', 'vip_transits_home_edit_notice' );

/**
 * Admin notice when ACF Pro is inactive.
 */
function vip_transits_acf_missing_notice() {
	if ( function_exists( 'acf' ) || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-error"><p>';
	echo esc_html__( 'VIP homepage requires Advanced Custom Fields PRO to be installed and activated.', 'tenku-child' );
	echo '</p></div>';
}
add_action( 'admin_notices', 'vip_transits_acf_missing_notice' );
