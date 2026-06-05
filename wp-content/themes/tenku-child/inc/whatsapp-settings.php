<?php
/**
 * Global WhatsApp number (Settings) and shared link helpers.
 *
 * @package Tenku_Child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Option key for stored WhatsApp number (digits only, with country code).
 */
define( 'VIP_TRANSITS_WHATSAPP_OPTION', 'vip_transits_whatsapp_number' );

/** @var string Enable homepage scroll animations (1 = on, 0 = off). */
define( 'VIP_TRANSITS_SCROLL_ANIMATIONS_OPTION', 'vip_transits_scroll_animations_enabled' );

/** @var string Instagram profile URL for header/footer social icons. */
define( 'VIP_TRANSITS_INSTAGRAM_URL_OPTION', 'vip_transits_instagram_url' );

/** @var string Open site links in a new browser tab (1 = on, 0 = off). */
define( 'VIP_TRANSITS_OPEN_LINKS_NEW_TAB_OPTION', 'vip_transits_open_links_new_tab' );

/** @var string Show black banner on /fleet/ archive (1 = on, 0 = off). */
define( 'VIP_TRANSITS_FLEET_ARCHIVE_BANNER_OPTION', 'vip_transits_fleet_archive_banner' );

/** @var string Fleet archive banner HTML (title + subtitle). */
define( 'VIP_TRANSITS_FLEET_ARCHIVE_DESCRIPTION_OPTION', 'vip_transits_fleet_archive_description' );

/**
 * Register settings page under Settings.
 */
function vip_transits_register_whatsapp_settings() {
	register_setting(
		'vip_transits_settings',
		VIP_TRANSITS_WHATSAPP_OPTION,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'vip_transits_sanitize_whatsapp_number',
			'default'           => '',
		)
	);

	add_settings_section(
		'vip_transits_whatsapp_section',
		__( 'WhatsApp', 'tenku-child' ),
		'vip_transits_whatsapp_section_cb',
		'vip-transits-settings'
	);

	add_settings_field(
		VIP_TRANSITS_WHATSAPP_OPTION,
		__( 'WhatsApp number', 'tenku-child' ),
		'vip_transits_whatsapp_number_field_cb',
		'vip-transits-settings',
		'vip_transits_whatsapp_section',
		array(
			'label_for' => 'vip_transits_whatsapp_number',
		)
	);

	register_setting(
		'vip_transits_settings',
		VIP_TRANSITS_SCROLL_ANIMATIONS_OPTION,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'vip_transits_sanitize_scroll_animations_enabled',
			'default'           => '1',
		)
	);

	add_settings_section(
		'vip_transits_display_section',
		__( 'Site display', 'tenku-child' ),
		'vip_transits_display_section_cb',
		'vip-transits-settings'
	);

	add_settings_field(
		VIP_TRANSITS_SCROLL_ANIMATIONS_OPTION,
		__( 'Scroll animations', 'tenku-child' ),
		'vip_transits_scroll_animations_field_cb',
		'vip-transits-settings',
		'vip_transits_display_section',
		array(
			'label_for' => 'vip_transits_scroll_animations_enabled',
		)
	);

	register_setting(
		'vip_transits_settings',
		VIP_TRANSITS_OPEN_LINKS_NEW_TAB_OPTION,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'vip_transits_sanitize_open_links_new_tab',
			'default'           => '1',
		)
	);

	add_settings_field(
		VIP_TRANSITS_OPEN_LINKS_NEW_TAB_OPTION,
		__( 'Open links in new tab', 'tenku-child' ),
		'vip_transits_open_links_new_tab_field_cb',
		'vip-transits-settings',
		'vip_transits_display_section',
		array(
			'label_for' => 'vip_transits_open_links_new_tab',
		)
	);

	register_setting(
		'vip_transits_settings',
		VIP_TRANSITS_INSTAGRAM_URL_OPTION,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'vip_transits_sanitize_instagram_url',
			'default'           => '',
		)
	);

	add_settings_section(
		'vip_transits_social_section',
		__( 'Social', 'tenku-child' ),
		'vip_transits_social_section_cb',
		'vip-transits-settings'
	);

	add_settings_field(
		VIP_TRANSITS_INSTAGRAM_URL_OPTION,
		__( 'Instagram URL', 'tenku-child' ),
		'vip_transits_instagram_url_field_cb',
		'vip-transits-settings',
		'vip_transits_social_section',
		array(
			'label_for' => 'vip_transits_instagram_url',
		)
	);

	register_setting(
		'vip_transits_settings',
		VIP_TRANSITS_FLEET_ARCHIVE_BANNER_OPTION,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'vip_transits_sanitize_fleet_archive_banner',
			'default'           => '0',
		)
	);

	register_setting(
		'vip_transits_settings',
		VIP_TRANSITS_FLEET_ARCHIVE_DESCRIPTION_OPTION,
		array(
			'type'              => 'string',
			'sanitize_callback' => 'vip_transits_sanitize_fleet_archive_description',
			'default'           => '',
		)
	);

	add_settings_section(
		'vip_transits_fleet_section',
		__( 'Fleet archive', 'tenku-child' ),
		'vip_transits_fleet_section_cb',
		'vip-transits-settings'
	);

	add_settings_field(
		VIP_TRANSITS_FLEET_ARCHIVE_BANNER_OPTION,
		__( 'Show banner', 'tenku-child' ),
		'vip_transits_fleet_archive_banner_field_cb',
		'vip-transits-settings',
		'vip_transits_fleet_section',
		array(
			'label_for' => 'vip_transits_fleet_archive_banner',
		)
	);

	add_settings_field(
		VIP_TRANSITS_FLEET_ARCHIVE_DESCRIPTION_OPTION,
		__( 'Banner content', 'tenku-child' ),
		'vip_transits_fleet_archive_description_field_cb',
		'vip-transits-settings',
		'vip_transits_fleet_section',
		array(
			'label_for' => 'vip_transits_fleet_archive_description',
		)
	);
}
add_action( 'admin_init', 'vip_transits_register_whatsapp_settings' );

