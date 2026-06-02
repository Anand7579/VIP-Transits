<?php
/**
 * Block: vip-vehicle-archive
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$per_page   = 9;
$query      = new WP_Query( vip_transits_vehicle_query_args( array( 'posts_per_page' => $per_page ) ) );
$categories = function_exists( 'vip_transits_get_homepage_vehicle_categories' )
	? vip_transits_get_homepage_vehicle_categories()
	: array();
?>
<section id="vip-fleet" class="vip-fleet vip-fleet--archive" data-vip-section>
	<div class="vip-fleet__container vip-content-container">
		<?php
		if ( $categories ) {
			get_template_part(
				'template-parts/vehicle/category',
				'row',
				array(
					'categories' => $categories,
					'context'    => 'fleet',
				)
			);
		}
		?>

		<header class="vip-fleet__header">
			<h1 class="vip-fleet__title"><?php esc_html_e( 'Our Luxury Car Fleet in Dubai', 'tenku-child' ); ?></h1>
			<p class="vip-fleet__subtitle"><?php esc_html_e( 'Every brand. Every model. Available on demand.', 'tenku-child' ); ?></p>
			<hr class="vip-fleet__rule" />
		</header>

		<?php
		vip_transits_render_fleet_grid(
			array(
				'query'          => $query,
				'per_page'       => $per_page,
				'show_load_more' => true,
				'show_filters'   => true,
			)
		);
		?>
	</div>
</section>
