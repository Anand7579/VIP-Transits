<?php
/**
 * Single vehicle — page masthead (Figma: black bar, title, price, CTAs).
 *
 * Expects $d from vip_transits_get_vehicle_single_data().
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

$d = isset( $args['d'] ) && is_array( $args['d'] ) ? $args['d'] : array();
if ( empty( $d['id'] ) ) {
	return;
}

$home_url    = home_url( '/' );
$price_fmt   = ! empty( $d['daily_price'] ) ? number_format_i18n( (int) $d['daily_price'] ) : '';
$deposit_fmt = number_format_i18n( (int) $d['security_deposit'] );
?>
<header class="vip-vdetail__masthead">
	<div class="vip-vdetail__masthead-inner vip-content-container">
		<div class="vip-vdetail__masthead-left">
			<h1 class="vip-vdetail__masthead-title"><?php echo esc_html( $d['display_title'] ); ?></h1>

			<?php if ( $price_fmt ) : ?>
				<div class="vip-vdetail__masthead-price">
					<span class="vip-vdetail__masthead-from"><?php esc_html_e( 'From', 'tenku-child' ); ?></span>
					<div class="vip-vdetail__masthead-rate">
						<span class="vip-vdetail__masthead-amount"><?php echo esc_html( sprintf( 'AED %s', $price_fmt ) ); ?></span><span class="vip-vdetail__masthead-unit">/day</span>
					</div>
				</div>
			<?php endif; ?>

			<p class="vip-vdetail__masthead-deposit">
				<?php esc_html_e( 'Refundable deposit:', 'tenku-child' ); ?>
				<span class="vip-vdetail__masthead-deposit-amount"> <?php echo esc_html( sprintf( 'AED %s', $deposit_fmt ) ); ?></span>
			</p>
		</div>

		<div class="vip-vdetail__masthead-right">
			<nav class="vip-vdetail__masthead-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'tenku-child' ); ?>">
				<a href="<?php echo esc_url( $home_url ); ?>"><?php esc_html_e( 'Home', 'tenku-child' ); ?></a>
				<span class="vip-vdetail__masthead-breadcrumb-sep" aria-hidden="true">
					<?php
					if ( function_exists( 'vip_transits_theme_icon_img' ) ) {
						echo vip_transits_theme_icon_img(
							'breadcrumb-arrow',
							array(
								'class'  => 'vip-vdetail__masthead-breadcrumb-arrow',
								'width'  => 12,
								'height' => 12,
							)
						); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					} else {
						echo '→';
					}
					?>
				</span>
				<span class="vip-vdetail__masthead-breadcrumb-current"><?php esc_html_e( 'CAR DETAILS', 'tenku-child' ); ?></span>
			</nav>

			<?php if ( ! empty( $d['wa_href_attr'] ) || ! empty( $d['tel_href'] ) ) : ?>
				<div class="vip-vdetail__masthead-actions">
					<?php if ( ! empty( $d['wa_href_attr'] ) ) : ?>
						<a class="vip-vdetail__masthead-wa" href="<?php echo $d['wa_href_attr']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" target="_blank" rel="noopener noreferrer">
							<span class="vip-vdetail__masthead-wa-inner">
								<span class="vip-vdetail__masthead-wa-icon" aria-hidden="true">
									<svg width="22" height="22" viewBox="0 0 24 24" focusable="false"><path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.882 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
								</span>
								<span class="vip-vdetail__masthead-wa-label"><?php esc_html_e( 'Book in a few minutes – Chat on WhatsApp now', 'tenku-child' ); ?></span>
							</span>
						</a>
					<?php endif; ?>
					<?php if ( ! empty( $d['tel_href'] ) ) : ?>
						<a class="vip-vdetail__masthead-call" href="<?php echo esc_url( $d['tel_href'] ); ?>" aria-label="<?php esc_attr_e( 'Call now', 'tenku-child' ); ?>">
							<svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 4h3l2 5-2.5 1.5a11 11 0 005 5L13 13l5 2v3a2 2 0 01-2 2A15 15 0 013 6a2 2 0 012-2z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/></svg>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</header>
