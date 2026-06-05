<?php
/**
 * Contact content layout: key takeaways.
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = function_exists( 'vip_transits_page_content_post_id' ) ? vip_transits_page_content_post_id() : 0;
$brand   = function_exists( 'vip_transits_contact_content_brand_name' )
	? vip_transits_contact_content_brand_name( $post_id )
	: get_bloginfo( 'name' );

$heading = (string) get_sub_field( 'heading' );
$summary = function_exists( 'vip_transits_contact_content_replace_brand' )
	? vip_transits_contact_content_replace_brand( (string) get_sub_field( 'summary' ), $brand )
	: (string) get_sub_field( 'summary' );

$items = array();
if ( have_rows( 'items' ) ) {
	while ( have_rows( 'items' ) ) {
		the_row();
		$text = trim( (string) get_sub_field( 'text' ) );
		if ( $text !== '' ) {
			$items[] = function_exists( 'vip_transits_contact_content_replace_brand' )
				? vip_transits_contact_content_replace_brand( $text, $brand )
				: $text;
		}
	}
}

if ( ! $heading && ! $items && ! $summary ) {
	return;
}
?>
<section class="vip-border-section vip-border-section--surface vip-border-section--plain-panel vip-contact-content-section vip-contact-content-section--takeaways" aria-labelledby="vip-contact-content-takeaways-title">
	<div class="vip-content-container">
		<?php if ( $heading ) : ?>
			<header class="vip-contact-content__head">
				<h2 id="vip-contact-content-takeaways-title" class="vip-page__section-title"><?php echo esc_html( $heading ); ?></h2>
				<hr class="vip-contact-content__rule" />
			</header>
		<?php endif; ?>
		<?php if ( $items ) : ?>
			<ul class="vip-contact-content-takeaways__grid">
				<?php foreach ( $items as $item_text ) : ?>
					<li class="vip-contact-content-takeaways__item">
						<svg class="vip-contact-content-takeaways__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="square"/></svg>
						<p class="vip-contact-content-takeaways__text"><?php echo esc_html( $item_text ); ?></p>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<?php if ( $summary ) : ?>
			<p class="vip-contact-content-takeaways__summary"><?php echo esc_html( $summary ); ?></p>
		<?php endif; ?>
	</div>
</section>
