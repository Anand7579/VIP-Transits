<?php
/**
 * Related articles — same card layout as vehicle detail “Also available”.
 *
 * @package Tenku_Child
 *
 * @var array $args {
 *     @type int[]  $post_ids Post IDs in display order.
 *     @type string $heading  Section heading (optional; omit to hide).
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_ids = isset( $args['post_ids'] ) && is_array( $args['post_ids'] ) ? array_map( 'intval', $args['post_ids'] ) : array();
$post_ids = array_values( array_filter( $post_ids ) );

if ( ! $post_ids || ! function_exists( 'vip_transits_get_article_card_data' ) ) {
	return;
}

$heading = isset( $args['heading'] ) ? trim( (string) $args['heading'] ) : '';
?>
<section class="vip-vdetail__related" data-vip-section>
	<?php if ( $heading ) : ?>
		<h2 class="vip-vdetail__section-title vip-vdetail__section-title--rule-only"><?php echo esc_html( $heading ); ?></h2>
	<?php endif; ?>
	<ul class="vip-vdetail__related-list">
		<?php
		foreach ( $post_ids as $article_id ) :
			$data = vip_transits_get_article_card_data( $article_id );
			if ( empty( $data['id'] ) ) {
				continue;
			}
			$date_display = get_the_date( 'j M Y', $article_id );
			?>
			<li class="vip-vdetail__related-card">
				<a class="vip-vdetail__related-link" href="<?php echo esc_url( $data['permalink'] ); ?>">
					<?php if ( ! empty( $data['thumbnail'] ) ) : ?>
						<img
							class="vip-vdetail__related-img"
							src="<?php echo esc_url( $data['thumbnail'] ); ?>"
							alt=""
							loading="lazy"
							decoding="async"
							width="120"
							height="80"
						/>
					<?php endif; ?>
					<span class="vip-vdetail__related-body">
						<span class="vip-vdetail__related-name"><?php echo esc_html( $data['title'] ); ?></span>
						<?php if ( $date_display ) : ?>
							<span class="vip-vdetail__related-price">
								<time datetime="<?php echo esc_attr( get_the_date( 'c', $article_id ) ); ?>">
									<?php echo esc_html( $date_display ); ?>
								</time>
							</span>
						<?php endif; ?>
					</span>
					<span class="vip-vdetail__related-arrow" aria-hidden="true">→</span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
