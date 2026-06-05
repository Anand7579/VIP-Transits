<?php
/**
 * About flexible layout: why choose us.
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = (string) get_sub_field( 'heading' );

if ( ! $heading && ! have_rows( 'features' ) ) {
	return;
}
?>
<section class="vip-border-section vip-border-section--surface vip-border-section--plain-panel vip-about-section vip-about-section--why-choose" aria-labelledby="vip-about-why-heading">
	<div class="vip-content-container">
		<?php if ( $heading ) : ?>
			<h2 id="vip-about-why-heading" class="vip-page__section-title vip-about-why__title"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>
		<?php if ( have_rows( 'features' ) ) : ?>
			<ul class="vip-about-why__grid" role="list">
				<?php
				while ( have_rows( 'features' ) ) :
					the_row();
					$icon  = get_sub_field( 'icon' );
					$title = (string) get_sub_field( 'title' );
					$desc  = (string) get_sub_field( 'description' );
					if ( ! $title && ! $desc && ( ! is_array( $icon ) || empty( $icon['url'] ) ) ) {
						continue;
					}
					?>
					<li class="vip-about-why__item" role="listitem">
						<?php if ( is_array( $icon ) && ( ! empty( $icon['ID'] ) || ! empty( $icon['url'] ) ) ) : ?>
							<span class="vip-about-why__icon" aria-hidden="true">
								<?php
								if ( ! empty( $icon['ID'] ) ) {
									echo wp_get_attachment_image(
										(int) $icon['ID'],
										'thumbnail',
										false,
										array(
											'class' => 'vip-about-why__icon-img',
											'alt'   => '',
										)
									);
								} else {
									?>
									<img class="vip-about-why__icon-img" src="<?php echo esc_url( $icon['url'] ); ?>" alt="" loading="lazy" decoding="async" />
									<?php
								}
								?>
							</span>
						<?php endif; ?>
						<?php if ( $title ) : ?>
							<h3 class="vip-about-why__item-title"><?php echo esc_html( $title ); ?></h3>
						<?php endif; ?>
						<?php if ( $desc ) : ?>
							<p class="vip-about-why__item-text"><?php echo esc_html( $desc ); ?></p>
						<?php endif; ?>
					</li>
				<?php endwhile; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
