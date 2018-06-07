<?php
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

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'bds');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', '192.168.1.41');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         't4LUtTT:#oXpkrhO2n7fHMZL3O/$|NhLv+Da&5Bb;!RdtCS)CA9LzQ52Wa_r,{S&');
define('SECURE_AUTH_KEY',  '/>t!@&AUUE{oJb~]lcW079Qw<M:/1z>*u*,886(ym%Znn=218ws2RN |%]|=/ez:');
define('LOGGED_IN_KEY',    '/6#3$2S:czpw!lofpBDF+[ Vsy+k;W<weGA~n7]SyV224T-`@.ja@xpBUl/Uc6nA');
define('NONCE_KEY',        'bvd-LaXW7U.*xh5Ffu_bYE.&e9qgddYc0dkQos1J3yjoCA,2DpB8|FrF9-Hdu|UZ');
define('AUTH_SALT',        'Kdq`}q#1?h?4Vv6+$WjX0{^d4rD^~$3o>.qb*<<EJt RFm4yeCULuTB|)N7Ac,wa');
define('SECURE_AUTH_SALT', '$q%%=}i|zt%yH /+N;/`tPU:+3+:=|!u*@3[#7&/[u .P`w<S;#Rmj!0 dY(@Fo$');
define('LOGGED_IN_SALT',   '^qQ5dWXWIdK`8i=XfcxWKP6}y(yj|Nsdw_wWR^X2<.Z?rn1y10N+~M:x7O 99<2D');
define('NONCE_SALT',       'qvu(?R3J%iO.!X~,AdfIQ2s?VsXwJ3bF=j,w/S$_OfbU[X,K`&tH.ZY-=K-Rf!u|');

/**#@-*/

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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
