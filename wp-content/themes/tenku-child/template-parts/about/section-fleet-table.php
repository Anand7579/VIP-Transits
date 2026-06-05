<?php
/**
 * About flexible layout: fleet overview table.
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading  = (string) get_sub_field( 'heading' );
$intro    = (string) get_sub_field( 'intro' );
$footnote = (string) get_sub_field( 'footnote' );

$rows = array();
if ( have_rows( 'rows' ) ) {
	while ( have_rows( 'rows' ) ) {
		the_row();
		$brand  = trim( (string) get_sub_field( 'brand' ) );
		$models = trim( (string) get_sub_field( 'models' ) );
		$price  = trim( (string) get_sub_field( 'price' ) );
		if ( ! $brand && ! $models && ! $price ) {
			continue;
		}
		$rows[] = array(
			'brand'  => $brand,
			'models' => $models,
			'price'  => $price,
		);
	}
}

if ( ! $heading && ! $intro && ! $rows && ! $footnote ) {
	return;
}
?>
<section class="vip-border-section vip-border-section--plain-panel vip-about-section vip-about-section--fleet-table">
	<div class="vip-content-container">
		<?php if ( $heading ) : ?>
			<h2 class="vip-page__section-title"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>
		<?php if ( $intro ) : ?>
			<p class="vip-about-fleet-table__intro"><?php echo esc_html( $intro ); ?></p>
		<?php endif; ?>
		<?php if ( $rows ) : ?>
			<div class="vip-about-fleet-table__wrap">
				<table class="vip-about-fleet-table">
					<thead>
						<tr>
							<th scope="col"><?php esc_html_e( 'Brand', 'tenku-child' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Models', 'tenku-child' ); ?></th>
							<th scope="col"><?php esc_html_e( 'Price', 'tenku-child' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $rows as $row ) : ?>
							<tr>
								<td><?php echo esc_html( $row['brand'] ); ?></td>
								<td><?php echo esc_html( $row['models'] ); ?></td>
								<td><?php echo esc_html( $row['price'] ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>
		<?php if ( $footnote ) : ?>
			<p class="vip-about-fleet-table__footnote"><?php echo esc_html( $footnote ); ?></p>
		<?php endif; ?>
	</div>
</section>
