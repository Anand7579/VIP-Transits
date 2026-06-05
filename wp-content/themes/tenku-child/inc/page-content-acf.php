<?php
/**
 * ACF: About Us & Contact Us page templates.
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Classic editor page template labels (block theme still shows these in Page attributes).
 *
 * @param array $templates Templates.
 * @return array
 */
function vip_transits_register_page_templates( $templates ) {
	$templates['templates/page-about.html']         = __( 'VIP About Us', 'tenku-child' );
	$templates['templates/page-contact.html']       = __( 'VIP Contact Us', 'tenku-child' );
	$templates['templates/page-contact-clean.html'] = __( 'VIP Contact Us (Clean)', 'tenku-child' );
	$templates['templates/page-occasion.html'] = __( 'VIP Occasion detail', 'tenku-child' );

	return $templates;
}
add_filter( 'theme_page_templates', 'vip_transits_register_page_templates' );

/**
 * Template slugs WordPress may store for VIP About / Contact pages (classic + block theme).
 *
 * @return array<string, array<int, string>>
 */
function vip_transits_page_template_slug_groups() {
	return array(
		'about'   => array(
			'page-about',
			'templates/page-about.html',
			'templates/page-about',
		),
		'contact' => array(
			'page-contact',
			'page-contact-clean',
			'page-contact.html',
			'page-contact-clean.html',
			'templates/page-contact.html',
			'templates/page-contact-clean.html',
			'templates/page-contact',
			'templates/page-contact-clean',
		),
		'occasion' => array(
			'page-occasion',
			'templates/page-occasion.html',
			'templates/page-occasion',
		),
	);
}

/**
 * @param string $kind about|contact|occasion.
 * @return string[]
 */
function vip_transits_page_template_slugs_for( $kind ) {
	$groups = vip_transits_page_template_slug_groups();
	return isset( $groups[ $kind ] ) ? $groups[ $kind ] : array();
}

/**
 * Whether a page uses a VIP About or Contact template (any stored slug) or contains the matching block.
 *
 * @param int    $post_id Page ID.
 * @param string $kind    about|contact|occasion.
 * @return bool
 */
function vip_transits_page_uses_vip_template( $post_id, $kind ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return false;
	}

	$slug = (string) get_page_template_slug( $post_id );
	if ( $slug !== '' && in_array( $slug, vip_transits_page_template_slugs_for( $kind ), true ) ) {
		return true;
	}

	if ( ! function_exists( 'has_block' ) ) {
		return false;
	}

	$blocks = array(
		'about'    => 'acf/vip-page-about',
		'contact'  => 'acf/vip-page-contact',
		'occasion' => 'acf/vip-occasion-listing',
	);

	if ( ! isset( $blocks[ $kind ] ) ) {
		return false;
	}

	return has_block( $blocks[ $kind ], $post_id );
}

/**
 * Whether the current request is the VIP Contact page.
 *
 * @param int $post_id Optional page ID.
 * @return bool
 */
function vip_transits_is_contact_page( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_queried_object_id();
	if ( $post_id <= 0 || ! is_page( $post_id ) ) {
		return false;
	}

	return vip_transits_page_uses_vip_template( $post_id, 'contact' );
}

/**
 * ACF location: match page template across block-theme and classic slug formats.
 *
 * @param bool  $match  Whether the rule matched.
 * @param array $rule   Location rule.
 * @param array $screen Screen args.
 * @return bool
 */
function vip_transits_acf_match_page_template( $match, $rule, $screen ) {
	if ( 'page_template' !== $rule['param'] || '==' !== $rule['operator'] ) {
		return $match;
	}

	if ( empty( $screen['post_id'] ) ) {
		return $match;
	}

	$wanted = (string) $rule['value'];
	$actual = (string) get_page_template_slug( (int) $screen['post_id'] );

	foreach ( vip_transits_page_template_slug_groups() as $aliases ) {
		if ( in_array( $wanted, $aliases, true ) && in_array( $actual, $aliases, true ) ) {
			return true;
		}
	}

	// Template meta empty but VIP block is in page content (e.g. default template + block only).
	if ( ! $match && $actual === '' && function_exists( 'has_block' ) ) {
		$post_id = (int) $screen['post_id'];
		if ( in_array( $wanted, vip_transits_page_template_slugs_for( 'contact' ), true ) && has_block( 'acf/vip-page-contact', $post_id ) ) {
			return true;
		}
		if ( in_array( $wanted, vip_transits_page_template_slugs_for( 'about' ), true ) && has_block( 'acf/vip-page-about', $post_id ) ) {
			return true;
		}
		if ( in_array( $wanted, vip_transits_page_template_slugs_for( 'occasion' ), true ) && has_block( 'acf/vip-occasion-listing', $post_id ) ) {
			return true;
		}
	}

	return $match;
}
add_filter( 'acf/location/rule_match/page_template', 'vip_transits_acf_match_page_template', 10, 3 );

