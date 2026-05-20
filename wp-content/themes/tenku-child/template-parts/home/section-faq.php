<?php
/**
 * FAQ section: accordion + side car image (Figma).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = get_sub_field( 'heading' ) ?: __( 'Frequently Asked Questions', 'tenku-child' );
$intro   = get_sub_field( 'intro' );
$image   = get_sub_field( 'side_image' );

if ( ! have_rows( 'faq_items' ) ) {
	return;
}

$img_id  = function_exists( 'vip_transits_acf_attachment_id' ) ? vip_transits_acf_attachment_id( $image ) : 0;
$img_url = function_exists( 'vip_transits_acf_image_url' ) ? vip_transits_acf_image_url( $image, 'large' ) : '';
$img_alt = function_exists( 'vip_transits_acf_image_alt' ) ? vip_transits_acf_image_alt( $image, __( 'Luxury car', 'tenku-child' ) ) : __( 'Luxury car', 'tenku-child' );
?>
<section class="vip-faq">
	<div class="vip-faq__container vip-content-container">
		<header class="vip-faq__header">
			<?php if ( $heading ) : ?>
				<h2 class="vip-faq__title"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( $intro ) : ?>
				<p class="vip-faq__intro"><?php echo esc_html( $intro ); ?></p>
			<?php endif; ?>
			<hr class="vip-faq__rule" />
		</header>

		<div class="vip-faq__layout">
			<div class="vip-faq__accordion" data-vip-faq-accordion>
				<?php
				$faq_i = 0;
				while ( have_rows( 'faq_items' ) ) :
					the_row();
					$question = get_sub_field( 'question' );
					$answer   = get_sub_field( 'answer' );
					if ( ! $question ) {
						continue;
					}
					$is_open   = 0 === $faq_i;
					$item_id   = 'vip-faq-' . (int) $faq_i;
					$panel_id  = $item_id . '-panel';
					$button_id = $item_id . '-btn';
					++$faq_i;
					?>
					<div class="vip-faq__item<?php echo $is_open ? ' is-open' : ''; ?>" data-vip-faq-item>
						<button
							type="button"
							class="vip-faq__question"
							id="<?php echo esc_attr( $button_id ); ?>"
							data-vip-faq-trigger
							aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>"
							aria-controls="<?php echo esc_attr( $panel_id ); ?>"
						>
							<span class="vip-faq__question-text"><?php echo esc_html( $question ); ?></span>
							<span class="vip-faq__chevron" aria-hidden="true"></span>
						</button>
						<?php if ( $answer ) : ?>
							<div
								class="vip-faq__answer"
								id="<?php echo esc_attr( $panel_id ); ?>"
								role="region"
								aria-labelledby="<?php echo esc_attr( $button_id ); ?>"
								<?php echo $is_open ? '' : ' inert'; ?>
							>
								<div class="vip-faq__answer-inner">
									<?php echo wp_kses_post( $answer ); ?>
								</div>
							</div>
						<?php endif; ?>
					</div>
				<?php endwhile; ?>
			</div>

			<aside class="vip-faq__visual"<?php echo ( $img_id || $img_url ) ? '' : ' aria-hidden="true"'; ?>>
				<div class="vip-faq__bars" aria-hidden="true">
					<span></span>
					<span></span>
					<span></span>
				</div>
				<?php if ( $img_id || $img_url ) : ?>
					<figure class="vip-faq__figure">
						<?php
						if ( function_exists( 'vip_transits_the_acf_image' ) ) {
							vip_transits_the_acf_image(
								$image,
								'large',
								array(
									'class' => 'vip-faq__car',
									'alt'   => $img_alt,
								)
							);
						}
						?>
					</figure>
				<?php endif; ?>
			</aside>
		</div>
	</div>
</section>