/**
 * Add options page: Settings → VIP Transits.
 */
function vip_transits_add_settings_page() {
	add_options_page(
		__( 'VIP Transits', 'tenku-child' ),
		__( 'VIP Transits', 'tenku-child' ),
		'manage_options',
		'vip-transits-settings',
		'vip_transits_render_settings_page'
	);
}
add_action( 'admin_menu', 'vip_transits_add_settings_page' );

/**
 * Remind admins to set the global WhatsApp number.
 */
function vip_transits_whatsapp_missing_notice() {
	if ( ! current_user_can( 'manage_options' ) || vip_transits_get_whatsapp_number() !== '' ) {
		return;
	}

	echo '<div class="notice notice-warning"><p>';
	printf(
		/* translators: %s: settings page URL */
		esc_html__( 'VIP Transits: set your WhatsApp number under %s so Book now and WhatsApp buttons work.', 'tenku-child' ),
		'<a href="' . esc_url( admin_url( 'options-general.php?page=vip-transits-settings' ) ) . '">' . esc_html__( 'Settings → VIP Transits', 'tenku-child' ) . '</a>'
	);
	echo '</p></div>';
}
add_action( 'admin_notices', 'vip_transits_whatsapp_missing_notice' );

/**
 * Section description.
 */
function vip_transits_whatsapp_section_cb() {
	echo '<p>';
	esc_html_e( 'Used for all Book now and WhatsApp buttons across the site (fleet cards, occasions, CTA banner). Each button opens WhatsApp with a message that includes the relevant vehicle or card details.', 'tenku-child' );
	echo '</p>';
}

/**
 * WhatsApp number field markup.
 */
function vip_transits_whatsapp_number_field_cb() {
	$value = vip_transits_get_whatsapp_number();
	?>
	<input
		type="text"
		id="vip_transits_whatsapp_number"
		name="<?php echo esc_attr( VIP_TRANSITS_WHATSAPP_OPTION ); ?>"
		value="<?php echo esc_attr( $value ); ?>"
		class="regular-text"
		inputmode="numeric"
		autocomplete="tel"
		placeholder="971501234567"
	/>
	<p class="description">
		<?php esc_html_e( 'International format without + or spaces (e.g. 971501234567 for UAE).', 'tenku-child' ); ?>
	</p>
	<?php
}

