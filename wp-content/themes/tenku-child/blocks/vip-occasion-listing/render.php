<?php
/**
 * Block: vip-occasion-listing
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

// Fetch ACF Occasion Fields
$text_image = vip_transits_get_page_field( 'text_image_section', array() );
$faq        = vip_transits_get_page_field( 'faq', array() );

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

// Fetch Fleet Query
$per_page = 6;
$query    = new WP_Query( vip_transits_vehicle_query_args( array( 'posts_per_page' => $per_page ) ) );
?>

<style>
/* Bulletproof premium layout styles for VIP Occasion Listing Page */
.vip-occasion-page {
	--vip-font-serif: "Newsreader", "Times New Roman", Georgia, serif !important;
	--vip-font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;
	--vip-text-dark: #0a0c0f !important;
	--vip-text-muted: #5c5c5c !important;
	--vip-bg-light: #f7f7f7 !important;
	--vip-border-color: #e3e6ea !important;
	--vip-accent-color: #000000 !important;
	font-family: var(--vip-font-serif) !important;
	color: var(--vip-text-dark) !important;
}

/* Force FSE wrapper to span full width */
.wp-block-acf-vip-occasion-listing {
	width: 100% !important;
	max-width: none !important;
	margin: 0 !important;
	padding: 0 !important;
}

/* Hero Section */
.vip-landing-hero {
	padding: clamp(3.5rem, 7vw, 5.5rem) 0 !important;
	background: #ffffff !important;
	width: 100% !important;
	display: block !important;
	border-bottom: 1px solid var(--vip-border-color) !important;
}

.vip-landing-hero__grid {
	display: grid !important;
	grid-template-columns: 1.2fr 0.8fr !important;
	gap: clamp(2rem, 5vw, 4.5rem) !important;
	align-items: center !important;
	max-width: 1440px !important;
	margin: 0 auto !important;
	padding: 0 1.5rem !important;
	box-sizing: border-box !important;
}

.vip-landing-hero__breadcrumb {
	font-family: var(--vip-font-sans) !important;
	font-size: 0.75rem !important;
	font-weight: 600 !important;
	text-transform: uppercase !important;
	letter-spacing: 0.08em !important;
	color: var(--vip-text-muted) !important;
	margin-bottom: 1.25rem !important;
}

.vip-landing-hero__breadcrumb a {
	color: var(--vip-text-muted) !important;
	text-decoration: none !important;
	transition: color 0.2s ease !important;
}

.vip-landing-hero__breadcrumb a:hover {
	color: var(--vip-accent-color) !important;
}

.vip-landing-hero__title {
	font-size: clamp(2.25rem, 5.5vw, 3.5rem) !important;
	font-weight: 400 !important;
	line-height: 1.15 !important;
	letter-spacing: -0.02em !important;
	margin: 0 0 1.25rem !important;
	color: var(--vip-text-dark) !important;
}

.vip-landing-hero__lead {
	font-size: 1.0625rem !important;
	line-height: 1.65 !important;
	color: var(--vip-text-muted) !important;
	margin: 0 !important;
}

.vip-landing-hero__lead p {
	margin: 0 0 1.5rem !important;
	color: var(--vip-text-muted) !important;
}

.vip-landing-hero__lead p:last-child {
	margin-bottom: 0 !important;
}

.vip-landing-hero__media {
	margin: 0 !important;
	aspect-ratio: 4 / 3 !important;
	overflow: hidden !important;
	background: var(--vip-bg-light) !important;
}

.vip-landing-hero__media img {
	width: 100% !important;
	height: 100% !important;
	object-fit: cover !important;
	display: block !important;
}

/* Editorial & Articles Section */
.vip-landing-body {
	padding: clamp(4rem, 8vw, 6rem) 0 !important;
	background: #ffffff !important;
	border-bottom: 1px solid var(--vip-border-color) !important;
	width: 100% !important;
	display: block !important;
}

.vip-landing-body__grid {
	display: grid !important;
	grid-template-columns: 1.25fr 0.75fr !important;
	gap: clamp(2rem, 5vw, 5rem) !important;
	align-items: start !important;
	max-width: 1440px !important;
	margin: 0 auto !important;
	padding: 0 1.5rem !important;
	box-sizing: border-box !important;
}

.vip-page__prose {
	font-size: 1.0625rem !important;
	line-height: 1.65 !important;
}

.vip-page__prose h2 {
	font-size: clamp(1.75rem, 3.5vw, 2.25rem) !important;
	font-weight: 400 !important;
	letter-spacing: -0.02em !important;
	margin-top: 0 !important;
	margin-bottom: 1.25rem !important;
	color: var(--vip-text-dark) !important;
}

