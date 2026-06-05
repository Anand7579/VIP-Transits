<?php
/**
 * About flexible layout: key takeaways.
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = (string) get_sub_field( 'heading' );
$summary = (string) get_sub_field( 'summary' );

$items = array();
if ( have_rows( 'items' ) ) {
	while ( have_rows( 'items' ) ) {
		the_row();
		$text = trim( (string) get_sub_field( 'text' ) );
		if ( $text !== '' ) {
			$items[] = $text;
		}
	}
}

if ( ! $heading && ! $items && ! $summary ) {
	return;
}
?>
<section class="vip-border-section vip-border-section--plain-panel vip-about-section vip-about-section--key-takeaways">
	<div class="vip-content-container vip-page__block-inner">
		<?php if ( $heading ) : ?>
			<h2 class="vip-page__section-title"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>
		<?php if ( $items ) : ?>
			<ul class="vip-about-takeaways__list">
				<?php foreach ( $items as $item_text ) : ?>
					<li class="vip-about-takeaways__item"><?php echo esc_html( $item_text ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<?php if ( $summary ) : ?>
			<div class="vip-about-takeaways__summary vip-page__prose"><?php echo wp_kses_post( $summary ); ?></div>
		<?php endif; ?>
	</div>
</section>
