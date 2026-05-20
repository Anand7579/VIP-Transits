<?php
define( 'WP_CACHE', true );

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

/**
 * Database connection information is automatically provided.
 * There is no need to set or change the following database configuration
 * values:
 *   DB_HOST
 *   DB_NAME
 *   DB_USER
 *   DB_PASSWORD
 *   DB_CHARSET
 *   DB_COLLATE
 */

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */

define('AUTH_KEY',         '9e>|DSR;*}k]srZ9FGN=q8e8S~ZZm[RCpcl#Pw7JdtDW?P+9c~6}cHc6gVw5baDv');
define('SECURE_AUTH_KEY',  '9Z3:m.7.z(t0U.*h;R*h]hiaa6H{L6v4RP6+?Q,L3A<JflhM@?n<T%8j@QSe7Wvf');
define('LOGGED_IN_KEY',    '-GP7WEYH1x|-OMHMjo9W{F)XqskEt{jqKt5[%v@$;Zlrpe;3s-;<z$M+YA)}O+R~');
define('NONCE_KEY',        '6jvIad==G*n;N;0MaX6p}x1?}?V#C@Li6z!qj$D!+otatJjJa*!tTqUP7Xs>bv,e');
define('AUTH_SALT',        '0?uEd!cS*a$YVZZNDgRI^*{9S.[vDow+|^|<;c?|+G}}Bi<+W*C0+8icgg=-ab;T');
define('SECURE_AUTH_SALT', '<5GQ+u[htY2=g:4G{|_>cRGKE{i8!#9^O$wGl,8xON<(Lnq^I}B3P#Yl@:I1?{Wl');
define('LOGGED_IN_SALT',   'V.CrA$uBp18=Jz2VdIuxr4]+X+=u4Ih2nDkyjBwuvLHy^azHs-Gu^5AbfH3v).dc');
define('NONCE_SALT',       '#pRUv0Hr_ec}RcWzubZ)?+>ko-,;.D-B|+Ag+z2AEwyp=rg2x>1kX@}U9[t?[$z7');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', true );
}
if ( ! defined( 'WP_DEBUG_LOG' ) ) {
	define( 'WP_DEBUG_LOG', true );
}
if ( ! defined( 'WP_DEBUG_DISPLAY' ) ) {
	define( 'WP_DEBUG_DISPLAY', false );
}

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
  define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
