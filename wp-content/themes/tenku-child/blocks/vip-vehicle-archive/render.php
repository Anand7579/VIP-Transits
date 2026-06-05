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
$categories    = function_exists( 'vip_transits_get_homepage_vehicle_categories' )
	? vip_transits_get_homepage_vehicle_categories()
	: array();
$show_banner  = function_exists( 'vip_transits_fleet_archive_show_banner' ) && vip_transits_fleet_archive_show_banner();
$banner_desc  = function_exists( 'vip_transits_get_fleet_archive_banner_description' )
	? vip_transits_get_fleet_archive_banner_description()
	: '';
?>
<?php
if ( $show_banner && function_exists( 'vip_transits_render_fleet_archive_banner' ) ) {
	vip_transits_render_fleet_archive_banner();
}
?>
<section id="vip-fleet" class="vip-fleet vip-fleet--archive<?php echo $show_banner ? ' vip-fleet--archive-has-banner' : ''; ?>" data-vip-section>
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

		<?php if ( ! $show_banner && $banner_desc !== '' ) : ?>
		<header class="vip-fleet__header vip-fleet__header--desc-only">
			<div class="vip-fleet__subtitle"><?php echo wp_kses_post( $banner_desc ); ?></div>
			<hr class="vip-fleet__rule" />
		</header>
		<?php endif; ?>

		<?php
		vip_transits_render_fleet_grid(
			array(
				'query'          => $query,
				'per_page'       => $per_page,
				'show_load_more' => true,
				'show_filters'   => true,
				'filter_mode'    => 'fleet',
			)
		);
		?>
	</div>
</section>
