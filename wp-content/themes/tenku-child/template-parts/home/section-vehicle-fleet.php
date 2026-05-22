<?php
/**
 * Homepage fleet section (queries vip_vehicle CPT).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title       = get_sub_field( 'section_title' ) ?: __( 'Our Luxury Car Fleet in Dubai', 'tenku-child' );
$subtitle    = get_sub_field( 'section_subtitle' ) ?: __( 'Every brand. Every model. Available on demand.', 'tenku-child' );
$per_page    = (int) get_sub_field( 'posts_per_page' );
$per_page    = $per_page > 0 ? $per_page : 9;
$show_filters = get_sub_field( 'show_filters' );
$show_filters = null === $show_filters ? true : (bool) $show_filters;
$load_more    = get_sub_field( 'show_load_more' );
$load_more    = null === $load_more ? true : (bool) $load_more;

$query = new WP_Query( vip_transits_vehicle_query_args( array( 'posts_per_page' => $per_page ) ) );
?>
<section class="vip-fleet vip-fleet--section" data-vip-section>
	<div class="vip-fleet__container vip-content-container">
		<header class="vip-fleet__header">
			<?php if ( $title ) : ?>
				<h2 class="vip-fleet__title"><?php echo esc_html( $title ); ?></h2>
			<?php endif; ?>
			<?php if ( $subtitle ) : ?>
				<p class="vip-fleet__subtitle"><?php echo esc_html( $subtitle ); ?></p>
			<?php endif; ?>
			<hr class="vip-fleet__rule" />
		</header>

		<?php
		vip_transits_render_fleet_grid(
			array(
				'query'          => $query,
				'per_page'       => $per_page,
				'show_load_more' => $load_more,
				'show_filters'   => $show_filters,
			)
		);
		?>
	</div>
</section>
