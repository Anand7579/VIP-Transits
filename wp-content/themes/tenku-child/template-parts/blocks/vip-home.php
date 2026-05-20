<?php
/**
 * Block: vip-home — render flexible homepage sections.
 *
 * @package Tenku_Child
 *
 * @var array $block The block settings and attributes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$anchor = ! empty( $block['anchor'] ) ? 'id="' . esc_attr( $block['anchor'] ) . '"' : '';
?>
<section class="vip-home" <?php echo $anchor; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php
	if ( have_rows( 'sections' ) ) :
		while ( have_rows( 'sections' ) ) :
			the_row();
			$layout = get_row_layout();

			if ( 'hero' === $layout ) {
				$kicker     = get_sub_field( 'kicker' );
				$heading    = get_sub_field( 'heading' );
				$sub        = get_sub_field( 'subheading' );
				$bg         = get_sub_field( 'background' );
				$cta        = get_sub_field( 'primary_cta' );
				$bg_url     = is_array( $bg ) && ! empty( $bg['url'] ) ? $bg['url'] : '';
				$style_attr = $bg_url ? ' style="--vip-hero-bg:url(' . esc_url( $bg_url ) . ')"' : '';
				?>
				<div class="vip-home__hero vip-hero"<?php echo $style_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<div class="vip-hero__inner">
						<?php if ( $kicker ) : ?>
							<p class="vip-hero__kicker"><?php echo esc_html( $kicker ); ?></p>
						<?php endif; ?>
						<?php if ( $heading ) : ?>
							<h1 class="vip-hero__heading"><?php echo esc_html( $heading ); ?></h1>
						<?php endif; ?>
						<?php if ( $sub ) : ?>
							<p class="vip-hero__sub"><?php echo esc_html( $sub ); ?></p>
						<?php endif; ?>
						<?php
						if ( is_array( $cta ) && ! empty( $cta['url'] ) ) :
							$target = ! empty( $cta['target'] ) ? ' target="' . esc_attr( $cta['target'] ) . '" rel="noopener noreferrer"' : '';
							?>
							<p class="vip-hero__cta">
								<a class="vip-hero__btn" href="<?php echo esc_url( $cta['url'] ); ?>"<?php echo $target; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
									<?php echo esc_html( $cta['title'] ? $cta['title'] : __( 'Book now', 'tenku-child' ) ); ?>
								</a>
							</p>
						<?php endif; ?>
					</div>
				</div>
				<?php
			} elseif ( 'booking_strip' === $layout ) {
				$title = get_sub_field( 'title' );
				$note  = get_sub_field( 'note' );
				?>
				<div class="vip-home__booking vip-booking">
					<div class="vip-booking__inner">
						<?php if ( $title ) : ?>
							<h2 class="vip-booking__title"><?php echo esc_html( $title ); ?></h2>
						<?php endif; ?>
						<?php if ( $note ) : ?>
							<p class="vip-booking__note"><?php echo esc_html( $note ); ?></p>
						<?php endif; ?>
					</div>
				</div>
				<?php
			} elseif ( 'vehicle_grid' === $layout || 'vehicle_fleet' === $layout ) {
				get_template_part( 'template-parts/home/section', 'vehicle-fleet' );
			} elseif ( 'feature_columns' === $layout ) {
				$section_title = get_sub_field( 'section_title' );
				?>
				<div class="vip-home__features vip-features">
					<div class="vip-features__inner">
						<?php if ( $section_title ) : ?>
							<h2 class="vip-features__title"><?php echo esc_html( $section_title ); ?></h2>
						<?php endif; ?>
						<?php if ( have_rows( 'columns' ) ) : ?>
							<ul class="vip-features__grid">
								<?php
								while ( have_rows( 'columns' ) ) :
									the_row();
									$icon = get_sub_field( 'icon' );
									$t    = get_sub_field( 'title' );
									$txt  = get_sub_field( 'text' );
									?>
									<li class="vip-features__col">
										<?php if ( $icon ) : ?>
											<span class="vip-features__icon" aria-hidden="true"><?php echo esc_html( $icon ); ?></span>
										<?php endif; ?>
										<?php if ( $t ) : ?>
											<h3 class="vip-features__h"><?php echo esc_html( $t ); ?></h3>
										<?php endif; ?>
										<?php if ( $txt ) : ?>
											<p class="vip-features__text"><?php echo esc_html( $txt ); ?></p>
										<?php endif; ?>
									</li>
								<?php endwhile; ?>
							</ul>
						<?php endif; ?>
					</div>
				</div>
				<?php
			} elseif ( 'cta_banner' === $layout ) {
				get_template_part( 'template-parts/home/section', 'cta-banner' );
			}

		endwhile;
	endif;
	?>
</section>
