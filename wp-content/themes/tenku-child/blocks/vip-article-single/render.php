<?php
/**
 * Block: vip-article-single — article detail (markup structure only).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

while ( have_posts() ) {
	the_post();
}

$d = vip_transits_get_article_single_data();

if ( empty( $d['id'] ) ) {
	return;
}

$home_url = home_url( '/' );
$blog_url = $d['blog_url'];
$toc      = ! empty( $d['toc'] ) && is_array( $d['toc'] ) ? $d['toc'] : array();
?>
<article <?php post_class( 'vip-article' ); ?>>

	<!-- Banner (unchanged) -->
	<header class="vip-article__masthead">
		<div class="vip-article__masthead-inner vip-content-container">
			<nav class="vip-article__breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'tenku-child' ); ?>">
				<a href="<?php echo esc_url( $home_url ); ?>"><?php esc_html_e( 'Home', 'tenku-child' ); ?></a>
				<span aria-hidden="true"> / </span>
				<a href="<?php echo esc_url( $blog_url ); ?>"><?php esc_html_e( 'Articles', 'tenku-child' ); ?></a>
				<span aria-hidden="true"> / </span>
				<span><?php esc_html_e( 'Article', 'tenku-child' ); ?></span>
			</nav>

			<h1><?php echo esc_html( $d['title'] ); ?></h1>

			<p>
				<?php if ( ! empty( $d['author'] ) ) : ?>
					<span><?php echo esc_html( $d['author'] ); ?></span>
				<?php endif; ?>
				<?php if ( ! empty( $d['date'] ) ) : ?>
					<time datetime="<?php echo esc_attr( $d['date_iso'] ); ?>"><?php echo esc_html( $d['date'] ); ?></time>
				<?php endif; ?>
			</p>
		</div>
	</header>

	<!-- Three columns: TOC | content | empty sidebar -->
	<div class="vip-article__body vip-content-container">
		<div class="vip-article__columns">

			<?php if ( $toc ) : ?>
				<aside class="vip-article__toc">
					<details class="vip-article__toc-accordion">
						<summary class="vip-article__toc-header">
							<span class="vip-article__toc-header-text"><?php esc_html_e( 'Table of Contents', 'tenku-child' ); ?></span>
							<span class="vip-article__toc-chevron" aria-hidden="true"></span>
						</summary>
						<div class="vip-article__toc-body">
							<nav aria-label="<?php esc_attr_e( 'Table of contents', 'tenku-child' ); ?>">
								<h2 class="vip-article__toc-heading"><?php esc_html_e( 'Table of Contents', 'tenku-child' ); ?></h2>
								<ol>
									<?php
									$first_toc_item = true;
									foreach ( $toc as $item ) :
										$item_id    = isset( $item['id'] ) ? (string) $item['id'] : '';
										$item_title = isset( $item['title'] ) ? (string) $item['title'] : '';
										if ( $item_id === '' || $item_title === '' ) {
											continue;
										}
										$is_first         = $first_toc_item;
										$first_toc_item   = false;
										?>
										<li class="vip-article__toc-item<?php echo $is_first ? ' is-active' : ''; ?>">
											<a class="vip-article__toc-link" href="#<?php echo esc_attr( $item_id ); ?>"><?php echo esc_html( $item_title ); ?></a>
										</li>
									<?php endforeach; ?>
								</ol>
							</nav>
						</div>
					</details>
				</aside>
			<?php endif; ?>

			<div class="vip-article__content">
				<?php if ( ! empty( $d['content'] ) ) : ?>
					<div class="entry-content">
						<?php echo $d['content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endif; ?>

				<footer>
					<a href="<?php echo esc_url( $blog_url ); ?>"><?php esc_html_e( '← Back to articles', 'tenku-child' ); ?></a>
				</footer>
			</div>

			<aside class="vip-article__sidebar"></aside>

		</div>
	</div>

	<?php if ( $toc ) : ?>
		<?php vip_transits_print_article_toc_script(); ?>
	<?php endif; ?>

</article>
