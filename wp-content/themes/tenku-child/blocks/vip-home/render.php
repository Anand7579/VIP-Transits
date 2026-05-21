<?php
/**
 * Block: vip-home — renders ACF fields from the static front page.
 *
 * @package Tenku_Child
 *
 * @var array $block Block settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$home_id = function_exists( 'vip_transits_home_page_id' ) ? vip_transits_home_page_id() : (int) get_option( 'page_on_front' );
$anchor  = ! empty( $block['anchor'] ) ? 'id="' . esc_attr( $block['anchor'] ) . '"' : '';
$preview = is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST );

if ( ! function_exists( 'have_rows' ) ) {
	if ( $preview ) {
		echo '<p class="vip-home vip-home--empty">' . esc_html__( 'Activate ACF Pro.', 'tenku-child' ) . '</p>';
	}
	return;
}

if ( ! $home_id ) {
	if ( $preview ) {
		echo '<p class="vip-home vip-home--empty">';
		echo esc_html__( 'Set Settings → Reading → Homepage to your Home page, then add sections under Pages → Home.', 'tenku-child' );
		echo ' <a href="' . esc_url( admin_url( 'options-reading.php' ) ) . '">' . esc_html__( 'Reading settings', 'tenku-child' ) . '</a>';
		echo '</p>';
	}
	return;
}
?>
<section class="vip-home" <?php echo $anchor; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<?php
	if ( have_rows( 'sections', $home_id ) ) :
		while ( have_rows( 'sections', $home_id ) ) :
			the_row();
			$layout = get_row_layout();

			if ( 'hero' === $layout ) {
				get_template_part( 'template-parts/home/section', 'hero' );
			} elseif ( 'vehicle_categories' === $layout ) {
				get_template_part( 'template-parts/home/section', 'vehicle-categories' );
			} elseif ( 'rent_by_occasion' === $layout ) {
				get_template_part( 'template-parts/home/section', 'rent-by-occasion' );
			} elseif ( 'faq' === $layout ) {
				get_template_part( 'template-parts/home/section', 'faq' );
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
	else :
		if ( $preview ) {
			$edit = get_edit_post_link( $home_id, 'raw' );
			echo '<p class="vip-home vip-home--empty">';
			echo esc_html__( 'No sections yet. Edit the Home page and add a Hero banner under VIP Homepage.', 'tenku-child' );
			if ( $edit ) {
				echo ' <a href="' . esc_url( $edit ) . '">' . esc_html__( 'Edit Home page', 'tenku-child' ) . '</a>';
			}
			echo '</p>';
		}
	endif;
	?>
</section>