/**
 * @param mixed $value Submitted value.
 * @return string '1' or '0'.
 */
function vip_transits_sanitize_fleet_archive_banner( $value ) {
	return ! empty( $value ) && '0' !== (string) $value ? '1' : '0';
}

/**
 * @param mixed $value Submitted value.
 * @return string
 */
function vip_transits_sanitize_fleet_archive_description( $value ) {
	return wp_kses_post( (string) $value );
}

/**
 * Fleet archive section description.
 */
function vip_transits_fleet_section_cb() {
	echo '<p>' . esc_html__( 'Black masthead at the top of the vehicle fleet listing (/fleet/).', 'tenku-child' ) . '</p>';
}

/**
 * Fleet archive banner toggle.
 */
function vip_transits_fleet_archive_banner_field_cb() {
	$enabled = vip_transits_fleet_archive_banner_enabled();
	?>
	<input type="hidden" name="<?php echo esc_attr( VIP_TRANSITS_FLEET_ARCHIVE_BANNER_OPTION ); ?>" value="0" />
	<label for="vip_transits_fleet_archive_banner">
		<input
			type="checkbox"
			name="<?php echo esc_attr( VIP_TRANSITS_FLEET_ARCHIVE_BANNER_OPTION ); ?>"
			id="vip_transits_fleet_archive_banner"
			value="1"
			<?php checked( $enabled ); ?>
		/>
		<?php esc_html_e( 'Show black banner on the fleet archive page', 'tenku-child' ); ?>
	</label>
	<?php
}

/**
 * Fleet archive banner WYSIWYG-style HTML (title + subtitle).
 */
function vip_transits_fleet_archive_description_field_cb() {
	$value = vip_transits_get_fleet_archive_banner_description_option();
	?>
	<textarea
		id="vip_transits_fleet_archive_description"
		name="<?php echo esc_attr( VIP_TRANSITS_FLEET_ARCHIVE_DESCRIPTION_OPTION ); ?>"
		class="large-text code"
		rows="8"
	><?php echo esc_textarea( $value ); ?></textarea>
	<p class="description">
		<?php
		esc_html_e(
			'Use HTML: H1 for the main heading (e.g. Our Fleet) and a paragraph for the subtitle. Example:',
			'tenku-child'
		);
		?>
		<br />
		<code>&lt;h1&gt;Our Fleet&lt;/h1&gt;&lt;p&gt;Browse supercars, Rolls-Royce, and SUVs — delivered free across Dubai.&lt;/p&gt;</code>
	</p>
	<?php
}

/**
 * @return bool
 */
function vip_transits_fleet_archive_banner_enabled() {
	if ( get_option( VIP_TRANSITS_FLEET_ARCHIVE_BANNER_OPTION, null ) !== null ) {
		return get_option( VIP_TRANSITS_FLEET_ARCHIVE_BANNER_OPTION, '0' ) === '1';
	}

	if ( function_exists( 'vip_transits_get_acf_option_field' ) ) {
		$show = strtolower( (string) vip_transits_get_acf_option_field( 'feleet_archive_banner' ) );
		return in_array( $show, array( 'yes', 'y', '1' ), true );
	}

	return false;
}

/**
 * Banner HTML stored in VIP Transits settings (falls back to legacy ACF options).
 *
 * @return string
 */
function vip_transits_get_fleet_archive_banner_description_option() {
	$value = get_option( VIP_TRANSITS_FLEET_ARCHIVE_DESCRIPTION_OPTION, null );
	if ( is_string( $value ) ) {
		return $value;
	}

	if ( function_exists( 'vip_transits_get_acf_option_field' ) ) {
		return trim( (string) vip_transits_get_acf_option_field( 'feet_archive_description' ) );
	}

	return '';
}

/**
 * Site display section description.
 */
function vip_transits_display_section_cb() {
	echo '<p>' . esc_html__( 'Control front-end motion and lazy section effects.', 'tenku-child' ) . '</p>';
}

