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
 * Load ACF JSON from the child theme.
 *
 * @param array $paths Load paths.
 * @return array
 */
function vip_transits_acf_json_load_paths( $paths ) {
	$paths[] = get_stylesheet_directory() . '/acf-json';
	return $paths;
}
add_filter( 'acf/settings/load_json', 'vip_transits_acf_json_load_paths' );

/**
 * Save synced field groups to the child theme.
 *
 * @param string $path Save path.
 * @return string
 */
function vip_transits_acf_json_save_path( $path ) {
	return get_stylesheet_directory() . '/acf-json';
}
add_filter( 'acf/settings/save_json', 'vip_transits_acf_json_save_path' );

/**
 * Register the VIP home ACF block (PHP only — no block.json).
 */
function vip_transits_register_home_block() {
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	$block_dir = get_stylesheet_directory() . '/blocks/vip-home';

	acf_register_block_type(
		array(
			'name'            => 'vip-home',
			'title'           => __( 'VIP Home sections', 'tenku-child' ),
			'description'     => __( 'Homepage sections from ACF on the static front page.', 'tenku-child' ),
			'category'        => 'layout',
			'icon'            => 'admin-site',
			'keywords'        => array( 'home', 'vip', 'acf' ),
			'render_template' => $block_dir . '/render.php',
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
 * Homepage section CSS on the front page (wp_enqueue_scripts — not enqueue_assets,
 * which runs too late after wp_head when the block renders).
 */
function vip_transits_enqueue_vip_home_assets() {
	if ( is_admin() || ! is_front_page() ) {
		return;
	}

	$version = wp_get_theme()->get( 'Version' );
	$uri     = get_stylesheet_directory_uri() . '/blocks/vip-home';

	wp_enqueue_style(
		'vip-home-block',
		$uri . '/style.css',
		array(),
		$version
	);

	wp_enqueue_script(
		'vip-home-faq',
		$uri . '/faq.js',
		array(),
		$version,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
}
add_action( 'wp_enqueue_scripts', 'vip_transits_enqueue_vip_home_assets', 20 );

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
