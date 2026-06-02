<?php
/**
 * Article card (grid or magazine listing).
 *
 * @package Tenku_Child
 *
 * @var array $args {
 *     @type array  $data   Optional card data.
 *     @type string $layout magazine|grid
 *     @type string $size   featured|compact|trending (magazine only)
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$data   = isset( $args['data'] ) && is_array( $args['data'] ) ? $args['data'] : vip_transits_get_article_card_data();
$layout = isset( $args['layout'] ) ? (string) $args['layout'] : 'grid';
$size   = isset( $args['size'] ) ? sanitize_html_class( (string) $args['size'] ) : '';

if ( empty( $data['id'] ) ) {
	return;
}

if ( 'magazine' === $layout ) :
	if ( 'featured' === $size ) {
		$large = get_the_post_thumbnail_url( (int) $data['id'], 'large' );
		if ( $large ) {
			$data['thumbnail'] = $large;
		}
	}

	$category = ! empty( $data['category'] ) ? (string) $data['category'] : '';
	if ( $category === '' && ! empty( $data['categories'][0] ) ) {
		$category = (string) $data['categories'][0];
	}

	$card_size = $size ? $size : 'compact';
	?>
	<article class="vip-article-card vip-article-card--<?php echo esc_attr( $card_size ); ?>">
		<?php if ( ! empty( $data['thumbnail'] ) ) : ?>
			<a class="vip-article-card__media" href="<?php echo esc_url( $data['permalink'] ); ?>">
				<img
					class="vip-article-card__img"
					src="<?php echo esc_url( $data['thumbnail'] ); ?>"
					alt="<?php echo esc_attr( $data['title'] ); ?>"
					width="400"
					height="250"
					loading="lazy"
					decoding="async"
				/>
			</a>
		<?php endif; ?>

		<div class="vip-article-card__body">
			<?php if ( $category ) : ?>
				<p class="vip-article-card__category"><?php echo esc_html( $category ); ?></p>
			<?php endif; ?>

			<h2 class="vip-article-card__title">
				<a href="<?php echo esc_url( $data['permalink'] ); ?>"><?php echo esc_html( $data['title'] ); ?></a>
			</h2>

			<?php if ( ! empty( $data['author'] ) ) : ?>
				<p class="vip-article-card__author"><?php echo esc_html( $data['author'] ); ?></p>
			<?php endif; ?>
		</div>
	</article>
	<?php
	return;
endif;
?>
<article class="vip-article-card">
	<?php if ( ! empty( $data['thumbnail'] ) ) : ?>
		<a class="vip-article-card__media" href="<?php echo esc_url( $data['permalink'] ); ?>">
			<img
				class="vip-article-card__img"
				src="<?php echo esc_url( $data['thumbnail'] ); ?>"
				alt="<?php echo esc_attr( $data['title'] ); ?>"
				width="400"
				height="250"
				loading="lazy"
				decoding="async"
			/>
		</a>
	<?php endif; ?>

	<div class="vip-article-card__body">
		<?php if ( ! empty( $data['date'] ) ) : ?>
			<time class="vip-article-card__date" datetime="<?php echo esc_attr( $data['date_iso'] ); ?>"><?php echo esc_html( $data['date'] ); ?></time>
		<?php endif; ?>

		<h2 class="vip-article-card__title">
			<a href="<?php echo esc_url( $data['permalink'] ); ?>"><?php echo esc_html( $data['title'] ); ?></a>
		</h2>

		<?php if ( ! empty( $data['author'] ) ) : ?>
			<p class="vip-article-card__author"><?php echo esc_html( $data['author'] ); ?></p>
		<?php endif; ?>

		<?php if ( ! empty( $data['excerpt'] ) ) : ?>
			<p class="vip-article-card__excerpt"><?php echo esc_html( $data['excerpt'] ); ?></p>
		<?php endif; ?>

		<a class="vip-article-card__link" href="<?php echo esc_url( $data['permalink'] ); ?>">
			<?php esc_html_e( 'Read article', 'tenku-child' ); ?>
		</a>
	</div>
</article>
