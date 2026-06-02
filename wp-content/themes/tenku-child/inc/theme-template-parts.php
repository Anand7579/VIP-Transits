<?php
/**
 * Header/footer template parts: fallbacks when Site Editor part or template is empty.
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render VIP footer markup from the theme pattern file.
 *
 * @return string
 */
function vip_transits_get_footer_markup() {
	static $cached = null;

	if ( null !== $cached ) {
		return $cached;
	}

	$cached = '';
	$path   = get_stylesheet_directory() . '/patterns/footer.php';

	if ( ! is_readable( $path ) ) {
		return $cached;
	}

	ob_start();
	include $path;
	$raw = (string) ob_get_clean();

	if ( $raw !== '' && function_exists( 'do_blocks' ) ) {
		$cached = (string) do_blocks( $raw );
	}

	return $cached;
}

/**
 * Footer template part: use theme markup when the customized part is empty.
 *
 * @param string $block_content Rendered HTML.
 * @param array  $block         Block instance.
 * @return string
 */
function vip_transits_render_footer_template_part( $block_content, $block ) {
	if ( empty( $block['blockName'] ) || 'core/template-part' !== $block['blockName'] ) {
		return $block_content;
	}

	if ( empty( $block['attrs']['slug'] ) || 'footer' !== $block['attrs']['slug'] ) {
		return $block_content;
	}

	$theme = isset( $block['attrs']['theme'] ) ? (string) $block['attrs']['theme'] : '';
	if ( $theme !== '' && $theme !== get_stylesheet() ) {
		return $block_content;
	}

	if ( str_contains( $block_content, 'vip-site-footer' ) ) {
		$GLOBALS['vip_transits_footer_rendered'] = true;
		return $block_content;
	}

	$fallback = vip_transits_get_footer_markup();
	if ( $fallback !== '' ) {
		$GLOBALS['vip_transits_footer_rendered'] = true;
		return $fallback;
	}

	return $block_content;
}
add_filter( 'render_block', 'vip_transits_render_footer_template_part', 9, 2 );

/**
 * If the page template has no footer block (customized template), output VIP footer before </body>.
 */
function vip_transits_footer_wp_footer_fallback() {
	if ( is_admin() || ! empty( $GLOBALS['vip_transits_footer_rendered'] ) ) {
		return;
	}

	$fallback = vip_transits_get_footer_markup();
	if ( $fallback === '' ) {
		return;
	}

	$GLOBALS['vip_transits_footer_rendered'] = true;
	echo $fallback; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_footer', 'vip_transits_footer_wp_footer_fallback', 5 );

/**
 * Ensure customized VIP About/Contact templates include a footer block (one-time per site).
 */
function vip_transits_sync_vip_page_templates_footer() {
	if ( get_option( 'vip_transits_vip_templates_footer_synced' ) ) {
		return;
	}

	if ( ! function_exists( 'get_block_template' ) ) {
		return;
	}

	$footer_block = '<!-- wp:template-part {"slug":"footer","theme":"tenku-child","tagName":"footer"} /-->';
	$theme_slug   = get_stylesheet();
	$updated      = false;

	foreach ( array( 'page-contact', 'page-about' ) as $template_slug ) {
		$template = get_block_template( $theme_slug . '//' . $template_slug, 'wp_template' );

		if ( ! $template || empty( $template->wp_id ) ) {
			continue;
		}

		$content = (string) get_post_field( 'post_content', (int) $template->wp_id );
		if ( $content === '' || str_contains( $content, '"slug":"footer"' ) ) {
			continue;
		}

		wp_update_post(
			array(
				'ID'           => (int) $template->wp_id,
				'post_content' => rtrim( $content ) . "\n\n" . $footer_block,
			)
		);
		$updated = true;
	}

	update_option( 'vip_transits_vip_templates_footer_synced', 1, false );

	if ( $updated && function_exists( 'wp_cache_flush' ) ) {
		wp_cache_flush();
	}
}
add_action( 'init', 'vip_transits_sync_vip_page_templates_footer', 20 );
