<?php
/**
 * Occasion detail (hero, why rent + articles, fleet, FAQ).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = function_exists( 'vip_transits_occasion_post_id' ) ? vip_transits_occasion_post_id() : 0;
if ( $post_id <= 0 ) {
	return;
}

$text_image = function_exists( 'vip_transits_get_occasion_field' )
	? vip_transits_get_occasion_field( 'text_image_section', array() )
	: array();
$faq = function_exists( 'vip_transits_get_occasion_field' )
	? vip_transits_get_occasion_field( 'faq', array() )
	: array();
$fleet_cfg = function_exists( 'vip_transits_get_occasion_field' )
	? vip_transits_get_occasion_field( 'fleet_section', array() )
	: array();

$text_image_img_url = '';
if ( ! empty( $text_image['image'] ) ) {
	$text_image_img_url = function_exists( 'vip_transits_acf_image_url' )
		? vip_transits_acf_image_url( $text_image['image'], 'large' )
		: ( is_array( $text_image['image'] ) ? $text_image['image']['url'] : $text_image['image'] );
}

$faq_img_url = '';
if ( ! empty( $faq['image'] ) ) {
	$faq_img_url = function_exists( 'vip_transits_acf_image_url' )
		? vip_transits_acf_image_url( $faq['image'], 'large' )
		: ( is_array( $faq['image'] ) ? $faq['image']['url'] : $faq['image'] );
}

$fleet_heading = ! empty( $fleet_cfg['heading'] )
	? (string) $fleet_cfg['heading']
	: __( 'Cars for your occasion', 'tenku-child' );
$fleet_subtitle = ! empty( $fleet_cfg['subtitle'] )
	? (string) $fleet_cfg['subtitle']
	: __( 'Filter by role, type, brand, budget, and passengers.', 'tenku-child' );
$per_page       = ! empty( $fleet_cfg['posts_per_page'] ) ? max( 1, min( 24, (int) $fleet_cfg['posts_per_page'] ) ) : 12;
$show_load_more = isset( $fleet_cfg['show_load_more'] ) ? (bool) $fleet_cfg['show_load_more'] : true;

$query = new WP_Query( vip_transits_vehicle_query_args( array( 'posts_per_page' => $per_page ) ) );

$editorial_heading = function_exists( 'vip_transits_get_occasion_field' )
	? (string) vip_transits_get_occasion_field( 'editorial_heading', '' )
	: '';
$editorial_content = function_exists( 'vip_transits_get_occasion_field' )
	? (string) vip_transits_get_occasion_field( 'editorial_content', '' )
	: '';
$page_content      = (string) get_post_field( 'post_content', $post_id );
$has_why_rent      = $editorial_heading !== '' || $editorial_content !== '' || trim( $page_content ) !== '';

$related_ids = function_exists( 'vip_transits_get_occasion_related_article_ids' )
	? vip_transits_get_occasion_related_article_ids( $post_id )
	: array();
$related_heading = function_exists( 'vip_transits_get_occasion_field' )
	? (string) vip_transits_get_occasion_field( 'related_articles_heading', __( 'Related articles', 'tenku-child' ) )
	: __( 'Related articles', 'tenku-child' );

$hero_heading = ! empty( $text_image['heading'] )
	? (string) $text_image['heading']
	: get_the_title( $post_id );
$hero_img_url = $text_image_img_url ? $text_image_img_url : get_the_post_thumbnail_url( $post_id, 'large' );

$faq_heading = ! empty( $faq['heading'] ) ? (string) $faq['heading'] : '';
$faq_intro   = ! empty( $faq['top_content'] ) ? (string) $faq['top_content'] : '';
$faq_items   = array();
if ( ! empty( $faq['faqs'] ) && is_array( $faq['faqs'] ) ) {
	foreach ( $faq['faqs'] as $item ) {
		$faq_items[] = array(
			'question' => isset( $item['question'] ) ? (string) $item['question'] : '',
			'answer'   => isset( $item['answer'] ) ? (string) $item['answer'] : '',
		);
	}
}
$has_faq = $faq_items !== [] || $faq_img_url !== '';

$role_filter_label = function_exists( 'vip_transits_get_occasion_role_filter_label' )
	? vip_transits_get_occasion_role_filter_label( $post_id )
	: __( 'Car role for occasion', 'tenku-child' );
?>
<article class="vip-page vip-occasion-page" data-vip-section>

	<section class="vip-landing-hero">
		<div class="vip-landing-hero__grid vip-content-container">
			<div>
				<h1 class="vip-landing-hero__title"><?php echo esc_html( $hero_heading ); ?></h1>

				<?php if ( ! empty( $text_image['content'] ) ) : ?>
					<div class="vip-landing-hero__lead">
						<?php echo apply_filters( 'the_content', $text_image['content'] ); ?>
					</div>
				<?php elseif ( has_excerpt( $post_id ) ) : ?>
					<div class="vip-landing-hero__lead">
						<p><?php echo esc_html( get_the_excerpt( $post_id ) ); ?></p>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( $hero_img_url ) : ?>
				<figure class="vip-landing-hero__media">
					<img
						src="<?php echo esc_url( $hero_img_url ); ?>"
						alt="<?php echo esc_attr( $hero_heading ); ?>"
						loading="lazy"
						decoding="async"
					/>
				</figure>
			<?php endif; ?>
		</div>
	</section>

	<?php if ( $has_why_rent || $related_ids ) : ?>
		<section class="vip-occasion-page__body">
			<div class="vip-content-container">
				<div class="main_content_wrap">
				<?php if ( $has_why_rent ) : ?>
					<div class="left_wrap">
						<section class="vip-vdetail__intro" data-vip-section>
							<?php if ( $editorial_heading ) : ?>
								<h2 class="vip-vdetail__section-title"><?php echo esc_html( $editorial_heading ); ?></h2>
							<?php endif; ?>
							<?php if ( $editorial_content ) : ?>
								<div class="vip-vdetail__lead">
									<?php echo apply_filters( 'the_content', $editorial_content ); ?>
								</div>
							<?php elseif ( $page_content ) : ?>
								<div class="vip-vdetail__lead">
									<?php echo apply_filters( 'the_content', $page_content ); ?>
								</div>
							<?php endif; ?>
						</section>
					</div>
				<?php endif; ?>

				<?php if ( $related_ids ) : ?>
					<div class="right_wrap">
						<?php
						get_template_part(
							'template-parts/article/related',
							'vdetail-list',
							array(
								'post_ids' => $related_ids,
								'heading'  => $related_heading,
							)
						);
						?>
					</div>
				<?php endif; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<section id="vip-fleet" class="vip-fleet vip-fleet--occasion">
		<div class="vip-fleet__container vip-content-container">
			<header class="vip-fleet__header">
				<h2 class="vip-fleet__title"><?php echo esc_html( $fleet_heading ); ?></h2>
				<?php if ( $fleet_subtitle ) : ?>
					<p class="vip-fleet__subtitle"><?php echo esc_html( $fleet_subtitle ); ?></p>
				<?php endif; ?>
				<hr class="vip-fleet__rule" />
			</header>

			<?php
			if ( function_exists( 'vip_transits_render_fleet_grid' ) ) {
				vip_transits_render_fleet_grid(
					array(
						'query'             => $query,
						'per_page'          => $per_page,
						'show_load_more'    => $show_load_more,
						'show_filters'      => true,
						'filter_mode'       => 'occasion',
						'role_filter_label' => $role_filter_label,
					)
				);
			}
			?>
		</div>
	</section>

	<?php if ( $has_faq ) : ?>
		<?php
		get_template_part(
			'template-parts/shared/faq',
			'section',
			array(
				'heading'   => $faq_heading ?: __( 'Frequently Asked Questions', 'tenku-child' ),
				'intro'     => $faq_intro,
				'items'     => $faq_items,
				'image'     => ! empty( $faq['image'] ) ? $faq['image'] : '',
				'id_prefix' => 'vip-occasion-faq',
			)
		);
		?>
	<?php endif; ?>

</article>
