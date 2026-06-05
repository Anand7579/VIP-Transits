<?php
/**
 * Block: vip-page-contact-content (client SEO content brief layout).
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
?>
<article class="vip-page vip-page--contact-content" data-vip-section>
	<?php vip_transits_render_page_banner( $post_id ); ?>

	<div class="vip-page__body">
		<?php
		if ( function_exists( 'vip_transits_render_contact_content_sections' ) ) {
			vip_transits_render_contact_content_sections( $post_id, $preview );
		} elseif ( $preview ) {
			echo '<p class="vip-page vip-page--contact-content-empty">' . esc_html__( 'Activate ACF Pro and sync VIP Contact Us (Content) fields.', 'tenku-child' ) . '</p>';
		}
		?>
	</div>
</article>
