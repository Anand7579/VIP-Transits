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
 * Plain HTML footer when block rendering fails (no do_blocks / template-part recursion).
 *
 * @return string
 */
function vip_transits_footer_static_html() {
	$footer_year = gmdate( 'Y' );
	$home_url    = home_url( '/' );
	$logo_html   = '';

	if ( function_exists( 'get_custom_logo' ) ) {
		$logo_html = (string) get_custom_logo();
	}

	if ( $logo_html === '' && function_exists( 'the_custom_logo' ) ) {
		ob_start();
		the_custom_logo();
		$logo_html = (string) ob_get_clean();
	}

	$legal = sprintf(
		/* translators: 1: year, 2: home link, 3: privacy link, 4: terms link */
		__( 'Copyright © %1$s | %2$s | %3$s | %4$s', 'tenku-child' ),
		esc_html( $footer_year ),
		'<a href="' . esc_url( $home_url ) . '">viptransits.com</a>',
		'<a href="' . esc_url( home_url( '/privacy-policy/' ) ) . '">' . esc_html__( 'Privacy Policy', 'tenku-child' ) . '</a>',
		'<a href="' . esc_url( home_url( '/terms-and-conditions/' ) ) . '">' . esc_html__( 'Terms & Conditions', 'tenku-child' ) . '</a>'
	);

	ob_start();
	?>
<footer class="wp-block-group alignfull vip-site-footer">
	<div class="wp-block-group vip-site-footer__container vip-content-container">
		<div class="wp-block-group vip-site-footer__inner">
			<div class="wp-block-group vip-site-footer__main">
				<?php if ( $logo_html !== '' ) : ?>
					<div class="wp-block-group vip-site-footer__brand">
						<div class="vip-site-footer__logo"><?php echo $logo_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					</div>
				<?php endif; ?>
				<div class="wp-block-group vip-site-footer__content">
					<p class="vip-site-footer__desc"><?php esc_html_e( 'VIP Transits delivers a seamless luxury car rental experience across Dubai, offering elite vehicles on demand to your hotel, villa, or airport with just a simple WhatsApp request.', 'tenku-child' ); ?></p>
					<p class="vip-site-footer__legal"><?php echo wp_kses_post( $legal ); ?></p>
				</div>
			</div>
		</div>
	</div>
</footer>
	<?php
	return (string) ob_get_clean();
}

/**
 * Render VIP footer markup from the theme pattern file.
 *
 * @return string
 */
function vip_transits_get_footer_markup() {
	static $cached = null;

	if ( is_string( $cached ) && $cached !== '' ) {
		return $cached;
	}

	// Prevent render_block footer hooks from calling take_footer_markup() mid-do_blocks
	// (that sets the "rendered" flag and discards markup before it reaches the page).
	if ( ! empty( $GLOBALS['vip_transits_footer_building'] ) ) {
		return vip_transits_footer_static_html();
	}

	$GLOBALS['vip_transits_footer_building'] = true;

	$markup = '';
	$path   = get_stylesheet_directory() . '/patterns/footer.php';

	if ( is_readable( $path ) ) {
		ob_start();
		include $path;
		$raw = (string) ob_get_clean();

		if ( $raw !== '' && function_exists( 'do_blocks' ) ) {
			$markup = (string) do_blocks( $raw );
		}
	}

	if ( $markup === '' ) {
		$markup = vip_transits_footer_static_html();
	}

	unset( $GLOBALS['vip_transits_footer_building'] );

	if ( $markup !== '' ) {
		$cached = $markup;
	}

	return $markup;
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

	$has_footer_markup = str_contains( $block_content, 'vip-site-footer' )
		&& trim( wp_strip_all_tags( $block_content ) ) !== '';

	if ( $has_footer_markup ) {
		$GLOBALS['vip_transits_footer_rendered'] = true;
		return $block_content;
	}

	if ( ! empty( $GLOBALS['vip_transits_footer_rendered'] ) ) {
		// Flag set during a nested do_blocks() call — allow this template part to output.
		$GLOBALS['vip_transits_footer_rendered'] = false;
	}

	$fallback = vip_transits_get_footer_markup();
	if ( function_exists( 'vip_transits_footer_debug_log' ) ) {
		vip_transits_footer_debug_log(
			'template-part-footer',
			array(
				'in_len'          => strlen( (string) $block_content ),
				'fallback_len'    => strlen( $fallback ),
				'has_vip_class_in' => str_contains( (string) $block_content, 'vip-site-footer' ) ? 1 : 0,
				'footer_rendered' => ! empty( $GLOBALS['vip_transits_footer_rendered'] ) ? 1 : 0,
			)
		);
	}

	if ( $fallback !== '' ) {
		$GLOBALS['vip_transits_footer_rendered'] = true;
		return $fallback;
	}

	return $block_content;
}

/**
 * Output footer markup on the contact page (inline after the contact block).
 */
