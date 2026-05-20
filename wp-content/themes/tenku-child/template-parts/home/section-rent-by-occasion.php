<?php
/**
 * Rent by occasion: featured card + 2×2 grid (Figma).
 * Phase 2: swap data source to CPT query; keep markup/classes for CSS.
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading   = get_sub_field( 'heading' );
$intro     = get_sub_field( 'intro' );
$wa_base   = get_sub_field( 'whatsapp_base_url' );
$wa_base   = is_string( $wa_base ) ? trim( $wa_base ) : '';
$wa_base   = (string) apply_filters( 'vip_transits_occasions_whatsapp_base_url', $wa_base );
$featured  = get_sub_field( 'featured_card' );
$has_cards = have_rows( 'occasion_cards' );

$has_featured = is_array( $featured ) && ( ! empty( $featured['image'] ) || ! empty( $featured['title'] ) );

if ( ! $heading && ! $intro && ! $has_featured && ! $has_cards ) {
	return;
}

/**
 * Render one occasion card (featured or grid).
 *
 * @param array  $card   ACF image, title, description, link keys.
 * @param string $class  Extra BEM modifier class.
 * @param string $wa_base WhatsApp base URL for prefilled messages (optional).
 */
$vip_render_occasion_card = static function ( array $card, $class = '', $wa_base = '' ) {
	$image = isset( $card['image'] ) ? $card['image'] : null;
	$title = isset( $card['title'] ) ? (string) $card['title'] : '';
	$desc  = isset( $card['description'] ) ? (string) $card['description'] : '';
	$link  = isset( $card['link'] ) && is_array( $card['link'] ) ? $card['link'] : array();

	if ( ! $title && ! $desc && ! ( is_array( $image ) && ! empty( $image['url'] ) ) ) {
		return;
	}

	$url    = isset( $link['url'] ) ? (string) $link['url'] : '';
	$target = ! empty( $link['target'] ) ? (string) $link['target'] : '';
	$label  = isset( $link['title'] ) && $link['title'] !== '' ? (string) $link['title'] : __( 'Learn more', 'tenku-child' );

	$btn_href   = '';
	$btn_target = '';
	$btn_rel    = '';

	if ( $wa_base !== '' && function_exists( 'vip_transits_occasions_whatsapp_href' ) ) {
		$btn_href = vip_transits_occasions_whatsapp_href( $wa_base, $title, $desc );
		if ( $btn_href !== '' ) {
			$btn_target = '_blank';
			$btn_rel    = 'noopener noreferrer';
		}
	} elseif ( $url !== '' ) {
		$btn_href = $url;
		if ( $target ) {
			$btn_target = $target;
			$btn_rel    = 'noopener noreferrer';
		}
	}

	if ( $wa_base !== '' && ( ! isset( $link['title'] ) || $link['title'] === '' ) ) {
		$label = __( 'WhatsApp us', 'tenku-child' );
	}

	?>
	<article class="vip-occasions-card<?php echo $class ? ' ' . esc_attr( $class ) : ''; ?>">
		<?php if ( is_array( $image ) && ! empty( $image['ID'] ) ) : ?>
			<div class="vip-occasions-card__media">
				<?php
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
				?>
			</div>
		<?php elseif ( is_array( $image ) && ! empty( $image['url'] ) ) : ?>
			<div class="vip-occasions-card__media">
				<img
					class="vip-occasions-card__img"
					src="<?php echo esc_url( $image['url'] ); ?>"
					alt="<?php echo esc_attr( ! empty( $image['alt'] ) ? (string) $image['alt'] : $title ); ?>"
					loading="lazy"
					decoding="async"
				/>
			</div>
		<?php endif; ?>

		<?php if ( $title ) : ?>
			<h3 class="vip-occasions-card__title"><?php echo esc_html( $title ); ?></h3>
		<?php endif; ?>

		<?php if ( $desc ) : ?>
			<p class="vip-occasions-card__text"><?php echo esc_html( $desc ); ?></p>
		<?php endif; ?>

		<?php if ( $btn_href ) : ?>
			<a
				class="vip-occasions-card__btn"
				href="<?php echo esc_url( $btn_href ); ?>"
				<?php echo $btn_target ? ' target="' . esc_attr( $btn_target ) . '"' : ''; ?>
				<?php echo $btn_rel ? ' rel="' . esc_attr( $btn_rel ) . '"' : ''; ?>
			>
				<span class="vip-occasions-card__btn-label"><?php echo esc_html( $label ); ?></span>
				<span class="vip-occasions-card__btn-arrow" aria-hidden="true">→</span>
			</a>
		<?php endif; ?>
	</article>
	<?php
};

?>
<section class="vip-occasions">
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
				<div class="vip-occasions__featured">
					<?php $vip_render_occasion_card( $featured, 'vip-occasions-card--featured', $wa_base ); ?>
				</div>
			<?php endif; ?>

			<?php if ( $has_cards ) : ?>
				<div class="vip-occasions__grid">
					<?php
					while ( have_rows( 'occasion_cards' ) ) :
						the_row();
						$card = array(
							'image'       => get_sub_field( 'image' ),
							'title'       => get_sub_field( 'title' ),
							'description' => get_sub_field( 'description' ),
							'link'        => get_sub_field( 'link' ),
						);
						$vip_render_occasion_card( $card, '', $wa_base );
					endwhile;
					?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php
unset( $vip_render_occasion_card );
