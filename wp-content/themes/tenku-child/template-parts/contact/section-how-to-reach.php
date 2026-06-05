<?php
/**
 * Contact content layout: how to reach us (steps).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = function_exists( 'vip_transits_page_content_post_id' ) ? vip_transits_page_content_post_id() : 0;
$brand   = function_exists( 'vip_transits_contact_content_brand_name' )
	? vip_transits_contact_content_brand_name( $post_id )
	: get_bloginfo( 'name' );

$heading = function_exists( 'vip_transits_contact_content_replace_brand' )
	? vip_transits_contact_content_replace_brand( (string) get_sub_field( 'heading' ), $brand )
	: (string) get_sub_field( 'heading' );
$show_wa  = (bool) get_sub_field( 'show_whatsapp' );
$wa_label = trim( (string) get_sub_field( 'whatsapp_label' ) );
$wa_href  = function_exists( 'vip_transits_whatsapp_href_attr' )
	? vip_transits_whatsapp_href_attr( (string) get_sub_field( 'whatsapp_message' ) )
	: '';

$steps = array();
if ( have_rows( 'steps' ) ) {
	while ( have_rows( 'steps' ) ) {
		the_row();
		$title = trim( (string) get_sub_field( 'title' ) );
		$text  = trim( (string) get_sub_field( 'text' ) );
		if ( $title === '' && $text === '' ) {
			continue;
		}
		$steps[] = array(
			'title' => $title,
			'text'  => $text,
		);
	}
}

if ( ! $heading && ! $steps && ! ( $show_wa && $wa_href ) ) {
	return;
}

$wa_label = $wa_label ? $wa_label : __( 'WhatsApp to Book', 'tenku-child' );
$cta_copy = __( 'Ready to book? Message us on WhatsApp and we will confirm your car within minutes.', 'tenku-child' );
?>
<section class="vip-border-section vip-border-section--plain-panel vip-contact-content-section vip-contact-content-section--how-to-reach" aria-labelledby="vip-contact-content-how-to-reach-title">
	<div class="vip-content-container">
		<?php if ( $heading ) : ?>
			<header class="vip-contact-content__head">
				<h2 id="vip-contact-content-how-to-reach-title" class="vip-page__section-title"><?php echo esc_html( $heading ); ?></h2>
				<hr class="vip-contact-content__rule" />
			</header>
		<?php endif; ?>
		<?php if ( $steps ) : ?>
			<ol class="vip-contact-content-steps">
				<?php foreach ( $steps as $index => $step ) : ?>
					<li class="vip-contact-content-steps__item">
						<span class="vip-contact-content-steps__number" aria-hidden="true"><?php echo esc_html( (string) ( $index + 1 ) ); ?></span>
						<?php if ( $step['title'] ) : ?>
							<h3 class="vip-contact-content-steps__title"><?php echo esc_html( $step['title'] ); ?></h3>
						<?php endif; ?>
						<?php if ( $step['text'] ) : ?>
							<p class="vip-contact-content-steps__text"><?php echo esc_html( $step['text'] ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
		<?php if ( $show_wa && $wa_href ) : ?>
			<div class="vip-contact-content-steps__cta-bar">
				<p class="vip-contact-content-steps__cta-copy"><?php echo esc_html( $cta_copy ); ?></p>
				<?php
				get_template_part(
					'template-parts/shared/whatsapp-split-button',
					null,
					array(
						'href'  => $wa_href,
						'label' => $wa_label,
					)
				);
				?>
			</div>
		<?php endif; ?>
	</div>
</section>
