<?php
/**
 * Block: vip-vehicle-single
 * Consolidates all vehicle details template parts into a single render file
 * with a unified two-column layout (left_wrap & right_wrap) for top sections,
 * and full-width layouts for lower sections (Variants, Steps, Routes, SEO, FAQ).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_singular( 'vip_vehicle' ) ) {
	if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		echo '<p class="vip-vehicle-single vip-vehicle-single--placeholder">' . esc_html__( 'Vehicle detail (preview on single vehicle pages).', 'tenku-child' ) . '</p>';
	}
	return;
}

// -------------------------------------------------------------
// 1. Data Initialization
// -------------------------------------------------------------
$d = vip_transits_get_vehicle_single_data();
if ( empty( $d['id'] ) ) {
	return;
}

$fleet_url   = get_post_type_archive_link( 'vip_vehicle' );
$price_fmt   = $d['daily_price'] ? number_format_i18n( (int) $d['daily_price'] ) : '';
$deposit_fmt = number_format_i18n( (int) $d['security_deposit'] );
$faq_items   = $d['faq'];

if ( empty( $faq_items ) ) {
	$faq_items = array(
		array(
			'question' => sprintf(
				/* translators: %s: vehicle short name */
				__( 'How much does %s rental cost in Dubai?', 'tenku-child' ),
				$d['short_name']
			),
			'answer'   => $d['daily_price']
				? sprintf(
					/* translators: 1: vehicle name, 2: price, 3: deposit */
					__( 'The %1$s starts from AED %2$s per day. A refundable security deposit of AED %3$s is held at delivery. Insurance is included. Weekly rates are available at a reduced daily rate.', 'tenku-child' ),
					$d['short_name'],
					$price_fmt,
					$deposit_fmt
				)
				: '',
		),
		array(
			'question' => sprintf(
				__( 'What %s variants are available?', 'tenku-child' ),
				$d['short_name']
			),
			'answer'   => __( 'Message us on WhatsApp with your preferred variant and dates — we will confirm availability within 15 minutes.', 'tenku-child' ),
		),
		array(
			'question' => sprintf(
				/* translators: %s: vehicle short name */
				__( 'Is the %s suitable for airport pickup in Dubai?', 'tenku-child' ),
				$d['short_name']
			),
			'answer'   => __( 'Yes. We meet you at DXB arrivals with your car ready. Share your flight details on WhatsApp and we handle timing and delivery.', 'tenku-child' ),
		),
		array(
			'question' => sprintf(
				__( 'Is the %s automatic?', 'tenku-child' ),
				$d['short_name']
			),
			'answer'   => ! empty( $d['transmission'] ) ? $d['transmission'] : __( 'Yes — automatic transmission is standard. Confirm your variant on WhatsApp.', 'tenku-child' ),
		),
	);
}

$faq_heading = sprintf(
	/* translators: %s: vehicle short name */
	__( '%s Rental Dubai — FAQ', 'tenku-child' ),
	$d['short_name']
);

$variants_heading = sprintf(
	/* translators: %s: vehicle short name */
	__( '%s Variants in Dubai', 'tenku-child' ),
	$d['short_name']
);

$related_heading = $d['brand_name']
	? sprintf(
		/* translators: %s: brand name */
		__( 'Also Available from %s', 'tenku-child' ),
		$d['brand_name']
	)
	: __( 'Also Available', 'tenku-child' );

$book_heading = sprintf(
	/* translators: %s: vehicle short name */
	__( 'How to Book the %s via WhatsApp', 'tenku-child' ),
	$d['short_name']
);

$routes_heading = sprintf(
	/* translators: %s: vehicle short name */
	__( 'Best Routes for a %s in Dubai', 'tenku-child' ),
	$d['short_name']
);

$included_heading = sprintf(
	/* translators: %s: vehicle short name */
	__( "What's Included With Your %s Rental", 'tenku-child' ),
	$d['short_name']
);

$specs_heading = sprintf(
	/* translators: %s: vehicle short name */
	__( '%s Specifications', 'tenku-child' ),
	$d['short_name']
);

