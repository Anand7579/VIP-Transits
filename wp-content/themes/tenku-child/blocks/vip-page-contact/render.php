<?php
/**
 * Block: vip-page-contact
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = vip_transits_page_content_post_id();
if ( ! $post_id ) {
	return;
}

// Deployment marker: if this comment is absent from the live page source, the
// updated theme files have NOT reached production (sync/deploy issue).
echo "\n<!-- vip-contact-render build:2026-06-03-J map-iframe-fix -->\n";

if ( function_exists( 'vip_transits_footer_debug_comment' ) ) {
	vip_transits_footer_debug_comment(
		'contact-block-start',
		array(
			'post_id'         => (int) $post_id,
			'footer_rendered' => 0,
			'static_len'      => function_exists( 'vip_transits_footer_static_html' )
				? strlen( (string) vip_transits_footer_static_html() )
				: -1,
		)
	);
}

// Render the whole block inside a buffered try/catch so a render-time error
// can never abort the request before the footer template-part (and wp_footer)
// run. Any failure discards this block's partial output and lets the page
// continue, keeping the footer intact.
try {
	ob_start();

	$lead            = (string) vip_transits_get_page_field( 'contact_masthead_lead', '' );
$intro_content   = (string) vip_transits_get_page_field( 'contact_intro_content', '' );
$form_heading    = (string) vip_transits_get_page_field( 'contact_form_heading', __( 'Get in touch', 'tenku-child' ) );
$form_shortcode  = (string) vip_transits_get_page_field( 'contact_form_shortcode', '' );
$sidebar_heading = (string) vip_transits_get_page_field( 'contact_sidebar_heading', __( 'Our contact', 'tenku-child' ) );
$contact_items   = vip_transits_get_page_field( 'contact_details', array() );
$hours           = vip_transits_get_page_field( 'contact_hours', array() );
$show_whatsapp   = (bool) vip_transits_get_page_field( 'contact_show_whatsapp', true );
$whatsapp_label  = (string) vip_transits_get_page_field( 'contact_whatsapp_label', __( 'Chat on WhatsApp', 'tenku-child' ) );
$wa_message      = (string) vip_transits_get_page_field( 'contact_whatsapp_message', __( 'Hello, I would like to enquire about luxury car rental.', 'tenku-child' ) );
$show_map        = (bool) vip_transits_get_page_field( 'contact_show_map', false );
$map_embed       = (string) vip_transits_get_page_field( 'contact_map_embed', '' );

$wa_href = '';
if ( $show_whatsapp && function_exists( 'vip_transits_whatsapp_href_attr' ) ) {
	$wa_href = vip_transits_whatsapp_href_attr( $wa_message );
}
?>
<article class="vip-page vip-page--contact" data-vip-section>
	<?php
	get_template_part(
		'template-parts/page/masthead',
		null,
		array(
			'title' => get_the_title( $post_id ),
			'lead'  => $lead,
		)
	);
	?>

	<div class="vip-page__body">
		<?php if ( $intro_content ) : ?>
			<section class="vip-border-section vip-border-section--surface vip-border-section--plain-panel">
				<div class="vip-content-container">
					<div class="vip-page__prose vip-page__prose--center"><?php echo wp_kses_post( $intro_content ); ?></div>
				</div>
			</section>
		<?php endif; ?>

		<section class="vip-border-section vip-border-section--surface vip-border-section--plain-panel">
			<div class="vip-content-container vip-page__contact-grid">
				<div class="vip-page__contact-form-col">
					<?php if ( $form_heading ) : ?>
						<h2 class="vip-page__section-title"><?php echo esc_html( $form_heading ); ?></h2>
					<?php endif; ?>
					<?php if ( $form_shortcode ) : ?>
						<div class="vip-page__form-wrap">
							<?php
							if ( function_exists( 'vip_transits_render_form_shortcode_field' ) ) {
								echo vip_transits_render_form_shortcode_field( $form_shortcode ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							} else {
								echo do_shortcode( $form_shortcode ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
						</div>
					<?php else : ?>
						<p class="vip-page__form-placeholder">
							<?php esc_html_e( 'Add your Contact Form 7 shortcode in the page editor under Contact Form (shortcode).', 'tenku-child' ); ?>
						</p>
					<?php endif; ?>
				</div>

				<aside class="vip-page__contact-aside">
					<?php if ( $sidebar_heading ) : ?>
						<h2 class="vip-page__section-title"><?php echo esc_html( $sidebar_heading ); ?></h2>
					<?php endif; ?>

					<?php if ( is_array( $contact_items ) && $contact_items ) : ?>
						<ul class="vip-page__contact-list">
							<?php foreach ( $contact_items as $item ) : ?>
								<?php
								$type  = isset( $item['type'] ) ? (string) $item['type'] : 'text';
								$label = isset( $item['label'] ) ? (string) $item['label'] : '';
								$value = isset( $item['value'] ) ? (string) $item['value'] : '';
								$link_raw = isset( $item['link'] ) ? (string) $item['link'] : '';
								$link     = function_exists( 'vip_transits_normalize_contact_detail_href' )
									? vip_transits_normalize_contact_detail_href( $link_raw, $type, $value )
									: $link_raw;
								if ( ! $value ) {
									continue;
								}
								$icon = 'text';
								if ( 'phone' === $type ) {
									$icon = 'phone';
								} elseif ( 'email' === $type ) {
									$icon = 'email';
								} elseif ( 'address' === $type ) {
									$icon = 'address';
								}
								?>
								<li class="vip-page__contact-item vip-page__contact-item--<?php echo esc_attr( $icon ); ?>">
									<?php if ( $label ) : ?>
										<span class="vip-page__contact-label"><?php echo esc_html( $label ); ?></span>
									<?php endif; ?>
									<?php if ( $link ) : ?>
										<a class="vip-page__contact-value" href="<?php echo esc_attr( $link ); ?>"><?php echo esc_html( $value ); ?></a>
									<?php else : ?>
										<span class="vip-page__contact-value"><?php echo esc_html( $value ); ?></span>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( $wa_href ) : ?>
						<a class="vip-page__btn vip-page__btn--whatsapp" href="<?php echo esc_url( $wa_href ); ?>" target="_blank" rel="noopener noreferrer">
							<span class="vip-page__btn-icon" aria-hidden="true">
								<svg width="22" height="22" viewBox="0 0 24 24" focusable="false"><path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.882 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
							</span>
							<span class="vip-page__btn-label"><?php echo esc_html( $whatsapp_label ); ?></span>
						</a>
					<?php endif; ?>

					<?php if ( is_array( $hours ) && $hours ) : ?>
						<div class="vip-page__hours">
							<h3 class="vip-page__hours-title"><?php esc_html_e( 'Operation hours', 'tenku-child' ); ?></h3>
							<ul class="vip-page__hours-list">
								<?php foreach ( $hours as $row ) : ?>
									<?php
									$day  = isset( $row['day'] ) ? (string) $row['day'] : '';
									$time = isset( $row['hours'] ) ? (string) $row['hours'] : '';
									if ( ! $day && ! $time ) {
										continue;
									}
									?>
									<li>
										<span><?php echo esc_html( $day ); ?></span>
										<span><?php echo esc_html( $time ); ?></span>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>
				</aside>
			</div>
		</section>

		<?php if ( $show_map && $map_embed ) : ?>
			<?php
			$map_html = function_exists( 'vip_transits_render_contact_map_embed' )
				? vip_transits_render_contact_map_embed( $map_embed )
				: '';
			?>
			<?php if ( $map_html ) : ?>
				<section class="vip-border-section vip-border-section--surface vip-border-section--plain-panel vip-border-section--map" aria-label="<?php esc_attr_e( 'Map', 'tenku-child' ); ?>">
					<div class="vip-content-container">
						<div class="vip-page__map-embed">
							<?php echo $map_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					</div>
				</section>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</article>
	<?php
	$vip_contact_html = (string) ob_get_clean();
	if ( function_exists( 'vip_transits_footer_debug_comment' ) ) {
		vip_transits_footer_debug_comment(
			'contact-block-buffer',
			array(
				'html_len'              => strlen( $vip_contact_html ),
				'has_vip_site_footer'   => str_contains( $vip_contact_html, 'vip-site-footer' ) ? 1 : 0,
				'footer_rendered'       => ! empty( $GLOBALS['vip_transits_footer_rendered'] ) ? 1 : 0,
			)
		);
	}
	echo $vip_contact_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
} catch ( \Throwable $vip_contact_error ) {
	if ( ob_get_level() > 0 ) {
		ob_end_clean();
	}
	if ( function_exists( 'error_log' ) ) {
		error_log( 'VIP contact block render failed: ' . $vip_contact_error->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
	}
	if ( current_user_can( 'manage_options' ) ) {
		echo '<!-- VIP contact block error: ' . esc_html( $vip_contact_error->getMessage() ) . ' -->';
	}

}
