<?php
/**
 * Hero section: video/image BG, search, value props (header is a separate template part).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bg_type       = get_sub_field( 'bg_type' ) ?: 'video';
$bg_image      = get_sub_field( 'background_image' );
$bg_video      = get_sub_field( 'background_video' );
$eyebrow_text  = get_sub_field( 'eyebrow_text' );
$eyebrow_badge = get_sub_field( 'eyebrow_badge' );
$heading           = get_sub_field( 'heading' );
$heading_highlight = get_sub_field( 'heading_highlight' );

if ( ! $heading_highlight && $heading && preg_match( '/^(.+?),\s*(via\b.+)$/iu', $heading, $heading_parts ) ) {
	$heading           = trim( $heading_parts[1] );
	$heading_highlight = trim( $heading_parts[2] );
	if ( $heading && ',' !== substr( $heading, -1 ) ) {
		$heading .= ',';
	}
}
$search_ph     = get_sub_field( 'search_placeholder' ) ?: __( 'Search', 'tenku-child' );
$search_url    = get_sub_field( 'search_action_url' );

$img_url   = is_array( $bg_image ) && ! empty( $bg_image['url'] ) ? $bg_image['url'] : '';
$video_url = '';
if ( is_array( $bg_video ) && ! empty( $bg_video['url'] ) ) {
	$video_url = $bg_video['url'];
} elseif ( is_string( $bg_video ) && $bg_video ) {
	$video_url = $bg_video;
}

$use_video     = ( 'video' === $bg_type && $video_url );
$use_image     = ( 'image' === $bg_type && $img_url ) || ( ! $use_video && $img_url );
$poster        = $img_url ?: '';
$search_action = $search_url ? $search_url : home_url( '/' );
$hero_classes  = 'vip-hero' . ( $use_video ? ' vip-hero--video' : ' vip-hero--image' );
?>
<section class="<?php echo esc_attr( $hero_classes ); ?>" data-vip-section data-bg-type="<?php echo esc_attr( $bg_type ); ?>">
	<div class="vip-hero__media" aria-hidden="true">
		<?php if ( $use_video ) : ?>
			<video
				class="vip-hero__video"
				autoplay
				muted
				loop
				playsinline
				<?php echo $poster ? 'poster="' . esc_url( $poster ) . '"' : ''; ?>
			>
				<source src="<?php echo esc_url( $video_url ); ?>" type="<?php echo esc_attr( is_array( $bg_video ) && ! empty( $bg_video['mime_type'] ) ? $bg_video['mime_type'] : 'video/mp4' ); ?>" />
			</video>
		<?php elseif ( $use_image ) : ?>
			<div class="vip-hero__image" style="background-image:url(<?php echo esc_url( $img_url ); ?>);"></div>
		<?php endif; ?>
		<div class="vip-hero__overlay"></div>
	</div>

	<div class="vip-hero__shell">
		<div class="vip-hero__container vip-content-container">
		<div class="vip-hero__body">
			<?php if ( $eyebrow_text || $eyebrow_badge ) : ?>
				<p class="vip-hero__eyebrow">
					<span class="vip-hero__eyebrow-line" aria-hidden="true"></span>
					<?php if ( $eyebrow_text ) : ?>
						<span class="vip-hero__eyebrow-text"><?php echo esc_html( $eyebrow_text ); ?></span>
					<?php endif; ?>
					<?php if ( $eyebrow_badge ) : ?>
						<span class="vip-hero__eyebrow-badge"><?php echo esc_html( $eyebrow_badge ); ?></span>
					<?php endif; ?>
				</p>
			<?php endif; ?>

			<?php if ( $heading || $heading_highlight ) : ?>
				<h1 class="vip-hero__title">
					<?php if ( $heading ) : ?>
						<span class="vip-hero__title-primary"><?php echo esc_html( $heading ); ?></span>
					<?php endif; ?>
					<?php if ( $heading_highlight ) : ?>
						<span class="vip-hero__title-highlight"><?php echo esc_html( $heading_highlight ); ?></span>
					<?php endif; ?>
				</h1>
			<?php endif; ?>

			<form class="vip-hero__search" role="search" method="get" action="<?php echo esc_url( $search_action ); ?>">
				<label class="screen-reader-text" for="vip-hero-search"><?php esc_html_e( 'Search', 'tenku-child' ); ?></label>
				<input
					id="vip-hero-search"
					class="vip-hero__search-input"
					type="search"
					name="s"
					placeholder="<?php echo esc_attr( $search_ph ); ?>"
					value="<?php echo esc_attr( get_search_query() ); ?>"
				/>
				<button class="vip-hero__search-btn" type="submit" aria-label="<?php esc_attr_e( 'Search', 'tenku-child' ); ?>">
					<?php
					if ( function_exists( 'vip_transits_theme_icon_img' ) ) {
						echo vip_transits_theme_icon_img( 'search', array( 'class' => 'vip-hero__search-icon' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</button>
			</form>

			<?php if ( have_rows( 'value_props' ) ) : ?>
				<ul class="vip-hero__props">
					<?php
					$prop_index = 0;
					while ( have_rows( 'value_props' ) ) :
						the_row();
						$prop_icon = get_sub_field( 'icon' );
						$prop_text = get_sub_field( 'text' );
						if ( ! $prop_text ) {
							continue;
						}
						$prop_classes = 'vip-hero__prop';
						if ( $prop_index > 0 && 0 === $prop_index % 3 ) {
							$prop_classes .= ' vip-hero__prop--row-start';
						}
						++$prop_index;
						?>
						<li class="<?php echo esc_attr( $prop_classes ); ?>">
							<?php if ( is_array( $prop_icon ) && ! empty( $prop_icon['url'] ) ) : ?>
								<img
									class="vip-hero__prop-icon"
									src="<?php echo esc_url( $prop_icon['url'] ); ?>"
									alt=""
									width="24"
									height="24"
									loading="lazy"
								/>
							<?php endif; ?>
							<span class="vip-hero__prop-text"><?php echo esc_html( $prop_text ); ?></span>
						</li>
					<?php endwhile; ?>
				</ul>
			<?php endif; ?>
		</div>
		</div>
	</div>
</section>
