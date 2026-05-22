<?php
/**
 * Delivery locations across Dubai (Figma: black section, 3×2 grid).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = get_sub_field( 'heading' );
$intro   = get_sub_field( 'intro' );

$heading = $heading ? (string) $heading : __( 'Delivery Locations Across Dubai', 'tenku-child' );
$intro   = $intro ? (string) $intro : __( 'We deliver to every corner of Dubai - free, on time, and to your exact address.', 'tenku-child' );

$default_locations = array(
	array(
		'title'       => __( 'Dubai Marina', 'tenku-child' ),
		'description' => __( 'Waterfront delivery to any Marina Walk residence, hotel, or yacht.', 'tenku-child' ),
	),
	array(
		'title'       => __( 'Palm Jumeirah', 'tenku-child' ),
		'description' => __( 'Delivered to Atlantis, FIVE Palm, One&Only, or your private villa gate.', 'tenku-child' ),
	),
	array(
		'title'       => __( 'Dubai Airport (DXB)', 'tenku-child' ),
		'description' => __( 'Land and drive. We meet you at Arrivals before your bags arrive.', 'tenku-child' ),
	),
	array(
		'title'       => __( 'Downtown Dubai', 'tenku-child' ),
		'description' => __( 'Burj Khalifa, Address Hotel, Dubai Mall - delivered to your door.', 'tenku-child' ),
	),
	array(
		'title'       => __( 'JBR & Bluewaters', 'tenku-child' ),
		'description' => __( 'The Walk, Ain Dubai, Bluewaters Island - anytime, same day.', 'tenku-child' ),
	),
	array(
		'title'       => __( 'DIFC', 'tenku-child' ),
		'description' => __( "Corporate delivery to Dubai's financial district. G63 or Rolls Royce at the curb.", 'tenku-child' ),
	),
);

$locations = array();

if ( have_rows( 'locations' ) ) {
	while ( have_rows( 'locations' ) ) {
		the_row();
		$title = get_sub_field( 'title' );
		$desc  = get_sub_field( 'description' );
		$image = get_sub_field( 'image' );

		if ( ! $title && ! $desc && ! ( is_array( $image ) && ! empty( $image['url'] ) ) ) {
			continue;
		}

		$locations[] = array(
			'title'       => (string) $title,
			'description' => (string) $desc,
			'image'       => is_array( $image ) ? $image : null,
		);
	}
}

if ( empty( $locations ) ) {
	foreach ( $default_locations as $item ) {
		$locations[] = array(
			'title'       => $item['title'],
			'description' => $item['description'],
			'image'       => null,
		);
	}
}

if ( ! $heading && empty( $locations ) ) {
	return;
}
?>
<section class="vip-delivery" data-vip-section aria-labelledby="vip-delivery-heading">
	<div class="vip-delivery__container vip-content-container">
		<header class="vip-delivery__header">
			<?php if ( $heading ) : ?>
				<h2 id="vip-delivery-heading" class="vip-delivery__title"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( $intro ) : ?>
				<p class="vip-delivery__intro"><?php echo esc_html( $intro ); ?></p>
			<?php endif; ?>
			<hr class="vip-delivery__rule" />
		</header>

		<div class="vip-delivery__grid" role="list">
			<?php foreach ( $locations as $location ) : ?>
				<?php
				$title = isset( $location['title'] ) ? (string) $location['title'] : '';
				$desc  = isset( $location['description'] ) ? (string) $location['description'] : '';
				$image = isset( $location['image'] ) && is_array( $location['image'] ) ? $location['image'] : null;
				?>
				<div class="vip-delivery__item" role="listitem">
					<article class="vip-delivery__card">
						<figure class="vip-delivery__media">
							<?php if ( $image && ! empty( $image['ID'] ) ) : ?>
								<?php
								echo wp_get_attachment_image(
									(int) $image['ID'],
									'large',
									false,
									array(
										'class'    => 'vip-delivery__img',
										'loading'  => 'lazy',
										'decoding' => 'async',
										'alt'      => ! empty( $image['alt'] ) ? (string) $image['alt'] : $title,
									)
								);
								?>
							<?php elseif ( $image && ! empty( $image['url'] ) ) : ?>
								<img
									class="vip-delivery__img"
									src="<?php echo esc_url( $image['url'] ); ?>"
									alt="<?php echo esc_attr( ! empty( $image['alt'] ) ? (string) $image['alt'] : $title ); ?>"
									loading="lazy"
									decoding="async"
								/>
							<?php else : ?>
								<span class="vip-delivery__placeholder" aria-hidden="true"></span>
							<?php endif; ?>
						</figure>
						<?php if ( $title ) : ?>
							<h3 class="vip-delivery__name"><?php echo esc_html( $title ); ?></h3>
						<?php endif; ?>
						<?php if ( $desc ) : ?>
							<p class="vip-delivery__text"><?php echo esc_html( $desc ); ?></p>
						<?php endif; ?>
					</article>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
