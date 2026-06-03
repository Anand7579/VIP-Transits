<?php
/**
 * Occasions archive grid (/occasions/).
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$occasions = function_exists( 'vip_transits_get_published_occasions' )
	? vip_transits_get_published_occasions()
	: array();
?>
<section class="vip-page vip-occasions-archive" data-vip-section>
	<div class="vip-content-container">
		<header class="vip-occasions-archive__header">
			<p class="vip-landing-hero__breadcrumb">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'tenku-child' ); ?></a> /
				<span><?php esc_html_e( 'Occasions', 'tenku-child' ); ?></span>
			</p>
			<h1 class="vip-occasions-archive__title"><?php esc_html_e( 'Rent by Occasion', 'tenku-child' ); ?></h1>
			<p class="vip-occasions-archive__lead">
				<?php esc_html_e( 'Every occasion has its perfect car. We handle the sourcing, delivery, and logistics — you focus on the moment.', 'tenku-child' ); ?>
			</p>
			<hr class="vip-occasions-archive__rule" />
		</header>

		<?php if ( $occasions ) : ?>
			<ul class="vip-occasions-archive__grid">
				<?php foreach ( $occasions as $occasion ) : ?>
					<?php
					$occ_id   = (int) $occasion->ID;
					$permalink = get_permalink( $occ_id );
					$thumb     = get_the_post_thumbnail_url( $occ_id, 'large' );
					$excerpt   = has_excerpt( $occ_id ) ? get_the_excerpt( $occ_id ) : '';
					?>
					<li class="vip-occasions-archive__item">
						<article class="vip-occasions-archive__card">
							<a class="vip-occasions-archive__card-link" href="<?php echo esc_url( $permalink ); ?>">
								<?php if ( $thumb ) : ?>
									<figure class="vip-occasions-archive__media">
										<img
											src="<?php echo esc_url( $thumb ); ?>"
											alt="<?php echo esc_attr( get_the_title( $occ_id ) ); ?>"
											loading="lazy"
											decoding="async"
										/>
									</figure>
								<?php endif; ?>
								<div class="vip-occasions-archive__body">
									<h2 class="vip-occasions-archive__card-title"><?php echo esc_html( get_the_title( $occ_id ) ); ?></h2>
									<?php if ( $excerpt ) : ?>
										<p class="vip-occasions-archive__card-text"><?php echo esc_html( $excerpt ); ?></p>
									<?php endif; ?>
									<span class="vip-occasions-archive__card-cta">
										<?php esc_html_e( 'View cars', 'tenku-child' ); ?>
										<span aria-hidden="true">→</span>
									</span>
								</div>
							</a>
						</article>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<p class="vip-occasions-archive__empty"><?php esc_html_e( 'No occasions published yet. Add one under Occasions in the admin.', 'tenku-child' ); ?></p>
		<?php endif; ?>
	</div>
</section>
