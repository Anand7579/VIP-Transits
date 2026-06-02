<?php
/**
 * Article listing — LATEST (5 per page) + pagination + TRENDING (AJAX on page 2+).
 *
 * @package Tenku_Child
 *
 * @var array $args {
 *     @type WP_Query $query
 *     @type bool     $show_pagination
 *     @type string   $layout
 *     @type int      $paged
 *     @type array    $trending_context
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$query            = isset( $args['query'] ) && $args['query'] instanceof WP_Query ? $args['query'] : null;
$show_pagination  = ! empty( $args['show_pagination'] );
$layout           = isset( $args['layout'] ) ? (string) $args['layout'] : 'grid';
$paged            = isset( $args['paged'] ) ? max( 1, (int) $args['paged'] ) : max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
$trending_context = isset( $args['trending_context'] ) && is_array( $args['trending_context'] ) ? $args['trending_context'] : array();

if ( ! $query || ! $query->have_posts() ) {
	?>
	<p class="vip-articles__empty"><?php esc_html_e( 'No articles found.', 'tenku-child' ); ?></p>
	<?php
	return;
}

$total_posts = (int) $query->found_posts;
$show_pag    = $show_pagination && ( $total_posts > 5 || (int) $query->max_num_pages > 1 );

if ( 'magazine' === $layout ) :
	$context_attr = wp_json_encode( $trending_context );
	if ( ! is_string( $context_attr ) ) {
		$context_attr = '{}';
	}
	?>
	<div
		class="vip-articles__magazine"
		data-vip-blog-listing
		data-vip-blog-context="<?php echo esc_attr( $context_attr ); ?>"
		data-vip-blog-paged="<?php echo esc_attr( (string) $paged ); ?>"
	>
		<section class="vip-articles__section vip-articles__section--latest" aria-labelledby="vip-articles-latest-heading">
			<h2 id="vip-articles-latest-heading" class="vip-articles__section-label"><?php esc_html_e( 'LATEST', 'tenku-child' ); ?></h2>

			<div class="vip-articles__latest-wrap" data-vip-blog-latest>
				<?php echo vip_transits_render_blog_latest_html( $query->posts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?>
			</div>
		</section>

		<?php
		echo vip_transits_render_blog_pagination_html( $query, $paged, $show_pag ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>

		<section class="vip-articles__section vip-articles__section--trending" aria-labelledby="vip-articles-trending-heading">
			<h2 id="vip-articles-trending-heading" class="vip-articles__section-label"><?php esc_html_e( 'TRENDING', 'tenku-child' ); ?></h2>

			<div class="vip-articles__trending-wrap" data-vip-blog-trending data-vip-blog-trending-ajax="<?php echo $paged > 1 ? '1' : '0'; ?>">
				<?php if ( $paged <= 1 ) : ?>
					<?php echo vip_transits_render_blog_trending_html( $trending_context ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php else : ?>
					<p class="vip-articles__loading" aria-live="polite"><?php esc_html_e( 'Loading trending…', 'tenku-child' ); ?></p>
				<?php endif; ?>
			</div>
		</section>
	</div>
	<?php
else :
	?>
	<div class="vip-articles__grid" role="list">
		<?php
		while ( $query->have_posts() ) {
			$query->the_post();
			echo '<div class="vip-articles__grid-item" role="listitem">';
			get_template_part( 'template-parts/article/card' );
			echo '</div>';
		}
		wp_reset_postdata();
		?>
	</div>
	<?php
	if ( $show_pag ) :
		echo vip_transits_render_blog_pagination_html( $query, $paged, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	endif;
endif;
