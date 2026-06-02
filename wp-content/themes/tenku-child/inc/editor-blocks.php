<?php
/**
 * Register VIP layout blocks (block.json in blocks/*).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Block slugs registered from blocks/{slug}/block.json.
 *
 * @return string[]
 */
function vip_transits_get_editor_block_slugs() {
	return array(
		'vip-bg-black-section',
		'vip-border-section',
	);
}

/**
 * Output inner block HTML for dynamic blocks that use InnerBlocks.
 *
 * @param string        $content Saved inner HTML passed to render.php.
 * @param WP_Block|null $block   Block instance.
 * @return string
 */
function vip_transits_render_block_inner_html( $content, $block ) {
	$html = is_string( $content ) ? trim( $content ) : '';

	if ( '' !== $html ) {
		return $html;
	}

	if ( ! $block instanceof WP_Block || empty( $block->inner_blocks ) ) {
		return '';
	}

	$rendered = '';
	foreach ( $block->inner_blocks as $inner_block ) {
		$rendered .= $inner_block->render();
	}

	return $rendered;
}

/**
 * Register block types from theme block.json files.
 */
function vip_transits_register_editor_blocks() {
	if ( ! function_exists( 'register_block_type' ) ) {
		return;
	}

	$base = get_stylesheet_directory() . '/blocks';

	foreach ( vip_transits_get_editor_block_slugs() as $slug ) {
		$dir = $base . '/' . $slug;
		if ( is_readable( $dir . '/block.json' ) ) {
			register_block_type( $dir );
		}
	}
}
add_action( 'init', 'vip_transits_register_editor_blocks' );

/**
 * Enqueue block styles when ACF page templates render section markup (no block in post content).
 */
function vip_transits_enqueue_editor_block_styles() {
	$deps = array( 'chld_thm_cfg_child' );

	foreach ( vip_transits_get_editor_block_slugs() as $slug ) {
		$path = get_stylesheet_directory() . '/blocks/' . $slug . '/style.css';
		if ( ! file_exists( $path ) ) {
			continue;
		}

		wp_enqueue_style(
			'vip-block-' . $slug,
			get_stylesheet_directory_uri() . '/blocks/' . $slug . '/style.css',
			$deps,
			(string) filemtime( $path )
		);
		$deps = array( 'vip-block-' . $slug );
	}
}

/**
 * @param array $categories Block categories.
 * @return array
 */
function vip_transits_editor_block_categories( $categories ) {
	$categories[] = array(
		'slug'  => 'vip-transits',
		'title' => __( 'VIP Transits', 'tenku-child' ),
		'icon'  => null,
	);

	return $categories;
}
add_filter( 'block_categories_all', 'vip_transits_editor_block_categories', 10, 1 );
add_action( 'enqueue_block_assets', 'vip_transits_enqueue_editor_block_styles' );