/**
 * @return int
 */
function vip_transits_page_content_post_id() {
	if ( is_singular( 'vip_occasion' ) ) {
		return 0;
	}

	$post_id = (int) get_queried_object_id();
	return ( is_page() && $post_id > 0 ) ? $post_id : 0;
}

/**
 * @param string $field ACF field name.
 * @param mixed  $default Default.
 * @return mixed
 */
function vip_transits_get_page_field( $field, $default = null ) {
	$post_id = vip_transits_page_content_post_id();
	if ( ! $post_id || ! function_exists( 'get_field' ) ) {
		return $default;
	}

	$value = get_field( $field, $post_id );
	return null !== $value && '' !== $value && array() !== $value ? $value : $default;
}

/**
 * Flexible About layout slug → template part suffix.
 *
 * @return array<string, string>
 */
function vip_transits_about_section_layout_templates() {
	return array(
		'hero_intro'          => 'hero-intro',
		'definition_callout'  => 'definition-callout',
		'key_takeaways'       => 'key-takeaways',
		'our_story'           => 'our-story',
		'fleet_table'         => 'fleet-table',
		'why_choose'          => 'why-choose',
		'cta'                 => 'cta',
		'faq'                 => 'faq',
		'content'             => 'content',
		// Legacy flexible layouts (pre–content-brief).
		'intro_media'         => 'intro-media',
		'trust_stats'         => 'trust-stats',
		'prose_center'        => 'prose-center',
	);
}

/**
 * Whether the About page flexible content includes a FAQ layout.
 *
 * @param int $post_id Page ID.
 * @return bool
 */
function vip_transits_about_page_has_faq_section( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || ! function_exists( 'get_field' ) ) {
		return false;
	}

	$sections = get_field( 'about_sections', $post_id );
	if ( ! is_array( $sections ) || ! $sections ) {
		return false;
	}

	foreach ( $sections as $row ) {
		if ( is_array( $row ) && isset( $row['acf_fc_layout'] ) && 'faq' === $row['acf_fc_layout'] ) {
			return true;
		}
	}

	return false;
}

/**
 * FAQPage schema entities from About flexible FAQ rows.
 *
 * @param int $post_id Page ID.
 * @return array<int, array<string, mixed>>
 */
function vip_transits_about_faq_schema_entities( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || ! function_exists( 'get_field' ) ) {
		return array();
	}

	$sections = get_field( 'about_sections', $post_id );
	if ( ! is_array( $sections ) ) {
		return array();
	}

	$entities = array();

	foreach ( $sections as $row ) {
		if ( ! is_array( $row ) || empty( $row['acf_fc_layout'] ) || 'faq' !== $row['acf_fc_layout'] ) {
			continue;
		}

		$faqs = isset( $row['faqs'] ) && is_array( $row['faqs'] ) ? $row['faqs'] : array();
		foreach ( $faqs as $faq_row ) {
			if ( ! is_array( $faq_row ) ) {
				continue;
			}
			$question = isset( $faq_row['question'] ) ? trim( (string) $faq_row['question'] ) : '';
			$answer   = isset( $faq_row['answer'] ) ? trim( wp_strip_all_tags( (string) $faq_row['answer'] ) ) : '';
			if ( $question === '' || $answer === '' ) {
				continue;
			}
			$entities[] = array(
				'@type'          => 'Question',
				'name'           => $question,
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $answer,
				),
			);
		}
	}

	return $entities;
}

/**
 * Output FAQPage JSON-LD on the About template when FAQ section exists.
 */
function vip_transits_output_about_faq_schema() {
	if ( is_admin() || ! is_page() ) {
		return;
	}

	$post_id = vip_transits_page_content_post_id();
	if ( ! $post_id || ! vip_transits_page_uses_vip_template( $post_id, 'about' ) ) {
		return;
	}

	$entities = vip_transits_about_faq_schema_entities( $post_id );
	if ( ! $entities ) {
		return;
	}

	$schema = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => $entities,
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'vip_transits_output_about_faq_schema', 20 );

/**
 * Whether legacy About fields (pre–flexible content) have any data.
 *
 * @param int $post_id Page ID.
 * @return bool
 */
function vip_transits_about_has_legacy_sections( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return false;
	}

	$intro_heading = trim( (string) get_field( 'about_intro_heading', $post_id ) );
	$intro_content = trim( (string) get_field( 'about_intro_content', $post_id ) );
	$intro_image   = get_field( 'about_intro_image', $post_id );
	$trust_stats   = get_field( 'about_trust_stats', $post_id );
	$blocks        = get_field( 'about_content_blocks', $post_id );
	$show_cta      = (bool) get_field( 'about_show_cta', $post_id );

	if ( $intro_heading || $intro_content || ( is_array( $intro_image ) && ! empty( $intro_image['url'] ) ) ) {
		return true;
	}

	if ( is_array( $trust_stats ) && $trust_stats ) {
		return true;
	}

	if ( is_array( $blocks ) && $blocks ) {
		return true;
	}

	if ( $show_cta ) {
		return true;
	}

	return false;
}