/**
 * Scroll animations checkbox.
 */
function vip_transits_scroll_animations_field_cb() {
	$enabled = vip_transits_scroll_animations_enabled();
	?>
	<input type="hidden" name="<?php echo esc_attr( VIP_TRANSITS_SCROLL_ANIMATIONS_OPTION ); ?>" value="0" />
	<label for="vip_transits_scroll_animations_enabled">
		<input
			type="checkbox"
			name="<?php echo esc_attr( VIP_TRANSITS_SCROLL_ANIMATIONS_OPTION ); ?>"
			id="vip_transits_scroll_animations_enabled"
			value="1"
			<?php checked( $enabled ); ?>
		/>
		<?php esc_html_e( 'Enable scroll animations on the public site', 'tenku-child' ); ?>
	</label>
	<p class="description">
		<?php
		esc_html_e(
			'When off, section reveal, card stagger, category motion, and hero entrance animations are disabled. Visitors who prefer reduced motion in their browser still skip animations automatically.',
			'tenku-child'
		);
		?>
	</p>
	<?php
}

/**
 * @param mixed $value Submitted value.
 * @return string '1' or '0'.
 */
function vip_transits_sanitize_scroll_animations_enabled( $value ) {
	return ! empty( $value ) && '0' !== (string) $value ? '1' : '0';
}

/**
 * @param mixed $value Submitted value.
 * @return string '1' or '0'.
 */
function vip_transits_sanitize_open_links_new_tab( $value ) {
	return ! empty( $value ) && '0' !== (string) $value ? '1' : '0';
}

/**
 * Whether front-end links should open in a new tab.
 *
 * @return bool
 */
function vip_transits_open_links_in_new_tab() {
	return get_option( VIP_TRANSITS_OPEN_LINKS_NEW_TAB_OPTION, '1' ) === '1';
}

/**
 * target + rel attributes for external-style links in templates.
 *
 * @return string HTML attribute fragment (leading space) or empty.
 */
function vip_transits_link_target_attr() {
	return vip_transits_open_links_in_new_tab() ? ' target="_blank" rel="noopener noreferrer"' : '';
}

/**
 * Open links in new tab checkbox.
 */
function vip_transits_open_links_new_tab_field_cb() {
	$enabled = vip_transits_open_links_in_new_tab();
	?>
	<input type="hidden" name="<?php echo esc_attr( VIP_TRANSITS_OPEN_LINKS_NEW_TAB_OPTION ); ?>" value="0" />
	<label for="vip_transits_open_links_new_tab">
		<input
			type="checkbox"
			name="<?php echo esc_attr( VIP_TRANSITS_OPEN_LINKS_NEW_TAB_OPTION ); ?>"
			id="vip_transits_open_links_new_tab"
			value="1"
			<?php checked( $enabled ); ?>
		/>
		<?php esc_html_e( 'Open site links in a new browser tab', 'tenku-child' ); ?>
	</label>
	<p class="description">
		<?php esc_html_e( 'When enabled, navigation, fleet cards, menus, and content links open in a new tab. Turn off to keep visitors on the same tab.', 'tenku-child' ); ?>
	</p>
	<?php
}

/**
 * Add target="_blank" to anchor tags in HTML when the setting is enabled.
 *
 * @param string $html HTML fragment.
 * @return string
 */