.vip-page__prose p {
	margin: 0 0 1.5rem !important;
	color: var(--vip-text-muted) !important;
}

.vip-page__prose ul {
	margin: 0 0 2rem !important;
	padding-left: 1.5rem !important;
	color: var(--vip-text-muted) !important;
}

.vip-page__prose li {
	margin-bottom: 0.75rem !important;
}

/* Sidebar Related Articles (Stacked exact matching) */
.vip-related-articles__title {
	font-family: var(--vip-font-sans) !important;
	font-size: 0.75rem !important;
	font-weight: 600 !important;
	text-transform: uppercase !important;
	letter-spacing: 0.08em !important;
	color: var(--vip-text-dark) !important;
	margin-bottom: 1.5rem !important;
	padding-bottom: 0.75rem !important;
	border-bottom: 2px solid var(--vip-text-dark) !important;
}

.vip-related-articles__list {
	display: flex !important;
	flex-direction: column !important;
	gap: 2rem !important;
}

.vip-related-article-card {
	display: flex !important;
	flex-direction: column !important;
	gap: 0.5rem !important;
	margin-bottom: 0.5rem !important;
}

.vip-related-article-card__media {
	aspect-ratio: 16 / 10 !important;
	overflow: hidden !important;
	margin: 0 !important;
	display: block !important;
}

.vip-related-article-card__img {
	width: 100% !important;
	height: 100% !important;
	object-fit: cover !important;
	display: block !important;
	transition: transform 0.3s ease !important;
}

.vip-related-article-card:hover .vip-related-article-card__img {
	transform: scale(1.05) !important;
}

.vip-related-article-card__date {
	font-family: var(--vip-font-sans) !important;
	font-size: 0.6875rem !important;
	font-weight: 600 !important;
	color: var(--vip-text-muted) !important;
	text-transform: uppercase !important;
	letter-spacing: 0.04em !important;
	margin: 0 !important;
	display: block !important;
}

.vip-related-article-card__title {
	font-size: 1rem !important;
	font-weight: 400 !important;
	line-height: 1.35 !important;
	margin: 0 !important;
	letter-spacing: -0.01em !important;
}

.vip-related-article-card__title a {
	color: var(--vip-text-dark) !important;
	text-decoration: none !important;
	transition: opacity 0.2s ease !important;
}

.vip-related-article-card__title a:hover {
	opacity: 0.75 !important;
}

/* FAQ layout styling */
.vip-faq {
	padding: clamp(4rem, 8vw, 6rem) 0 !important;
	background: var(--vip-bg-light) !important;
	border-top: 1px solid var(--vip-border-color) !important;
	width: 100% !important;
	display: block !important;
}

.vip-faq__layout {
	display: grid !important;
	grid-template-columns: 1.2fr 0.8fr !important;
	gap: clamp(2rem, 5vw, 4rem) !important;
	align-items: start !important;
	max-width: 1440px !important;
	margin: 0 auto !important;
	padding: 0 1.5rem !important;
	box-sizing: border-box !important;
}

.vip-faq__top-content h2 {
	font-size: clamp(1.75rem, 3.5vw, 2.25rem) !important;
	font-weight: 400 !important;
	letter-spacing: -0.02em !important;
	margin-top: 0 !important;
	margin-bottom: 0.75rem !important;
	color: var(--vip-text-dark) !important;
}

.vip-faq__top-content p {
	font-size: 1.0625rem !important;
	color: var(--vip-text-muted) !important;
	margin: 0 0 2rem !important;
}

.vip-faq__items {
	display: flex !important;
	flex-direction: column !important;
	gap: 1.5rem !important;
}

.vip-faq__item {
	padding-bottom: 1.5rem !important;
	border-bottom: 1px solid var(--vip-border-color) !important;
}

.vip-faq__item:last-child {
	border-bottom: none !important;
}

.vip-faq__q {
	font-size: 1.125rem !important;
	font-weight: 600 !important;
	margin: 0 0 0.5rem !important;
	font-family: var(--vip-font-sans) !important;
	color: var(--vip-text-dark) !important;
}

.vip-faq__a {
	font-size: 1rem !important;
	line-height: 1.55 !important;
	color: var(--vip-text-muted) !important;
}

.vip-faq__visual {
	aspect-ratio: 4 / 5 !important;
	overflow: hidden !important;
	background: #ffffff !important;
}

.vip-faq__visual img {
	width: 100% !important;
	height: 100% !important;
	object-fit: cover !important;
	display: block !important;
}

