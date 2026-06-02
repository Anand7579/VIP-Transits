<?php
/**
 * ACF: About Us & Contact Us page templates.
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classic editor page template labels (block theme still shows these in Page attributes).
 *
 * @param array $templates Templates.
 * @return array
 */
function vip_transits_register_page_templates( $templates ) {
	$templates['templates/page-about.html']   = __( 'VIP About Us', 'tenku-child' );
	$templates['templates/page-contact.html'] = __( 'VIP Contact Us', 'tenku-child' );

	return $templates;
}
add_filter( 'theme_page_templates', 'vip_transits_register_page_templates' );

/**
 * Template slugs WordPress may store for VIP About / Contact pages (classic + block theme).
 *
 * @return array<string, array<int, string>>
 */
function vip_transits_page_template_slug_groups() {
	return array(
		'about'   => array(
			'page-about',
			'templates/page-about.html',
			'templates/page-about',
		),
		'contact' => array(
			'page-contact',
			'templates/page-contact.html',
			'templates/page-contact',
		),
	);
}

/**
 * @param string $kind about|contact.
 * @return string[]
 */
function vip_transits_page_template_slugs_for( $kind ) {
	$groups = vip_transits_page_template_slug_groups();
	return isset( $groups[ $kind ] ) ? $groups[ $kind ] : array();
}

/**
 * Whether a page uses a VIP About or Contact template (any stored slug) or contains the matching block.
 *
 * @param int    $post_id Page ID.
 * @param string $kind    about|contact.
 * @return bool
 */
function vip_transits_page_uses_vip_template( $post_id, $kind ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return false;
	}

	$slug = (string) get_page_template_slug( $post_id );
	if ( $slug !== '' && in_array( $slug, vip_transits_page_template_slugs_for( $kind ), true ) ) {
		return true;
	}

	if ( ! function_exists( 'has_block' ) ) {
		return false;
	}

	$block = 'about' === $kind ? 'acf/vip-page-about' : 'acf/vip-page-contact';
	return has_block( $block, $post_id );
}

/**
 * ACF location: match page template across block-theme and classic slug formats.
 *
 * @param bool  $match  Whether the rule matched.
 * @param array $rule   Location rule.
 * @param array $screen Screen args.
 * @return bool
 */
function vip_transits_acf_match_page_template( $match, $rule, $screen ) {
	if ( 'page_template' !== $rule['param'] || '==' !== $rule['operator'] ) {
		return $match;
	}

	if ( empty( $screen['post_id'] ) ) {
		return $match;
	}

	$wanted = (string) $rule['value'];
	$actual = (string) get_page_template_slug( (int) $screen['post_id'] );

	foreach ( vip_transits_page_template_slug_groups() as $aliases ) {
		if ( in_array( $wanted, $aliases, true ) && in_array( $actual, $aliases, true ) ) {
			return true;
		}
	}

	// Template meta empty but VIP block is in page content (e.g. default template + block only).
	if ( ! $match && $actual === '' && function_exists( 'has_block' ) ) {
		$post_id = (int) $screen['post_id'];
		if ( in_array( $wanted, vip_transits_page_template_slugs_for( 'contact' ), true ) && has_block( 'acf/vip-page-contact', $post_id ) ) {
			return true;
		}
		if ( in_array( $wanted, vip_transits_page_template_slugs_for( 'about' ), true ) && has_block( 'acf/vip-page-about', $post_id ) ) {
			return true;
		}
	}

	return $match;
}
add_filter( 'acf/location/rule_match/page_template', 'vip_transits_acf_match_page_template', 10, 3 );

/**
 * @return int
 */
function vip_transits_page_content_post_id() {
	$post_id = (int) get_queried_object_id();
	return $post_id > 0 ? $post_id : 0;
}

/**
 * @param string $field ACF field name.
 * @param mixed  $default Default.
 * @return mixed
 */
function vip_transits_get_page_field( $field, $default = null ) {
	$post_id = vip_transits_page_content_post_id();
	if ( ! $post_id || ! function_exists( 'get_field' ) ) {
		return $default;
	}

	$value = get_field( $field, $post_id );
	return null !== $value && '' !== $value && array() !== $value ? $value : $default;
}

/**
 * Register ACF blocks.
 */
