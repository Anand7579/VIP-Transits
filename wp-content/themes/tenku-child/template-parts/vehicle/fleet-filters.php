<?php
/**
 * Fleet sidebar filters (Figma fleet layout).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$tpl_args    = get_query_var( 'vip_fleet_grid' );
$filter_mode = ( is_array( $tpl_args ) && ! empty( $tpl_args['filter_mode'] ) ) ? (string) $tpl_args['filter_mode'] : 'fleet';
$is_occasion = ( 'occasion' === $filter_mode );

$role_filter_label = '';
if ( $is_occasion && is_array( $tpl_args ) && ! empty( $tpl_args['role_filter_label'] ) ) {
	$role_filter_label = (string) $tpl_args['role_filter_label'];
} elseif ( $is_occasion ) {
	$role_filter_label = __( 'Occasion role', 'tenku-child' );
}

$category_order = array(
	'sports',
	'convertible',
	'luxury',
	'suv',
);

$categories = vip_transits_get_ordered_terms( 'vehicle_category', $category_order );

$brands = function_exists( 'vip_transits_get_fleet_brand_terms' )
	? vip_transits_get_fleet_brand_terms()
	: array();

$seat_order = array( '2-seats', '4-seats', '5-seats', '7-seats' );
$seats      = vip_transits_get_ordered_terms( 'vehicle_seat', $seat_order );

$occasion_roles = $is_occasion && function_exists( 'vip_transits_get_fleet_occasion_role_terms' )
	? vip_transits_get_fleet_occasion_role_terms()
	: array();

$price_bounds = function_exists( 'vip_transits_get_fleet_price_bounds' )
	? vip_transits_get_fleet_price_bounds()
	: array(
		'min' => 500,
		'max' => 5000,
	);
$price_min    = (int) $price_bounds['min'];
$price_max    = (int) $price_bounds['max'];
?>
<aside id="vip-fleet-filters-panel" class="vip-fleet__filters" aria-label="<?php esc_attr_e( 'Filter vehicles', 'tenku-child' ); ?>">
	<div class="vip-fleet__filters-head">
		<h2 class="vip-fleet__filters-title"><?php esc_html_e( 'Filter', 'tenku-child' ); ?></h2>
		<button type="button" class="vip-fleet__filter-reset" data-vip-fleet-filter-reset>
			<?php esc_html_e( 'Reset', 'tenku-child' ); ?>
		</button>
	</div>

	<?php if ( $is_occasion && $occasion_roles && $role_filter_label ) : ?>
		<div class="vip-fleet__filter-group vip-fleet__filter-group--occasion-role">
			<h3 class="vip-fleet__filter-label"><?php echo esc_html( $role_filter_label ); ?></h3>
			<ul class="vip-fleet__filter-list">
				<?php foreach ( $occasion_roles as $term ) : ?>
					<li>
						<label class="vip-fleet__check">
							<input type="checkbox" name="role" value="<?php echo esc_attr( $term->slug ); ?>" data-vip-fleet-filter="role" />
							<span class="vip-fleet__check-box" aria-hidden="true"></span>
							<span class="vip-fleet__check-text"><?php echo esc_html( $term->name ); ?></span>
						</label>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<?php if ( $categories ) : ?>
		<div class="vip-fleet__filter-group">
			<h3 class="vip-fleet__filter-label"><?php esc_html_e( 'Car Category', 'tenku-child' ); ?></h3>
			<ul class="vip-fleet__filter-list">
				<?php foreach ( $categories as $term ) : ?>
					<li>
						<label class="vip-fleet__check">
							<input type="checkbox" name="category" value="<?php echo esc_attr( $term->slug ); ?>" data-vip-fleet-filter="category" />
							<span class="vip-fleet__check-box" aria-hidden="true"></span>
							<span class="vip-fleet__check-text"><?php echo esc_html( $term->name ); ?></span>
						</label>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<?php if ( $brands ) : ?>
		<div class="vip-fleet__filter-group">
			<h3 class="vip-fleet__filter-label"><?php esc_html_e( 'Car Brand', 'tenku-child' ); ?></h3>
			<ul class="vip-fleet__filter-list">
				<?php foreach ( $brands as $term ) : ?>
					<li>
						<label class="vip-fleet__check">
							<input type="checkbox" name="brand" value="<?php echo esc_attr( $term->slug ); ?>" data-vip-fleet-filter="brand" />
							<span class="vip-fleet__check-box" aria-hidden="true"></span>
							<span class="vip-fleet__check-text"><?php echo esc_html( $term->name ); ?></span>
						</label>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<?php if ( $seats ) : ?>
		<div class="vip-fleet__filter-group">
			<h3 class="vip-fleet__filter-label"><?php esc_html_e( 'Number of Seats', 'tenku-child' ); ?></h3>
			<ul class="vip-fleet__filter-list">
				<?php foreach ( $seats as $term ) : ?>
					<li>
						<label class="vip-fleet__check">
							<input type="checkbox" name="seat" value="<?php echo esc_attr( $term->slug ); ?>" data-vip-fleet-filter="seat" />
							<span class="vip-fleet__check-box" aria-hidden="true"></span>
							<span class="vip-fleet__check-text"><?php echo esc_html( $term->name ); ?></span>
						</label>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<div class="vip-fleet__filter-group">
		<h3 class="vip-fleet__filter-label"><?php esc_html_e( 'Price Balance (AED)', 'tenku-child' ); ?></h3>
		<div class="vip-fleet__range" data-vip-fleet-price-range>
			<div class="vip-fleet__range-track">
				<div class="vip-fleet__range-fill" data-vip-fleet-range-fill></div>
				<input type="range" class="vip-fleet__range-input vip-fleet__range-input--min" data-vip-fleet-price-min min="<?php echo esc_attr( (string) $price_min ); ?>" max="<?php echo esc_attr( (string) $price_max ); ?>" value="<?php echo esc_attr( (string) $price_min ); ?>" step="50" aria-label="<?php esc_attr_e( 'Minimum price', 'tenku-child' ); ?>" />
				<input type="range" class="vip-fleet__range-input vip-fleet__range-input--max" data-vip-fleet-price-max min="<?php echo esc_attr( (string) $price_min ); ?>" max="<?php echo esc_attr( (string) $price_max ); ?>" value="<?php echo esc_attr( (string) $price_max ); ?>" step="50" aria-label="<?php esc_attr_e( 'Maximum price', 'tenku-child' ); ?>" />
			</div>
			<div class="vip-fleet__range-labels">
				<span data-vip-fleet-price-min-label><?php echo esc_html( number_format_i18n( $price_min ) ); ?></span>
				<span data-vip-fleet-price-max-label><?php echo esc_html( number_format_i18n( $price_max ) ); ?></span>
			</div>
		</div>
	</div>

	<div class="vip-fleet__filter-group vip-fleet__filter-group--last">
		<h3 class="vip-fleet__filter-label"><?php esc_html_e( 'Delivery Option', 'tenku-child' ); ?></h3>
		<label class="vip-fleet__toggle">
			<span class="vip-fleet__toggle-text"><?php esc_html_e( 'Deliver To Hotel / Home', 'tenku-child' ); ?></span>
			<span class="vip-fleet__toggle-control">
				<input type="checkbox" data-vip-fleet-filter="delivery" class="vip-fleet__toggle-input" value="1" />
				<span class="vip-fleet__toggle-switch" aria-hidden="true"></span>
			</span>
		</label>
	</div>
</aside>
