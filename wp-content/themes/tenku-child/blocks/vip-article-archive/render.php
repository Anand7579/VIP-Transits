<?php
/**
 * Block: vip-article-archive — article listing.
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Fleet URL must show vehicles — not the blog (fixes wrong template or /fleet Page slug).
if ( function_exists( 'vip_transits_is_fleet_listing_view' ) && vip_transits_is_fleet_listing_view() ) {
	if ( function_exists( 'vip_transits_render_fleet_archive_block' ) ) {
		vip_transits_render_fleet_archive_block();
	}
	return;
}

global $wp_query;

$paged            = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
$trending_context = array();

if ( $wp_query instanceof WP_Query && $wp_query->is_main_query() && vip_transits_is_blog_listing_request() ) {
	$query = $wp_query;

	if ( is_category() ) {
		$trending_context['cat'] = (int) get_queried_object_id();
	} elseif ( is_tag() ) {
		$trending_context['tag_id'] = (int) get_queried_object_id();
	}
} else {
	$query_args = vip_transits_article_query_args( array( 'paged' => $paged ) );

	if ( is_category() ) {
		$query_args['cat']       = (int) get_queried_object_id();
		$trending_context['cat'] = (int) get_queried_object_id();
	} elseif ( is_tag() ) {
		$query_args['tag_id']           = (int) get_queried_object_id();
		$trending_context['tag_id']     = (int) get_queried_object_id();
	} elseif ( is_author() ) {
		$query_args['author'] = (int) get_queried_object_id();
	} elseif ( is_date() ) {
		$query_args['year']     = (int) get_query_var( 'year' );
		$query_args['monthnum'] = (int) get_query_var( 'monthnum' );
		$query_args['day']      = (int) get_query_var( 'day' );
	}

	$query = new WP_Query( $query_args );
}

$masthead_title = vip_transits_get_article_masthead_title();
$subtitle       = vip_transits_get_article_archive_subtitle();
$show_topic     = is_category() || is_tag() || is_author() || is_date();
$topic_label    = $show_topic ? vip_transits_get_article_archive_title() : '';
?>
<section class="vip-articles vip-articles--archive" data-vip-section>
	<header class="vip-articles__masthead">
		<div class="vip-articles__masthead-inner vip-content-container">
			<h1 class="vip-articles__masthead-title"><?php echo esc_html( $masthead_title ); ?></h1>
			<?php if ( $subtitle ) : ?>
				<p class="vip-articles__masthead-lead"><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>
		</div>
	</header>

	<div class="vip-articles__body">
		<div class="vip-articles__container vip-content-container">
			<?php if ( $show_topic && $topic_label ) : ?>
				<div class="vip-articles__topic">
					<h2 class="vip-articles__topic-title"><?php echo esc_html( strtoupper( $topic_label ) ); ?></h2>
				</div>
			<?php endif; ?>

			<?php
			vip_transits_render_article_listing(
				array(
					'query'            => $query,
					'show_pagination'  => true,
					'layout'           => 'magazine',
					'paged'            => $paged,
					'trending_context' => $trending_context,
				)
			);
			?>
		</div>
	</div>
</section>