function vip_transits_register_page_content_blocks() {
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$css = vip_transits_page_content_assets();

	acf_register_block_type(
		array(
			'name'            => 'vip-page-about',
			'title'           => __( 'VIP About Us content', 'tenku-child' ),
			'description'     => __( 'About page body (ACF fields on this page).', 'tenku-child' ),
			'category'        => 'layout',
			'icon'            => 'id-alt',
			'keywords'        => array( 'about', 'company' ),
			'render_template' => $dir . '/blocks/vip-page-about/render.php',
			'enqueue_style'   => $css['style'],
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
			'name'            => 'vip-page-contact',
			'title'           => __( 'VIP Contact Us content', 'tenku-child' ),
			'description'     => __( 'Contact page with CF7 shortcode area (ACF fields).', 'tenku-child' ),
			'category'        => 'layout',
			'icon'            => 'email',
			'keywords'        => array( 'contact', 'form' ),
			'render_template' => $dir . '/blocks/vip-page-contact/render.php',
			'enqueue_style'   => $css['style'],
			'mode'            => 'preview',
			'supports'        => array(
				'align'  => array( 'wide', 'full' ),
				'anchor' => true,
				'mode'   => false,
			),
		)
	);
}
add_action( 'acf/init', 'vip_transits_register_page_content_blocks', 5 );

/**
 * @return array{style:string,version:string}
 */
function vip_transits_page_content_assets() {
	$path = get_stylesheet_directory() . '/assets/css/vip-pages.css';
	$ver  = file_exists( $path ) ? (string) filemtime( $path ) : wp_get_theme()->get( 'Version' );

	return array(
		'style'   => get_stylesheet_directory_uri() . '/assets/css/vip-pages.css',
		'version' => $ver,
	);
}

/**
 * Build a safe href for a contact detail row (phone, email, address, or custom link).
 *
 * @param string $link  Optional link from ACF (tel:, mailto:, https:, or plain phone/email).
 * @param string $type  phone|email|address|text.
 * @param string $value Display value (used to auto-build tel:/mailto: when link is empty).
 * @return string Empty string if not linkable.
 */
function vip_transits_normalize_contact_detail_href( $link, $type, $value ) {
	$link  = trim( (string) $link );
	$value = trim( (string) $value );
	$type  = (string) $type;

	if ( $link !== '' ) {
		if ( preg_match( '#^tel:#i', $link ) ) {
			return vip_transits_build_tel_href( substr( $link, 4 ) );
		}
		if ( preg_match( '#^mailto:#i', $link ) ) {
			$email = sanitize_email( substr( $link, 7 ) );
			return $email ? 'mailto:' . $email : '';
		}
		if ( preg_match( '#^https?://#i', $link ) ) {
			return (string) esc_url( $link );
		}
		if ( is_email( $link ) ) {
			return 'mailto:' . sanitize_email( $link );
		}
		if ( 'email' === $type || str_contains( $link, '@' ) ) {
			return 'mailto:' . sanitize_email( $link );
		}
		$tel = vip_transits_build_tel_href( $link );
		if ( $tel !== '' ) {
			return $tel;
		}
		return (string) esc_url( 'https://' . ltrim( $link, '/' ) );
	}

	if ( $value === '' ) {
		return '';
	}

	if ( 'phone' === $type ) {
		return vip_transits_build_tel_href( $value );
	}

	if ( 'email' === $type || is_email( $value ) ) {
		return 'mailto:' . sanitize_email( $value );
	}

	return '';
}

/**
 * tel: href without encoding + (esc_url turns + into %2B).
 *
 * @param string $raw Phone digits / formatted number.
 * @return string e.g. tel:+971507350049
 */
function vip_transits_build_tel_href( $raw ) {
	$raw = trim( (string) $raw );
	if ( $raw === '' ) {
		return '';
	}

	$digits = preg_replace( '/[^\d+]/', '', $raw );
	if ( $digits === '' ) {
		return '';
	}

	if ( '+' !== $digits[0] ) {
		$digits = ltrim( $digits, '0' );
	}

	return 'tel:' . $digits;
}

/**
 * Render map field (iframe HTML, oEmbed, or Google Maps share / short links).
 *
 * @param string $raw ACF oembed / text value.
 * @return string HTML or empty.
 */
