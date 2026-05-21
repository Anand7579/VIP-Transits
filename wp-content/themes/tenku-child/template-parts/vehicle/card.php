<?php
/**
 * Vehicle card (fleet grid).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$data = vip_transits_get_vehicle_card_data();
if ( empty( $data['id'] ) ) {
	return;
}

$brand_slugs = implode( ' ', array_map( 'sanitize_html_class', $data['brands'] ) );
$seat_slugs  = implode( ' ', array_map( 'sanitize_html_class', $data['seat_terms'] ) );
$wa_href_attr = function_exists( 'vip_transits_vehicle_whatsapp_href_attr' ) ? vip_transits_vehicle_whatsapp_href_attr( $data['id'] ) : '';
$phone_url   = $data['phone'] ? 'tel:' . preg_replace( '/[^\d+]/', '', $data['phone'] ) : '';
$color_hex   = $data['color_hex'] ? $data['color_hex'] : '#cccccc';
?>
<article
	class="vip-fleet-card"
	data-brands="<?php echo esc_attr( $brand_slugs ); ?>"
	data-seats="<?php echo esc_attr( $seat_slugs ); ?>"
	data-price="<?php echo esc_attr( (string) $data['daily_price'] ); ?>"
	data-delivery="<?php echo $data['delivery'] ? '1' : '0'; ?>"
>
	<?php if ( $data['thumbnail'] ) : ?>
		<a class="vip-fleet-card__media" href="<?php echo esc_url( $data['permalink'] ); ?>">
			<img
				class="vip-fleet-card__img"
				src="<?php echo esc_url( $data['thumbnail'] ); ?>"
				alt="<?php echo esc_attr( $data['title'] ); ?>"
				loading="lazy"
				width="333"
				height="170"
				decoding="async"
			/>
		</a>
	<?php endif; ?>

	<div class="vip-fleet-card__body">
		<div class="vip-fleet-card__head">
			<h3 class="vip-fleet-card__title">
				<a href="<?php echo esc_url( $data['permalink'] ); ?>"><?php echo esc_html( $data['title'] ); ?></a>
			</h3>
			<?php if ( $data['color_name'] ) : ?>
				<span class="vip-fleet-card__color">
					<?php echo esc_html( $data['color_name'] ); ?>
					<span class="vip-fleet-card__swatch" style="background-color: <?php echo esc_attr( $color_hex ); ?>;"></span>
				</span>
			<?php endif; ?>
		</div>

		<p class="vip-fleet-card__desc"><?php echo esc_html( $data['excerpt'] ); ?></p>

		<p class="vip-fleet-card__meta">
			<?php
			$meta_parts = array();
			if ( $data['engine_type'] ) {
				$meta_parts[] = sprintf(
					/* translators: %s: engine type value */
					__( 'Engine Type: %s', 'tenku-child' ),
					'<strong>' . esc_html( $data['engine_type'] ) . '</strong>'
				);
			}
			if ( $data['acceleration'] ) {
				$meta_parts[] = sprintf(
					/* translators: %s: acceleration value */
					__( '0-100 km/h: %s', 'tenku-child' ),
					'<strong>' . esc_html( $data['acceleration'] ) . '</strong>'
				);
			}
			if ( $meta_parts ) {
				echo wp_kses( implode( ' | ', $meta_parts ) . ' |', array( 'strong' => array() ) );
			}
			?>
		</p>

		<div class="vip-fleet-card__foot">
			<p class="vip-fleet-card__specs">
				<?php
				$spec_parts = array();
				if ( $data['doors'] ) {
					$spec_parts[] = sprintf(
						/* translators: %s: door count */
						__( 'Doors: %s', 'tenku-child' ),
						'<strong>' . esc_html( $data['doors'] ) . '</strong>'
					);
				}
				if ( $data['seats'] ) {
					$spec_parts[] = sprintf(
						/* translators: %s: seat count */
						__( 'Seats: %s', 'tenku-child' ),
						'<strong>' . esc_html( $data['seats'] ) . '</strong>'
					);
				}
				echo wp_kses( implode( ' | ', $spec_parts ), array( 'strong' => array() ) );
				?>
			</p>
			<?php if ( $data['daily_price'] ) : ?>
				<p class="vip-fleet-card__price">
					<?php
					echo wp_kses(
						sprintf(
							/* translators: %s: formatted price */
							__( 'AED %s / Per day', 'tenku-child' ),
							'<strong>' . esc_html( number_format_i18n( $data['daily_price'] ) ) . '</strong>'
						),
						array( 'strong' => array() )
					);
					?>
				</p>
			<?php endif; ?>
		</div>

		<div class="vip-fleet-card__actions">
			<?php if ( $wa_href_attr ) : ?>
			<a class="vip-fleet-card__book" href="<?php echo $wa_href_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- pre-escaped attr. ?>" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'BOOK NOW', 'tenku-child' ); ?>
			</a>
			<a class="vip-fleet-card__icon vip-fleet-card__icon--wa" href="<?php echo $wa_href_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'WhatsApp', 'tenku-child' ); ?>">
				<svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true"><path fill="#fff" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.882 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
			</a>
			<?php endif; ?>
			<?php if ( $phone_url ) : ?>
				<a class="vip-fleet-card__icon vip-fleet-card__icon--phone" href="<?php echo esc_url( $phone_url ); ?>" aria-label="<?php esc_attr_e( 'Call', 'tenku-child' ); ?>">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 4h3l2 5-2.5 1.5a11 11 0 005 5L13 13l5 2v3a2 2 0 01-2 2A15 15 0 013 6a2 2 0 012-2z" stroke="#fff" stroke-width="1.75" stroke-linejoin="round"/></svg>
				</a>
			<?php endif; ?>
		</div>
	</div>
</article>
