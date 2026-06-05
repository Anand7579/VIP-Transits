<?php
/**
 * Contact page: key takeaways.
 *
 * @package Tenku_Child
 *
 * @var array $args {
 *     @type string $heading Section title.
 *     @type array  $items   List of text strings.
 *     @type string $summary Optional summary paragraph.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = isset( $args['heading'] ) ? trim( (string) $args['heading'] ) : '';
$summary = isset( $args['summary'] ) ? trim( (string) $args['summary'] ) : '';
$items   = isset( $args['items'] ) && is_array( $args['items'] ) ? $args['items'] : array();

$item_rows = array();
foreach ( $items as $item ) {
	$text = trim( (string) $item );
	if ( $text !== '' ) {
		$item_rows[] = $text;
	}
}

if ( ! $item_rows && ! $summary ) {
	return;
}
?>
<section class="vip-border-section vip-border-section--surface vip-border-section--plain-panel vip-contact-takeaways" aria-labelledby="vip-contact-takeaways-title">
	<div class="vip-content-container">
		<?php if ( $heading ) : ?>
			<header class="vip-contact-takeaways__header">
				<h2 id="vip-contact-takeaways-title" class="vip-fleet__title"><?php echo esc_html( $heading ); ?></h2>
				<hr class="vip-fleet__rule" />
			</header>
		<?php endif; ?>
		<?php if ( $item_rows ) : ?>
			<ul class="vip-contact-takeaways__grid">
				<?php foreach ( $item_rows as $item_text ) : ?>
					<li class="vip-contact-takeaways__item">
						<svg class="vip-contact-takeaways__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="square"/></svg>
						<p class="vip-contact-takeaways__text"><?php echo esc_html( $item_text ); ?></p>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<?php if ( $summary ) : ?>
			<p class="vip-contact-takeaways__summary"><?php echo esc_html( $summary ); ?></p>
		<?php endif; ?>
	</div>
</section>
