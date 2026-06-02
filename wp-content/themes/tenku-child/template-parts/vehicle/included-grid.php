<?php
/**
 * What's included card grid (from ACF included_items).
 *
 * @package Tenku_Child
 *
 * @var array $args {
 *     @type array $items Rows with title, description, icon_url, icon_alt.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = isset( $args['items'] ) && is_array( $args['items'] ) ? $args['items'] : array();
if ( ! $items ) {
	return;
}
?>
<div class="vip-included__grid">
	<?php foreach ( $items as $item ) : ?>
		<?php
		$title    = isset( $item['title'] ) ? trim( (string) $item['title'] ) : '';
		$desc     = isset( $item['description'] ) ? trim( (string) $item['description'] ) : '';
		$icon_url = isset( $item['icon_url'] ) ? trim( (string) $item['icon_url'] ) : '';
		$icon_alt = isset( $item['icon_alt'] ) ? trim( (string) $item['icon_alt'] ) : '';
		if ( ! $title && ! $desc && ! $icon_url ) {
			continue;
		}
		if ( ! $icon_alt && $title ) {
			$icon_alt = $title;
		}
		?>
		<div class="vip-included__item">
			<div class="vip-included__icon" aria-hidden="true">
				<?php if ( $icon_url ) : ?>
					<img
						class="vip-included__icon-img"
						src="<?php echo esc_url( $icon_url ); ?>"
						alt=""
						loading="lazy"
						decoding="async"
					/>
				<?php else : ?>
					<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
				<?php endif; ?>
			</div>
			<div class="vip-included__text">
				<?php if ( $title ) : ?>
					<span class="vip-included__title"><?php echo esc_html( $title ); ?></span>
				<?php endif; ?>
				<?php if ( $desc ) : ?>
					<span class="vip-included__desc"><?php echo esc_html( $desc ); ?></span>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>
