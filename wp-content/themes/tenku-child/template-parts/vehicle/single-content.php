<?php
/**
 * Single vehicle detail page (Figma car detail).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$d = vip_transits_get_vehicle_single_data();
if ( empty( $d['id'] ) ) {
	return;
}

$home_url   = home_url( '/' );
$fleet_url  = get_post_type_archive_link( 'vip_vehicle' );
$price_fmt  = $d['daily_price'] ? number_format_i18n( (int) $d['daily_price'] ) : '';
$deposit_fmt = number_format_i18n( (int) $d['security_deposit'] );
$delivery_label = ! empty( $d['delivery'] )
	? __( 'Free - Anywhere Dubai', 'tenku-child' )
	: __( 'Ask on WhatsApp', 'tenku-child' );

$faq_items = $d['faq'];
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
<article <?php post_class( 'vip-vdetail' ); ?>>
	<div class="vip-vdetail__container vip-content-container">
		<nav class="vip-vdetail__breadcrumb" aria-label="<?php esc_attr_e( 'Breadcrumb', 'tenku-child' ); ?>">
			<a href="<?php echo esc_url( $home_url ); ?>"><?php esc_html_e( 'Home', 'tenku-child' ); ?></a>
			<span class="vip-vdetail__breadcrumb-sep" aria-hidden="true">/</span>
			<span class="vip-vdetail__breadcrumb-current"><?php esc_html_e( 'CAR DETAILS', 'tenku-child' ); ?></span>
		</nav>

		<header class="vip-vdetail__page-title">
			<h1 class="vip-vdetail__h1"><?php echo esc_html( $d['display_title'] ); ?></h1>
		</header>

		<div class="vip-vdetail__hero">
			<div class="vip-vdetail__hero-main">
				<div class="vip-vdetail__hero-media">
					<?php if ( $d['hero_image'] ) : ?>
						<img
							class="vip-vdetail__hero-img"
							src="<?php echo esc_url( $d['hero_image'] ); ?>"
							alt="<?php echo esc_attr( $d['short_name'] ); ?>"
							width="960"
							height="540"
							decoding="async"
						/>
					<?php else : ?>
						<span class="vip-vdetail__hero-placeholder" aria-hidden="true"></span>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $d['stats'] ) ) : ?>
					<ul class="vip-vdetail__stats">
						<?php foreach ( $d['stats'] as $stat ) : ?>
							<li class="vip-vdetail__stat">
								<span class="vip-vdetail__stat-value"><?php echo esc_html( $stat['value'] ); ?></span>
								<span class="vip-vdetail__stat-label"><?php echo esc_html( $stat['label'] ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<aside class="vip-vdetail__booking" aria-label="<?php esc_attr_e( 'Booking summary', 'tenku-child' ); ?>">
				<?php if ( $price_fmt ) : ?>
					<p class="vip-vdetail__booking-from">
						<span class="vip-vdetail__booking-from-label"><?php esc_html_e( 'From', 'tenku-child' ); ?></span>
						<span class="vip-vdetail__booking-from-price"><?php echo esc_html( sprintf( 'AED %s/day', $price_fmt ) ); ?></span>
					</p>
				<?php endif; ?>

				<table class="vip-vdetail__booking-table">
					<tbody>
						<tr>
							<th scope="row"><?php esc_html_e( 'Daily rate', 'tenku-child' ); ?></th>
							<td><?php echo $price_fmt ? esc_html( sprintf( 'AED %s', $price_fmt ) ) : '—'; ?></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Security deposit', 'tenku-child' ); ?></th>
							<td><?php echo esc_html( sprintf( 'AED %s (refundable)', $deposit_fmt ) ); ?></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Insurance', 'tenku-child' ); ?></th>
							<td><?php esc_html_e( 'Included', 'tenku-child' ); ?></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Delivery', 'tenku-child' ); ?></th>
							<td><?php echo esc_html( $delivery_label ); ?></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Weekly rate', 'tenku-child' ); ?></th>
							<td><?php echo esc_html( $d['weekly_rate'] ); ?></td>
						</tr>
					</tbody>
				</table>

				<?php if ( $d['wa_href_attr'] ) : ?>
					<div class="vip-vdetail__booking-actions">
						<a class="vip-vdetail__btn vip-vdetail__btn--primary" href="<?php echo $d['wa_href_attr']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Book in a Few Seconds', 'tenku-child' ); ?>
						</a>
						<a class="vip-vdetail__btn vip-vdetail__btn--wa" href="<?php echo $d['wa_href_attr']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" target="_blank" rel="noopener noreferrer">
							<?php esc_html_e( 'Chat on WhatsApp Now', 'tenku-child' ); ?>
						</a>
					</div>
				<?php endif; ?>

				<p class="vip-vdetail__booking-note">
					<?php
					echo esc_html(
						sprintf(
							/* translators: %s: deposit amount */
							__( 'Refundable deposit: AED %s', 'tenku-child' ),
							$deposit_fmt
						)
					);
					?>
				</p>
				<p class="vip-vdetail__booking-response"><?php esc_html_e( 'Response within 15 minutes · No form · Instant confirmation', 'tenku-child' ); ?></p>
			</aside>
		</div>

		<section class="vip-vdetail__intro">
			<h2 class="vip-vdetail__h2"><?php echo esc_html( $d['short_name'] ); ?></h2>
			<?php if ( $d['intro'] ) : ?>
				<p class="vip-vdetail__lead"><?php echo esc_html( $d['intro'] ); ?></p>
			<?php endif; ?>
		</section>

		<?php if ( ! empty( $d['included'] ) ) : ?>
			<section class="vip-vdetail__included">
				<h2 class="vip-vdetail__section-title vip-vdetail__section-title--rule-only"><?php echo esc_html( $included_heading ); ?></h2>
				<ul class="vip-vdetail__included-list">
					<?php foreach ( $d['included'] as $item ) : ?>
						<?php
						$inc_title = isset( $item['title'] ) ? trim( (string) $item['title'] ) : '';
						if ( ! $inc_title ) {
							continue;
						}
						?>
						<li class="vip-vdetail__included-item"><?php echo esc_html( $inc_title ); ?></li>
					<?php endforeach; ?>
				</ul>
			</section>
		<?php endif; ?>

		<?php if ( ! empty( $d['specs'] ) ) : ?>
			<section class="vip-vdetail__specs">
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

		<div class="vip-vdetail__mid<?php echo empty( $d['related'] ) ? ' vip-vdetail__mid--no-related' : ''; ?>">
			<?php if ( ! empty( $d['related'] ) ) : ?>
				<section class="vip-vdetail__related">
					<h2 class="vip-vdetail__section-title"><?php echo esc_html( $related_heading ); ?></h2>
					<ul class="vip-vdetail__related-list">
						<?php
						foreach ( $d['related'] as $related_post ) {
							$rel = vip_transits_get_vehicle_card_data( $related_post->ID );
							$rel_price = $rel['daily_price'] ? number_format_i18n( (int) $rel['daily_price'] ) : '';
							?>
							<li class="vip-vdetail__related-card">
								<a class="vip-vdetail__related-link" href="<?php echo esc_url( $rel['permalink'] ); ?>">
									<?php if ( $rel['thumbnail'] ) : ?>
										<img class="vip-vdetail__related-img" src="<?php echo esc_url( $rel['thumbnail'] ); ?>" alt="" loading="lazy" decoding="async" width="333" height="170" />
									<?php endif; ?>
									<span class="vip-vdetail__related-body">
										<span class="vip-vdetail__related-name"><?php echo esc_html( $rel['title'] ); ?></span>
										<?php if ( $rel_price ) : ?>
											<span class="vip-vdetail__related-price"><?php echo esc_html( sprintf( __( 'From AED %s / day', 'tenku-child' ), $rel_price ) ); ?></span>
										<?php endif; ?>
									</span>
								</a>
							</li>
							<?php
						}
						?>
					</ul>
				</section>
			<?php endif; ?>

			<aside class="vip-vdetail__requirements" aria-label="<?php esc_attr_e( 'Rental requirements', 'tenku-child' ); ?>">
				<ul class="vip-vdetail__requirements-list">
					<li><?php echo esc_html( sprintf( __( 'Minimum age %s', 'tenku-child' ), $d['minimum_age'] ) ); ?></li>
					<li><?php echo esc_html( sprintf( __( 'Refundable security deposit: AED %s', 'tenku-child' ), $deposit_fmt ) ); ?></li>
					<li><?php esc_html_e( 'Valid driving licence required', 'tenku-child' ); ?></li>
				</ul>
			</aside>
		</div>

		<?php if ( ! empty( $d['variants'] ) ) : ?>
			<section class="vip-vdetail__variants">
				<h2 class="vip-vdetail__section-title"><?php echo esc_html( $variants_heading ); ?></h2>
				<ul class="vip-vdetail__variants-grid">
					<?php foreach ( $d['variants'] as $variant ) : ?>
						<?php
						$vname = isset( $variant['name'] ) ? trim( (string) $variant['name'] ) : '';
						$vnote = isset( $variant['note'] ) ? trim( (string) $variant['note'] ) : '';
						if ( ! $vname ) {
							continue;
						}
						?>
						<li class="vip-vdetail__variant">
							<span class="vip-vdetail__variant-name"><?php echo esc_html( $vname ); ?></span>
							<?php if ( $vnote ) : ?>
								<span class="vip-vdetail__variant-note"><?php echo esc_html( $vnote ); ?></span>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</section>
		<?php endif; ?>

		<section class="vip-vdetail__steps">
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
						<?php
						echo esc_html(
							sprintf(
								/* translators: %s: vehicle short name */
								__( 'Book the %s via WhatsApp', 'tenku-child' ),
								$d['short_name']
							)
						);
						?>
					</a>
				<?php endif; ?>
				<?php if ( $d['tel_href'] ) : ?>
					<a class="vip-vdetail__cta vip-vdetail__cta--call" href="<?php echo esc_url( $d['tel_href'] ); ?>">
						<?php esc_html_e( 'CALL NOW', 'tenku-child' ); ?>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $d['routes'] ) ) : ?>
			<section class="vip-vdetail__routes">
				<h2 class="vip-vdetail__section-title"><?php echo esc_html( $routes_heading ); ?></h2>
				<ul class="vip-vdetail__routes-grid">
					<?php foreach ( $d['routes'] as $route ) : ?>
						<li class="vip-vdetail__route">
							<h3 class="vip-vdetail__route-title"><?php echo esc_html( $route['title'] ?? '' ); ?></h3>
							<?php if ( ! empty( $route['description'] ) ) : ?>
								<p class="vip-vdetail__route-text"><?php echo esc_html( $route['description'] ); ?></p>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</section>
		<?php endif; ?>

		<?php if ( $d['seo_content'] || get_the_content() ) : ?>
			<section class="vip-vdetail__seo">
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

		<?php if ( ! empty( $faq_items ) ) : ?>
			<section class="vip-vdetail__faq vip-vdetail__faq--panel">
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

		<?php if ( $fleet_url ) : ?>
			<p class="vip-vdetail__back">
				<a href="<?php echo esc_url( $fleet_url ); ?>">← <?php esc_html_e( 'Back to Fleets', 'tenku-child' ); ?></a>
			</p>
		<?php endif; ?>
	</div>
</article>
