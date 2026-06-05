<?php
/**
 * Contact content layout: contact details list.
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = (string) get_sub_field( 'heading' );
$rows    = array();

if ( have_rows( 'details' ) ) {
	while ( have_rows( 'details' ) ) {
		the_row();
		$label = trim( (string) get_sub_field( 'label' ) );
		$value = trim( (string) get_sub_field( 'value' ) );
		$type  = (string) get_sub_field( 'type' );
		$link  = (string) get_sub_field( 'link' );

		if ( $value === '' ) {
			continue;
		}

		if ( function_exists( 'vip_transits_normalize_contact_detail_href' ) ) {
			$href = vip_transits_normalize_contact_detail_href( $link, $type, $value );
		} else {
			$href = $link;
		}

		$rows[] = array(
			'label' => $label,
			'value' => $value,
			'href'  => $href,
		);
	}
}

if ( ! $heading && ! $rows ) {
	return;
}

$link_target = function_exists( 'vip_transits_link_target_attr' ) ? vip_transits_link_target_attr() : '';
$intro       = __( 'Reach our concierge team directly. For the fastest response, use WhatsApp.', 'tenku-child' );
?>
<section class="vip-border-section vip-border-section--surface vip-border-section--plain-panel vip-contact-content-section vip-contact-content-section--details" aria-labelledby="vip-contact-content-details-title">
	<div class="vip-content-container vip-contact-content-details__layout">
		<div class="vip-contact-content-details__intro">
			<?php if ( $heading ) : ?>
				<header class="vip-contact-content__head">
					<h2 id="vip-contact-content-details-title" class="vip-page__section-title"><?php echo esc_html( $heading ); ?></h2>
					<hr class="vip-contact-content__rule" />
				</header>
			<?php endif; ?>
			<p class="vip-contact-content-details__intro-text"><?php echo esc_html( $intro ); ?></p>
		</div>
		<?php if ( $rows ) : ?>
			<ul class="vip-contact-content-details__card">
				<?php foreach ( $rows as $row ) : ?>
					<li class="vip-contact-content-details__row">
						<?php if ( $row['label'] ) : ?>
							<p class="vip-contact-content-details__label"><?php echo esc_html( $row['label'] ); ?></p>
						<?php endif; ?>
						<?php if ( $row['href'] ) : ?>
							<p class="vip-contact-content-details__value"><a href="<?php echo esc_attr( $row['href'] ); ?>"<?php echo $link_target; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_html( $row['value'] ); ?></a></p>
						<?php else : ?>
							<p class="vip-contact-content-details__value"><?php echo esc_html( $row['value'] ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