/* Cars Listing & Sidebar Filter (Exact screenshot styling) */
.vip-occasion-page .vip-fleet {
	padding: clamp(3.5rem, 7vw, 5.5rem) 0 !important;
	background: #ffffff !important;
	width: 100% !important;
	display: block !important;
}

.vip-occasion-page .vip-fleet__container {
	max-width: 1440px !important;
	margin: 0 auto !important;
	padding: 0 1.5rem !important;
	box-sizing: border-box !important;
}

.vip-occasion-page .vip-fleet__header {
	margin-bottom: 3rem !important;
}

.vip-occasion-page .vip-fleet__title {
	font-size: clamp(1.75rem, 3.5vw, 2.25rem) !important;
	font-weight: 400 !important;
	letter-spacing: -0.02em !important;
	margin: 0 0 0.5rem !important;
	color: var(--vip-text-dark) !important;
}

.vip-occasion-page .vip-fleet__subtitle {
	font-size: 1.0625rem !important;
	color: var(--vip-text-muted) !important;
	margin: 0 0 1.5rem !important;
}

.vip-occasion-page .vip-fleet__rule {
	margin: 0 !important;
	border: 0 !important;
	border-top: 1px solid var(--vip-text-dark) !important;
}

/* Sidebar Filters custom premium styling */
.vip-fleet__filters {
	background: #ffffff !important;
	border: 1px solid var(--vip-border-color) !important;
	padding: 1.75rem !important;
	box-sizing: border-box !important;
	display: flex !important;
	flex-direction: column !important;
	gap: 1.75rem !important;
}

.vip-fleet__filters-head {
	display: flex !important;
	justify-content: space-between !important;
	align-items: center !important;
	border-bottom: 1px solid var(--vip-border-color) !important;
	padding-bottom: 1rem !important;
	margin-bottom: 0.5rem !important;
}

.vip-fleet__filters-title {
	font-family: var(--vip-font-sans) !important;
	font-size: 0.875rem !important;
	font-weight: 600 !important;
	text-transform: uppercase !important;
	letter-spacing: 0.08em !important;
	color: var(--vip-text-dark) !important;
	margin: 0 !important;
}

.vip-fleet__filter-reset {
	font-family: var(--vip-font-sans) !important;
	font-size: 0.75rem !important;
	color: var(--vip-text-muted) !important;
	background: none !important;
	border: none !important;
	cursor: pointer !important;
	text-transform: uppercase !important;
	letter-spacing: 0.04em !important;
	padding: 0 !important;
	transition: color 0.2s ease !important;
}

.vip-fleet__filter-reset:hover {
	color: var(--vip-accent-color) !important;
}

.vip-fleet__filter-group {
	border-bottom: 1px solid var(--vip-border-color) !important;
	padding-bottom: 1.5rem !important;
}

.vip-fleet__filter-group--last {
	border-bottom: none !important;
	padding-bottom: 0 !important;
}

.vip-fleet__filter-label {
	font-family: var(--vip-font-sans) !important;
	font-size: 0.75rem !important;
	font-weight: 600 !important;
	text-transform: uppercase !important;
	letter-spacing: 0.08em !important;
	color: var(--vip-text-dark) !important;
	margin: 0 0 1rem 0 !important;
}

.vip-fleet__filter-list {
	list-style: none !important;
	margin: 0 !important;
	padding: 0 !important;
	display: flex !important;
	flex-direction: column !important;
	gap: 0.65rem !important;
}

.vip-fleet__check {
	display: flex !important;
	align-items: center !important;
	gap: 0.65rem !important;
	font-family: var(--vip-font-sans) !important;
	font-size: 0.8125rem !important;
	color: var(--vip-text-muted) !important;
	cursor: pointer !important;
	user-select: none !important;
}

.vip-fleet__check input[type="checkbox"] {
	position: absolute !important;
	opacity: 0 !important;
	cursor: pointer !important;
	height: 0 !important;
	width: 0 !important;
}

.vip-fleet__check-box {
	width: 14px !important;
	height: 14px !important;
	border: 1px solid #7a7a7a !important;
	background-color: #ffffff !important;
	display: inline-block !important;
	position: relative !important;
	box-sizing: border-box !important;
}

.vip-fleet__check input[type="checkbox"]:checked ~ .vip-fleet__check-box {
	background-color: #000000 !important;
	border-color: #000000 !important;
}

.vip-fleet__check input[type="checkbox"]:checked ~ .vip-fleet__check-box::after {
	content: "" !important;
	position: absolute !important;
	left: 4px !important;
	top: 1px !important;
	width: 3px !important;
	height: 6px !important;
	border: solid #ffffff !important;
	border-width: 0 2px 2px 0 !important;
	transform: rotate(45deg) !important;
}

