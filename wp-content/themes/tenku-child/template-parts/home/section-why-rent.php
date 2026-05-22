<?php
/**
 * Why rent with VIP Transits — 4-column feature grid (Figma).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = get_sub_field( 'heading' );
$heading = $heading ? (string) $heading : __( 'Why Rent With VIP Transits', 'tenku-child' );

$default_cards = array(
	array(
		'title'        => __( 'Any car in Dubai, not just what we own', 'tenku-child' ),
		'description'  => __( 'Most rental companies only offer cars from their fixed fleet. We operate differently. If a car exists in Dubai and you want to rent it, message us. We source on demand - which is how we can offer Bugatti, Koenigsegg, and Pagani when every other company says no.', 'tenku-child' ),
		'button_label' => __( 'CORPORATE ENQUIRY', 'tenku-child' ),
	),
	array(
		'title'        => __( 'WhatsApp response in under 15 minutes', 'tenku-child' ),
		'description'  => __( 'No contact forms that go to a queue. No email threads that take days. One WhatsApp message gets you a real reply from our team within 15 minutes during operating hours - and usually faster.', 'tenku-child' ),
		'button_label' => __( 'CHECK F1 AVAILABILITY', 'tenku-child' ),
	),
	array(
		'title'        => __( 'Free delivery anywhere in Dubai', 'tenku-child' ),
		'description'  => __( 'The car comes to you. Hotel, villa, apartment, or airport - delivery is included in every rental. You choose the address; we handle the logistics.', 'tenku-child' ),
		'button_label' => __( 'BOOK A BIRTHDAY SURPRISE', 'tenku-child' ),
	),
	array(
		'title'        => __( 'Every licence accepted, tourists welcome', 'tenku-child' ),
		'description'  => __( "UK, US, EU, Indian, Australian, and GCC licences are all accepted. If your nationality requires an International Driving Permit, we'll tell you upfront when you enquire - no surprises at delivery.", 'tenku-child' ),
		'button_label' => __( 'BOOK YOUR PHOTOSHOOT', 'tenku-child' ),
	),
);

$cards = array();

if ( have_rows( 'cards' ) ) {
	while ( have_rows( 'cards' ) ) {
		the_row();
		$title = get_sub_field( 'title' );
		$desc  = get_sub_field( 'description' );
		$image = get_sub_field( 'image' );

		if ( ! $title && ! $desc && ! ( is_array( $image ) && ! empty( $image['url'] ) ) ) {
			continue;
		}

		$cards[] = array(
			'title'        => (string) $title,
			'description'  => (string) $desc,
			'image'        => is_array( $image ) ? $image : null,
			'button_label' => (string) get_sub_field( 'button_label' ),
		);
	}
}

if ( empty( $cards ) ) {
	foreach ( $default_cards as $item ) {
		$cards[] = array(
			'title'        => $item['title'],
			'description'  => $item['description'],
			'image'        => null,
			'button_label' => $item['button_label'],
		);
	}
}

if ( ! $heading && empty( $cards ) ) {
	return;
}
?>
<section class="vip-why-rent" aria-labelledby="vip-why-rent-heading">
	<div class="vip-why-rent__container vip-content-container">
		<header class="vip-why-rent__header">
			<?php if ( $heading ) : ?>
				<h2 id="vip-why-rent-heading" class="vip-why-rent__title"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<hr class="vip-why-rent__rule" />
		</header>

		<div class="vip-why-rent__grid" role="list">
			<?php foreach ( $cards as $card ) : ?>
				<?php
				$title = isset( $card['title'] ) ? (string) $card['title'] : '';
				$desc  = isset( $card['description'] ) ? (string) $card['description'] : '';
				$image = isset( $card['image'] ) && is_array( $card['image'] ) ? $card['image'] : null;

				$label = function_exists( 'vip_transits_occasion_button_label' )
					? vip_transits_occasion_button_label( $card, __( 'WhatsApp us', 'tenku-child' ) )
					: __( 'WhatsApp us', 'tenku-child' );

				$btn_href_attr = function_exists( 'vip_transits_whatsapp_href_attr' )
					? vip_transits_whatsapp_href_attr( vip_transits_occasion_whatsapp_message( $title, $desc ) )
					: '';
				?>
				<article class="vip-why-rent__card" role="listitem">
					<figure class="vip-why-rent__media">
						<?php if ( $image && ! empty( $image['ID'] ) ) : ?>
							<?php
							echo wp_get_attachment_image(
								(int) $image['ID'],
								'large',
								false,
								array(
									'class'    => 'vip-why-rent__img',
									'loading'  => 'lazy',
									'decoding' => 'async',
									'alt'      => ! empty( $image['alt'] ) ? (string) $image['alt'] : $title,
								)
							);
							?>
						<?php elseif ( $image && ! empty( $image['url'] ) ) : ?>
							<img
								class="vip-why-rent__img"
								src="<?php echo esc_url( $image['url'] ); ?>"
								alt="<?php echo esc_attr( ! empty( $image['alt'] ) ? (string) $image['alt'] : $title ); ?>"
								loading="lazy"
								decoding="async"
							/>
						<?php else : ?>
							<span class="vip-why-rent__placeholder" aria-hidden="true"></span>
						<?php endif; ?>
					</figure>
					<div class="vip-why-rent__body">
						<?php if ( $title ) : ?>
							<h3 class="vip-why-rent__name"><?php echo esc_html( $title ); ?></h3>
						<?php endif; ?>
						<?php if ( $desc ) : ?>
							<p class="vip-why-rent__text"><?php echo esc_html( $desc ); ?></p>
						<?php endif; ?>
						<?php if ( $btn_href_attr ) : ?>
							<a class="vip-why-rent__btn" href="<?php echo $btn_href_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" target="_blank" rel="noopener noreferrer">
								<span class="vip-why-rent__btn-label"><?php echo esc_html( $label ); ?></span>
								<?php
								if ( function_exists( 'vip_transits_theme_icon_arrow_html' ) ) {
									echo vip_transits_theme_icon_arrow_html( 'vip-why-rent__btn-arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								} else {
									echo '<span class="vip-why-rent__btn-arrow" aria-hidden="true">→</span>';
								}
								?>
							</a>
						<?php endif; ?>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
