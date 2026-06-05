<?php
/**
 * About flexible layout: our story.
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = (string) get_sub_field( 'heading' );

if ( ! $heading && ! have_rows( 'blocks' ) ) {
	return;
}
?>
<section class="vip-border-section vip-border-section--surface vip-border-section--plain-panel vip-about-section vip-about-section--our-story">
	<div class="vip-content-container">
		<?php if ( $heading ) : ?>
			<h2 class="vip-page__section-title vip-about-story__title"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>
		<?php if ( have_rows( 'blocks' ) ) : ?>
			<div class="vip-about-story__blocks">
				<?php
				while ( have_rows( 'blocks' ) ) :
					the_row();
					$subheading = (string) get_sub_field( 'subheading' );
					$content    = (string) get_sub_field( 'content' );
					$image      = get_sub_field( 'image' );
					if ( ! $subheading && ! $content && ( ! is_array( $image ) || empty( $image['url'] ) ) ) {
						continue;
					}
					?>
					<article class="vip-about-story__block">
						<div class="vip-about-story__copy">
							<?php if ( $subheading ) : ?>
								<h3 class="vip-about-story__subheading"><?php echo esc_html( $subheading ); ?></h3>
							<?php endif; ?>
							<?php if ( $content ) : ?>
								<div class="vip-page__prose"><?php echo wp_kses_post( $content ); ?></div>
							<?php endif; ?>
						</div>
						<?php if ( is_array( $image ) && ! empty( $image['url'] ) ) : ?>
							<figure class="vip-about-story__media">
								<?php
								if ( ! empty( $image['ID'] ) ) {
									echo wp_get_attachment_image(
										(int) $image['ID'],
										'large',
										false,
										array(
											'class'    => 'vip-about-story__img',
											'alt'      => ! empty( $image['alt'] ) ? (string) $image['alt'] : $subheading,
											'loading'  => 'lazy',
											'decoding' => 'async',
										)
									);
								} else {
									?>
									<img
										class="vip-about-story__img"
										src="<?php echo esc_url( $image['url'] ); ?>"
										alt="<?php echo esc_attr( $image['alt'] ?? $subheading ); ?>"
										loading="lazy"
										decoding="async"
									/>
									<?php
								}
								?>
							</figure>
						<?php endif; ?>
					</article>
				<?php endwhile; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