$seo_heading = sprintf(
	/* translators: %s: vehicle short name */
	__( 'What is %s rental in Dubai?', 'tenku-child' ),
	$d['short_name']
);
?>
<style>
	.vip-vdetail__spec-table thead th {
		background: #000000;
		color: #fff;
		padding: 20px 15px 15px 40px !important;
	}
	.vip-vdetail__spec-table tbody {
		background: #F3F3F3;
	}
	.vip-vdetail__spec-table tbody th {
		padding: 20px 15px 15px 40px !important;
	}

	.vip-vdetail__related-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 15px; }
	.vip-vdetail__related-link { display: flex; align-items: center; background: #F3F3F3; padding: 15px; text-decoration: none; color: #000; gap: 15px; transition: opacity 0.2s; }
	.vip-vdetail__related-link:hover { opacity: 0.8; }
	.vip-vdetail__related-img { width: 120px; height: 80px; object-fit: cover; flex-shrink: 0; }
	.vip-vdetail__related-body { display: flex; flex-direction: column; flex-grow: 1; }
	.vip-vdetail__related-name { font-weight: 600; font-size: 16px; margin-bottom: 5px; }
	.vip-vdetail__related-price { font-size: 13px; color: #666; }
	.vip-vdetail__related-arrow { font-size: 20px; color: #000; margin-left: auto; padding-left: 10px; }

	.vip-vdetail__variants-grid {
		display: grid;
		grid-template-columns: repeat(5, 245px);
		gap: 20px;
		padding-bottom: 20px;
		list-style: none;
		margin: 25px 0 0 0;
		overflow-x: auto;
		scrollbar-width: none;
	}
	.vip-vdetail__variants-grid::-webkit-scrollbar {
		display: none;
	}
	.vip-vdetail__variant-media {
		position: relative;
		overflow: hidden;
		width: 245px;
		height: 245px;
	}
	.vip-vdetail__variant-media img {
		width: 100% !important;
		height: 100% !important;
		object-fit: cover;
		display: block;
	}

	.vip-vdetail__cta-bar {
		display: flex;
		justify-content: center;
		gap: 20px;
		margin-top: 40px;
	}
	.vip-vdetail__cta {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		padding: 5px 30px;
		font-weight: 600;
		font-size: 14px;
		color: #fff !important;
		text-decoration: none;
		transition: opacity 0.3s;
	}
	.vip-vdetail__cta:hover {
		opacity: 0.9;
	}
	.vip-vdetail__cta--wa {
		background-color: #25D366;
	}
	.vip-vdetail__cta--call {
		background: linear-gradient(90deg, #17A2B8, #117A8B);
	}
	.vip-vdetail__variant-overlay {
		position: absolute;
		bottom: 0;
		left: 0;
		right: 0;
		padding: 30px 15px 15px;
		background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 100%);
		color: #fff;
		display: flex;
		flex-direction: column;
	}
	.vip-vdetail__variant-name {
		font-weight: 600;
		font-size: 15px;
	}
	.vip-vdetail__variant-note {
		font-size: 13px;
		opacity: 0.9;
	}
</style>
<article <?php post_class( 'vip-vdetail' ); ?>>

	<?php
	// -------------------------------------------------------------
	// 2. Masthead (Full width across the top)
	// -------------------------------------------------------------
	$home_url = home_url( '/' );
	?>
	<header class="vip-vdetail__masthead">
		<div class="vip-vdetail__masthead-inner vip-content-container">
			<div class="vip-vdetail__masthead-left">
				<h1 class="vip-vdetail__masthead-title"><?php echo esc_html( $d['display_title'] ); ?></h1>

				<?php if ( $price_fmt ) : ?>
					<div class="vip-vdetail__masthead-price">
						<span class="vip-vdetail__masthead-from"><?php esc_html_e( 'From', 'tenku-child' ); ?></span>
						<div class="vip-vdetail__masthead-rate">
							<span class="vip-vdetail__masthead-amount"><?php echo esc_html( sprintf( 'AED %s', $price_fmt ) ); ?></span><span class="vip-vdetail__masthead-unit">/day</span>
						</div>
					</div>
				<?php endif; ?>

				<p class="vip-vdetail__masthead-deposit">
					<?php esc_html_e( 'Refundable deposit:', 'tenku-child' ); ?>
					<span class="vip-vdetail__masthead-deposit-amount"> <?php echo esc_html( sprintf( 'AED %s', $deposit_fmt ) ); ?></span>
				</p>
			</div>

			<div class="vip-vdetail__masthead-right">
				<nav class="vip-vdetail__masthead-breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'tenku-child' ); ?>">
					<a href="<?php echo esc_url( $home_url ); ?>"><?php esc_html_e( 'Home', 'tenku-child' ); ?></a>
					<span class="vip-vdetail__masthead-breadcrumb-sep" aria-hidden="true">
						<?php
						if ( function_exists( 'vip_transits_theme_icon_img' ) ) {
							echo vip_transits_theme_icon_img(
								'breadcrumb-arrow',
								array(
									'class'  => 'vip-vdetail__masthead-breadcrumb-arrow',
									'width'  => 12,
									'height' => 12,
								)
							); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						} else {
							echo '→';
						}
						?>
					</span>
					<span class="vip-vdetail__masthead-breadcrumb-current"><?php esc_html_e( 'CAR DETAILS', 'tenku-child' ); ?></span>
				</nav>

				<?php if ( ! empty( $d['wa_href_attr'] ) || ! empty( $d['tel_href'] ) ) : ?>
					<div class="vip-vdetail__masthead-actions">
						<?php if ( ! empty( $d['wa_href_attr'] ) ) : ?>
							<a class="vip-vdetail__masthead-wa" href="<?php echo $d['wa_href_attr']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" target="_blank" rel="noopener noreferrer">
								<span class="vip-vdetail__masthead-wa-inner">
									<span class="vip-vdetail__masthead-wa-icon" aria-hidden="true">
										<svg width="22" height="22" viewBox="0 0 24 24" focusable="false"><path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.882 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
									</span>
									<span class="vip-vdetail__masthead-wa-label"><?php esc_html_e( 'Book in a few minutes – Chat on WhatsApp now', 'tenku-child' ); ?></span>
								</span>
							</a>
						<?php endif; ?>
						<?php if ( ! empty( $d['tel_href'] ) ) : ?>
							<a class="vip-vdetail__masthead-call" href="<?php echo esc_url( $d['tel_href'] ); ?>" aria-label="<?php esc_attr_e( 'Call now', 'tenku-child' ); ?>">
								<svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 4h3l2 5-2.5 1.5a11 11 0 005 5L13 13l5 2v3a2 2 0 01-2 2A15 15 0 013 6a2 2 0 012-2z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/></svg>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</header>

	<!-- 3. Two-Column Structural Wrap (White Area) -->
	<div class="vip-vdetail__container vip-content-container">
		
		<div class="main_content_wrap">
			
			<!-- ========================================== -->
			<!-- LEFT COLUMN (LEFT WRAP)                    -->
			<!-- ========================================== -->
			<div class="left_wrap">
				
				<!-- A. Gallery -->
				<?php
				$gallery = ! empty( $d['gallery'] ) && is_array( $d['gallery'] ) ? $d['gallery'] : array();
				if ( empty( $gallery ) && ! empty( $d['hero_image'] ) ) {
					$gallery[] = array(
						'id'    => (int) get_post_thumbnail_id( $d['id'] ),
						'url'   => $d['hero_image'],
						'thumb' => $d['hero_image'],
						'alt'   => $d['short_name'],
					);
				}
				$main_image = ! empty( $gallery[0] ) ? $gallery[0] : null;
				$has_slider = count( $gallery ) > 3;
				?>
				<?php if ( $main_image ) : ?>
					<div class="vip-vdetail__gallery" data-vip-gallery>
						<div class="vip-vdetail__gallery-main">
							<img
								class="vip-vdetail__gallery-main-img"
								data-vip-gallery-main
								src="<?php echo esc_url( $main_image['url'] ); ?>"
								alt="<?php echo esc_attr( $main_image['alt'] ); ?>"
								width="960"
								height="540"
								decoding="async"
							/>
						</div>

						<?php if ( count( $gallery ) > 1 ) : ?>
							<div class="vip-vdetail__gallery-thumbs-bar">
								<button
									type="button"
									class="vip-vdetail__gallery-nav vip-vdetail__gallery-nav--prev"
									data-vip-gallery-prev
									aria-label="<?php esc_attr_e( 'Previous images', 'tenku-child' ); ?>"
									<?php echo $has_slider ? '' : ' hidden'; ?>
								>
									<span aria-hidden="true">‹</span>
								</button>

								<div class="vip-vdetail__gallery-thumbs-viewport" data-vip-gallery-viewport>
									<div class="vip-vdetail__gallery-thumbs-track" data-vip-gallery-track>
										<?php foreach ( $gallery as $index => $image ) : ?>
											<button
												type="button"
												class="vip-vdetail__gallery-thumb<?php echo 0 === (int) $index ? ' is-active' : ''; ?>"
												data-vip-gallery-thumb
												data-index="<?php echo esc_attr( (string) $index ); ?>"
												data-full="<?php echo esc_url( $image['url'] ); ?>"
												aria-label="<?php echo esc_attr( sprintf( __( 'Show image %d', 'tenku-child' ), (int) $index + 1 ) ); ?>"
												aria-pressed="<?php echo 0 === (int) $index ? 'true' : 'false'; ?>"
											>
												<img
													src="<?php echo esc_url( $image['thumb'] ); ?>"
													alt=""
													width="280"
													height="160"
													loading="lazy"
													decoding="async"
												/>
											</button>
										<?php endforeach; ?>
									</div>
								</div>

								<button
									type="button"
									class="vip-vdetail__gallery-nav vip-vdetail__gallery-nav--next"
									data-vip-gallery-next
									aria-label="<?php esc_attr_e( 'Next images', 'tenku-child' ); ?>"
									<?php echo $has_slider ? '' : ' hidden'; ?>
								>
									<span aria-hidden="true">›</span>
								</button>
							</div>
						<?php endif; ?>
					</div>
				<?php else : ?>
					<span class="vip-vdetail__gallery-placeholder" aria-hidden="true"></span>
				<?php endif; ?>

				<!-- B. Intro -->
				<section class="vip-vdetail__intro" data-vip-section>
					<h2 class="vip-vdetail__h2"><?php echo esc_html( $d['short_name'] ); ?></h2>
					<?php if ( $d['intro'] ) : ?>
						<p class="vip-vdetail__lead"><?php echo esc_html( $d['intro'] ); ?></p>
					<?php endif; ?>
				</section>

				<!-- C. Stats (styled as premium black horizontal bar) -->
				<?php if ( ! empty( $d['stats'] ) ) : ?>
					<ul class="vip-vdetail__stats">
						<?php foreach ( $d['stats'] as $stat ) : ?>
							<li class="vip-vdetail__stat">
								<span class="vip-vdetail__stat-label"><?php echo esc_html( $stat['label'] ); ?></span>
								<span class="vip-vdetail__stat-value"><?php echo esc_html( $stat['value'] ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<!-- D. Specifications Table -->
				<?php if ( ! empty( $d['specs'] ) ) : ?>
					<section class="vip-vdetail__specs" data-vip-section>
						<h2 class="vip-vdetail__section-title"><?php echo esc_html( $specs_heading ); ?></h2>
						<table class="vip-vdetail__spec-table">
							<thead>
								<tr>
									<th scope="col"><?php esc_html_e( 'Specification', 'tenku-child' ); ?></th>
									<th scope="col"><?php esc_html_e( 'Value', 'tenku-child' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $d['specs'] as $row ) : ?>
									<tr>
										<th scope="row"><?php echo esc_html( $row['label'] ); ?></th>
										<td><?php echo esc_html( $row['value'] ); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</section>
				<?php endif; ?>

			</div>

			<!-- ========================================== -->
			<!-- RIGHT COLUMN (RIGHT WRAP)                  -->
			<!-- ========================================== -->
			<div class="right_wrap">
				
				<!-- A. Pricing Card -->
				<?php
				$delivery_label = ! empty( $d['delivery'] )
					? __( 'Free - Anywhere Dubai', 'tenku-child' )
					: __( 'Ask on WhatsApp', 'tenku-child' );
				?>
				<aside class="vip-vdetail__pricing-card" aria-label="<?php esc_attr_e( 'Booking summary', 'tenku-child' ); ?>">
					<div class="vip-vdetail__pricing-head">
						<div class="vip-vdetail__pricing-head-left">
							<span class="vip-vdetail__pricing-from"><?php esc_html_e( 'From', 'tenku-child' ); ?></span>
							<?php if ( $price_fmt ) : ?>
								<p class="vip-vdetail__pricing-rate">
									<span class="vip-vdetail__pricing-amount"><?php echo esc_html( sprintf( 'AED %s', $price_fmt ) ); ?></span><span class="vip-vdetail__pricing-unit">/day</span>
								</p>
							<?php endif; ?>
						</div>
						<p class="vip-vdetail__pricing-deposit-hd">
							<?php esc_html_e( 'Deposit:', 'tenku-child' ); ?>
							<span><?php echo esc_html( sprintf( 'AED %s', $deposit_fmt ) ); ?></span>
						</p>
					</div>

					<div class="vip-vdetail__pricing-body">
						<dl class="vip-vdetail__pricing-rows">
							<div class="vip-vdetail__pricing-row">
								<dt><?php esc_html_e( 'Daily rate', 'tenku-child' ); ?></dt>
								<dd><?php echo $price_fmt ? esc_html( sprintf( 'AED %s', $price_fmt ) ) : '—'; ?></dd>
							</div>
							<div class="vip-vdetail__pricing-row">
								<dt><?php esc_html_e( 'Security deposit', 'tenku-child' ); ?></dt>
								<dd><?php echo esc_html( sprintf( 'AED %s (refundable)', $deposit_fmt ) ); ?></dd>
							</div>
							<div class="vip-vdetail__pricing-row">
								<dt><?php esc_html_e( 'Insurance', 'tenku-child' ); ?></dt>
								<dd><?php esc_html_e( 'Included', 'tenku-child' ); ?></dd>
							</div>
							<div class="vip-vdetail__pricing-row">
								<dt><?php esc_html_e( 'Delivery', 'tenku-child' ); ?></dt>
								<dd><?php echo esc_html( $delivery_label ); ?></dd>
							</div>
							<div class="vip-vdetail__pricing-row">
								<dt><?php esc_html_e( 'Weekly rate', 'tenku-child' ); ?></dt>
								<dd><?php echo esc_html( $d['weekly_rate'] ); ?></dd>
							</div>
						</dl>
					</div>

					<div class="vip-vdetail__pricing-foot">
						<?php if ( ! empty( $d['wa_href_attr'] ) || ! empty( $d['tel_href'] ) ) : ?>
							<div class="vip-vdetail__pricing-actions">
								<?php if ( ! empty( $d['wa_href_attr'] ) ) : ?>
									<a class="vip-vdetail__pricing-wa" href="<?php echo $d['wa_href_attr']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" target="_blank" rel="noopener noreferrer">
										<span class="vip-vdetail__pricing-wa-icon" aria-hidden="true">
											<svg width="22" height="22" viewBox="0 0 24 24" focusable="false"><path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.882 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
										</span>
										<span class="vip-vdetail__pricing-wa-text">
											<span class="vip-vdetail__pricing-wa-kicker"><?php esc_html_e( 'Book in a few minutes', 'tenku-child' ); ?></span>
											<span class="vip-vdetail__pricing-wa-label"><?php esc_html_e( 'Chat on WhatsApp now', 'tenku-child' ); ?></span>
										</span>
									</a>
								<?php endif; ?>
								<?php if ( ! empty( $d['tel_href'] ) ) : ?>
									<a class="vip-vdetail__pricing-call" href="<?php echo esc_url( $d['tel_href'] ); ?>" aria-label="<?php esc_attr_e( 'Call now', 'tenku-child' ); ?>">
										<svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 4h3l2 5-2.5 1.5a11 11 0 005 5L13 13l5 2v3a2 2 0 01-2 2A15 15 0 013 6a2 2 0 012-2z" stroke="currentColor" stroke-width="1.75" stroke-linejoin="round"/></svg>
									</a>
								<?php endif; ?>
							</div>
						<?php endif; ?>
						<p class="vip-vdetail__pricing-note"><?php esc_html_e( 'Response within 15 minutes · No form · Instant confirmation', 'tenku-child' ); ?></p>
					</div>
				</aside>

				<!-- B. What's Included Card Grid (Figma style) -->
				<section class="vip-vdetail__included" data-vip-section>
					<h2 class="vip-vdetail__section-title vip-vdetail__section-title--rule-only"><?php echo esc_html( $included_heading ); ?></h2>
					<div class="vip-included__grid">
						
						<!-- Item 1: Insurance -->
						<div class="vip-included__item">
							<div class="vip-included__icon">
								<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
							</div>
							<div class="vip-included__text"><?php esc_html_e( 'Full comprehensive insurance', 'tenku-child' ); ?></div>
						</div>

						<!-- Item 2: Free Delivery -->
						<div class="vip-included__item">
							<div class="vip-included__icon">
								<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="2"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
							</div>
							<div class="vip-included__text"><?php esc_html_e( 'Free delivery to hotel/home', 'tenku-child' ); ?></div>
						</div>

						<!-- Item 3: Min Age -->
						<div class="vip-included__item">
							<div class="vip-included__icon">
								<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
							</div>
							<div class="vip-included__text"><?php echo esc_html( sprintf( __( 'Minimum age %s', 'tenku-child' ), $d['minimum_age'] ) ); ?></div>
						</div>

						<!-- Item 4: Driving Licence -->
						<div class="vip-included__item">
							<div class="vip-included__icon">
								<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><line x1="7" y1="8" x2="17" y2="8"/><line x1="7" y1="12" x2="17" y2="12"/><line x1="7" y1="16" x2="13" y2="16"/></svg>
							</div>
							<div class="vip-included__text"><?php esc_html_e( 'Valid driving licence required', 'tenku-child' ); ?></div>
						</div>

						<!-- Item 5: Security Deposit -->
						<div class="vip-included__item">
							<div class="vip-included__icon">
								<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><line x1="12" y1="4" x2="12" y2="20"/><line x1="2" y1="12" x2="22" y2="12"/></svg>
							</div>
							<div class="vip-included__text"><?php echo esc_html( sprintf( __( 'Refundable security deposit: AED %s', 'tenku-child' ), $deposit_fmt ) ); ?></div>
						</div>

					</div>
				</section>

				<!-- C. Related Vehicles -->
				<?php if ( ! empty( $d['related'] ) ) : ?>
					<section class="vip-vdetail__related" data-vip-section>
						<h2 class="vip-vdetail__section-title vip-vdetail__section-title--rule-only"><?php echo esc_html( $related_heading ); ?></h2>
						<ul class="vip-vdetail__related-list">
							<?php
							foreach ( $d['related'] as $related_post ) {
								$rel       = vip_transits_get_vehicle_card_data( $related_post->ID );
								$rel_price = $rel['daily_price'] ? number_format_i18n( (int) $rel['daily_price'] ) : '';
								?>
								<li class="vip-vdetail__related-card">
									<a class="vip-vdetail__related-link" href="<?php echo esc_url( $rel['permalink'] ); ?>">
										<?php if ( $rel['thumbnail'] ) : ?>
											<img class="vip-vdetail__related-img" src="<?php echo esc_url( $rel['thumbnail'] ); ?>" alt="" loading="lazy" decoding="async" width="120" height="80" />
										<?php endif; ?>
										<span class="vip-vdetail__related-body">
											<span class="vip-vdetail__related-name"><?php echo esc_html( $rel['title'] ); ?></span>
											<?php if ( $rel_price ) : ?>
												<span class="vip-vdetail__related-price"><?php echo esc_html( sprintf( __( 'From AED %s / day', 'tenku-child' ), $rel_price ) ); ?></span>
											<?php endif; ?>
										</span>
										<span class="vip-vdetail__related-arrow" aria-hidden="true">→</span>
									</a>
								</li>
								<?php
							}
							?>
						</ul>
					</section>
				<?php endif; ?>

			</div>

		</div>

		<!-- 4. Variants Grid (Full Width, below the columns) -->
		<?php if ( ! empty( $d['variants'] ) ) : ?>
			<section class="vip-vdetail__variants" data-vip-section>
				<h2 class="vip-vdetail__section-title"><?php echo esc_html( $variants_heading ); ?></h2>
				<ul class="vip-vdetail__variants-grid">
					<?php foreach ( $d['variants'] as $variant ) : ?>
						<?php
						$vname = isset( $variant['name'] ) ? trim( (string) $variant['name'] ) : '';
						$vnote = isset( $variant['note'] ) ? trim( (string) $variant['note'] ) : '';
						if ( ! $vname ) {
							continue;
						}
						// If we have an image field for the variant (like in FSE screenshot, let's fall back to default vehicle card image if not specified)
						$vimage = isset( $variant['image'] ) ? $variant['image'] : $d['hero_image'];
						?>
						<li class="vip-vdetail__variant-card">
							<div class="vip-vdetail__variant-media">
								<img src="<?php echo esc_url( $vimage ); ?>" alt="<?php echo esc_attr( $vname ); ?>" loading="lazy" width="220" height="140" />
								<div class="vip-vdetail__variant-overlay">
									<span class="vip-vdetail__variant-name"><?php echo esc_html( $vname ); ?></span>
									<?php if ( $vnote ) : ?>
										<span class="vip-vdetail__variant-note"><?php echo esc_html( $vnote ); ?></span>
									<?php endif; ?>
								</div>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			</section>
		<?php endif; ?>

	</div> <!-- Close container for steps layout breakout -->

	<!-- 5. How to Book Steps (Full Width, Black Background breakout!) -->
	<div class="cta_section">
		<div class="vip-content-container">
			<section class="vip-vdetail__steps" data-vip-section>
				<h2 class="vip-vdetail__section-title"><?php echo esc_html( $book_heading ); ?></h2>
				<ol class="vip-vdetail__steps-list">
					<?php foreach ( $d['booking_steps'] as $i => $step ) : ?>
						<li class="vip-vdetail__step">
							<span class="vip-vdetail__step-num"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
							<div class="vip-vdetail__step-body">
								<h3 class="vip-vdetail__step-title"><?php echo esc_html( $step['title'] ); ?></h3>
								<p class="vip-vdetail__step-text"><?php echo esc_html( $step['text'] ); ?></p>
							</div>
						</li>
					<?php endforeach; ?>
				</ol>
			</section>

			<?php if ( $d['wa_href_attr'] || $d['tel_href'] ) : ?>
				<div class="vip-vdetail__cta-bar">
					<?php if ( $d['wa_href_attr'] ) : ?>
						<a class="vip-vdetail__cta vip-vdetail__cta--wa" href="<?php echo $d['wa_href_attr']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" target="_blank" rel="noopener noreferrer">
							<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:8px"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
							<?php
							echo esc_html(
								strtoupper( sprintf(
									/* translators: %s: vehicle short name */
									__( 'Book the %s via WhatsApp', 'tenku-child' ),
									$d['short_name']
								) )
							);
							?>
						</a>
					<?php endif; ?>
					<?php if ( $d['tel_href'] ) : ?>
						<a class="vip-vdetail__cta vip-vdetail__cta--call" href="<?php echo esc_url( $d['tel_href'] ); ?>">
							<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:8px"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
							<?php esc_html_e( 'CALL NOW', 'tenku-child' ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<!-- Reopen Container for remaining full width blocks -->
	<div class="vip-vdetail__container vip-content-container">
		
		<!-- 6. Best Routes -->
		<?php if ( ! empty( $d['routes'] ) ) : ?>
			<section class="vip-vdetail__routes" data-vip-section>
				<h2 class="vip-vdetail__section-title"><?php echo esc_html( $routes_heading ); ?></h2>
				<ul class="vip-vdetail__routes-grid">
					<?php foreach ( $d['routes'] as $route ) : ?>
						<?php
						// Fetch matching routing card thumbnails
						$route_img = $d['hero_image']; // Fallback
						if ( stripos( $route['title'] ?? '', 'Zayed' ) !== false ) {
							$route_img = get_stylesheet_directory_uri() . '/assets/images/sheikh-zayed-road.jpg';
						} elseif ( stripos( $route['title'] ?? '', 'Palm' ) !== false ) {
							$route_img = get_stylesheet_directory_uri() . '/assets/images/palm-jumeirah.jpg';
						} elseif ( stripos( $route['title'] ?? '', 'Marina' ) !== false ) {
							$route_img = get_stylesheet_directory_uri() . '/assets/images/dubai-marina.jpg';
						}
						?>
						<li class="vip-vdetail__route-card">
							<div class="vip-vdetail__route-media">
								<img src="<?php echo esc_url( $route_img ); ?>" alt="<?php echo esc_attr( $route['title'] ?? '' ); ?>" loading="lazy" width="380" height="200" />
								<div class="vip-vdetail__route-overlay">
									<h3 class="vip-vdetail__route-title"><?php echo esc_html( $route['title'] ?? '' ); ?></h3>
									<?php if ( ! empty( $route['description'] ) ) : ?>
										<p class="vip-vdetail__route-text"><?php echo esc_html( $route['description'] ); ?></p>
									<?php endif; ?>
								</div>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			</section>
		<?php endif; ?>

		<!-- 7. SEO narrative -->
		<?php if ( $d['seo_content'] || get_the_content() ) : ?>
			<section class="vip-vdetail__seo" data-vip-section>
				<h2 class="vip-vdetail__section-title"><?php echo esc_html( $seo_heading ); ?></h2>
				<div class="vip-vdetail__seo-body">
					<?php
					if ( $d['seo_content'] ) {
						echo wp_kses_post( $d['seo_content'] );
					} else {
						the_content();
					}
					?>
				</div>
			</section>
		<?php endif; ?>

		<!-- 8. FAQ Section (Full Width, two columns grid) -->
		<?php if ( ! empty( $faq_items ) ) : ?>
			<section class="vip-vdetail__faq" data-vip-section>
				<h2 class="vip-vdetail__faq-title"><?php echo esc_html( $faq_heading ); ?></h2>
				<div class="vip-vdetail__faq-accordion" data-vip-faq-accordion>
					<?php
					$faq_i = 0;
					foreach ( $faq_items as $faq_row ) :
						$question = isset( $faq_row['question'] ) ? trim( (string) $faq_row['question'] ) : '';
						$answer   = isset( $faq_row['answer'] ) ? (string) $faq_row['answer'] : '';
						if ( ! $question ) {
							continue;
						}
						$is_open   = 0 === $faq_i;
						$item_id   = 'vip-vdetail-faq-' . (int) $faq_i;
						$panel_id  = $item_id . '-panel';
						$button_id = $item_id . '-btn';
						++$faq_i;
						?>
						<div class="vip-faq__item<?php echo $is_open ? ' is-open' : ''; ?>" data-vip-faq-item>
							<button
								type="button"
								class="vip-faq__question"
								id="<?php echo esc_attr( $button_id ); ?>"
								data-vip-faq-trigger
								aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>"
								aria-controls="<?php echo esc_attr( $panel_id ); ?>"
							>
								<span class="vip-faq__question-text"><?php echo esc_html( $question ); ?></span>
								<span class="vip-faq__chevron" aria-hidden="true"></span>
							</button>
							<?php if ( $answer ) : ?>
								<div
									class="vip-faq__answer"
									id="<?php echo esc_attr( $panel_id ); ?>"
									role="region"
									aria-labelledby="<?php echo esc_attr( $button_id ); ?>"
									<?php echo $is_open ? '' : ' inert'; ?>
								>
									<div class="vip-faq__answer-inner">
										<?php echo wp_kses_post( $answer ); ?>
									</div>
								</div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>


	</div>
</article>
