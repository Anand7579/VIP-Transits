<?php
/**
 * Fleet grid shell (toolbar + cards + load more).
 *
 * Args via get_template_part( ..., array( query, per_page, show_load_more, show_filters ) ).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// get_template_part( ..., $args ) uses extract() in load_template(); do not read $args['key'] only.
$tpl_args = get_query_var( 'args' );
if ( ! is_array( $tpl_args ) ) {
	$tpl_args = array();
}

$query = ( isset( $query ) && $query instanceof WP_Query )
	? $query
	: ( $tpl_args['query'] ?? null );
$per_page = isset( $per_page )
	? (int) $per_page
	: ( isset( $tpl_args['per_page'] ) ? (int) $tpl_args['per_page'] : 9 );
$show_load_more = isset( $show_load_more )
	? (bool) $show_load_more
	: ! empty( $tpl_args['show_load_more'] );
$show_filters = isset( $show_filters )
	? (bool) $show_filters
	: ! empty( $tpl_args['show_filters'] );

if ( ! $query instanceof WP_Query ) {
	return;
}

$found = (int) $query->found_posts;
$max   = (int) $query->max_num_pages;
?>
<div class="vip-fleet__layout<?php echo $show_filters ? '' : ' vip-fleet__layout--no-sidebar'; ?>" data-vip-fleet data-per-page="<?php echo esc_attr( (string) $per_page ); ?>" data-max-pages="<?php echo esc_attr( (string) $max ); ?>">
	<?php if ( $show_filters ) : ?>
		<?php get_template_part( 'template-parts/vehicle/fleet', 'filters' ); ?>
	<?php endif; ?>

	<div class="vip-fleet__main">
		<div class="vip-fleet__toolbar">
			<p class="vip-fleet__count" data-vip-fleet-count>
				<?php
				printf(
					/* translators: %s: number of vehicles */
					esc_html__( 'Showing %s vehicles', 'tenku-child' ),
					'<span>' . esc_html( (string) $found ) . '</span>'
				);
				?>
			</p>
			<div class="vip-fleet__sort" data-vip-fleet-sort-wrap>
				<input type="hidden" data-vip-fleet-sort value="title-asc" />
				<div class="vip-fleet__sort-box">
					<button type="button" class="vip-fleet__sort-trigger" data-vip-fleet-sort-trigger aria-expanded="false" aria-haspopup="listbox">
						<span class="vip-fleet__sort-prefix"><?php esc_html_e( 'Sort:', 'tenku-child' ); ?></span>
						<span class="vip-fleet__sort-current" data-vip-fleet-sort-label><?php esc_html_e( 'Car Type', 'tenku-child' ); ?></span>
						<span class="vip-fleet__sort-chevron" aria-hidden="true"></span>
					</button>
				</div>
				<ul class="vip-fleet__sort-menu" data-vip-fleet-sort-menu role="listbox" hidden></ul>
			</div>
		</div>

		<div class="vip-fleet__results" data-vip-fleet-results>
			<div class="vip-fleet__loader" data-vip-fleet-loader aria-hidden="true" aria-live="polite">
				<span class="vip-fleet__loader-spinner" aria-hidden="true"></span>
				<span class="screen-reader-text"><?php esc_html_e( 'Updating vehicle results…', 'tenku-child' ); ?></span>
			</div>
			<div class="vip-fleet__grid" data-vip-fleet-grid>
				<?php
				if ( $query->have_posts() ) :
					while ( $query->have_posts() ) :
						$query->the_post();
						get_template_part( 'template-parts/vehicle/card' );
					endwhile;
					wp_reset_postdata();
				else :
					?>
					<p class="vip-fleet__empty"><?php esc_html_e( 'No vehicles match your filters. Add vehicles under Vehicles in the admin.', 'tenku-child' ); ?></p>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $show_load_more && $max > 1 ) : ?>
			<div class="vip-fleet__more-wrap">
				<button type="button" class="vip-fleet__load-more" data-vip-fleet-load-more data-page="1">
					<?php esc_html_e( 'Load more', 'tenku-child' ); ?> →
				</button>
			</div>
		<?php endif; ?>
	</div>
</div>
