<?php
/**
 * Vehicle categories: image on top, title below (Figma category row).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! have_rows( 'categories' ) ) {
	return;
}
?>
<section class="vip-categories">
	<div class="vip-categories__container vip-content-container">
		<ul class="vip-categories__grid">
			<?php
			while ( have_rows( 'categories' ) ) :
				the_row();
				$image = get_sub_field( 'image' );
				$title = get_sub_field( 'title' );
				$link  = get_sub_field( 'link' );

				$img_id  = function_exists( 'vip_transits_acf_attachment_id' ) ? vip_transits_acf_attachment_id( $image ) : 0;
				$img_url = function_exists( 'vip_transits_acf_image_url' ) ? vip_transits_acf_image_url( $image, 'medium_large' ) : '';

				if ( ! $title && ! $img_id && ! $img_url ) {
					continue;
				}

				$img_alt = function_exists( 'vip_transits_acf_image_alt' ) ? vip_transits_acf_image_alt( $image, $title ) : $title;
				$href    = is_array( $link ) && ! empty( $link['url'] ) ? $link['url'] : '';
				$target  = is_array( $link ) && ! empty( $link['target'] ) ? $link['target'] : '';
				?>
				<li class="vip-categories__item">
					<?php if ( $href ) : ?>
						<a class="vip-categories__card" href="<?php echo esc_url( $href ); ?>"<?php echo $target ? ' target="' . esc_attr( $target ) . '" rel="noopener noreferrer"' : ''; ?>>
					<?php else : ?>
						<div class="vip-categories__card">
					<?php endif; ?>

						<?php if ( $img_id || $img_url ) : ?>
							<figure class="vip-categories__media">
								<?php
								if ( function_exists( 'vip_transits_the_acf_image' ) ) {
									vip_transits_the_acf_image(
										$image,
										'medium_large',
										array(
											'class' => 'vip-categories__img',
											'alt'   => $img_alt,
										)
									);
								}
								?>
							</figure>
						<?php endif; ?>

						<?php if ( $title ) : ?>
							<p class="vip-categories__label"><?php echo esc_html( $title ); ?></p>
						<?php endif; ?>

					<?php if ( $href ) : ?>
						</a>
					<?php else : ?>
						</div>
					<?php endif; ?>
				</li>
			<?php endwhile; ?>
		</ul>
	</div>
</section>
