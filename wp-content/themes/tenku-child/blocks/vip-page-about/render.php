<?php
/**
 * Block: vip-page-about
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = vip_transits_page_content_post_id();
if ( ! $post_id ) {
	return;
}

$lead           = (string) vip_transits_get_page_field( 'about_masthead_lead', '' );
$intro_heading  = (string) vip_transits_get_page_field( 'about_intro_heading', '' );
$intro_content  = (string) vip_transits_get_page_field( 'about_intro_content', '' );
$intro_image    = vip_transits_get_page_field( 'about_intro_image', null );
$trust_stats    = vip_transits_get_page_field( 'about_trust_stats', array() );
$content_blocks = vip_transits_get_page_field( 'about_content_blocks', array() );
$show_cta       = (bool) vip_transits_get_page_field( 'about_show_cta', true );
$cta_heading    = (string) vip_transits_get_page_field( 'about_cta_heading', __( 'Ready to book your luxury car?', 'tenku-child' ) );
$cta_text       = (string) vip_transits_get_page_field( 'about_cta_text', '' );
$cta_label      = (string) vip_transits_get_page_field( 'about_cta_button_label', __( 'Speak to concierge', 'tenku-child' ) );
$fleet_url      = get_post_type_archive_link( 'vip_vehicle' );
$wa_href        = function_exists( 'vip_transits_whatsapp_href_attr' ) ? vip_transits_whatsapp_href_attr() : '';
?>
<article class="vip-page vip-page--about" data-vip-section>
	<?php
	get_template_part(
		'template-parts/page/masthead',
		null,
		array(
			'title' => get_the_title( $post_id ),
			'lead'  => $lead,
		)
	);
	?>

	<div class="vip-page__body">
		<?php if ( $intro_heading || $intro_content || ( is_array( $intro_image ) && ! empty( $intro_image['url'] ) ) ) : ?>
			<section class="vip-border-section vip-border-section--surface vip-border-section--plain-panel">
				<div class="vip-content-container vip-page__intro-grid">
					<div class="vip-page__intro-copy">
						<?php if ( $intro_heading ) : ?>
							<h2 class="vip-page__section-title"><?php echo esc_html( $intro_heading ); ?></h2>
						<?php endif; ?>
						<?php if ( $intro_content ) : ?>
							<div class="vip-page__prose"><?php echo wp_kses_post( $intro_content ); ?></div>
						<?php endif; ?>
					</div>
					<?php if ( is_array( $intro_image ) && ! empty( $intro_image['url'] ) ) : ?>
						<figure class="vip-page__intro-media">
							<img
								src="<?php echo esc_url( $intro_image['url'] ); ?>"
								alt="<?php echo esc_attr( $intro_image['alt'] ?? $intro_heading ); ?>"
								loading="lazy"
								decoding="async"
							/>
						</figure>
					<?php endif; ?>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( is_array( $trust_stats ) && $trust_stats ) : ?>
			<section class="vip-border-section vip-border-section--surface vip-border-section--plain-panel" aria-label="<?php esc_attr_e( 'Company highlights', 'tenku-child' ); ?>">
				<div class="vip-content-container">
					<ul class="vip-page__stats-grid">
						<?php foreach ( $trust_stats as $stat ) : ?>
							<?php
							$value = isset( $stat['stat_value'] ) ? (string) $stat['stat_value'] : '';
							$label = isset( $stat['stat_label'] ) ? (string) $stat['stat_label'] : '';
							if ( ! $value && ! $label ) {
								continue;
							}
							?>
							<li class="vip-page__stat">
								<?php if ( $value ) : ?>
									<p class="vip-page__stat-value"><?php echo esc_html( $value ); ?></p>
								<?php endif; ?>
								<?php if ( $label ) : ?>
									<p class="vip-page__stat-label"><?php echo esc_html( $label ); ?></p>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</section>
		<?php endif; ?>

		<?php if ( is_array( $content_blocks ) && $content_blocks ) : ?>
			<?php foreach ( $content_blocks as $block ) : ?>
				<?php
				$heading = isset( $block['heading'] ) ? (string) $block['heading'] : '';
				$content = isset( $block['content'] ) ? (string) $block['content'] : '';
				if ( ! $heading && ! $content ) {
					continue;
				}
				?>
				<section class="vip-border-section vip-border-section--plain-panel">
					<div class="vip-content-container vip-page__block-inner">
						<?php if ( $heading ) : ?>
							<h2 class="vip-page__section-title"><?php echo esc_html( $heading ); ?></h2>
						<?php endif; ?>
						<?php if ( $content ) : ?>
							<div class="vip-page__prose"><?php echo wp_kses_post( $content ); ?></div>
						<?php endif; ?>
					</div>
				</section>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php if ( $show_cta && ( $cta_heading || $wa_href || $fleet_url ) ) : ?>
			<section class="vip-bg-black-section vip-bg-black-section--cta">
				<div class="vip-bg-black-section__inner vip-content-container vip-page__cta-inner">
					<?php if ( $cta_heading ) : ?>
						<h2 class="vip-page__cta-title"><?php echo esc_html( $cta_heading ); ?></h2>
					<?php endif; ?>
					<?php if ( $cta_text ) : ?>
						<p class="vip-page__cta-text"><?php echo esc_html( $cta_text ); ?></p>
					<?php endif; ?>
					<div class="vip-page__cta-actions">
						<?php if ( $wa_href ) : ?>
							<a class="vip-page__btn vip-page__btn--primary" href="<?php echo esc_url( $wa_href ); ?>" target="_blank" rel="noopener noreferrer">
								<?php echo esc_html( $cta_label ); ?>
							</a>
						<?php endif; ?>
						<?php if ( $fleet_url ) : ?>
							<a class="vip-page__btn vip-page__btn--ghost" href="<?php echo esc_url( $fleet_url ); ?>">
								<?php esc_html_e( 'View our fleet', 'tenku-child' ); ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</section>
		<?php endif; ?>
	</div>
</article>
