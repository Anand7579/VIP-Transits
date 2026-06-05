<?php
/**
 * Contact content layout: definition callout.
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
$content = function_exists( 'vip_transits_contact_content_replace_brand' )
	? vip_transits_contact_content_replace_brand( (string) get_sub_field( 'content' ), $brand )
	: (string) get_sub_field( 'content' );

if ( ! $heading && ! $content ) {
	return;
}
?>
<section class="vip-border-section vip-border-section--plain-panel vip-contact-content-section vip-contact-content-section--definition">
	<div class="vip-content-container">
		<div class="vip-contact-content-definition">
			<?php if ( $heading ) : ?>
				<h2 class="vip-contact-content-definition__question"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( $content ) : ?>
				<p class="vip-contact-content-definition__answer"><?php echo esc_html( $content ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>
