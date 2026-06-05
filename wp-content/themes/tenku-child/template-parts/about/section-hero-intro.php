<?php
/**
 * About flexible layout: hero intro.
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading      = (string) get_sub_field( 'heading' );
$intro        = (string) get_sub_field( 'intro' );
$image        = get_sub_field( 'image' );
$show_wa      = (bool) get_sub_field( 'show_whatsapp' );
$wa_label     = trim( (string) get_sub_field( 'whatsapp_label' ) );
$show_fleet   = (bool) get_sub_field( 'show_fleet_link' );
$fleet_label  = trim( (string) get_sub_field( 'fleet_label' ) );
$fleet_url    = get_post_type_archive_link( 'vip_vehicle' );
$wa_href      = function_exists( 'vip_transits_whatsapp_href_attr' ) ? vip_transits_whatsapp_href_attr() : '';

if ( ! $heading && ! $intro && ( ! is_array( $image ) || empty( $image['url'] ) ) && ! $show_wa && ! $show_fleet ) {
	return;
}

$wa_label    = $wa_label ? $wa_label : __( 'WhatsApp Us Now', 'tenku-child' );
$fleet_label = $fleet_label ? $fleet_label : __( 'Browse our Fleet', 'tenku-child' );
$has_image   = is_array( $image ) && ! empty( $image['url'] );
$grid_class  = 'vip-page__intro-grid' . ( $has_image ? '' : ' vip-page__intro-grid--copy-only' );
?>
<section class="vip-border-section vip-border-section--surface vip-border-section--plain-panel vip-about-section vip-about-section--hero-intro">
	<div class="vip-content-container <?php echo esc_attr( $grid_class ); ?>">
		<div class="vip-page__intro-copy">
			<?php if ( $heading ) : ?>
				<h2 class="vip-page__section-title"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( $intro ) : ?>
				<div class="vip-page__prose"><?php echo wp_kses_post( $intro ); ?></div>
			<?php endif; ?>
			<?php if ( ( $show_wa && $wa_href ) || ( $show_fleet && $fleet_url ) ) : ?>
				<div class="vip-page__cta-actions vip-about-hero__actions">
					<?php if ( $show_wa && $wa_href ) : ?>
						<a class="vip-page__btn vip-page__btn--primary" href="<?php echo esc_url( $wa_href ); ?>" target="_blank" rel="noopener noreferrer">
							<?php echo esc_html( $wa_label ); ?>
						</a>
					<?php endif; ?>
					<?php if ( $show_fleet && $fleet_url ) : ?>
						<a class="vip-page__btn vip-page__btn--ghost" href="<?php echo esc_url( $fleet_url ); ?>">
							<?php echo esc_html( $fleet_label ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php if ( $has_image ) : ?>
			<figure class="vip-page__intro-media">
				<?php
				if ( ! empty( $image['ID'] ) ) {
					echo wp_get_attachment_image(
						(int) $image['ID'],
						'large',
						false,
						array(
							'class'    => 'vip-page__intro-img',
							'alt'      => ! empty( $image['alt'] ) ? (string) $image['alt'] : $heading,
							'loading'  => 'lazy',
							'decoding' => 'async',
						)
					);
				} else {
					?>
					<img
						class="vip-page__intro-img"
						src="<?php echo esc_url( $image['url'] ); ?>"
						alt="<?php echo esc_attr( $image['alt'] ?? $heading ); ?>"
						loading="lazy"
						decoding="async"
					/>
					<?php
				}
				?>
			</figure>
		<?php endif; ?>
	</div>
</section>