function vip_transits_add_link_targets_to_html( $html ) {
	if ( ! vip_transits_open_links_in_new_tab() || ! is_string( $html ) || $html === '' || ! preg_match( '/<a\s/i', $html ) ) {
		return $html;
	}

	return (string) preg_replace_callback(
		'/<a\s([^>]*?)href=(["\'])([^"\']+)\2([^>]*)>/i',
		static function ( $matches ) {
			$href = (string) $matches[3];
			if ( preg_match( '/^(#|mailto:|tel:|javascript:)/i', $href ) ) {
				return $matches[0];
			}

			$attrs = $matches[1] . $matches[4];
			if ( preg_match( '/\bvip-article__back\b/i', $attrs ) ) {
				return $matches[0];
			}

			$tag = $matches[0];
			if ( preg_match( '/\starget\s*=/i', $tag ) ) {
				$tag = (string) preg_replace( '/\starget=(["\'])[^"\']*\1/i', ' target="_blank"', $tag, 1 );
			} else {
				$tag = preg_replace( '/<a\s/i', '<a target="_blank" ', $tag, 1 );
			}

			if ( preg_match( '/\srel=(["\'])([^"\']*)\1/i', $tag, $rel_match ) ) {
				$rels = array_filter( array_map( 'trim', explode( ' ', strtolower( $rel_match[2] ) ) ) );
				foreach ( array( 'noopener', 'noreferrer' ) as $required ) {
					if ( ! in_array( $required, $rels, true ) ) {
						$rels[] = $required;
					}
				}
				$tag = (string) preg_replace(
					'/\srel=(["\'])[^"\']*\1/i',
					' rel="' . esc_attr( implode( ' ', $rels ) ) . '"',
					$tag,
					1
				);
			} else {
				$tag = preg_replace( '/<a\s/i', '<a rel="noopener noreferrer" ', $tag, 1 );
			}

			return $tag;
		},
		$html
	);
}

/**
 * @param string $content Post content.
 * @return string
 */
function vip_transits_filter_content_link_targets( $content ) {
	return vip_transits_add_link_targets_to_html( $content );
}
add_filter( 'the_content', 'vip_transits_filter_content_link_targets', 99 );

/**
 * @param string $block_content Rendered block HTML.
 * @return string
 */
function vip_transits_filter_block_link_targets( $block_content ) {
	return vip_transits_add_link_targets_to_html( $block_content );
}
add_filter( 'render_block', 'vip_transits_filter_block_link_targets', 20 );

/**
 * @param array<string, string> $atts Link attributes.
 * @return array<string, string>
 */
function vip_transits_nav_menu_link_target_attrs( $atts ) {
	if ( ! vip_transits_open_links_in_new_tab() ) {
		return $atts;
	}

	$href = isset( $atts['href'] ) ? (string) $atts['href'] : '';
	if ( $href === '' || preg_match( '/^(#|mailto:|tel:|javascript:)/i', $href ) ) {
		return $atts;
	}

	$atts['target'] = '_blank';
	$rels           = array_filter( array_map( 'trim', explode( ' ', strtolower( (string) ( $atts['rel'] ?? '' ) ) ) ) );
	foreach ( array( 'noopener', 'noreferrer' ) as $required ) {
		if ( ! in_array( $required, $rels, true ) ) {
			$rels[] = $required;
		}
	}
	$atts['rel'] = implode( ' ', $rels );

	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'vip_transits_nav_menu_link_target_attrs', 10, 1 );

/**
 * Social section description.
 */
function vip_transits_social_section_cb() {
	echo '<p>';
	esc_html_e( 'Used for Instagram icons in the site header and footer. Leave empty to keep the URL set in the Site Editor.', 'tenku-child' );
	echo '</p>';
}

/**
 * Instagram URL field markup.
 */
function vip_transits_instagram_url_field_cb() {
	$value = vip_transits_get_instagram_url();
	?>
	<input
		type="url"
		id="vip_transits_instagram_url"
		name="<?php echo esc_attr( VIP_TRANSITS_INSTAGRAM_URL_OPTION ); ?>"
		value="<?php echo esc_attr( $value ); ?>"
		class="regular-text"
		placeholder="https://www.instagram.com/yourusername/"
	/>
	<p class="description">
		<?php esc_html_e( 'Full profile link (https://www.instagram.com/…). Overrides placeholder links on Instagram social icons.', 'tenku-child' ); ?>
	</p>
	<?php
}

/**
 * @param mixed $value Raw input.
 * @return string Sanitized URL or empty.
 */
