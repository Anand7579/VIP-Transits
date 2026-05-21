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
					'If Custom Fields → Sync does nothing, import field groups from the theme JSON files here (tenku-child/acf-json). Use this after deploying theme updates.',
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
					esc_html__( 'You can also use Custom Fields → Field Groups when “Sync available” appears: %s', 'tenku-child' ),
					'<a href="' . esc_url( admin_url( 'edit.php?post_type=acf-field-group' ) ) . '">' . esc_html__( 'open field groups', 'tenku-child' ) . '</a>'
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
 * Full WhatsApp URL with optional prefilled message.
 *
 * @param string $message Plain-text message (newlines allowed).
 * @return string Escaped URL or empty string.
 */
function vip_transits_whatsapp_href( $message = '' ) {
	$base = vip_transits_whatsapp_base_url();
	if ( $base === '' ) {
		return '';
	}

	$message = trim( (string) $message );
	if ( $message === '' ) {
		return esc_url( $base );
	}

	$sep = str_contains( $base, '?' ) ? '&' : '?';

	return esc_url( $base . $sep . 'text=' . rawurlencode( $message ) );
}

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
	$title = trim( (string) $title );
	$desc  = trim( (string) $desc );

	if ( $title !== '' && $desc !== '' ) {
		return $title . ' — ' . $desc;
	}

	return $title !== '' ? $title : $desc;
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
 * Prefilled message for a fleet vehicle.
 *
 * @param array $data From vip_transits_get_vehicle_card_data().
 * @return string
 */
function vip_transits_vehicle_whatsapp_message( array $data ) {
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
		$lines[] = $data['permalink'];
	}

	return implode( "\n", $lines );
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

	return vip_transits_whatsapp_href( vip_transits_vehicle_whatsapp_message( vip_transits_get_vehicle_card_data( $post_id ) ) );
}

/**
 * Prefilled message for the homepage CTA banner.
 *
 * @param string $heading Banner heading.
 * @param string $text    Banner text.
 * @return string
 */
function vip_transits_cta_whatsapp_message( $heading, $text ) {
	$parts = array_filter( array( trim( (string) $heading ), trim( (string) $text ) ) );
	return implode( "\n", $parts );
}