function vip_transits_render_contact_map_embed( $raw ) {
	$raw = vip_transits_normalize_map_embed_raw( $raw );
	if ( $raw === '' ) {
		return '';
	}

	$allowed_iframe = array(
		'iframe' => array(
			'src'             => true,
			'width'           => true,
			'height'          => true,
			'style'           => true,
			'frameborder'     => true,
			'allowfullscreen' => true,
			'loading'         => true,
			'referrerpolicy'  => true,
			'title'           => true,
			'aria-label'      => true,
		),
	);

	if ( stripos( $raw, '<iframe' ) !== false ) {
		if ( preg_match( '/<iframe\b[^>]*\/?>/is', $raw, $match ) ) {
			return (string) wp_kses( $match[0], $allowed_iframe );
		}
		return (string) wp_kses( $raw, $allowed_iframe );
	}

	$url = wp_strip_all_tags( $raw );
	if ( $url === '' ) {
		return '';
	}

	$embed = wp_oembed_get( $url, array( 'width' => 1200 ) );
	if ( $embed ) {
		return (string) wp_kses( $embed, $allowed_iframe );
	}

	$resolved = vip_transits_resolve_redirect_url( $url );
	if ( $resolved !== $url ) {
		$embed = wp_oembed_get( $resolved, array( 'width' => 1200 ) );
		if ( $embed ) {
			return (string) wp_kses( $embed, $allowed_iframe );
		}
		$url = $resolved;
	}

	$iframe_src = vip_transits_google_maps_embed_src( $url );
	if ( $iframe_src ) {
		return sprintf(
			'<iframe src="%s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="%s"></iframe>',
			esc_url( $iframe_src ),
			esc_attr__( 'Location map', 'tenku-child' )
		);
	}

	return '';
}

/**
 * Fix map field value before save / display (oEmbed used to prefix iframe with https://).
 *
 * @param mixed $raw ACF value.
 * @return string
 */
function vip_transits_sanitize_map_embed_storage( $raw ) {
	$raw = trim( (string) $raw );
	if ( $raw === '' ) {
		return '';
	}

	$raw = str_replace( array( '\\"', "\\'", '\/' ), array( '"', "'", '/' ), $raw );
	$raw = html_entity_decode( $raw, ENT_QUOTES, 'UTF-8' );

	// Legacy oEmbed / URL field: "https://<iframe..." or "https://%3Ciframe..."
	$raw = preg_replace( '#^https?://(?=<iframe)#i', '', $raw );
	$raw = preg_replace( '#^https?://(?=&lt;iframe)#i', '', $raw );
	if ( preg_match( '#^https?://%3[Cc]iframe#', $raw ) ) {
		$raw = preg_replace( '#^https?://#i', '', $raw );
		$raw = rawurldecode( $raw );
	}

	return $raw;
}

/**
 * Force map field to a plain textarea (not oEmbed URL field).
 *
 * @param array $field ACF field.
 * @return array
 */
function vip_transits_acf_map_embed_load_field( $field ) {
	if ( empty( $field['key'] ) || 'field_vipcontact_map_embed' !== $field['key'] ) {
		return $field;
	}

	$field['type']         = 'textarea';
	$field['rows']         = 6;
	$field['new_lines']    = '';
	$field['instructions'] = __( 'Paste a Google Maps share link, OR the full iframe HTML from Share → Embed a map. Do not add https:// before the iframe tag.', 'tenku-child' );

	return $field;
}
add_filter( 'acf/load_field/key=field_vipcontact_map_embed', 'vip_transits_acf_map_embed_load_field' );

/**
 * @param mixed $value Submitted value.
 * @return string
 */
function vip_transits_acf_map_embed_update_value( $value ) {
	return vip_transits_sanitize_map_embed_storage( $value );
}
add_filter( 'acf/update_value/key=field_vipcontact_map_embed', 'vip_transits_acf_map_embed_update_value' );

/**
 * @param mixed $value Stored value.
 * @return string
 */
function vip_transits_acf_map_embed_format_value( $value ) {
	return vip_transits_sanitize_map_embed_storage( $value );
}
add_filter( 'acf/format_value/key=field_vipcontact_map_embed', 'vip_transits_acf_map_embed_format_value' );

/**
 * Clean map field input from the editor (iframe HTML, share URL, or messy paste).
 *
 * @param string $raw ACF value.
 * @return string Normalized iframe HTML, URL, or empty.
 */
function vip_transits_normalize_map_embed_raw( $raw ) {
	$raw = vip_transits_sanitize_map_embed_storage( $raw );
	if ( $raw === '' ) {
		return '';
	}

	if ( preg_match( '/<iframe\b[^>]*\/?>/is', $raw, $match ) ) {
		return trim( $match[0] );
	}

	if ( preg_match( '#https?://[^\s<>"\']+#i', $raw, $match ) ) {
		return trim( $match[0] );
	}

	return $raw;
}