function vip_transits_sanitize_instagram_url( $value ) {
	$url = esc_url_raw( trim( (string) $value ) );
	if ( $url === '' ) {
		return '';
	}

	$host = wp_parse_url( $url, PHP_URL_HOST );
	if ( ! is_string( $host ) ) {
		return '';
	}

	$host = strtolower( $host );
	if ( ! in_array( $host, array( 'instagram.com', 'www.instagram.com' ), true ) ) {
		add_settings_error(
			VIP_TRANSITS_INSTAGRAM_URL_OPTION,
			'invalid_instagram_url',
			__( 'Please enter a valid Instagram profile URL (instagram.com).', 'tenku-child' ),
			'error'
		);
		return '';
	}

	return $url;
}

/**
 * Global Instagram profile URL from settings.
 *
 * @return string
 */
function vip_transits_get_instagram_url() {
	return (string) get_option( VIP_TRANSITS_INSTAGRAM_URL_OPTION, '' );
}

/**
 * Whether scroll animation assets should load on the front end.
 *
 * @return bool
 */
function vip_transits_scroll_animations_enabled() {
	return '1' === (string) get_option( VIP_TRANSITS_SCROLL_ANIMATIONS_OPTION, '1' );
}

/**
 * Settings page output.
 */
function vip_transits_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'VIP Transits settings', 'tenku-child' ); ?></h1>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'vip_transits_settings' );
			do_settings_sections( 'vip-transits-settings' );
			submit_button();
			?>
		</form>

		<?php if ( function_exists( 'vip_transits_import_acf_json_field_groups' ) ) : ?>
			<hr />
			<h2><?php esc_html_e( 'ACF field groups', 'tenku-child' ); ?></h2>
			<p>
				<?php
				esc_html_e(
					'Import field groups from the theme JSON files (tenku-child/acf-json). This updates existing groups by key, removes duplicate database copies, and clears “Sync available” after deploys.',
					'tenku-child'
				);
				?>
			</p>
			<p>
				<strong><?php esc_html_e( 'Active theme:', 'tenku-child' ); ?></strong>
				<?php echo esc_html( wp_get_theme()->get( 'Name' ) ); ?>
				<br />
				<strong><?php esc_html_e( 'JSON path:', 'tenku-child' ); ?></strong>
				<code><?php echo esc_html( vip_transits_acf_json_dir() ); ?></code>
			</p>
			<form method="post">
				<?php wp_nonce_field( 'vip_acf_sync_json' ); ?>
				<input type="hidden" name="vip_acf_sync_json" value="1" />
				<?php submit_button( __( 'Import ACF JSON from theme', 'tenku-child' ), 'secondary', 'submit', false ); ?>
			</form>
			<p class="description">
				<?php
				printf(
					/* translators: 1: field groups admin URL */
					esc_html__( 'If you still see two rows for one group, run import here once, then check %s.', 'tenku-child' ),
					'<a href="' . esc_url( admin_url( 'edit.php?post_type=acf-field-group' ) ) . '">' . esc_html__( 'Custom Fields → Field Groups', 'tenku-child' ) . '</a>'
				);
				?>
			</p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Sanitize WhatsApp number to digits only.
 *
 * @param string $value Raw input.
 * @return string
 */
function vip_transits_sanitize_whatsapp_number( $value ) {
	return preg_replace( '/\D+/', '', (string) $value );
}

/**
 * Stored WhatsApp number (digits).
 *
 * @return string
 */
function vip_transits_get_whatsapp_number() {
	return vip_transits_sanitize_whatsapp_number( (string) get_option( VIP_TRANSITS_WHATSAPP_OPTION, '' ) );
}

/**
 * wa.me base URL for the global number.
 *
 * @return string Empty if number not set.
 */
function vip_transits_whatsapp_base_url() {
	$number = vip_transits_get_whatsapp_number();
	if ( $number === '' ) {
		return '';
	}

	return 'https://wa.me/' . $number;
}

/**
 * Encode message lines for wa.me ?text= (literal %0A between lines).
 *
 * @see https://faq.whatsapp.com/general/chats/how-to-use-click-to-chat
 * @param string[] $lines Non-empty lines; empty strings become blank lines.
 * @return string Encoded query value (no leading ?).
 */
function vip_transits_whatsapp_encode_lines( array $lines ) {
	$parts = array();

	foreach ( $lines as $line ) {
		$parts[] = rawurlencode( (string) $line );
	}

	return implode( '%0A', $parts );
}

