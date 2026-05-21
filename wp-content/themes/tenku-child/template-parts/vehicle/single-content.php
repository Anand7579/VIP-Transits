<?php
/**
 * Single vehicle body (used by acf/vip-vehicle-single block).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$data = vip_transits_get_vehicle_card_data();
$wa_href_attr = function_exists( 'vip_transits_vehicle_whatsapp_href_attr' ) ? vip_transits_vehicle_whatsapp_href_attr( $data['id'] ) : '';
$tel  = $data['phone'] ? 'tel:' . preg_replace( '/[^\d+]/', '', $data['phone'] ) : '';
?>
<main class="vip-vehicle-single">
	<div class="vip-vehicle-single__container vip-content-container">
		<article <?php post_class( 'vip-vehicle-single__article' ); ?>>
			<?php if ( $data['thumbnail'] ) : ?>
				<div class="vip-vehicle-single__media">
					<img src="<?php echo esc_url( $data['thumbnail'] ); ?>" alt="<?php echo esc_attr( $data['title'] ); ?>" />
				</div>
			<?php endif; ?>

			<div class="vip-vehicle-single__content">
				<header class="vip-vehicle-single__header">
					<h1 class="vip-vehicle-single__title"><?php the_title(); ?></h1>
					<?php if ( $data['color_name'] ) : ?>
						<span class="vip-fleet-card__color">
							<span class="vip-fleet-card__swatch" style="background-color: <?php echo esc_attr( $data['color_hex'] ?: '#ccc' ); ?>;"></span>
							<?php echo esc_html( $data['color_name'] ); ?>
						</span>
					<?php endif; ?>
				</header>

				<div class="vip-vehicle-single__body">
					<?php the_content(); ?>
				</div>

				<ul class="vip-vehicle-single__specs">
					<?php if ( $data['engine_type'] ) : ?>
						<li><?php echo esc_html( sprintf( __( 'Engine: %s', 'tenku-child' ), $data['engine_type'] ) ); ?></li>
					<?php endif; ?>
					<?php if ( $data['acceleration'] ) : ?>
						<li><?php echo esc_html( sprintf( __( '0-100 km/h: %s', 'tenku-child' ), $data['acceleration'] ) ); ?></li>
					<?php endif; ?>
					<?php if ( $data['doors'] ) : ?>
						<li><?php echo esc_html( sprintf( __( 'Doors: %s', 'tenku-child' ), $data['doors'] ) ); ?></li>
					<?php endif; ?>
					<?php if ( $data['seats'] ) : ?>
						<li><?php echo esc_html( sprintf( __( 'Seats: %s', 'tenku-child' ), $data['seats'] ) ); ?></li>
					<?php endif; ?>
				</ul>

				<?php if ( $data['daily_price'] ) : ?>
					<p class="vip-vehicle-single__price">
						<strong><?php echo esc_html( sprintf( 'AED %s', number_format_i18n( $data['daily_price'] ) ) ); ?></strong>
						<?php esc_html_e( '/ Per day', 'tenku-child' ); ?>
					</p>
				<?php endif; ?>

				<?php if ( $wa_href_attr ) : ?>
				<div class="vip-fleet-card__actions vip-vehicle-single__actions">
					<a class="vip-fleet-card__book" href="<?php echo $wa_href_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Book now', 'tenku-child' ); ?></a>
					<a class="vip-fleet-card__icon vip-fleet-card__icon--wa" href="<?php echo $wa_href_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'WhatsApp', 'tenku-child' ); ?>">
						<svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true"><path fill="#fff" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.882 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
					</a>
					<?php if ( $tel ) : ?>
						<a class="vip-fleet-card__icon vip-fleet-card__icon--phone" href="<?php echo esc_url( $tel ); ?>" aria-label="<?php esc_attr_e( 'Call', 'tenku-child' ); ?>">
							<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 4h3l2 5-2.5 1.5a11 11 0 005 5L13 13l5 2v3a2 2 0 01-2 2A15 15 0 013 6a2 2 0 012-2z" stroke="#fff" stroke-width="1.75" stroke-linejoin="round"/></svg>
						</a>
					<?php endif; ?>
				</div>
				<?php endif; ?>

				<p class="vip-vehicle-single__back">
					<a href="<?php echo esc_url( get_post_type_archive_link( 'vip_vehicle' ) ); ?>">← <?php esc_html_e( 'Back to fleet', 'tenku-child' ); ?></a>
				</p>
			</div>
		</article>
	</div>
</main>