/**
 * Render legacy About sections (fixed field order) when flexible content is empty.
 *
 * @param int $post_id Page ID.
 */
function vip_transits_render_about_legacy_sections( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return;
	}

	$intro_heading = (string) get_field( 'about_intro_heading', $post_id );
	$intro_content = (string) get_field( 'about_intro_content', $post_id );
	$intro_image   = get_field( 'about_intro_image', $post_id );

	if ( $intro_heading || $intro_content || ( is_array( $intro_image ) && ! empty( $intro_image['url'] ) ) ) {
		?>
		<section class="vip-border-section vip-border-section--surface vip-border-section--plain-panel vip-about-section vip-about-section--intro-media">
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
		<?php
	}

	$trust_stats = get_field( 'about_trust_stats', $post_id );
	if ( is_array( $trust_stats ) && $trust_stats ) {
		?>
		<section class="vip-border-section vip-border-section--surface vip-border-section--plain-panel vip-about-section vip-about-section--trust-stats" aria-label="<?php esc_attr_e( 'Company highlights', 'tenku-child' ); ?>">
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
		<?php
	}

	$content_blocks = get_field( 'about_content_blocks', $post_id );
	if ( is_array( $content_blocks ) && $content_blocks ) {
		foreach ( $content_blocks as $block ) {
			$heading = isset( $block['heading'] ) ? (string) $block['heading'] : '';
			$content = isset( $block['content'] ) ? (string) $block['content'] : '';
			if ( ! $heading && ! $content ) {
				continue;
			}
			?>
			<section class="vip-border-section vip-border-section--plain-panel vip-about-section vip-about-section--content">
				<div class="vip-content-container vip-page__block-inner">
					<?php if ( $heading ) : ?>
						<h2 class="vip-page__section-title"><?php echo esc_html( $heading ); ?></h2>
					<?php endif; ?>
					<?php if ( $content ) : ?>
						<div class="vip-page__prose"><?php echo wp_kses_post( $content ); ?></div>
					<?php endif; ?>
				</div>
			</section>
			<?php
		}
	}

	if ( (bool) get_field( 'about_show_cta', $post_id ) ) {
		$cta_heading = (string) get_field( 'about_cta_heading', $post_id );
		$cta_text    = (string) get_field( 'about_cta_text', $post_id );
		$cta_label   = (string) get_field( 'about_cta_button_label', $post_id );
		$fleet_url   = get_post_type_archive_link( 'vip_vehicle' );
		$wa_href     = function_exists( 'vip_transits_whatsapp_href_attr' ) ? vip_transits_whatsapp_href_attr() : '';
		$cta_label   = $cta_label ? $cta_label : __( 'Speak to concierge', 'tenku-child' );

		if ( $cta_heading || $cta_text || $wa_href || $fleet_url ) {
			?>
			<section class="vip-bg-black-section vip-bg-black-section--cta vip-about-section vip-about-section--cta">
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
			<?php
		}
	}
}

/**
 * Render About page body sections (ACF flexible content).
 *
 * @param int  $post_id Page ID.
 * @param bool $preview Show editor hint when empty.
 * @return bool True if any section output was rendered.
 */
function vip_transits_render_about_sections( $post_id, $preview = false ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 || ! function_exists( 'have_rows' ) ) {
		return false;
	}

	$rendered = false;
	$layouts  = vip_transits_about_section_layout_templates();

	if ( have_rows( 'about_sections', $post_id ) ) {
		while ( have_rows( 'about_sections', $post_id ) ) {
			the_row();
			$layout = get_row_layout();
			if ( ! isset( $layouts[ $layout ] ) ) {
				continue;
			}
			get_template_part( 'template-parts/about/section', $layouts[ $layout ] );
			$rendered = true;
		}
		return $rendered;
	}

	if ( vip_transits_about_has_legacy_sections( $post_id ) ) {
		vip_transits_render_about_legacy_sections( $post_id );
		return true;
	}

	if ( $preview ) {
		echo '<p class="vip-page vip-page--about-empty">';
		echo esc_html__( 'Add About sections under Page sections → Sections (Hero intro, Definition callout, Key takeaways, Our story, Fleet table, Why choose us, CTA, FAQ). See docs/ABOUT-PAGE.md.', 'tenku-child' );
		echo '</p>';
	}

	return false;
}

/**
 * Whether the page is the static front page (no banner).
 *
 * @param int $post_id Page ID.
 * @return bool
 */
