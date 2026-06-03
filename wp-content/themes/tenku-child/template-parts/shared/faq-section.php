<?php
/**
 * FAQ section (homepage accordion + side image).
 *
 * @package Tenku_Child
 *
 * @var array $args {
 *     @type string $heading     Section title.
 *     @type string $intro       Optional intro below title.
 *     @type array  $items       List of { question, answer }.
 *     @type mixed  $image       ACF image array, attachment ID, or URL string.
 *     @type string $id_prefix   Prefix for accordion DOM ids. Default vip-faq.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading   = isset( $args['heading'] ) ? trim( (string) $args['heading'] ) : '';
$intro     = isset( $args['intro'] ) ? trim( (string) $args['intro'] ) : '';
$items     = isset( $args['items'] ) && is_array( $args['items'] ) ? $args['items'] : array();
$id_prefix = isset( $args['id_prefix'] ) && (string) $args['id_prefix'] !== ''
	? sanitize_html_class( (string) $args['id_prefix'] )
	: 'vip-faq';

$faq_rows = array();
foreach ( $items as $row ) {
	$question = isset( $row['question'] ) ? trim( (string) $row['question'] ) : '';
	$answer   = isset( $row['answer'] ) ? (string) $row['answer'] : '';
	if ( $question === '' ) {
		continue;
	}
	$faq_rows[] = array(
		'question' => $question,
		'answer'   => $answer,
	);
}

if ( ! $faq_rows ) {
	return;
}

$image   = $args['image'] ?? null;
$img_id  = 0;
$img_url = '';
$img_alt = __( 'Luxury car', 'tenku-child' );

if ( is_numeric( $image ) ) {
	$img_id = (int) $image;
} elseif ( is_array( $image ) ) {
	if ( ! empty( $image['ID'] ) ) {
		$img_id = (int) $image['ID'];
	}
	if ( ! empty( $image['url'] ) ) {
		$img_url = (string) $image['url'];
	} elseif ( $img_id ) {
		$img_url = (string) wp_get_attachment_image_url( $img_id, 'large' );
	}
	if ( ! empty( $image['alt'] ) ) {
		$img_alt = (string) $image['alt'];
	} elseif ( $img_id ) {
		$stored_alt = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
		if ( is_string( $stored_alt ) && $stored_alt !== '' ) {
			$img_alt = $stored_alt;
		}
	}
} elseif ( is_string( $image ) && $image !== '' ) {
	$img_url = $image;
}

if ( ! $heading ) {
	$heading = __( 'Frequently Asked Questions', 'tenku-child' );
}
?>
<section class="vip-faq" data-vip-section>
	<div class="vip-faq__container vip-content-container">
		<header class="vip-faq__header">
			<?php if ( $heading ) : ?>
				<h2 class="vip-faq__title"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( $intro ) : ?>
				<?php if ( str_contains( $intro, '<' ) ) : ?>
					<div class="vip-faq__intro"><?php echo wp_kses_post( $intro ); ?></div>
				<?php else : ?>
					<p class="vip-faq__intro"><?php echo esc_html( $intro ); ?></p>
				<?php endif; ?>
			<?php endif; ?>
			<hr class="vip-faq__rule" />
		</header>

		<div class="vip-faq__layout">
			<div class="vip-faq__accordion" data-vip-faq-accordion>
				<?php
				$faq_i = 0;
				foreach ( $faq_rows as $faq_row ) :
					$is_open   = 0 === $faq_i;
					$item_id   = $id_prefix . '-' . (int) $faq_i;
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
							<span class="vip-faq__question-text"><?php echo esc_html( $faq_row['question'] ); ?></span>
							<span class="vip-faq__chevron" aria-hidden="true"></span>
						</button>
						<?php if ( $faq_row['answer'] ) : ?>
							<div
								class="vip-faq__answer"
								id="<?php echo esc_attr( $panel_id ); ?>"
								role="region"
								aria-labelledby="<?php echo esc_attr( $button_id ); ?>"
								<?php echo $is_open ? '' : ' inert'; ?>
							>
								<div class="vip-faq__answer-inner">
									<?php echo wp_kses_post( $faq_row['answer'] ); ?>
								</div>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>

			<aside class="vip-faq__visual"<?php echo ( $img_id || $img_url ) ? '' : ' aria-hidden="true"'; ?>>
				<?php if ( $img_id || $img_url ) : ?>
					<figure class="vip-faq__figure">
						<?php
						if ( $img_id ) {
							echo wp_get_attachment_image(
								$img_id,
								'large',
								false,
								array(
									'class'    => 'vip-faq__car',
									'alt'      => $img_alt,
									'loading'  => 'lazy',
									'decoding' => 'async',
								)
							);
						} elseif ( $img_url ) {
							printf(
								'<img class="vip-faq__car" src="%1$s" alt="%2$s" loading="lazy" decoding="async" />',
								esc_url( $img_url ),
								esc_attr( $img_alt )
							);
						}
						?>
					</figure>
				<?php endif; ?>
			</aside>
		</div>
	</div>
</section>