/**
 * @param string $url Starting URL.
 * @return string Final URL after redirects.
 */
function vip_transits_resolve_redirect_url( $url ) {
	$url = esc_url_raw( $url );
	if ( ! $url ) {
		return '';
	}

	for ( $i = 0; $i < 5; $i++ ) {
		$response = wp_safe_remote_head(
			$url,
			array(
				'timeout'     => 8,
				'redirection' => 0,
			)
		);

		if ( is_wp_error( $response ) ) {
			break;
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		if ( ! in_array( $code, array( 301, 302, 303, 307, 308 ), true ) ) {
			return $url;
		}

		$location = wp_remote_retrieve_header( $response, 'location' );
		if ( ! $location ) {
			break;
		}

		$next = esc_url_raw( $location );
		if ( ! $next ) {
			break;
		}

		$url = $next;
	}

	return $url;
}

/**
 * Build Google Maps embed iframe src from a maps URL.
 *
 * @param string $url Maps or share URL.
 * @return string Embed src or empty.
 */
function vip_transits_google_maps_embed_src( $url ) {
	$url = (string) $url;
	if ( $url === '' ) {
		return '';
	}

	if ( preg_match( '#google\.com/maps|maps\.google\.#i', $url ) ) {
		$embed = $url;
		if ( ! preg_match( '#output=embed#i', $embed ) ) {
			$embed .= ( str_contains( $embed, '?' ) ? '&' : '?' ) . 'output=embed';
		}
		return $embed;
	}

	if ( preg_match( '#maps\.app\.goo\.gl|goo\.gl/maps#i', $url ) ) {
		$resolved = vip_transits_resolve_redirect_url( $url );
		if ( $resolved && $resolved !== $url ) {
			return vip_transits_google_maps_embed_src( $resolved );
		}
	}

	return '';
}

/**
 * Run Contact Form 7 (or other) shortcodes from an ACF WYSIWYG value.
 *
 * @param string $raw Field value (may include <p> wrappers).
 * @return string
 */
function vip_transits_render_form_shortcode_field( $raw ) {
	$raw = trim( (string) $raw );
	if ( $raw === '' ) {
		return '';
	}

	if ( strpos( $raw, '[' ) !== false ) {
		$stripped = trim( wp_strip_all_tags( $raw ) );
		if ( $stripped !== '' ) {
			return (string) do_shortcode( $stripped );
		}
	}

	return (string) apply_filters( 'the_content', $raw );
}

/**
 * Enqueue page template styles.
 */
function vip_transits_enqueue_page_content_assets() {
	if ( is_admin() ) {
		return;
	}

	$post_id = vip_transits_page_content_post_id();
	if ( ! $post_id || ! is_page() ) {
		return;
	}

	if ( ! vip_transits_page_uses_vip_template( $post_id, 'about' ) && ! vip_transits_page_uses_vip_template( $post_id, 'contact' ) ) {
		return;
	}

	if ( function_exists( 'vip_transits_enqueue_editor_block_styles' ) ) {
		vip_transits_enqueue_editor_block_styles();
	}

	$assets = vip_transits_page_content_assets();
	wp_enqueue_style(
		'vip-pages',
		$assets['style'],
		array( 'chld_thm_cfg_child' ),
		$assets['version']
	);

	if ( function_exists( 'wpcf7_enqueue_scripts' ) && vip_transits_page_uses_vip_template( $post_id, 'contact' ) ) {
		wpcf7_enqueue_scripts();
	}
}
add_action( 'wp_enqueue_scripts', 'vip_transits_enqueue_page_content_assets', 20 );

/**
 * Editor preview styles.
 */
function vip_transits_enqueue_page_content_editor_assets() {
	if ( function_exists( 'vip_transits_enqueue_editor_block_styles' ) ) {
		vip_transits_enqueue_editor_block_styles();
	}

	$path = get_stylesheet_directory() . '/assets/css/vip-pages.css';
	if ( ! file_exists( $path ) ) {
		return;
	}

	wp_enqueue_style(
		'vip-pages-editor',
		get_stylesheet_directory_uri() . '/assets/css/vip-pages.css',
		array(),
		(string) filemtime( $path )
	);
}
add_action( 'enqueue_block_editor_assets', 'vip_transits_enqueue_page_content_editor_assets' );
