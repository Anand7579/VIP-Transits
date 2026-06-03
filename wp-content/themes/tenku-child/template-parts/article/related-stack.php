<?php
/**
 * Stacked related articles (occasion listing sidebar).
 *
 * @package Tenku_Child
 *
 * @var array $args {
 *     @type int[]  $post_ids Post IDs in display order.
 *     @type string $heading       Section heading (ignored when show_heading is false).
 *     @type bool   $show_heading   Output the aside heading. Default true.
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

$show_heading = ! isset( $args['show_heading'] ) || (bool) $args['show_heading'];
$heading      = isset( $args['heading'] ) && (string) $args['heading'] !== ''
	? (string) $args['heading']
	: __( 'Related articles', 'tenku-child' );
?>
<aside class="vip-related-articles">
	<?php if ( $show_heading ) : ?>
		<h3 class="vip-related-articles__title"><?php echo esc_html( $heading ); ?></h3>
	<?php endif; ?>
	<div class="vip-related-articles__list">
		<?php
		foreach ( $post_ids as $article_id ) :
			$data = vip_transits_get_article_card_data( $article_id );
			if ( empty( $data['id'] ) ) {
				continue;
			}
			$date_display = get_the_date( 'j M Y', $article_id );
			?>
			<article class="vip-related-article-card">
				<?php if ( ! empty( $data['thumbnail'] ) ) : ?>
					<a class="vip-related-article-card__media" href="<?php echo esc_url( $data['permalink'] ); ?>">
						<img
							class="vip-related-article-card__img"
							src="<?php echo esc_url( $data['thumbnail'] ); ?>"
							alt="<?php echo esc_attr( $data['title'] ); ?>"
							loading="lazy"
							decoding="async"
						/>
					</a>
				<?php endif; ?>
				<?php if ( $date_display ) : ?>
					<time class="vip-related-article-card__date" datetime="<?php echo esc_attr( get_the_date( 'c', $article_id ) ); ?>">
						<?php echo esc_html( $date_display ); ?>
					</time>
				<?php endif; ?>
				<h4 class="vip-related-article-card__title">
					<a href="<?php echo esc_url( $data['permalink'] ); ?>"><?php echo esc_html( $data['title'] ); ?></a>
				</h4>
			</article>
		<?php endforeach; ?>
	</div>
</aside>
