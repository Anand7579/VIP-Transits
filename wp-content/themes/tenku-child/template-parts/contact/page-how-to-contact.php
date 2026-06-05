<?php
/**
 * Contact page: how to contact VIP (steps).
 *
 * @package Tenku_Child
 *
 * @var array $args {
 *     @type string $heading Section title.
 *     @type array  $steps   List of { title, text }.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$heading = isset( $args['heading'] ) ? trim( (string) $args['heading'] ) : '';
$steps   = isset( $args['steps'] ) && is_array( $args['steps'] ) ? $args['steps'] : array();

$step_rows = array();
foreach ( $steps as $step ) {
	if ( ! is_array( $step ) ) {
		continue;
	}
	$title = isset( $step['title'] ) ? trim( (string) $step['title'] ) : '';
	$text  = isset( $step['text'] ) ? trim( (string) $step['text'] ) : '';
	if ( $title === '' && $text === '' ) {
		continue;
	}
	$step_rows[] = array(
		'title' => $title,
		'text'  => $text,
	);
}

if ( ! $step_rows ) {
	return;
}
?>
<section class="vip-border-section vip-border-section--surface vip-border-section--plain-panel vip-contact-how" aria-labelledby="vip-contact-how-title">
	<div class="vip-content-container">
		<?php if ( $heading ) : ?>
			<header class="vip-contact-how__header">
				<h2 id="vip-contact-how-title" class="vip-fleet__title"><?php echo esc_html( $heading ); ?></h2>
				<hr class="vip-fleet__rule" />
			</header>
		<?php endif; ?>
		<?php if ( $step_rows ) : ?>
			<ol class="vip-contact-how__steps">
				<?php foreach ( $step_rows as $index => $step ) : ?>
					<li class="vip-contact-how__item">
						<span class="vip-contact-how__number" aria-hidden="true"><?php echo esc_html( (string) ( $index + 1 ) ); ?></span>
						<?php if ( $step['title'] ) : ?>
							<h3 class="vip-contact-how__item-title"><?php echo esc_html( $step['title'] ); ?></h3>
						<?php endif; ?>
						<?php if ( $step['text'] ) : ?>
							<p class="vip-contact-how__item-text"><?php echo esc_html( $step['text'] ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
	</div>
</section>
