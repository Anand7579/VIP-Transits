<?php
/**
 * About flexible layout: definition callout.
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = (string) get_sub_field( 'heading' );
$content = (string) get_sub_field( 'content' );

if ( ! $heading && ! $content ) {
	return;
}
?>
<section class="vip-border-section vip-border-section--surface vip-border-section--plain-panel vip-about-section vip-about-section--definition-callout">
	<div class="vip-content-container">
		<div class="vip-about-callout">
			<?php if ( $heading ) : ?>
				<h2 class="vip-about-callout__title"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( $content ) : ?>
				<div class="vip-about-callout__body vip-page__prose"><?php echo wp_kses_post( $content ); ?></div>
			<?php endif; ?>
		</div>
	</div>
</section>
