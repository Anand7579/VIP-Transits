<?php
/**
 * Theme icon URLs (media library or bundled assets).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default icon URLs (production media library).
 *
 * @return array<string, string>
 */
function vip_transits_theme_icon_defaults() {
	return array(
		'search'       => 'https://viptransits.com/wp-content/uploads/2026/05/Search-icon.png',
		'arrow-right'  => 'https://viptransits.com/wp-content/uploads/2026/05/right-arrow.svg',
	);
}

/**
 * Resolve icon URL: bundled file in theme, then filter, then default CDN URL.
 *
 * @param string $slug search|arrow-right
 * @return string
 */
function vip_transits_theme_icon_url( $slug ) {
	$files = array(
		'search'      => array( 'Search-icon.png' ),
		'arrow-right' => array( 'right-arrow.svg', 'right-arrow.png' ),
	);

	$defaults = vip_transits_theme_icon_defaults();
	$url      = isset( $defaults[ $slug ] ) ? $defaults[ $slug ] : '';

	if ( isset( $files[ $slug ] ) ) {
		foreach ( (array) $files[ $slug ] as $filename ) {
			$local = get_stylesheet_directory() . '/assets/images/' . $filename;
			if ( file_exists( $local ) ) {
				$url = get_stylesheet_directory_uri() . '/assets/images/' . $filename;
				break;
			}
		}
	}

	/**
	 * Filter theme icon URL.
	 *
	 * @param string $url  Resolved URL.
	 * @param string $slug Icon slug.
	 */
	return (string) apply_filters( 'vip_transits_theme_icon_url', $url, $slug );
}

/**
 * Markup for an icon <img>.
 *
 * @param string $slug   Icon slug.
 * @param array  $attrs  Extra attributes (class, width, height, alt).
 * @return string HTML or empty.
 */
function vip_transits_theme_icon_img( $slug, $attrs = array() ) {
	$url = vip_transits_theme_icon_url( $slug );
	if ( $url === '' ) {
		return '';
	}

	$width  = isset( $attrs['width'] ) ? (int) $attrs['width'] : ( 'search' === $slug ? 22 : 12 );
	$height = isset( $attrs['height'] ) ? (int) $attrs['height'] : ( 'search' === $slug ? 22 : 12 );
	$class  = isset( $attrs['class'] ) ? (string) $attrs['class'] : 'vip-theme-icon__img';
	$alt    = isset( $attrs['alt'] ) ? (string) $attrs['alt'] : '';

	$classes = trim( 'vip-theme-icon__img ' . $class );

	return sprintf(
		'<img src="%1$s" alt="%2$s" width="%3$d" height="%4$d" class="%5$s" decoding="async" />',
		esc_url( $url ),
		esc_attr( $alt ),
		$width,
		$height,
		esc_attr( $classes )
	);
}

/**
 * Right-arrow span for ghost CTA buttons (Rent by occasion, Why rent).
 *
 * @param string $modifier_class Optional BEM class (e.g. vip-occasions-card__btn-arrow).
 * @return string
 */
function vip_transits_theme_icon_arrow_html( $modifier_class = '' ) {
	$img = vip_transits_theme_icon_img(
		'arrow-right',
		array(
			'class' => 'vip-theme-icon__img--arrow',
		)
	);

	$span_class = 'vip-theme-icon__arrow';
	if ( $modifier_class !== '' ) {
		$span_class .= ' ' . sanitize_html_class( $modifier_class );
	}

	if ( $img === '' ) {
		$fallback_class = $modifier_class !== '' ? $modifier_class : 'vip-theme-icon__arrow';
		return '<span class="' . esc_attr( $fallback_class ) . '" aria-hidden="true">→</span>';
	}

	return '<span class="' . esc_attr( $span_class ) . '" aria-hidden="true">' . $img . '</span>';
}
