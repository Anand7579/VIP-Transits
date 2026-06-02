<?php
/**
 * Block: tenku-child/vip-border-section
 *
 * @package Tenku_Child
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Inner blocks HTML.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$surface = ! empty( $attributes['surface'] );
$classes = array( 'vip-border-section' );
if ( $surface ) {
	$classes[] = 'vip-border-section--surface';
}

$inner_html = function_exists( 'vip_transits_render_block_inner_html' )
	? vip_transits_render_block_inner_html( $content, $block )
	: (string) $content;

$wrapper = get_block_wrapper_attributes(
	array(
		'class' => implode( ' ', $classes ),
	)
);
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="vip-content-container-fluid">
		<div class="vip-border-section__frame">
			<div class="vip-border-section__content">
				<?php echo $inner_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
	</div>
</section>
