<?php
/**
 * ACF blocks for article (post) listing and single templates.
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array{list_style:string,single_style:string,list_version:string,single_version:string}
 */
function vip_transits_article_block_assets() {
	$theme_dir = get_stylesheet_directory();
	$theme_ver = wp_get_theme()->get( 'Version' );

	$list_path   = $theme_dir . '/assets/css/article-list.css';
	$single_path = $theme_dir . '/assets/css/article-single.css';

	$list_ver   = file_exists( $list_path ) ? (string) filemtime( $list_path ) : $theme_ver;
	$single_ver = file_exists( $single_path ) ? (string) filemtime( $single_path ) : $theme_ver;

	return array(
		'list_style'     => get_stylesheet_directory_uri() . '/assets/css/article-list.css',
		'single_style'   => get_stylesheet_directory_uri() . '/assets/css/article-single.css',
		'list_version'   => $list_ver,
		'single_version' => $single_ver,
	);
}

/**
 * Register article ACF blocks.
 */
function vip_transits_register_article_blocks() {
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	$dir = get_stylesheet_directory();

	acf_register_block_type(
		array(
			'name'            => 'vip-article-archive',
			'title'           => __( 'Article listing', 'tenku-child' ),
			'description'     => __( 'Blog / article grid with pagination.', 'tenku-child' ),
			'category'        => 'layout',
			'icon'            => 'admin-post',
			'keywords'        => array( 'blog', 'articles', 'news', 'archive' ),
			'render_template' => $dir . '/blocks/vip-article-archive/render.php',
			'enqueue_style'   => get_stylesheet_directory_uri() . '/assets/css/article-list.css',
			'mode'            => 'preview',
			'supports'        => array(
				'align'  => array( 'wide', 'full' ),
				'anchor' => true,
				'mode'   => false,
			),
		)
	);

	acf_register_block_type(
		array(
			'name'            => 'vip-article-single',
			'title'           => __( 'Article detail', 'tenku-child' ),
			'description'     => __( 'Single blog post layout.', 'tenku-child' ),
			'category'        => 'layout',
			'icon'            => 'text-page',
			'keywords'        => array( 'blog', 'article', 'post', 'single' ),
			'render_template' => $dir . '/blocks/vip-article-single/render.php',
			'enqueue_style'   => get_stylesheet_directory_uri() . '/assets/css/article-single.css',
			'mode'            => 'preview',
			'supports'        => array(
				'align'  => array( 'wide', 'full' ),
				'anchor' => true,
				'mode'   => false,
			),
		)
	);
}
add_action( 'acf/init', 'vip_transits_register_article_blocks', 5 );

/**
 * Enqueue article listing / single styles on blog templates.
 */
function vip_transits_enqueue_article_block_assets() {
	$is_listing = is_home() || is_category() || is_tag() || is_author() || is_date() || is_archive();
	$is_single  = is_singular( 'post' );

	if ( ! $is_listing && ! $is_single ) {
		return;
	}

	$assets = vip_transits_article_block_assets();

	if ( $is_listing ) {
		wp_enqueue_style(
			'vip-article-list',
			$assets['list_style'],
			array( 'chld_thm_cfg_child', 'chld_thm_cfg_parent' ),
			$assets['list_version']
		);
	}

	if ( $is_single ) {
		wp_enqueue_style(
			'vip-article-single',
			$assets['single_style'],
			array( 'chld_thm_cfg_child', 'chld_thm_cfg_parent' ),
			$assets['single_version']
		);

		$js_path = get_stylesheet_directory() . '/assets/js/article-single.js';
		$js_ver  = file_exists( $js_path ) ? (string) filemtime( $js_path ) : $assets['single_version'];
		wp_enqueue_script(
			'vip-article-single',
			get_stylesheet_directory_uri() . '/assets/js/article-single.js',
			array(),
			$js_ver,
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'vip_transits_enqueue_article_block_assets', 20 );