/**
 * Full WhatsApp click-to-chat URL (official wa.me format).
 *
 * @param string|string[] $message Plain text, or array of lines (preferred).
 * @return string Raw URL or empty string.
 */
function vip_transits_whatsapp_href( $message = '' ) {
	$number = vip_transits_get_whatsapp_number();
	if ( $number === '' ) {
		return '';
	}

	if ( is_array( $message ) ) {
		$lines = array_values( $message );
		if ( empty( $lines ) ) {
			return 'https://wa.me/' . $number;
		}
		$encoded = vip_transits_whatsapp_encode_lines( $lines );
		return 'https://wa.me/' . $number . '?text=' . $encoded;
	}

	$message = trim( (string) $message );
	if ( $message === '' ) {
		return 'https://wa.me/' . $number;
	}

	if ( str_contains( $message, "\n" ) || str_contains( $message, "\r" ) ) {
		$lines = preg_split( '/\r\n|\r|\n/', $message );
		$encoded = vip_transits_whatsapp_encode_lines( $lines );
		return 'https://wa.me/' . $number . '?text=' . $encoded;
	}

	return 'https://wa.me/' . $number . '?text=' . rawurlencode( $message );
}

/**
 * WhatsApp URL safe for HTML href (preserves %0A; never use esc_url).
 *
 * @param string|string[] $message Plain text or lines array.
 * @return string
 */
function vip_transits_whatsapp_href_attr( $message = '' ) {
	$url = vip_transits_whatsapp_href( $message );
	if ( $url === '' ) {
		return '';
	}

	return htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
}

/**
 * Keep wa.me / api.whatsapp.com line breaks if another layer runs esc_url().
 *
 * @param string $good_url     Filtered URL.
 * @param string $original_url Original URL before clean_url.
 * @return string
 */
function vip_transits_preserve_whatsapp_url_newlines( $good_url, $original_url ) {
	$check = (string) $original_url;
	if ( $check === '' ) {
		$check = (string) $good_url;
	}

	if (
		str_contains( $check, 'wa.me' ) ||
		str_contains( $check, 'api.whatsapp.com' )
	) {
		return $check;
	}

	return $good_url;
}
add_filter( 'clean_url', 'vip_transits_preserve_whatsapp_url_newlines', 10, 2 );

/**
 * Back-compat wrapper (Rent by Occasion).
 *
 * @param string $base_url Ignored; uses global number.
 * @param string $title    Card title.
 * @param string $desc     Card description.
 * @return string
 */
function vip_transits_occasions_whatsapp_href( $base_url, $title, $desc = '' ) {
	unset( $base_url );
	return vip_transits_whatsapp_href( vip_transits_occasion_whatsapp_message( $title, $desc ) );
}

/**
 * Prefilled message for an occasion card.
 *
 * @param string $title Card title.
 * @param string $desc  Card description.
 * @return string
 */
function vip_transits_occasion_whatsapp_message( $title, $desc = '' ) {
	$lines = array();
	$title = trim( (string) $title );
	$desc  = trim( (string) $desc );
	if ( $title !== '' ) {
		$lines[] = $title;
	}
	if ( $desc !== '' ) {
		$lines[] = $desc;
	}
	return $lines;
}

/**
 * Button label from ACF (supports legacy link field).
 *
 * @param array  $card    Card/featured row.
 * @param string $default Default label.
 * @return string
 */
function vip_transits_occasion_button_label( array $card, $default = '' ) {
	if ( ! empty( $card['button_label'] ) ) {
		return (string) $card['button_label'];
	}

	if ( ! empty( $card['link'] ) && is_array( $card['link'] ) && ! empty( $card['link']['title'] ) ) {
		return (string) $card['link']['title'];
	}

	return $default;
}

/**
 * Lines for a fleet vehicle WhatsApp message (one line = one row in chat).
 *
 * @param array $data From vip_transits_get_vehicle_card_data().
 * @return string[]
 */