function vip_transits_is_home_page_id( $post_id ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return false;
	}

	if ( (int) get_option( 'page_on_front' ) === $post_id ) {
		return true;
	}

	if ( function_exists( 'vip_transits_home_page_id' ) && $post_id === (int) vip_transits_home_page_id() ) {
		return true;
	}

	return false;
}

/**
 * Whether the Page Banner ACF group should output on this page.
 *
 * @param int $post_id Optional page ID.
 * @return bool
 */
function vip_transits_page_show_banner( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : vip_transits_page_content_post_id();
	if ( $post_id <= 0 || ! is_page( $post_id ) ) {
		return false;
	}

	if ( vip_transits_is_home_page_id( $post_id ) ) {
		return false;
	}

	$show = (string) vip_transits_get_page_field( 'show_banner', 'No' );
	return in_array( strtolower( $show ), array( 'yes', 'y', '1' ), true );
}

/**
 * Banner description (WYSIWYG), with legacy About/Contact masthead fallbacks.
 *
 * @param int $post_id Optional page ID.
 * @return string
 */
function vip_transits_get_page_banner_description( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : vip_transits_page_content_post_id();
	if ( $post_id <= 0 || ! function_exists( 'get_field' ) ) {
		return '';
	}

	$description = trim( (string) get_field( 'description', $post_id ) );
	if ( $description !== '' ) {
		return $description;
	}

	if ( vip_transits_page_uses_vip_template( $post_id, 'about' ) ) {
		return trim( (string) get_field( 'about_masthead_lead', $post_id ) );
	}

	if ( vip_transits_page_uses_vip_template( $post_id, 'contact' ) ) {
		return trim( (string) get_field( 'contact_masthead_lead', $post_id ) );
	}

	return '';
}

/**
 * Whether the page renders body content via a VIP ACF block (banner is output there).
 *
 * @param int $post_id Page ID.
 * @return bool
 */
