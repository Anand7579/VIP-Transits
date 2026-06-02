<?php
/**
 * Block: tenku-child/vip-bg-black-section
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

$button_text = isset( $attributes['buttonText'] ) ? trim( (string) $attributes['buttonText'] ) : '';
$button_url  = isset( $attributes['buttonUrl'] ) ? trim( (string) $attributes['buttonUrl'] ) : '';

// Backward compatibility for earlier attribute names.
if ( '' === $button_text && ! empty( $attributes['buttonLabel'] ) ) {
	$button_text = trim( (string) $attributes['buttonLabel'] );
}
if ( '' === $button_text && ! empty( $attributes['ctaText'] ) ) {
	$button_text = trim( (string) $attributes['ctaText'] );
}
if ( '' === $button_url && ! empty( $attributes['buttonLink'] ) ) {
	$button_url = trim( (string) $attributes['buttonLink'] );
}
if ( '' === $button_url && ! empty( $attributes['ctaUrl'] ) ) {
	$button_url = trim( (string) $attributes['ctaUrl'] );
}

$inner_html = function_exists( 'vip_transits_render_block_inner_html' )
	? vip_transits_render_block_inner_html( $content, $block )
	: (string) $content;

// Fallback if helper isn't loaded or saved inner HTML is empty.
if ( '' === trim( (string) $inner_html ) && $block instanceof WP_Block && ! empty( $block->parsed_block['innerBlocks'] ) ) {
	$rendered = '';
	foreach ( $block->parsed_block['innerBlocks'] as $inner_block ) {
		$rendered .= render_block( $inner_block );
	}
	$inner_html = $rendered;
}

$wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'vip-bg-black-section',
	)
);

$button_href_attr = '';
$button_external  = false;

if ( $button_text && $button_url ) {
	$button_href_attr = esc_url( $button_url );
	$button_external  = true;
}
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="vip-content-container">
		<div class="vip-bg-black-section__inner">
			<?php echo $inner_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php if ( $button_text && $button_href_attr ) : ?>
				<p class="vip-bg-black-section__button-wrap">
					<a
						class="vip-bg-black-section__button"
						href="<?php echo $button_href_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"
						<?php echo $button_external ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>
					>
						<svg width="22" height="22" viewBox="0 0 24 24" focusable="false"><path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.882 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
						<span class="vip-bg-black-section__button-label"><?php echo esc_html( $button_text ); ?></span>
					</a>
				</p>
			<?php endif; ?>
		</div>
	</div>
</section>
