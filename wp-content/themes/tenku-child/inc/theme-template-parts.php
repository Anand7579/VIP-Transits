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
 * Mark footer as rendered and return markup once.
 *
 * @return string
 */
function vip_transits_take_footer_markup() {
	if ( ! empty( $GLOBALS['vip_transits_footer_rendered'] ) ) {
		return '';
	}

	$markup = vip_transits_get_footer_markup();
	if ( $markup === '' ) {
		return '';
	}

	$GLOBALS['vip_transits_footer_rendered'] = true;

	return $markup;
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

	if ( ! empty( $GLOBALS['vip_transits_footer_rendered'] ) ) {
		return '';
	}

	$has_footer_markup = str_contains( $block_content, 'vip-site-footer' )
		&& trim( wp_strip_all_tags( $block_content ) ) !== '';

	if ( $has_footer_markup ) {
		$GLOBALS['vip_transits_footer_rendered'] = true;
		return $block_content;
	}

	$fallback = vip_transits_take_footer_markup();
	if ( $fallback !== '' ) {
		return $fallback;
	}

	return $block_content;
}
add_filter( 'render_block', 'vip_transits_render_footer_template_part', 9, 2 );

/**
 * Append footer after the VIP Contact block (fixes customized page-contact templates with no footer).
 *
 * @param string $block_content Rendered HTML.
 * @param array  $block         Block instance.
 * @return string
 */
function vip_transits_append_footer_after_contact_block( $block_content, $block ) {
	if ( is_admin() || empty( $block['blockName'] ) || 'acf/vip-page-contact' !== $block['blockName'] ) {
		return $block_content;
	}

	if ( ! function_exists( 'vip_transits_is_contact_page' ) || ! vip_transits_is_contact_page() ) {
		return $block_content;
	}

	$footer = vip_transits_take_footer_markup();
	if ( $footer === '' ) {
		return $block_content;
	}

	return $block_content . $footer;
}
add_filter( 'render_block', 'vip_transits_append_footer_after_contact_block', 15, 2 );

/**
 * If the page template has no footer block (customized template), output VIP footer before </body>.
 */
function vip_transits_footer_wp_footer_fallback() {
	if ( is_admin() || ! empty( $GLOBALS['vip_transits_footer_rendered'] ) ) {
		return;
	}

	$fallback = vip_transits_take_footer_markup();
	if ( $fallback === '' ) {
		return;
	}

	echo $fallback; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_footer', 'vip_transits_footer_wp_footer_fallback', 5 );

/**
 * Restore page-contact template from theme file when the Site Editor copy dropped the footer.
 */
function vip_transits_restore_page_contact_template() {
	if ( ! function_exists( 'get_block_template' ) ) {
		return;
	}

	$theme_slug = get_stylesheet();
	$template   = get_block_template( $theme_slug . '//page-contact', 'wp_template' );

	if ( ! $template || empty( $template->wp_id ) ) {
		return;
	}

	$db_content = (string) get_post_field( 'post_content', (int) $template->wp_id );
	if ( str_contains( $db_content, '"slug":"footer"' ) ) {
		return;
	}

	$theme_path = get_stylesheet_directory() . '/templates/page-contact.html';
	if ( ! is_readable( $theme_path ) ) {
		return;
	}

	$theme_content = (string) file_get_contents( $theme_path );
	if ( $theme_content === '' ) {
		return;
	}

	wp_update_post(
		array(
			'ID'           => (int) $template->wp_id,
			'post_content' => $theme_content,
		)
	);
}
add_action( 'init', 'vip_transits_restore_page_contact_template', 19 );

/**
 * Append footer block to VIP templates when missing (Site Editor customized copies).
 */
function vip_transits_ensure_vip_page_templates_footer() {
	if ( ! function_exists( 'get_block_template' ) ) {
		return;
	}

	$footer_block = '<!-- wp:template-part {"slug":"footer","theme":"tenku-child","tagName":"footer"} /-->';
	$theme_slug   = get_stylesheet();
	$templates    = array(
		'page-about',
		'page-occasion',
		'single-vip_occasion',
	);

	foreach ( $templates as $template_slug ) {
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
	}
}
add_action( 'init', 'vip_transits_ensure_vip_page_templates_footer', 20 );

/**
 * Force footer on VIP Contact / About when the active template omits it.
 */
function vip_transits_footer_on_vip_pages_fallback() {
	if ( is_admin() || ! empty( $GLOBALS['vip_transits_footer_rendered'] ) ) {
		return;
	}

	if ( ! is_page() || ! function_exists( 'vip_transits_page_uses_vip_template' ) ) {
		return;
	}

	$post_id = (int) get_queried_object_id();
	if ( ! $post_id ) {
		return;
	}

	$is_vip_page = vip_transits_page_uses_vip_template( $post_id, 'contact' )
		|| vip_transits_page_uses_vip_template( $post_id, 'about' );

	if ( ! $is_vip_page ) {
		return;
	}

	$fallback = vip_transits_take_footer_markup();
	if ( $fallback === '' ) {
		return;
	}

	echo $fallback; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_footer', 'vip_transits_footer_on_vip_pages_fallback', 8 );
