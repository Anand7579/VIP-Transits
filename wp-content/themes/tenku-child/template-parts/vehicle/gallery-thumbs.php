<?php
/**
 * Vehicle single — thumbnail strip with overlay nav.
 *
 * @package Tenku_Child
 *
 * @var array $args {
 *     @type array $gallery Gallery image rows from vehicle data.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$gallery = isset( $args['gallery'] ) && is_array( $args['gallery'] ) ? $args['gallery'] : array();

if ( count( $gallery ) < 2 ) {
	return;
}

$has_slider  = count( $gallery ) > 3;
$bar_classes = 'vip-vdetail__gallery-thumbs-bar';
if ( $has_slider ) {
	$bar_classes .= ' vip-vdetail__gallery-thumbs-bar--has-nav';
}
?>
<div class="<?php echo esc_attr( $bar_classes ); ?>">
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

	<?php if ( $has_slider ) : ?>
		<span class="vip-vdetail__gallery-fade vip-vdetail__gallery-fade--start" aria-hidden="true"></span>
		<span class="vip-vdetail__gallery-fade vip-vdetail__gallery-fade--end" aria-hidden="true"></span>

		<button
			type="button"
			class="vip-vdetail__gallery-nav vip-vdetail__gallery-nav--prev"
			data-vip-gallery-prev
			aria-label="<?php esc_attr_e( 'Previous images', 'tenku-child' ); ?>"
		>
			<img
				class="vip-vdetail__gallery-nav-icon"
				src="<?php echo esc_url( vip_transits_vehicle_gallery_arrow_url( 'prev' ) ); ?>"
				alt=""
				width="20"
				height="6"
				decoding="async"
			/>
		</button>

		<button
			type="button"
			class="vip-vdetail__gallery-nav vip-vdetail__gallery-nav--next"
			data-vip-gallery-next
			aria-label="<?php esc_attr_e( 'Next images', 'tenku-child' ); ?>"
		>
			<img
				class="vip-vdetail__gallery-nav-icon"
				src="<?php echo esc_url( vip_transits_vehicle_gallery_arrow_url( 'next' ) ); ?>"
				alt=""
				width="20"
				height="6"
				decoding="async"
			/>
		</button>
	<?php endif; ?>
</div>
