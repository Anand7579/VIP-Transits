<?php
/**
 * Contact content layout: hero intro.
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
$intro   = function_exists( 'vip_transits_contact_content_replace_brand' )
	? vip_transits_contact_content_replace_brand( (string) get_sub_field( 'intro' ), $brand )
	: (string) get_sub_field( 'intro' );
$show_wa = (bool) get_sub_field( 'show_whatsapp' );
$wa_label = trim( (string) get_sub_field( 'whatsapp_label' ) );
$wa_href  = function_exists( 'vip_transits_whatsapp_href_attr' )
	? vip_transits_whatsapp_href_attr( (string) get_sub_field( 'whatsapp_message' ) )
	: '';

if ( ! $heading && ! $intro && ! ( $show_wa && $wa_href ) ) {
	return;
}

$wa_label = $wa_label ? $wa_label : __( 'WhatsApp Us Now', 'tenku-child' );
?>
<section class="vip-contact-content-hero" aria-labelledby="vip-contact-content-hero-title">
	<div class="vip-content-container vip-contact-content-hero__inner">
		<div class="vip-contact-content-hero__copy">
			<?php if ( $heading ) : ?>
				<h2 id="vip-contact-content-hero-title" class="vip-contact-content-hero__title"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( $intro ) : ?>
				<p class="vip-contact-content-hero__text"><?php echo esc_html( $intro ); ?></p>
			<?php endif; ?>
		</div>
		<?php if ( $show_wa && $wa_href ) : ?>
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
		<?php endif; ?>
	</div>
</section>
