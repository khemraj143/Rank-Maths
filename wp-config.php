<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'j+2kyGn=8u2;&ee26hMDM{8^V=Im@pmvsdK[&~},rV^AZHcK,Tb^0=BpxtJSQ|qh' );
define( 'SECURE_AUTH_KEY',   'eO@F2Zh d!<M?1d:VW(hzdw&R|sOCcrr4opU2VL8D]J;A)XI_0rNE^joh0{>g=Y=' );
define( 'LOGGED_IN_KEY',     '^`a?7w44>#}=1v>#4!)+2pK=}w0Sf+ahl.*6b!GA4w`}xrf2y7JvG%KB#-H79k#v' );
define( 'NONCE_KEY',         'n{q0OgV0!AMOWH?GA!PPYl.1Mi_ dky@y>jco4f13o0W $/MM7rKi7`i<9u$RdL-' );
define( 'AUTH_SALT',         'e;qjGyF{|+SWi3kp(*<tv|?T[0EVai~6E~Ao~=VCJE~[;4G? 0}I&m6]7L bthX_' );
define( 'SECURE_AUTH_SALT',  '$m$N6km/>X=-?1Y%8yd8hc5r$UIw(~-F.<}]JnmiFNUD#bQst&(E/(Q[/-q33EbX' );
define( 'LOGGED_IN_SALT',    'F<r)[%n  Sr|GV5#y `wn:4b#0N%C^<UQZl;V}j=:92?}<Z@JFVrosI<b#!r^a^[' );
define( 'NONCE_SALT',        'dxI.Ws=sw =#blaXJ8brd:H6>vGlw6,EPcQ~7F].YR,mDy| 0rE1b7j>)9D%@kDn' );
define( 'WP_CACHE_KEY_SALT', 'nv:z@>Q_t7oiwASPxnUVa%01f7[SvY9Oz=&^Rf}mmx}CZC>,89<(}&s;}(1-)Z >' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

define('JWT_AUTH_SECRET_KEY', 'cool-kids-auth');
define('JWT_AUTH_CORS_ENABLE', true);

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