.vip-fleet__check-text {
	line-height: 1 !important;
}

/* Range Slider */
.vip-fleet__range {
	padding-top: 0.5rem !important;
}

.vip-fleet__range-track {
	height: 2px !important;
	background: var(--vip-border-color) !important;
	position: relative !important;
	margin-bottom: 1rem !important;
}

.vip-fleet__range-fill {
	height: 100% !important;
	background: #000000 !important;
	position: absolute !important;
}

.vip-fleet__range-input {
	position: absolute !important;
	width: 100% !important;
	height: 2px !important;
	background: none !important;
	pointer-events: none !important;
	-webkit-appearance: none !important;
	appearance: none !important;
	margin: 0 !important;
	top: 0 !important;
}

.vip-fleet__range-input::-webkit-slider-thumb {
	height: 14px !important;
	width: 14px !important;
	border-radius: 50% !important;
	background: #000000 !important;
	pointer-events: auto !important;
	-webkit-appearance: none !important;
	cursor: pointer !important;
}

.vip-fleet__range-labels {
	display: flex !important;
	justify-content: space-between !important;
	font-family: var(--vip-font-sans) !important;
	font-size: 0.75rem !important;
	color: var(--vip-text-muted) !important;
}

/* Responsive adjustments */
@media (max-width: 900px) {
	.vip-landing-hero__grid,
	.vip-landing-body__grid,
	.vip-faq__layout {
		grid-template-columns: 1fr !important;
		gap: 2.5rem !important;
	}
	
	.vip-landing-hero__media {
		order: -1 !important;
	}
	
	.vip-landing-body__aside-col {
		margin-top: 1.5rem !important;
	}
	
	.vip-faq__visual {
		max-height: 400px !important;
		aspect-ratio: 16 / 9 !important;
	}
}

/* Layout grid for Filters (left) and Cars (right) */
.vip-occasion-page .vip-fleet__layout {
	display: grid !important;
	grid-template-columns: 280px 1fr !important;
	gap: clamp(2rem, 4vw, 3.5rem) !important;
	align-items: start !important;
}

.vip-occasion-page .vip-fleet__grid {
	display: grid !important;
	grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
	gap: 2rem !important;
}

@media (max-width: 900px) {
	.vip-occasion-page .vip-fleet__layout {
		grid-template-columns: 1fr !important;
		gap: 2rem !important;
	}
	
	.vip-occasion-page .vip-fleet__grid {
		grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
	}
}

@media (max-width: 600px) {
	.vip-occasion-page .vip-fleet__grid {
		grid-template-columns: 1fr !important;
	}
}
</style>

