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

				if ( ! $title && ! ( is_array( $image ) && ! empty( $image['url'] ) ) ) {
					continue;
				}

				$img_url = is_array( $image ) && ! empty( $image['url'] ) ? $image['url'] : '';
				$img_alt = is_array( $image ) && ! empty( $image['alt'] ) ? $image['alt'] : $title;
				$href    = is_array( $link ) && ! empty( $link['url'] ) ? $link['url'] : '';
				$target  = is_array( $link ) && ! empty( $link['target'] ) ? $link['target'] : '';
				?>
				<li class="vip-categories__item">
					<?php if ( $href ) : ?>
						<a class="vip-categories__card" href="<?php echo esc_url( $href ); ?>"<?php echo $target ? ' target="' . esc_attr( $target ) . '" rel="noopener noreferrer"' : ''; ?>>
					<?php else : ?>
						<div class="vip-categories__card">
					<?php endif; ?>

						<?php if ( $img_url ) : ?>
							<figure class="vip-categories__media">
								<img
									class="vip-categories__img"
									src="<?php echo esc_url( $img_url ); ?>"
									alt="<?php echo esc_attr( $img_alt ); ?>"
									loading="lazy"
									decoding="async"
								/>
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
