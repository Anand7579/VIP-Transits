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
<section class="vip-categories" data-vip-section>
	<div class="vip-categories__container vip-content-container">
		<ul class="vip-categories__grid">
			<?php
			while ( have_rows( 'categories' ) ) :
				the_row();
				$image       = get_sub_field( 'image' );
				$title       = get_sub_field( 'title' );
				$filter_slug = get_sub_field( 'filter_slug' );

				if ( ! $title && ! ( is_array( $image ) && ! empty( $image['url'] ) ) ) {
					continue;
				}

				$slug    = function_exists( 'vip_transits_category_filter_slug' )
					? vip_transits_category_filter_slug( (string) $title, (string) $filter_slug )
					: sanitize_title( (string) $title );
				$img_url = is_array( $image ) && ! empty( $image['url'] ) ? $image['url'] : '';
				$img_alt = is_array( $image ) && ! empty( $image['alt'] ) ? $image['alt'] : $title;
				?>
				<li class="vip-categories__item">
					<button
						type="button"
						class="vip-categories__card"
						data-vip-category-filter="<?php echo esc_attr( $slug ); ?>"
						aria-pressed="false"
					>
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
					</button>
				</li>
			<?php endwhile; ?>
		</ul>
	</div>
</section>
