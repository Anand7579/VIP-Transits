<?php
/**
 * Title: VIP site header
 * Slug: tenku-child/header
 * Categories: header
 * Block Types: core/template-part/header
 *
 * @package Tenku_Child
 */
?>
<!-- wp:group {"tagName":"header","metadata":{"name":"VIP Header"},"align":"full","className":"vip-site-header","layout":{"type":"default"}} -->
<header class="wp-block-group alignfull vip-site-header">
	<!-- wp:group {"className":"vip-site-header__container vip-content-container","layout":{"type":"default"}} -->
	<div class="wp-block-group vip-site-header__container vip-content-container">
		<!-- wp:group {"className":"vip-site-header__inner","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between","verticalAlignment":"center"}} -->
		<div class="wp-block-group vip-site-header__inner">
			<!-- wp:group {"className":"vip-site-header__brand","layout":{"type":"flex","orientation":"vertical","justifyContent":"center"}} -->
			<div class="wp-block-group vip-site-header__brand">
				<!-- wp:site-logo {"width":72,"shouldSyncIcon":true} /-->

				<!-- wp:site-tagline {"style":{"typography":{"fontSize":"0.55rem","textTransform":"uppercase","letterSpacing":"0.14em"}},"textColor":"base","className":"vip-site-header__tagline"} /-->
			</div>
			<!-- /wp:group -->

			<!-- wp:group {"className":"vip-site-header__end","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"right","verticalAlignment":"center"}} -->
			<div class="wp-block-group vip-site-header__end">
				<!-- wp:navigation {"className":"vip-site-header__nav","fontSize":"medium","layout":{"type":"flex","justifyContent":"right"}} /-->

				<!-- wp:html -->
				<a class="vip-site-header__search-link" href="<?php echo esc_url( home_url( '/?s=' ) ); ?>" aria-label="<?php esc_attr_e( 'Search', 'tenku-child' ); ?>">
					<?php
					if ( function_exists( 'vip_transits_theme_icon_img' ) ) {
						echo vip_transits_theme_icon_img( 'search', array( 'class' => 'vip-site-header__search-icon' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</a>
				<!-- /wp:html -->

				<!-- wp:social-links {"iconColor":"base","iconColorValue":"#ffffff","size":"has-small-icon-size","className":"is-style-logos-only vip-site-header__social","layout":{"type":"flex","justifyContent":"right"}} -->
				<ul class="wp-block-social-links has-small-icon-size has-icon-color is-style-logos-only vip-site-header__social">
					<!-- wp:social-link {"url":"#","service":"instagram"} /-->
				</ul>
				<!-- /wp:social-links -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</header>
<!-- /wp:group -->
