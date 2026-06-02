<?php
/**
 * Page masthead (black band, centered title).
 *
 * @package Tenku_Child
 *
 * @var array $args {
 *     @type string $title    H1 text (defaults to post title).
 *     @type string $lead     Optional subtitle.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$title = isset( $args['title'] ) && $args['title'] ? (string) $args['title'] : get_the_title();
$lead  = isset( $args['lead'] ) ? (string) $args['lead'] : '';
?>
<header class="vip-bg-black-section vip-bg-black-section--masthead">
	<div class="vip-bg-black-section__inner vip-content-container">
		<h1 class="vip-page__masthead-title"><?php echo esc_html( $title ); ?></h1>
		<?php if ( $lead ) : ?>
			<p class="vip-page__masthead-lead"><?php echo esc_html( $lead ); ?></p>
		<?php endif; ?>
	</div>
</header>
