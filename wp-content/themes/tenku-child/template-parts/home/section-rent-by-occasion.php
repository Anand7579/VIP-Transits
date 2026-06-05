<?php
/**
 * Rent by occasion: featured image + 2×2 grid (Figma mosaic layout).
 * Cards load from Occasion posts chosen on the homepage (or auto from published occasions).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = get_sub_field( 'heading' );
$intro   = get_sub_field( 'intro' );

$featured = null;
$cards    = array();

if ( function_exists( 'vip_transits_get_homepage_occasions_layout_for_section' ) ) {
	$layout = vip_transits_get_homepage_occasions_layout_for_section(
		get_sub_field( 'featured_occasion' ),
		get_sub_field( 'grid_occasions' )
	);
	$featured = ! empty( $layout['featured'] ) ? $layout['featured'] : null;
	$cards    = ! empty( $layout['cards'] ) ? $layout['cards'] : array();
}

$has_featured = is_array( $featured ) && ( ! empty( $featured['image'] ) || ! empty( $featured['title'] ) );
$has_cards    = ! empty( $cards );

if ( ! $heading && ! $intro && ! $has_featured && ! $has_cards ) {
	return;
}

/**
 * @param array|null $image      ACF image.
 * @param string     $title      Alt text fallback.
 * @param string     $permalink  Occasion detail URL (optional).
 */
$vip_occasions_render_media = static function ( $image, $title = '', $permalink = '' ) {
	if ( ! is_array( $image ) || ( empty( $image['ID'] ) && empty( $image['url'] ) ) ) {
		return;
	}

	$permalink = $permalink ? (string) $permalink : '';
	if ( $permalink ) {
		printf(
			'<a class="vip-occasions-card__media-link" href="%1$s" aria-label="%2$s">',
			esc_url( $permalink ),
			esc_attr( $title ? $title : __( 'View occasion', 'tenku-child' ) )
		);
	}

	echo '<div class="vip-occasions-card__media">';
	if ( ! empty( $image['ID'] ) ) {
		echo wp_get_attachment_image(
			(int) $image['ID'],
			'large',
			false,
			array(
				'class'    => 'vip-occasions-card__img',
				'loading'  => 'lazy',
				'decoding' => 'async',
				'alt'      => ! empty( $image['alt'] ) ? (string) $image['alt'] : $title,
			)
		);
	} else {
		printf(
			'<img class="vip-occasions-card__img" src="%1$s" alt="%2$s" loading="lazy" decoding="async" />',
			esc_url( $image['url'] ),
			esc_attr( ! empty( $image['alt'] ) ? (string) $image['alt'] : $title )
		);
	}
	echo '</div>';

	if ( $permalink ) {
		echo '</a>';
	}
};

/**
 * @param array $card Card or featured row data.
 */
$vip_occasions_render_body = static function ( array $card ) {
	$title     = isset( $card['title'] ) ? (string) $card['title'] : '';
	$desc      = isset( $card['description'] ) ? (string) $card['description'] : '';
	$permalink = ! empty( $card['permalink'] ) ? (string) $card['permalink'] : '';

	if ( ! $title && ! $desc && ! $permalink ) {
		return;
	}

	$label = function_exists( 'vip_transits_occasion_button_label' )
		? vip_transits_occasion_button_label( $card, __( 'Learn more', 'tenku-child' ) )
		: __( 'Learn more', 'tenku-child' );

	echo '<div class="vip-occasions-card__body">';
	if ( $title ) {
		echo '<h3 class="vip-occasions-card__title">';
		if ( $permalink ) {
			printf(
				'<a class="vip-occasions-card__title-link" href="%1$s">%2$s</a>',
				esc_url( $permalink ),
				esc_html( $title )
			);
		} else {
			echo esc_html( $title );
		}
		echo '</h3>';
	}
	if ( $desc ) {
		echo '<p class="vip-occasions-card__text"><span class="vip-occasions-card__text-inner">' . esc_html( $desc ) . '</span></p>';
	}
	if ( $permalink && $label ) {
		$arrow = function_exists( 'vip_transits_theme_icon_arrow_html' )
			? vip_transits_theme_icon_arrow_html( 'vip-occasions-card__btn-arrow' )
			: '<span class="vip-occasions-card__btn-arrow" aria-hidden="true">→</span>';
		printf(
			'<a class="vip-occasions-card__btn" href="%1$s"><span class="vip-occasions-card__btn-label">%2$s</span>%3$s</a>',
			esc_url( $permalink ),
			esc_html( $label ),
			$arrow // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	}
	echo '</div>';
};

$feat_title = is_array( $featured ) && ! empty( $featured['title'] ) ? (string) $featured['title'] : '';
$feat_url   = is_array( $featured ) && ! empty( $featured['permalink'] ) ? (string) $featured['permalink'] : '';
?>
<section id="vip-occasions" class="vip-occasions" data-vip-section>
	<div class="vip-occasions__container vip-content-container">
		<header class="vip-occasions__header">
			<?php if ( $heading ) : ?>
				<h2 class="vip-occasions__title"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( $intro ) : ?>
				<p class="vip-occasions__intro"><?php echo esc_html( $intro ); ?></p>
			<?php endif; ?>
			<hr class="vip-occasions__rule" />
		</header>

		<div class="vip-occasions__layout<?php echo $has_featured ? '' : ' vip-occasions__layout--no-featured'; ?>">
			<?php if ( $has_featured ) : ?>
				<div class="vip-occasions__col vip-occasions__col--left">
					<div class="vip-occasions__feat-media">
						<?php
						$feat_image = isset( $featured['image'] ) ? $featured['image'] : null;
						$vip_occasions_render_media( $feat_image, $feat_title, $feat_url );
						?>
					</div>
					<div class="vip-occasions__feat-body">
						<?php $vip_occasions_render_body( $featured ); ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( $has_cards ) : ?>
				<div class="vip-occasions__col vip-occasions__col--right">
					<div class="vip-occasions__grid" aria-label="<?php esc_attr_e( 'Occasion options', 'tenku-child' ); ?>">
						<?php foreach ( $cards as $card ) : ?>
							<?php
							$title = isset( $card['title'] ) ? (string) $card['title'] : '';
							$url   = ! empty( $card['permalink'] ) ? (string) $card['permalink'] : '';
							?>
							<article class="vip-occasions__card">
								<?php $vip_occasions_render_media( isset( $card['image'] ) ? $card['image'] : null, $title, $url ); ?>
								<?php $vip_occasions_render_body( $card ); ?>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
