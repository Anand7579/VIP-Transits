<?php
/**
 * Title: VIP site footer
 * Slug: tenku-child/footer
 * Categories: footer
 * Block Types: core/template-part/footer
 *
 * @package Tenku_Child
 */

$footer_year = gmdate( 'Y' );
$home_url    = home_url( '/' );
?>
<!-- wp:group {"tagName":"footer","metadata":{"name":"VIP Footer"},"align":"full","className":"vip-site-footer","layout":{"type":"default"}} -->
<footer class="wp-block-group alignfull vip-site-footer">
	<!-- wp:group {"className":"vip-site-footer__container vip-content-container","layout":{"type":"default"}} -->
	<div class="wp-block-group vip-site-footer__container vip-content-container">
		<!-- wp:group {"className":"vip-site-footer__inner","layout":{"type":"default"}} -->
		<div class="wp-block-group vip-site-footer__inner">
			<!-- wp:group {"className":"vip-site-footer__main","layout":{"type":"default"}} -->
			<div class="wp-block-group vip-site-footer__main">
				<!-- wp:group {"className":"vip-site-footer__brand","layout":{"type":"default"}} -->
				<div class="wp-block-group vip-site-footer__brand">
					<!-- wp:site-logo {"width":140,"shouldSyncIcon":true,"className":"vip-site-footer__logo"} /-->
				</div>
				<!-- /wp:group -->

				<!-- wp:group {"className":"vip-site-footer__content","layout":{"type":"default"}} -->
				<div class="wp-block-group vip-site-footer__content">
					<!-- wp:paragraph {"className":"vip-site-footer__desc"} -->
					<p class="vip-site-footer__desc"><?php esc_html_e( 'VIP Transits delivers a seamless luxury car rental experience across Dubai, offering elite vehicles on demand to your hotel, villa, or airport with just a simple WhatsApp request.', 'tenku-child' ); ?></p>
					<!-- /wp:paragraph -->

					<!-- wp:group {"className":"vip-site-footer__nav-row","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"left","verticalAlignment":"center"}} -->
					<div class="wp-block-group vip-site-footer__nav-row">
						<!-- wp:navigation {"className":"vip-site-footer__nav","overlayMenu":"never","fontSize":"medium","layout":{"type":"flex","justifyContent":"left"}} /-->

						<!-- wp:social-links {"iconColor":"base","iconColorValue":"#ffffff","size":"has-small-icon-size","className":"is-style-logos-only vip-site-footer__social","layout":{"type":"flex","justifyContent":"left"}} -->
						<ul class="wp-block-social-links has-small-icon-size has-icon-color is-style-logos-only vip-site-footer__social">
							<!-- wp:social-link {"url":"#","service":"instagram"} /-->
						</ul>
						<!-- /wp:social-links -->
					</div>
					<!-- /wp:group -->

					<!-- wp:paragraph {"className":"vip-site-footer__legal"} -->
					<p class="vip-site-footer__legal">
						<?php
						echo wp_kses_post(
							sprintf(
								/* translators: 1: year, 2: home link, 3: privacy link, 4: terms link */
								__( 'Copyright © %1$s | %2$s | %3$s | %4$s', 'tenku-child' ),
								esc_html( $footer_year ),
								'<a href="' . esc_url( $home_url ) . '">viptransits.com</a>',
								'<a href="' . esc_url( home_url( '/privacy-policy/' ) ) . '">' . esc_html__( 'Privacy Policy', 'tenku-child' ) . '</a>',
								'<a href="' . esc_url( home_url( '/terms-and-conditions/' ) ) . '">' . esc_html__( 'Terms & Conditions', 'tenku-child' ) . '</a>'
							)
						);
						?>
					</p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</footer>
<!-- /wp:group -->
