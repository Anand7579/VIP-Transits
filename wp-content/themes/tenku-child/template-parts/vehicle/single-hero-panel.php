<?php
/**
 * Single vehicle — 60/40 gallery + pricing card (below masthead).
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

$gallery     = ! empty( $d['gallery'] ) && is_array( $d['gallery'] ) ? $d['gallery'] : array();
$price_fmt   = ! empty( $d['daily_price'] ) ? number_format_i18n( (int) $d['daily_price'] ) : '';
$deposit_fmt = number_format_i18n( (int) $d['security_deposit'] );

if ( empty( $gallery ) && ! empty( $d['hero_image'] ) ) {
	$gallery[] = array(
		'id'    => (int) get_post_thumbnail_id( $d['id'] ),
		'url'   => $d['hero_image'],
		'thumb' => $d['hero_image'],
		'alt'   => $d['short_name'],
	);
}

$delivery_label = ! empty( $d['delivery'] )
	? __( 'Free - Anywhere Dubai', 'tenku-child' )
	: __( 'Ask on WhatsApp', 'tenku-child' );

$main_image = ! empty( $gallery[0] ) ? $gallery[0] : null;
$has_slider = count( $gallery ) > 3;
?>
<section class="vip-vdetail__hero" data-vip-section>
	<div class="vip-vdetail__hero-layout">
		<div class="vip-vdetail__hero-main">
			<?php if ( $main_image ) : ?>
				<div class="vip-vdetail__gallery" data-vip-gallery>
					<div class="vip-vdetail__gallery-main">
						<img
							class="vip-vdetail__gallery-main-img"
							data-vip-gallery-main
							src="<?php echo esc_url( $main_image['url'] ); ?>"
							alt="<?php echo esc_attr( $main_image['alt'] ); ?>"
							width="960"
							height="540"
							decoding="async"
						/>
					</div>

					<?php if ( count( $gallery ) > 1 ) : ?>
						<div class="vip-vdetail__gallery-thumbs-bar">
							<button
								type="button"
								class="vip-vdetail__gallery-nav vip-vdetail__gallery-nav--prev"
								data-vip-gallery-prev
								aria-label="<?php esc_attr_e( 'Previous images', 'tenku-child' ); ?>"
								<?php echo $has_slider ? '' : ' hidden'; ?>
							>
								<span aria-hidden="true">‹</span>
							</button>

							<div class="vip-vdetail__gallery-thumbs-viewport" data-vip-gallery-viewport>
								<div class="vip-vdetail__gallery-thumbs-track" data-vip-gallery-track>
									<?php foreach ( $gallery as $index => $image ) : ?>
										<button
											type="button"
											class="vip-vdetail__gallery-thumb<?php echo 0 === (int) $index ? ' is-active' : ''; ?>"
											data-vip-gallery-thumb
											data-index="<?php echo esc_attr( (string) $index ); ?>"
											data-full="<?php echo esc_url( $image['url'] ); ?>"
											aria-label="<?php echo esc_attr( sprintf( __( 'Show image %d', 'tenku-child' ), (int) $index + 1 ) ); ?>"
											aria-pressed="<?php echo 0 === (int) $index ? 'true' : 'false'; ?>"
										>
											<img
												src="<?php echo esc_url( $image['thumb'] ); ?>"
												alt=""
												width="280"
												height="160"
												loading="lazy"
												decoding="async"
											/>
										</button>
									<?php endforeach; ?>
								</div>
							</div>

							<button
								type="button"
								class="vip-vdetail__gallery-nav vip-vdetail__gallery-nav--next"
								data-vip-gallery-next
								aria-label="<?php esc_attr_e( 'Next images', 'tenku-child' ); ?>"
								<?php echo $has_slider ? '' : ' hidden'; ?>
							>
								<span aria-hidden="true">›</span>
							</button>
						</div>
					<?php endif; ?>
				</div>
			<?php else : ?>
				<span class="vip-vdetail__gallery-placeholder" aria-hidden="true"></span>
			<?php endif; ?>
		</div>

		<aside class="vip-vdetail__pricing-card" aria-label="<?php esc_attr_e( 'Booking summary', 'tenku-child' ); ?>">
			<div class="vip-vdetail__pricing-head">
				<div class="vip-vdetail__pricing-head-left">
					<span class="vip-vdetail__pricing-from"><?php esc_html_e( 'From', 'tenku-child' ); ?></span>
					<?php if ( $price_fmt ) : ?>
						<p class="vip-vdetail__pricing-rate">
							<span class="vip-vdetail__pricing-amount"><?php echo esc_html( sprintf( 'AED %s', $price_fmt ) ); ?></span><span class="vip-vdetail__pricing-unit">/day</span>
						</p>
					<?php endif; ?>
				</div>
				<p class="vip-vdetail__pricing-deposit-hd">
					<?php esc_html_e( 'Deposit:', 'tenku-child' ); ?>
					<span><?php echo esc_html( sprintf( 'AED %s', $deposit_fmt ) ); ?></span>
				</p>
			</div>

			<div class="vip-vdetail__pricing-body">
				<dl class="vip-vdetail__pricing-rows">
					<div class="vip-vdetail__pricing-row">
						<dt><?php esc_html_e( 'Daily rate', 'tenku-child' ); ?></dt>
						<dd><?php echo $price_fmt ? esc_html( sprintf( 'AED %s', $price_fmt ) ) : '—'; ?></dd>
					</div>
					<div class="vip-vdetail__pricing-row">
						<dt><?php esc_html_e( 'Security deposit', 'tenku-child' ); ?></dt>
						<dd><?php echo esc_html( sprintf( 'AED %s (refundable)', $deposit_fmt ) ); ?></dd>
					</div>
					<div class="vip-vdetail__pricing-row">
						<dt><?php esc_html_e( 'Insurance', 'tenku-child' ); ?></dt>
						<dd><?php esc_html_e( 'Included', 'tenku-child' ); ?></dd>
					</div>
					<div class="vip-vdetail__pricing-row">
						<dt><?php esc_html_e( 'Delivery', 'tenku-child' ); ?></dt>
						<dd><?php echo esc_html( $delivery_label ); ?></dd>
					</div>
					<div class="vip-vdetail__pricing-row">
						<dt><?php esc_html_e( 'Weekly rate', 'tenku-child' ); ?></dt>
						<dd><?php echo esc_html( $d['weekly_rate'] ); ?></dd>
					</div>
				</dl>
			</div>

			<div class="vip-vdetail__pricing-foot">
				<?php if ( ! empty( $d['wa_href_attr'] ) || ! empty( $d['tel_href'] ) ) : ?>
					<div class="vip-vdetail__pricing-actions">
						<?php if ( ! empty( $d['wa_href_attr'] ) ) : ?>
							<a class="vip-vdetail__pricing-wa" href="<?php echo $d['wa_href_attr']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" target="_blank" rel="noopener noreferrer">
								<span class="vip-vdetail__pricing-wa-icon" aria-hidden="true">
									<svg width="22" height="22" viewBox="0 0 24 24" focusable="false"><path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.882 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
								</span>
								<span class="vip-vdetail__pricing-wa-text">
									<span class="vip-vdetail__pricing-wa-kicker"><?php esc_html_e( 'Book in a few minutes', 'tenku-child' ); ?></span>
									<span class="vip-vdetail__pricing-wa-label"><?php esc_html_e( 'Chat on WhatsApp now', 'tenku-child' ); ?></span>
								</span>
							</a>
						<?php endif; ?>
						<?php if ( ! empty( $d['tel_href'] ) ) : ?>
							<a class="vip-vdetail__pricing-call" href="<?php echo esc_url( $d['tel_href'] ); ?>" aria-label="<?php esc_attr_e( 'Call now', 'tenku-child' ); ?>">
								<svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 4h3l2 5-2.5 1.5a11 11 0 005 5L13 13l5 2v3a2 2 0 01-2 2A15 15 0 013 6a2 2 0 012-2z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/></svg>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<p class="vip-vdetail__pricing-note"><?php esc_html_e( 'Response within 15 minutes · No form · Instant confirmation', 'tenku-child' ); ?></p>
			</div>
		</aside>

		<?php if ( ! empty( $d['stats'] ) ) : ?>
			<ul class="vip-vdetail__stats">
				<?php foreach ( $d['stats'] as $stat ) : ?>
					<li class="vip-vdetail__stat">
						<span class="vip-vdetail__stat-value"><?php echo esc_html( $stat['value'] ); ?></span>
						<span class="vip-vdetail__stat-label"><?php echo esc_html( $stat['label'] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