<article class="vip-page vip-occasion-page" data-vip-section>
	
	<!-- 1. Hero / Banner Section (Text Image Section ACF values used for dynamic Hero) -->
	<section class="vip-landing-hero">
		<div class="vip-landing-hero__grid">
			<div>
				<!-- Breadcrumbs aligned exactly as in Figma screenshot -->
				<p class="vip-landing-hero__breadcrumb">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'tenku-child' ); ?></a> / 
					<a href="<?php echo esc_url( home_url( '/occasions/' ) ); ?>"><?php esc_html_e( 'Occasions', 'tenku-child' ); ?></a> / 
					<span><?php echo esc_html( get_the_title( $post_id ) ); ?></span>
				</p>
				
				<!-- Lead content pulled dynamically from ACF 'Text Image Section' content! -->
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
			
			<!-- Image pulled dynamically from ACF 'Text Image Section' image! -->
			<?php 
			$hero_img_url = ! empty( $text_image_img_url ) ? $text_image_img_url : get_the_post_thumbnail_url( $post_id, 'large' );
			if ( ! empty( $hero_img_url ) ) :
			?>
				<figure class="vip-landing-hero__media">
					<img 
						src="<?php echo esc_url( $hero_img_url ); ?>" 
						alt="<?php echo esc_attr( get_the_title( $post_id ) ); ?>" 
						loading="lazy"
						decoding="async"
					/>
				</figure>
			<?php endif; ?>
		</div>
	</section>

	<!-- 2. Editorial Section (Default Post Content on Left + Related Articles Stack on Right) -->
	<?php 
	$default_content = get_post_field( 'post_content', $post_id );
	if ( ! empty( $default_content ) ) : 
	?>
		<section class="vip-landing-body">
			<div class="vip-landing-body__grid">
				
				<!-- Left Column: Standard/Default Page Content -->
				<div class="vip-page__prose">
					<?php echo apply_filters( 'the_content', $default_content ); ?>
				</div>
				
				<!-- Right Column: Recent Articles stacked list (exactly matching layout) -->
				<div class="vip-landing-body__aside-col">
					<?php 
					// Query 2 recent blog articles
					$related_posts = get_posts( array(
						'post_type'      => 'post',
						'posts_per_page' => 2,
						'post_status'    => 'publish',
					) );
					
					if ( ! empty( $related_posts ) ) :
					?>
						<aside class="vip-related-articles">
							<h3 class="vip-related-articles__title"><?php esc_html_e( 'Related articles', 'tenku-child' ); ?></h3>
							<div class="vip-related-articles__list">
								<?php 
								foreach ( $related_posts as $p ) : 
									$p_id = $p->ID;
									$p_thumb = get_the_post_thumbnail_url( $p_id, 'full' );
									$p_title = get_the_title( $p_id );
									$p_link = get_permalink( $p_id );
									$p_date = get_the_date( 'd M Y', $p_id );
									?>
									<article class="vip-related-article-card">
										<?php if ( $p_thumb ) : ?>
											<a class="vip-related-article-card__media" href="<?php echo esc_url( $p_link ); ?>">
												<img 
													class="vip-related-article-card__img" 
													src="<?php echo esc_url( $p_thumb ); ?>" 
													alt="<?php echo esc_attr( $p_title ); ?>" 
													loading="lazy"
													decoding="async"
												/>
											</a>
										<?php endif; ?>
										<time class="vip-related-article-card__date"><?php echo esc_html( $p_date ); ?></time>
										<h4 class="vip-related-article-card__title">
											<a href="<?php echo esc_url( $p_link ); ?>"><?php echo esc_html( $p_title ); ?></a>
										</h4>
									</article>
								<?php 
								endforeach;
								?>
							</div>
						</aside>
					<?php endif; ?>
				</div>
				
			</div>
		</section>
	<?php endif; ?>

	<!-- 3. Fleet Grid Section (Cars Grid + Styled Filters) -->
	<section id="vip-fleet" class="vip-fleet">
		<div class="vip-fleet__container">
			<header class="vip-fleet__header">
				<h2 class="vip-fleet__title"><?php esc_html_e( 'Cars for your occasion', 'tenku-child' ); ?></h2>
				<p class="vip-fleet__subtitle"><?php esc_html_e( 'Filter by role, type, brand, budget, and passengers.', 'tenku-child' ); ?></p>
				<hr class="vip-fleet__rule" />
			</header>

			<?php
			if ( function_exists( 'vip_transits_render_fleet_grid' ) ) {
				vip_transits_render_fleet_grid(
					array(
						'query'          => $query,
						'per_page'       => $per_page,
						'show_load_more' => true,
						'show_filters'   => true,
						'simplified'     => true,
					)
				);
			}
			?>
		</div>
	</section>

	<!-- 4. FAQ Section (Accordion FAQs on Left + Side Image on Right) -->
	<?php if ( ! empty( $faq['top_content'] ) || ! empty( $faq['faqs'] ) ) : ?>
		<section class="vip-faq">
			<div class="vip-faq__layout">
				
				<!-- Left Column: FAQ dynamic list -->
				<div>
					<?php if ( ! empty( $faq['top_content'] ) ) : ?>
						<div class="vip-faq__top-content">
							<?php echo wp_kses_post( $faq['top_content'] ); ?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $faq['faqs'] ) && is_array( $faq['faqs'] ) ) : ?>
						<div class="vip-faq__items">
							<?php foreach ( $faq['faqs'] as $item ) : ?>
								<?php
								$q = isset( $item['question'] ) ? trim( (string) $item['question'] ) : '';
								$a = isset( $item['answer'] ) ? trim( (string) $item['answer'] ) : '';
								if ( $q === '' || $a === '' ) {
									continue;
								}
								?>
								<div class="vip-faq__item">
									<p class="vip-faq__q"><?php echo esc_html( $q ); ?></p>
									<div class="vip-faq__a"><?php echo wp_kses_post( $a ); ?></div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>

				<!-- Right Column: Visual image -->
				<?php if ( ! empty( $faq_img_url ) ) : ?>
					<div class="vip-faq__visual" aria-hidden="true">
						<img 
							src="<?php echo esc_url( $faq_img_url ); ?>" 
							alt="<?php esc_attr_e( 'FAQ visual', 'tenku-child' ); ?>" 
							loading="lazy"
							decoding="async"
						/>
					</div>
				<?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

</article>
