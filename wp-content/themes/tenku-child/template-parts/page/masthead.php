<?php
/**
 * Page masthead (black band, centered title).
 *
 * @package Tenku_Child
 *
 * Page banners pass data via set_query_var( 'vip_masthead_*' ) or $args from get_template_part().
 * Fleet archive uses vip_transits_render_fleet_archive_banner() and does not load this file.
 *
 * @var array $args {
 *     @type string $title               H1 text.
 *     @type string $lead                Subtitle / WYSIWYG lead.
 *     @type bool   $hide_title_fallback Hide title fallback to get_the_title().
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$args = isset( $args ) && is_array( $args ) ? $args : array();

if ( get_query_var( 'vip_masthead_active' ) ) {
	$title         = trim( (string) get_query_var( 'vip_masthead_title', '' ) );
	$lead          = trim( (string) get_query_var( 'vip_masthead_lead', '' ) );
	$hide_fallback = (bool) get_query_var( 'vip_masthead_hide_title_fallback', false );
} elseif ( $args ) {
	$title         = isset( $args['title'] ) ? trim( (string) $args['title'] ) : '';
	$lead          = isset( $args['lead'] ) ? trim( (string) $args['lead'] ) : '';
	$hide_fallback = ! empty( $args['hide_title_fallback'] );
} else {
	$hide_fallback = ! empty( $hide_title_fallback );
	$title         = isset( $title ) ? trim( (string) $title ) : '';
	$lead          = isset( $lead ) ? trim( (string) $lead ) : '';
}

if ( ! $hide_fallback && $title === '' ) {
	$title = get_the_title();
}

if ( $title === '' && $lead === '' ) {
	return;
}
?>
<header class="vip-bg-black-section vip-bg-black-section--masthead<?php echo $title === '' ? ' vip-bg-black-section--masthead-no-title' : ''; ?>">
	<div class="vip-bg-black-section__inner vip-content-container">
		<?php if ( $title !== '' ) : ?>
			<h1 class="vip-page__masthead-title"><?php echo esc_html( $title ); ?></h1>
		<?php endif; ?>
		<?php if ( $lead !== '' ) : ?>
			<div class="vip-page__masthead-lead"><?php echo wp_kses_post( $lead ); ?></div>
		<?php endif; ?>
	</div>
</header>