function vip_transits_echo_contact_page_footer() {
	if ( is_admin() ) {
		return;
	}

	$markup = vip_transits_get_footer_markup();
	if ( $markup === '' ) {
		echo "\n<!-- vip-footer:still-empty -->\n";
		return;
	}

	$GLOBALS['vip_transits_footer_rendered'] = true;
	echo $markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

add_filter( 'render_block', 'vip_transits_render_footer_template_part', 9, 2 );

/**
 * If the page template has no footer block (customized template), output VIP footer before </body>.
 *
 * This is the single, reliable footer guarantee for any front-end page (incl.
 * Contact). We intentionally do NOT inject the footer inside the contact block's
 * <main> wrapper any more: that placement could be clipped/hidden by layout and
 * made Contact behave differently from About. Letting the footer render via the
 * template part — or, if the template lacks one, here before </body> — keeps all
 * pages consistent.
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
 * Last-resort footer guarantee: if a fatal error aborts the request before
 * wp_footer fires (e.g. a block render error inside <main>), the normal
 * fallbacks never run and the page ends with no footer. On shutdown we detect
 * an unrendered footer following a fatal and emit the footer markup so the
 * site chrome is never lost.
 */
function vip_transits_footer_shutdown_fallback() {
	if ( is_admin() || ! empty( $GLOBALS['vip_transits_footer_rendered'] ) ) {
		return;
	}

	if ( wp_doing_ajax() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	$error = error_get_last();
	if ( empty( $error ) || ! in_array( (int) $error['type'], array( E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR ), true ) ) {
		return;
	}

	$markup = vip_transits_take_footer_markup();
	if ( $markup === '' ) {
		return;
	}

	echo $markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
register_shutdown_function( 'vip_transits_footer_shutdown_fallback' );

/**
 * Contact-only: if the request ends without any footer, emit pattern markup once.
 *
 * Covers edge cases where neither the template part nor wp_footer ran (cached
 * partial output, timeout after main, etc.).
 */
function vip_transits_footer_contact_shutdown_guarantee() {
	if ( is_admin() || ! empty( $GLOBALS['vip_transits_footer_rendered'] ) ) {
		return;
	}

	if ( wp_doing_ajax() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	if ( ! function_exists( 'vip_transits_is_contact_page' ) || ! vip_transits_is_contact_page() ) {
		return;
	}

	$markup = vip_transits_take_footer_markup();
	if ( $markup === '' ) {
		return;
	}

	echo $markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
register_shutdown_function( 'vip_transits_footer_contact_shutdown_guarantee' );

/**
 * Restore VIP contact templates from theme files when the Site Editor copy dropped the footer.
 */
function vip_transits_restore_page_contact_template() {
	if ( ! function_exists( 'get_block_template' ) ) {
		return;
	}

	$theme_slug = get_stylesheet();
	$pairs      = array(
		'page-contact'       => 'page-contact.html',
		'page-contact-clean' => 'page-contact-clean.html',
	);

	foreach ( $pairs as $slug => $filename ) {
		$template = get_block_template( $theme_slug . '//' . $slug, 'wp_template' );

		if ( ! $template || empty( $template->wp_id ) ) {
			continue;
		}

		$db_content = (string) get_post_field( 'post_content', (int) $template->wp_id );
		if ( str_contains( $db_content, '"slug":"footer"' ) ) {
			continue;
		}

		$theme_path = get_stylesheet_directory() . '/templates/' . $filename;
		if ( ! is_readable( $theme_path ) ) {
			continue;
		}

		$theme_content = (string) file_get_contents( $theme_path );
		if ( $theme_content === '' ) {
			continue;
		}

		wp_update_post(
			array(
				'ID'           => (int) $template->wp_id,
				'post_content' => $theme_content,
			)
		);
	}
}
add_action( 'init', 'vip_transits_restore_page_contact_template', 19 );

/**
 * Append footer block to VIP templates when missing (Site Editor customized copies).
 */
function vip_transits_ensure_vip_page_templates_footer() {
	if ( ! function_exists( 'get_block_template' ) ) {
		return;
	}

	$theme_slug = get_stylesheet();
	$templates  = array(
		'page',
		'page-about',
		'page-contact',
		'page-contact-clean',
		'page-occasion',
		'single-vip_occasion',
	);

	foreach ( $templates as $template_slug ) {
		$template = get_block_template( $theme_slug . '//' . $template_slug, 'wp_template' );

		if ( ! $template || empty( $template->wp_id ) ) {
			continue;
		}

		$content = (string) get_post_field( 'post_content', (int) $template->wp_id );
		if ( $content === '' ) {
			continue;
		}

		$tag_name     = in_array( $template_slug, array( 'page-contact', 'page-contact-clean' ), true ) ? 'div' : 'footer';
		$footer_block = '<!-- wp:template-part {"slug":"footer","theme":"tenku-child","tagName":"' . $tag_name . '"} /-->';

		if ( ! str_contains( $content, '"slug":"footer"' ) ) {
			wp_update_post(
				array(
					'ID'           => (int) $template->wp_id,
					'post_content' => rtrim( $content ) . "\n\n" . $footer_block,
				)
			);
			continue;
		}

		// Contact templates: avoid nested <footer> inside <footer> (breaks layout).
		if ( 'footer' === $tag_name ) {
			continue;
		}

		$fixed = (string) preg_replace(
			'/<!-- wp:template-part \\{"slug":"footer","theme":"tenku-child","tagName":"footer"\\} \\/-->/',
			$footer_block,
			$content,
			1
		);

		if ( $fixed !== $content ) {
			wp_update_post(
				array(
					'ID'           => (int) $template->wp_id,
					'post_content' => $fixed,
				)
			);
		}
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