function vip_transits_page_uses_vip_content_block( $post_id = 0 ) {
	$post_id = (int) ( $post_id ? $post_id : get_queried_object_id() );
	if ( $post_id <= 0 || ! function_exists( 'has_block' ) ) {
		return false;
	}

	$blocks = array(
		'acf/vip-page-about',
		'acf/vip-page-contact',
		'acf/vip-occasion-listing',
	);

	foreach ( $blocks as $block_name ) {
		if ( has_block( $block_name, $post_id ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Black banner with optional title + WYSIWYG body (fleet archive, occasions, etc.).
 *
 * @param string $lead             HTML from ACF description.
 * @param string $modifier_class   Extra BEM modifier on the header.
 * @param string $title            Optional H1 above the description.
 */
function vip_transits_render_black_banner_lead( $lead, $modifier_class = '', $title = '' ) {
	$lead  = trim( (string) $lead );
	$title = trim( (string) $title );
	if ( $lead === '' && $title === '' ) {
		return;
	}

	$modifier_class = trim( (string) $modifier_class );
	$class          = 'vip-bg-black-section vip-bg-black-section--masthead';
	if ( $title === '' ) {
		$class .= ' vip-bg-black-section--masthead-no-title';
	}
	if ( $modifier_class !== '' ) {
		$class .= ' ' . sanitize_html_class( $modifier_class );
	}
	?>
	<header class="<?php echo esc_attr( $class ); ?>">
		<div class="vip-bg-black-section__inner vip-content-container">
			<?php if ( $title !== '' ) : ?>
				<h1 class="vip-page__masthead-title"><?php echo esc_html( $title ); ?></h1>
			<?php endif; ?>
			<?php if ( $lead !== '' ) : ?>
				<div class="vip-page__masthead-lead"><?php echo wp_kses_post( $lead ); ?></div>
			<?php endif; ?>
		</div>
	</header>
	<?php
}

/**
 * Output the black page masthead when Show Banner is Yes.
 *
 * @param int $post_id Optional page ID.
 */
function vip_transits_render_page_banner( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : vip_transits_page_content_post_id();
	if ( ! vip_transits_page_show_banner( $post_id ) ) {
		return;
	}

	set_query_var( 'vip_masthead_active', true );
	set_query_var( 'vip_masthead_title', get_the_title( $post_id ) );
	set_query_var( 'vip_masthead_lead', vip_transits_get_page_banner_description( $post_id ) );
	set_query_var( 'vip_masthead_hide_title_fallback', false );
	get_template_part( 'template-parts/page/masthead' );
}

/**
 * Prepend the page banner before generic page post content.
 *
 * @param string $block_content Block HTML.
 * @param array  $block         Block data.
 * @return string
 */
function vip_transits_prepend_page_banner_to_post_content( $block_content, $block ) {
	if ( ! is_array( $block ) || empty( $block['blockName'] ) || 'core/post-content' !== $block['blockName'] ) {
		return $block_content;
	}

	if ( ! is_page() || is_front_page() ) {
		return $block_content;
	}

	$post_id = vip_transits_page_content_post_id();
	if ( $post_id <= 0 || vip_transits_page_uses_vip_content_block( $post_id ) ) {
		return $block_content;
	}

	if ( ! vip_transits_page_show_banner( $post_id ) ) {
		return $block_content;
	}

	ob_start();
	vip_transits_render_page_banner( $post_id );
	$banner = (string) ob_get_clean();

	return $banner . $block_content;
}
add_filter( 'render_block', 'vip_transits_prepend_page_banner_to_post_content', 9, 2 );

/**
 * Register ACF blocks.
 */
function vip_transits_register_page_content_blocks() {
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	$dir = get_stylesheet_directory();
	$css = vip_transits_page_content_assets();

	acf_register_block_type(
		array(
			'name'            => 'vip-page-about',
			'title'           => __( 'VIP About Us content', 'tenku-child' ),
			'description'     => __( 'About page body (ACF fields on this page).', 'tenku-child' ),
			'category'        => 'layout',
			'icon'            => 'id-alt',
			'keywords'        => array( 'about', 'company' ),
			'render_template' => $dir . '/blocks/vip-page-about/render.php',
			'enqueue_style'   => $css['style'],
			'mode'            => 'preview',
			'supports'        => array(
				'align'  => array( 'wide', 'full' ),
				'anchor' => true,
				'mode'   => false,
			),
		)
	);

	acf_register_block_type(
		array(
			'name'            => 'vip-page-contact',
			'title'           => __( 'VIP Contact Us content', 'tenku-child' ),
			'description'     => __( 'Contact page with CF7 shortcode area (ACF fields).', 'tenku-child' ),
			'category'        => 'layout',
			'icon'            => 'email',
			'keywords'        => array( 'contact', 'form' ),
			'render_template' => $dir . '/blocks/vip-page-contact/render.php',
			'enqueue_style'   => $css['style'],
			'mode'            => 'preview',
			'supports'        => array(
				'align'  => array( 'wide', 'full' ),
				'anchor' => true,
				'mode'   => false,
			),
		)
	);

	acf_register_block_type(
		array(
			'name'            => 'vip-occasion-listing',
			'title'           => __( 'VIP Occasion detail', 'tenku-child' ),
			'description'     => __( 'Occasion detail: hero, fleet, FAQ. Use on vip_occasion singles or legacy pages.', 'tenku-child' ),
			'category'        => 'layout',
			'icon'            => 'calendar-alt',
			'keywords'        => array( 'occasion', 'wedding', 'fleet' ),
			'render_template' => $dir . '/blocks/vip-occasion-listing/render.php',
			'enqueue_style'   => $css['style'],
			'mode'            => 'preview',
			'supports'        => array(
				'align'  => array( 'wide', 'full' ),
				'anchor' => true,
				'mode'   => false,
			),
		)
	);
}
add_action( 'acf/init', 'vip_transits_register_page_content_blocks', 5 );

/**
 * @return array{style:string,version:string}
 */
function vip_transits_page_content_assets() {
	$path = get_stylesheet_directory() . '/assets/css/vip-pages.css';
	$ver  = file_exists( $path ) ? (string) filemtime( $path ) : wp_get_theme()->get( 'Version' );

	return array(
		'style'   => get_stylesheet_directory_uri() . '/assets/css/vip-pages.css',
		'version' => $ver,
	);
}

/**
 * Build a safe href for a contact detail row (phone, email, address, or custom link).
 *
 * @param string $link  Optional link from ACF (tel:, mailto:, https:, or plain phone/email).
 * @param string $type  phone|email|address|text.
 * @param string $value Display value (used to auto-build tel:/mailto: when link is empty).
 * @return string Empty string if not linkable.
 */
function vip_transits_normalize_contact_detail_href( $link, $type, $value ) {
	$link  = trim( (string) $link );
	$value = trim( (string) $value );
	$type  = (string) $type;

	if ( $link !== '' ) {
		if ( preg_match( '#^tel:#i', $link ) ) {
			return vip_transits_build_tel_href( substr( $link, 4 ) );
		}
		if ( preg_match( '#^mailto:#i', $link ) ) {
			$email = sanitize_email( substr( $link, 7 ) );
			return $email ? 'mailto:' . $email : '';
		}
		if ( preg_match( '#^https?://#i', $link ) ) {
			return (string) esc_url( $link );
		}
		if ( is_email( $link ) ) {
			return 'mailto:' . sanitize_email( $link );
		}
		if ( 'email' === $type || str_contains( $link, '@' ) ) {
			return 'mailto:' . sanitize_email( $link );
		}
		$tel = vip_transits_build_tel_href( $link );
		if ( $tel !== '' ) {
			return $tel;
		}
		return (string) esc_url( 'https://' . ltrim( $link, '/' ) );
	}

	if ( $value === '' ) {
		return '';
	}

	if ( 'phone' === $type ) {
		return vip_transits_build_tel_href( $value );
	}

	if ( 'email' === $type || is_email( $value ) ) {
		return 'mailto:' . sanitize_email( $value );
	}

	return '';
}

/**
 * tel: href without encoding + (esc_url turns + into %2B).
 *
 * @param string $raw Phone digits / formatted number.
 * @return string e.g. tel:+971507350049
 */
function vip_transits_build_tel_href( $raw ) {
	$raw = trim( (string) $raw );
	if ( $raw === '' ) {
		return '';
	}

	$digits = preg_replace( '/[^\d+]/', '', $raw );
	if ( $digits === '' ) {
		return '';
	}

	if ( '+' !== $digits[0] ) {
		$digits = ltrim( $digits, '0' );
	}

	return 'tel:' . $digits;
}

/**
 * Extract iframe src from pasted HTML (handles truncated / unclosed iframe tags).
 *
 * @param string $html Raw iframe HTML or fragment.
 * @return string
 */
function vip_transits_extract_iframe_src( $html ) {
	$html = (string) $html;
	if ( $html === '' || stripos( $html, '<iframe' ) === false ) {
		return '';
	}

	if ( preg_match( '/\bsrc\s*=\s*"([^"]*)"/is', $html, $match ) ) {
		return trim( $match[1] );
	}

	if ( preg_match( "/\bsrc\s*=\s*'([^']*)'/is", $html, $match ) ) {
		return trim( $match[1] );
	}

	// Truncated paste: src="https://... with no closing quote or >.
	if ( preg_match( '/\bsrc\s*=\s*"([^"]+)/is', $html, $match ) ) {
		return trim( $match[1] );
	}

	if ( preg_match( "/\bsrc\s*=\s*'([^']+)/is", $html, $match ) ) {
		return trim( $match[1] );
	}

	return '';
}

/**
 * Build a valid, closed map iframe (never output a broken opening tag).
 *
 * @param string $src Iframe src URL.
 * @return string
 */
function vip_transits_build_map_iframe( $src ) {
	$src = esc_url( trim( (string) $src ) );
	if ( $src === '' ) {
		return '';
	}

	return sprintf(
		'<iframe src="%s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="%s"></iframe>',
		$src,
		esc_attr__( 'Location map', 'tenku-child' )
	);
}

/**
 * Render map field (iframe HTML, oEmbed, or Google Maps share / short links).
 *
 * @param string $raw ACF oembed / text value.
 * @return string HTML or empty.
 */
function vip_transits_render_contact_map_embed( $raw ) {
	$raw = vip_transits_normalize_map_embed_raw( $raw );
	if ( $raw === '' ) {
		return '';
	}

	$allowed_iframe = array(
		'iframe' => array(
			'src'             => true,
			'width'           => true,
			'height'          => true,
			'style'           => true,
			'frameborder'     => true,
			'allowfullscreen' => true,
			'loading'         => true,
			'referrerpolicy'  => true,
			'title'           => true,
			'aria-label'      => true,
		),
	);

	if ( stripos( $raw, '<iframe' ) !== false ) {
		$src = vip_transits_extract_iframe_src( $raw );
		if ( $src !== '' ) {
			return vip_transits_build_map_iframe( $src );
		}

		if ( preg_match( '/<iframe\b[^>]*>/is', $raw, $match ) ) {
			return (string) wp_kses( $match[0], $allowed_iframe );
		}

		return '';
	}

	$url = wp_strip_all_tags( $raw );
	if ( $url === '' ) {
		return '';
	}

	$embed = wp_oembed_get( $url, array( 'width' => 1200 ) );
	if ( $embed ) {
		$embed_src = vip_transits_extract_iframe_src( $embed );
		if ( $embed_src !== '' ) {
			return vip_transits_build_map_iframe( $embed_src );
		}
		return (string) wp_kses( $embed, $allowed_iframe );
	}

	$resolved = vip_transits_resolve_redirect_url( $url );
	if ( $resolved !== $url ) {
		$embed = wp_oembed_get( $resolved, array( 'width' => 1200 ) );
		if ( $embed ) {
			$embed_src = vip_transits_extract_iframe_src( $embed );
			if ( $embed_src !== '' ) {
				return vip_transits_build_map_iframe( $embed_src );
			}
			return (string) wp_kses( $embed, $allowed_iframe );
		}
		$url = $resolved;
	}

	$iframe_src = vip_transits_google_maps_embed_src( $url );
	if ( $iframe_src ) {
		return vip_transits_build_map_iframe( $iframe_src );
	}

	return '';
}

/**
 * Fix map field value before save / display (oEmbed used to prefix iframe with https://).
 *
 * @param mixed $raw ACF value.
 * @return string
 */
function vip_transits_sanitize_map_embed_storage( $raw ) {
	$raw = trim( (string) $raw );
	if ( $raw === '' ) {
		return '';
	}

	$raw = str_replace( array( '\\"', "\\'", '\/' ), array( '"', "'", '/' ), $raw );
	$raw = html_entity_decode( $raw, ENT_QUOTES, 'UTF-8' );

	// Legacy oEmbed / URL field: "https://<iframe..." or "https://%3Ciframe..."
	$raw = preg_replace( '#^https?://(?=<iframe)#i', '', $raw );
	$raw = preg_replace( '#^https?://(?=&lt;iframe)#i', '', $raw );
	if ( preg_match( '#^https?://%3[Cc]iframe#', $raw ) ) {
		$raw = preg_replace( '#^https?://#i', '', $raw );
		$raw = rawurldecode( $raw );
	}

	return $raw;
}

/**
 * Force map field to a plain textarea (not oEmbed URL field).
 *
 * @param array $field ACF field.
 * @return array
 */
function vip_transits_acf_map_embed_load_field( $field ) {
	if ( empty( $field['key'] ) || 'field_vipcontact_map_embed' !== $field['key'] ) {
		return $field;
	}

	$field['type']         = 'textarea';
	$field['rows']         = 6;
	$field['new_lines']    = '';
	$field['instructions'] = __( 'Paste a Google Maps share link, OR the full iframe HTML from Share → Embed a map. Do not add https:// before the iframe tag.', 'tenku-child' );

	return $field;
}
add_filter( 'acf/load_field/key=field_vipcontact_map_embed', 'vip_transits_acf_map_embed_load_field' );

/**
 * @param mixed $value Submitted value.
 * @return string
 */
function vip_transits_acf_map_embed_update_value( $value ) {
	return vip_transits_sanitize_map_embed_storage( $value );
}
add_filter( 'acf/update_value/key=field_vipcontact_map_embed', 'vip_transits_acf_map_embed_update_value' );

/**
 * @param mixed $value Stored value.
 * @return string
 */
function vip_transits_acf_map_embed_format_value( $value ) {
	return vip_transits_sanitize_map_embed_storage( $value );
}
add_filter( 'acf/format_value/key=field_vipcontact_map_embed', 'vip_transits_acf_map_embed_format_value' );

/**
 * Clean map field input from the editor (iframe HTML, share URL, or messy paste).
 *
 * @param string $raw ACF value.
 * @return string Normalized iframe HTML, URL, or empty.
 */
function vip_transits_normalize_map_embed_raw( $raw ) {
	$raw = vip_transits_sanitize_map_embed_storage( $raw );
	if ( $raw === '' ) {
		return '';
	}

	if ( stripos( $raw, '<iframe' ) !== false ) {
		$src = vip_transits_extract_iframe_src( $raw );
		if ( $src !== '' ) {
			return vip_transits_build_map_iframe( $src );
		}
	}

	if ( preg_match( '#https?://[^\s<>"\']+#i', $raw, $match ) ) {
		return trim( $match[0] );
	}

	return $raw;
}

/**
 * @param string $url Starting URL.
 * @return string Final URL after redirects.
 */
function vip_transits_resolve_redirect_url( $url, $follow_remote = null ) {
	$url = esc_url_raw( $url );
	if ( ! $url ) {
		return '';
	}

	// Avoid HTTP during front-end block render (map field); slow redirects can
	// exceed max execution time and abort the page before the footer runs.
	if ( null === $follow_remote ) {
		$follow_remote = is_admin() || wp_doing_cron();
	}
	if ( ! $follow_remote ) {
		return $url;
	}

	for ( $i = 0; $i < 5; $i++ ) {
		$response = wp_safe_remote_head(
			$url,
			array(
				'timeout'     => 8,
				'redirection' => 0,
			)
		);

		if ( is_wp_error( $response ) ) {
			break;
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		if ( ! in_array( $code, array( 301, 302, 303, 307, 308 ), true ) ) {
			return $url;
		}

		$location = wp_remote_retrieve_header( $response, 'location' );
		if ( ! $location ) {
			break;
		}

		$next = esc_url_raw( $location );
		if ( ! $next ) {
			break;
		}

		$url = $next;
	}

	return $url;
}

/**
 * Build Google Maps embed iframe src from a maps URL.
 *
 * @param string $url Maps or share URL.
 * @return string Embed src or empty.
 */
function vip_transits_google_maps_embed_src( $url ) {
	$url = (string) $url;
	if ( $url === '' ) {
		return '';
	}

	if ( preg_match( '#google\.com/maps|maps\.google\.#i', $url ) ) {
		$embed = $url;
		if ( ! preg_match( '#output=embed#i', $embed ) ) {
			$embed .= ( str_contains( $embed, '?' ) ? '&' : '?' ) . 'output=embed';
		}
		return $embed;
	}

	if ( preg_match( '#maps\.app\.goo\.gl|goo\.gl/maps#i', $url ) ) {
		$resolved = vip_transits_resolve_redirect_url( $url );
		if ( $resolved && $resolved !== $url ) {
			return vip_transits_google_maps_embed_src( $resolved );
		}
	}

	return '';
}

/**
 * Run Contact Form 7 (or other) shortcodes from an ACF WYSIWYG value.
 *
 * @param string $raw Field value (may include <p> wrappers).
 * @return string
 */
function vip_transits_render_form_shortcode_field( $raw ) {
	$raw = trim( (string) $raw );
	if ( $raw === '' ) {
		return '';
	}

	if ( strpos( $raw, '[' ) !== false ) {
		$stripped = trim( wp_strip_all_tags( $raw ) );
		if ( $stripped !== '' ) {
			return (string) do_shortcode( $stripped );
		}
	}

	return (string) apply_filters( 'the_content', $raw );
}

/**
 * Enqueue page template styles.
 */
function vip_transits_enqueue_page_content_assets() {
	if ( is_admin() ) {
		return;
	}

	$load_styles = false;

	if ( is_singular( 'vip_occasion' ) ) {
		$load_styles = true;
	}

	$post_id = vip_transits_page_content_post_id();
	if ( $post_id && is_page() ) {
		if (
			vip_transits_page_uses_vip_template( $post_id, 'about' )
			|| vip_transits_page_uses_vip_template( $post_id, 'contact' )
			|| vip_transits_page_uses_vip_template( $post_id, 'occasion' )
			|| vip_transits_page_show_banner( $post_id )
		) {
			$load_styles = true;
		}
	}

	if ( ! $load_styles ) {
		return;
	}

	if ( function_exists( 'vip_transits_enqueue_editor_block_styles' ) ) {
		vip_transits_enqueue_editor_block_styles();
	}

	$assets = vip_transits_page_content_assets();
	wp_enqueue_style(
		'vip-pages',
		$assets['style'],
		array( 'chld_thm_cfg_child' ),
		$assets['version']
	);

	if ( $post_id && vip_transits_page_uses_vip_template( $post_id, 'about' ) ) {
		$about_css = get_stylesheet_directory() . '/assets/css/vip-about.css';
		if ( file_exists( $about_css ) ) {
			wp_enqueue_style(
				'vip-about',
				get_stylesheet_directory_uri() . '/assets/css/vip-about.css',
				array( 'vip-pages', 'chld_thm_cfg_child' ),
				(string) filemtime( $about_css )
			);
		}
	}

	if ( function_exists( 'vip_transits_is_occasion_detail' ) && vip_transits_is_occasion_detail() ) {
		if ( function_exists( 'vip_transits_vehicle_single_assets' ) ) {
			$vd_assets = vip_transits_vehicle_single_assets();
			wp_enqueue_style(
				'vip-vehicle-single',
				$vd_assets['style'],
				array( 'vip-pages', 'chld_thm_cfg_child', 'chld_thm_cfg_parent' ),
				$vd_assets['version']
			);
		}

		if ( function_exists( 'vip_transits_enqueue_faq_section_assets' ) ) {
			vip_transits_enqueue_faq_section_assets();
		}
	}

	if (
		$post_id
		&& vip_transits_page_uses_vip_template( $post_id, 'about' )
		&& function_exists( 'vip_transits_about_page_has_faq_section' )
		&& vip_transits_about_page_has_faq_section( $post_id )
		&& function_exists( 'vip_transits_enqueue_faq_section_assets' )
	) {
		vip_transits_enqueue_faq_section_assets();
	}

	if ( function_exists( 'wpcf7_enqueue_scripts' ) && vip_transits_page_uses_vip_template( $post_id, 'contact' ) ) {
		wpcf7_enqueue_scripts();
	}
}
add_action( 'wp_enqueue_scripts', 'vip_transits_enqueue_page_content_assets', 20 );

/**
 * Editor preview styles.
 */
function vip_transits_enqueue_page_content_editor_assets() {
	if ( function_exists( 'vip_transits_enqueue_editor_block_styles' ) ) {
		vip_transits_enqueue_editor_block_styles();
	}

	$path = get_stylesheet_directory() . '/assets/css/vip-pages.css';
	if ( ! file_exists( $path ) ) {
		return;
	}

	wp_enqueue_style(
		'vip-pages-editor',
		get_stylesheet_directory_uri() . '/assets/css/vip-pages.css',
		array(),
		(string) filemtime( $path )
	);
}
add_action( 'enqueue_block_editor_assets', 'vip_transits_enqueue_page_content_editor_assets' );
