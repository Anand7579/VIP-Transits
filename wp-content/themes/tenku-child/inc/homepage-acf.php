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
 * Import all field-group JSON files from the child theme into the database.
 *
 * @return array{ok:bool,message:string,titles:string[]}
 */
function vip_transits_import_acf_json_field_groups() {
	if ( ! function_exists( 'acf_import_field_group' ) ) {
		return array(
			'ok'      => false,
			'message' => __( 'ACF Pro is not active.', 'tenku-child' ),
			'titles'  => array(),
		);
	}

	$dir = vip_transits_acf_json_dir();
	if ( ! is_dir( $dir ) || ! is_readable( $dir ) ) {
		return array(
			'ok'      => false,
			'message' => __( 'acf-json folder is missing or not readable in the child theme.', 'tenku-child' ),
			'titles'  => array(),
		);
	}

	// Prevent JSON files being overwritten while importing.
	acf_update_setting( 'json', false );

	$titles = array();
	$files  = glob( $dir . '/*.json' );
	if ( ! $files ) {
		acf_update_setting( 'json', true );
		return array(
			'ok'      => false,
			'message' => __( 'No JSON field group files found.', 'tenku-child' ),
			'titles'  => array(),
		);
	}

	foreach ( $files as $file ) {
		$json = json_decode( (string) file_get_contents( $file ), true );
		if ( ! is_array( $json ) || empty( $json['key'] ) || empty( $json['fields'] ) ) {
			continue;
		}

		$existing = function_exists( 'acf_get_field_group' ) ? acf_get_field_group( $json['key'] ) : null;
		if ( is_array( $existing ) && ! empty( $existing['ID'] ) ) {
			$json['ID'] = (int) $existing['ID'];
		}

		$result = acf_import_field_group( $json );
		if ( is_array( $result ) && ! empty( $result['title'] ) ) {
			$titles[] = (string) $result['title'];
		}
	}

	acf_update_setting( 'json', true );

	if ( function_exists( 'acf_reset_local' ) ) {
		acf_reset_local();
	}

	if ( empty( $titles ) ) {
		return array(
			'ok'      => false,
			'message' => __( 'Import failed — check JSON files in acf-json.', 'tenku-child' ),
			'titles'  => array(),
		);
	}

	return array(
		'ok'      => true,
		'message' => sprintf(
			/* translators: %s: comma-separated field group titles */
			__( 'Imported: %s', 'tenku-child' ),
			implode( ', ', $titles )
		),
		'titles'  => $titles,
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
 * Homepage section CSS (wp_enqueue_scripts — not enqueue_assets, which runs too late).
 */
function vip_transits_enqueue_vip_home_assets() {
	if ( ! vip_transits_should_enqueue_vip_home_assets() ) {
		return;
	}

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
