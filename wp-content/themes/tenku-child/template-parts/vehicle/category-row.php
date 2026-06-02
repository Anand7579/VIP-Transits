<?php
/**
 * Vehicle category filter row (homepage + fleet archive).
 *
 * @package Tenku_Child
 *
 * @var array $args {
 *     @type array  $categories Normalized rows from vip_transits_normalize_vehicle_category_row().
 *     @type string $context    Optional modifier: `fleet` on archive.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$categories = isset( $args['categories'] ) && is_array( $args['categories'] ) ? $args['categories'] : array();
$context    = isset( $args['context'] ) ? (string) $args['context'] : '';

if ( ! $categories ) {
	return;
}

$section_class = 'vip-categories';
if ( 'fleet' === $context ) {
	$section_class .= ' vip-categories--fleet';
}
?>
<section class="<?php echo esc_attr( $section_class ); ?>" data-vip-section aria-label="<?php esc_attr_e( 'Car categories', 'tenku-child' ); ?>">
	<div class="vip-categories__container vip-content-container">
		<ul class="vip-categories__grid">
			<?php foreach ( $categories as $item ) : ?>
				<li class="vip-categories__item">
					<button
						type="button"
						class="vip-categories__card"
						data-vip-category-filter="<?php echo esc_attr( $item['slug'] ); ?>"
						aria-pressed="false"
					>
						<?php if ( ! empty( $item['image_url'] ) ) : ?>
							<figure class="vip-categories__media">
								<img
									class="vip-categories__img"
									src="<?php echo esc_url( $item['image_url'] ); ?>"
									alt="<?php echo esc_attr( $item['image_alt'] ); ?>"
									loading="lazy"
									decoding="async"
								/>
							</figure>
						<?php endif; ?>

						<?php if ( ! empty( $item['title'] ) ) : ?>
							<p class="vip-categories__label"><?php echo esc_html( $item['title'] ); ?></p>
						<?php endif; ?>
					</button>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
