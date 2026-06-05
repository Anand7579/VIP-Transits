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

$preview = is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST );

echo "\n<!-- vip-contact-render build:2026-06-03-strict-sections -->\n";

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

try {
	ob_start();

	$lead = function_exists( 'vip_transits_get_page_banner_description' )
		? vip_transits_get_page_banner_description( $post_id )
		: trim( (string) vip_transits_get_page_field( 'contact_masthead_lead', '' ) );

	$intro_content = trim( (string) vip_transits_get_page_field( 'contact_intro_content', '' ) );
	$form_heading  = trim( (string) vip_transits_get_page_field( 'contact_form_heading', '' ) );
	$form_shortcode = trim( (string) vip_transits_get_page_field( 'contact_form_shortcode', '' ) );
	$sidebar_heading = trim( (string) vip_transits_get_page_field( 'contact_sidebar_heading', '' ) );
	$contact_items   = vip_transits_get_page_field( 'contact_details', array() );
	$hours           = vip_transits_get_page_field( 'contact_hours', array() );
	$takeaways_heading = trim( (string) vip_transits_get_page_field( 'contact_takeaways_heading', '' ) );
	$takeaways_items   = vip_transits_get_page_field( 'contact_takeaways_items', array() );
	$takeaways_summary = trim( (string) vip_transits_get_page_field( 'contact_takeaways_summary', '' ) );
	$how_heading     = trim( (string) vip_transits_get_page_field( 'contact_how_heading', '' ) );
	$how_steps       = vip_transits_get_page_field( 'contact_how_steps', array() );
	$show_whatsapp   = (bool) vip_transits_get_page_field( 'contact_show_whatsapp', true );
	$whatsapp_label  = trim( (string) vip_transits_get_page_field( 'contact_whatsapp_label', '' ) );
	$wa_message      = trim( (string) vip_transits_get_page_field( 'contact_whatsapp_message', '' ) );
	$wa_band_copy    = trim( (string) vip_transits_get_page_field( 'contact_whatsapp_band_copy', '' ) );
	$show_map        = (bool) vip_transits_get_page_field( 'contact_show_map', false );
	$map_embed       = trim( (string) vip_transits_get_page_field( 'contact_map_embed', '' ) );
	$faq_heading     = trim( (string) vip_transits_get_page_field( 'contact_faq_heading', '' ) );
	$faq_intro       = trim( (string) vip_transits_get_page_field( 'contact_faq_intro', '' ) );
	$faq_image       = vip_transits_get_page_field( 'contact_faq_image', null );
	$faq_items       = vip_transits_get_page_field( 'contact_faq_items', array() );

	$wa_href = '';
	if ( $show_whatsapp && $wa_message !== '' && function_exists( 'vip_transits_whatsapp_href_attr' ) ) {
		$wa_href = vip_transits_whatsapp_href_attr( $wa_message );
	} elseif ( $show_whatsapp && function_exists( 'vip_transits_whatsapp_href_attr' ) ) {
		$wa_href = vip_transits_whatsapp_href_attr();
	}

	$contact_rows = array();
	if ( is_array( $contact_items ) ) {
		foreach ( $contact_items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$value = trim( (string) ( $item['value'] ?? '' ) );
			if ( $value === '' ) {
				continue;
			}
			$contact_rows[] = $item;
		}
	}

	$hour_rows = array();
	if ( is_array( $hours ) ) {
		foreach ( $hours as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$day  = trim( (string) ( $row['day'] ?? '' ) );
			$time = trim( (string) ( $row['hours'] ?? '' ) );
			if ( $day === '' && $time === '' ) {
				continue;
			}
			$hour_rows[] = $row;
		}
	}

	$takeaway_rows = array();
	if ( is_array( $takeaways_items ) ) {
		foreach ( $takeaways_items as $takeaway_row ) {
			if ( ! is_array( $takeaway_row ) ) {
				continue;
			}
			$text = isset( $takeaway_row['text'] ) ? trim( (string) $takeaway_row['text'] ) : '';
			if ( $text !== '' ) {
				$takeaway_rows[] = $text;
			}
		}
	}

	$how_step_rows = array();
	if ( is_array( $how_steps ) ) {
		foreach ( $how_steps as $step ) {
			if ( ! is_array( $step ) ) {
				continue;
			}
			$title = trim( (string) ( $step['title'] ?? '' ) );
			$text  = trim( (string) ( $step['text'] ?? '' ) );
			if ( $title === '' && $text === '' ) {
				continue;
			}
			$how_step_rows[] = array(
				'title' => $title,
				'text'  => $text,
			);
		}
	}

	$faq_rows = array();
	if ( is_array( $faq_items ) ) {
		foreach ( $faq_items as $faq_row ) {
			if ( ! is_array( $faq_row ) ) {
				continue;
			}
			$question = isset( $faq_row['question'] ) ? trim( (string) $faq_row['question'] ) : '';
			$answer   = isset( $faq_row['answer'] ) ? (string) $faq_row['answer'] : '';
			if ( $question === '' ) {
				continue;
			}
			$faq_rows[] = array(
				'question' => $question,
				'answer'   => $answer,
			);
		}
	}

	$has_intro = $intro_content !== '' && wp_strip_all_tags( $intro_content ) !== '';
	$has_form_section = $form_shortcode !== ''
		|| $form_heading !== ''
		|| $sidebar_heading !== ''
		|| $contact_rows
		|| $hour_rows
		|| ( $wa_href && $whatsapp_label !== '' );
	$has_takeaways = $takeaway_rows || $takeaways_summary !== '';
	$has_how_section = $how_step_rows;
	$has_wa_band = $wa_href && $wa_band_copy !== '';
	$map_html = '';
	if ( $show_map && $map_embed !== '' && function_exists( 'vip_transits_render_contact_map_embed' ) ) {
		$map_html = vip_transits_render_contact_map_embed( $map_embed );
	}

	$link_target = function_exists( 'vip_transits_link_target_attr' ) ? vip_transits_link_target_attr() : '';
	?>
<article class="vip-page vip-page--contact" data-vip-section>
	<?php if ( get_the_title( $post_id ) || $lead !== '' ) : ?>
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
	<?php endif; ?>

	<div class="vip-page__body">
		<?php if ( $has_intro ) : ?>
			<section class="vip-border-section vip-border-section--surface vip-border-section--plain-panel vip-contact-intro">
				<div class="vip-content-container">
					<div class="vip-page__prose vip-page__prose--center"><?php echo wp_kses_post( $intro_content ); ?></div>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( $has_form_section ) : ?>
			<section class="vip-border-section vip-border-section--surface vip-border-section--plain-panel vip-contact-form-section">
				<div class="vip-content-container vip-page__contact-grid">
					<?php if ( $form_shortcode || $form_heading || $preview ) : ?>
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
							<?php elseif ( $preview ) : ?>
								<p class="vip-page__form-placeholder">
									<?php esc_html_e( 'Add your Contact Form 7 shortcode in the page editor under Contact Form (shortcode).', 'tenku-child' ); ?>
								</p>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( $sidebar_heading || $contact_rows || $hour_rows || ( $wa_href && $whatsapp_label !== '' ) ) : ?>
						<div class="vip-page__contact-aside-wrap">
							<aside class="vip-page__contact-aside">
								<?php if ( $sidebar_heading ) : ?>
									<h2 class="vip-page__section-title"><?php echo esc_html( $sidebar_heading ); ?></h2>
								<?php endif; ?>

								<?php if ( $contact_rows ) : ?>
									<ul class="vip-page__contact-list">
										<?php foreach ( $contact_rows as $item ) : ?>
											<?php
											$type     = isset( $item['type'] ) ? (string) $item['type'] : 'text';
											$label    = isset( $item['label'] ) ? (string) $item['label'] : '';
											$value    = isset( $item['value'] ) ? (string) $item['value'] : '';
											$link_raw = isset( $item['link'] ) ? (string) $item['link'] : '';
											$link     = function_exists( 'vip_transits_normalize_contact_detail_href' )
												? vip_transits_normalize_contact_detail_href( $link_raw, $type, $value )
												: $link_raw;
											$icon = 'text';
											if ( 'phone' === $type ) {
												$icon = 'phone';
											} elseif ( 'email' === $type ) {
												$icon = 'email';
											} elseif ( 'address' === $type ) {
												$icon = 'address';
											} elseif ( 'url' === $type ) {
												$icon = 'url';
											}
											?>
											<li class="vip-page__contact-item vip-page__contact-item--<?php echo esc_attr( $icon ); ?>">
												<?php if ( $label ) : ?>
													<span class="vip-page__contact-label"><?php echo esc_html( $label ); ?></span>
												<?php endif; ?>
												<?php if ( $link ) : ?>
													<a class="vip-page__contact-value" href="<?php echo esc_attr( $link ); ?>"<?php echo $link_target; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $value ); ?></a>
												<?php else : ?>
													<span class="vip-page__contact-value"><?php echo esc_html( $value ); ?></span>
												<?php endif; ?>
											</li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>

								<?php if ( $wa_href && $whatsapp_label !== '' ) : ?>
									<a class="vip-page__btn vip-page__btn--whatsapp" href="<?php echo esc_url( $wa_href ); ?>"<?php echo $link_target; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
										<span class="vip-page__btn-icon" aria-hidden="true">
											<svg width="22" height="22" viewBox="0 0 24 24" focusable="false"><path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.882 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
										</span>
										<span class="vip-page__btn-label"><?php echo esc_html( $whatsapp_label ); ?></span>
									</a>
								<?php endif; ?>

								<?php if ( $hour_rows ) : ?>
									<div class="vip-page__hours">
										<h3 class="vip-page__hours-title"><?php esc_html_e( 'Operation hours', 'tenku-child' ); ?></h3>
										<ul class="vip-page__hours-list">
											<?php foreach ( $hour_rows as $row ) : ?>
												<li>
													<span><?php echo esc_html( (string) ( $row['day'] ?? '' ) ); ?></span>
													<span><?php echo esc_html( (string) ( $row['hours'] ?? '' ) ); ?></span>
												</li>
											<?php endforeach; ?>
										</ul>
									</div>
								<?php endif; ?>
							</aside>
						</div>
					<?php endif; ?>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( $has_takeaways ) : ?>
			<?php
			get_template_part(
				'template-parts/contact/page-key-takeaways',
				null,
				array(
					'heading' => $takeaways_heading,
					'items'   => $takeaway_rows,
					'summary' => $takeaways_summary,
				)
			);
			?>
		<?php endif; ?>

		<?php if ( $has_how_section ) : ?>
			<?php
			get_template_part(
				'template-parts/contact/page-how-to-contact',
				null,
				array(
					'heading' => $how_heading,
					'steps'   => $how_step_rows,
				)
			);
			?>
		<?php endif; ?>

		<?php if ( $has_wa_band ) : ?>
			<?php
			get_template_part(
				'template-parts/shared/cta-wa-banner',
				null,
				array(
					'text'  => $wa_band_copy,
					'href'  => $wa_href,
					'label' => $whatsapp_label !== '' ? $whatsapp_label : __( 'Chat on WhatsApp', 'tenku-child' ),
					'id'    => 'vip-contact-cta-heading',
				)
			);
			?>
		<?php endif; ?>

		<?php if ( $map_html ) : ?>
			<section class="vip-border-section vip-border-section--surface vip-border-section--plain-panel vip-border-section--map" aria-label="<?php esc_attr_e( 'Map', 'tenku-child' ); ?>">
				<div class="vip-content-container">
					<div class="vip-page__map-embed">
						<?php echo $map_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( $faq_rows ) : ?>
			<?php
			get_template_part(
				'template-parts/shared/faq',
				'section',
				array(
					'heading'   => $faq_heading !== '' ? $faq_heading : __( 'Frequently Asked Questions', 'tenku-child' ),
					'intro'     => $faq_intro,
					'items'     => $faq_rows,
					'image'     => $faq_image,
					'id_prefix' => 'vip-contact-faq',
				)
			);
			?>
		<?php endif; ?>
	</div>
</article>
	<?php
	$vip_contact_html = (string) ob_get_clean();
	if ( function_exists( 'vip_transits_footer_debug_comment' ) ) {
		vip_transits_footer_debug_comment(
			'contact-block-buffer',
			array(
				'html_len'            => strlen( $vip_contact_html ),
				'has_vip_site_footer' => str_contains( $vip_contact_html, 'vip-site-footer' ) ? 1 : 0,
				'footer_rendered'     => ! empty( $GLOBALS['vip_transits_footer_rendered'] ) ? 1 : 0,
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
