<?php
/**
 * Driving route card (vehicle single — best routes grid).
 *
 * @package Tenku_Child
 *
 * @var array $args {
 *     @type array $route Keys: title, description, image_url.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$route = isset( $args['route'] ) && is_array( $args['route'] ) ? $args['route'] : array();
$title = isset( $route['title'] ) ? trim( (string) $route['title'] ) : '';
$desc  = isset( $route['description'] ) ? trim( (string) $route['description'] ) : '';
$img   = isset( $route['image_url'] ) ? (string) $route['image_url'] : '';

if ( $title === '' && $desc === '' && $img === '' ) {
	return;
}

if ( $img === '' && function_exists( 'vip_transits_driving_route_image_url' ) ) {
	$img = vip_transits_driving_route_image_url( $title, get_the_ID() );
}
?>
<li class="vip-vdetail__route-card">
	<div class="vip-vdetail__route-media">
		<?php if ( $img ) : ?>
			<img
				src="<?php echo esc_url( $img ); ?>"
				alt="<?php echo esc_attr( $title ); ?>"
				loading="lazy"
				width="380"
				height="200"
				decoding="async"
			/>
		<?php endif; ?>
		<div class="vip-vdetail__route-overlay">
			<?php if ( $title ) : ?>
				<h3 class="vip-vdetail__route-title"><?php echo esc_html( $title ); ?></h3>
			<?php endif; ?>
			<?php if ( $desc ) : ?>
				<p class="vip-vdetail__route-text"><?php echo esc_html( $desc ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</li>