function vip_transits_vehicle_whatsapp_lines( array $data ) {
	$lines = array();

	if ( ! empty( $data['title'] ) ) {
		$lines[] = sprintf(
			/* translators: %s: vehicle name */
			__( 'Hi, I would like to book: %s', 'tenku-child' ),
			$data['title']
		);
	}

	if ( ! empty( $data['daily_price'] ) ) {
		$lines[] = sprintf(
			/* translators: %s: formatted price */
			__( 'Daily rate: AED %s', 'tenku-child' ),
			number_format_i18n( (int) $data['daily_price'] )
		);
	}

	$specs = array();
	if ( ! empty( $data['engine_type'] ) ) {
		$specs[] = sprintf( __( 'Engine: %s', 'tenku-child' ), $data['engine_type'] );
	}
	if ( ! empty( $data['acceleration'] ) ) {
		$specs[] = sprintf( __( '0-100 km/h: %s', 'tenku-child' ), $data['acceleration'] );
	}
	if ( ! empty( $data['doors'] ) ) {
		$specs[] = sprintf( __( 'Doors: %s', 'tenku-child' ), $data['doors'] );
	}
	if ( ! empty( $data['seats'] ) ) {
		$specs[] = sprintf( __( 'Seats: %s', 'tenku-child' ), $data['seats'] );
	}
	if ( $specs ) {
		$lines[] = implode( ' | ', $specs );
	}

	if ( ! empty( $data['permalink'] ) ) {
		$lines[] = '';
		$lines[] = $data['permalink'];
	}

	return $lines;
}

/**
 * Prefilled message for a fleet vehicle (plain text with newlines).
 *
 * @param array $data From vip_transits_get_vehicle_card_data().
 * @return string
 */
function vip_transits_vehicle_whatsapp_message( array $data ) {
	return implode( "\n", vip_transits_vehicle_whatsapp_lines( $data ) );
}

/**
 * WhatsApp URL for a vehicle post.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function vip_transits_vehicle_whatsapp_url( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	if ( ! $post_id ) {
		return '';
	}

	return vip_transits_whatsapp_href( vip_transits_vehicle_whatsapp_lines( vip_transits_get_vehicle_card_data( $post_id ) ) );
}

/**
 * Vehicle WhatsApp href attribute (use in templates instead of esc_url).
 *
 * @param int $post_id Post ID.
 * @return string
 */
function vip_transits_vehicle_whatsapp_href_attr( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	if ( ! $post_id ) {
		return '';
	}

	return vip_transits_whatsapp_href_attr( vip_transits_vehicle_whatsapp_lines( vip_transits_get_vehicle_card_data( $post_id ) ) );
}

/**
 * Prefilled message for the homepage CTA banner.
 *
 * @param string $heading Banner heading.
 * @param string $text    Banner text.
 * @return string
 */
function vip_transits_cta_whatsapp_message( $heading, $text ) {
	$parts = array();
	$heading = trim( (string) $heading );
	$text    = trim( (string) $text );
	if ( $heading !== '' ) {
		$parts[] = $heading;
	}
	if ( $text !== '' ) {
		$parts[] = $text;
	}
	return $parts;
}

/**
 * Apply Settings Instagram URL and open header/footer Instagram icons in a new tab.
 *
 * @param string $block_content Rendered block HTML.
 * @param array  $block         Block data.
 * @return string
 */
function vip_transits_render_instagram_social_link( $block_content, $block ) {
	if ( ( $block['blockName'] ?? '' ) !== 'core/social-link' ) {
		return $block_content;
	}

	$service = isset( $block['attrs']['service'] ) ? (string) $block['attrs']['service'] : '';
	if ( 'instagram' !== $service ) {
		return $block_content;
	}

	$url = vip_transits_get_instagram_url();
	if ( $url !== '' ) {
		$updated = preg_replace( '/\shref=(["\'])[^"\']*\1/', ' href="' . esc_url( $url ) . '"', $block_content, 1 );
		if ( is_string( $updated ) ) {
			$block_content = $updated;
		}
	}

	return vip_transits_add_link_targets_to_html( $block_content );
}
add_filter( 'render_block', 'vip_transits_render_instagram_social_link', 10, 2 );
