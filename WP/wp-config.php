<?php
//define('WP_AUTO_UPDATE_CORE', 'minor');// This setting is required to make sure that WordPress updates can be properly managed in WordPress Toolkit. Remove this line if this WordPress website is not managed by WordPress Toolkit anymore.
define( 'WP_AUTO_UPDATE_CORE', true );
define( 'WP_CACHE', false /* Modified by NitroPack */ );
//define( 'WP_CACHE', true );
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
define( 'DB_NAME', "boattheglobe" );

/** MySQL database username */
define( 'DB_USER', "boats_ca" );

/** MySQL database password */
define( 'DB_PASSWORD', "W!u6lu94" );

/** MySQL hostname */
define( 'DB_HOST', "localhost" );


/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname(__FILE__) . '/' );
}
//define( 'WP_TEMP_DIR', '/home/boattheg/public_html/wp-content/temp' );

define('WP_TEMP_DIR', ABSPATH . '/../temp/');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'BRa/=k9cCK2GUXM=H^)/t24YO[fr*gti,[2f$NMDv<!bvT~H]gqoE.0I&N-9@WZR' );
define( 'SECURE_AUTH_KEY',  '94`NkEE})F8.NsG(V!;LY/ZNnJuup-|OtFh]ff@)R{C7Ewi0pqY!&@br;LqOoAs5' );
define( 'LOGGED_IN_KEY',    'i*#1),*k;11gD3H.pl0Y(d9{B1_klAY[mCTAT3UnIJ1}GoxwU(0U_BoeXg%c7j;s' );
define( 'NONCE_KEY',        'rf^^pG{UW#Cns7JL,jU-FP V}0PYJ$8U?d@n<=&LT<{^0nN`Zx|(~*S/Zj*03*NI' );
define( 'AUTH_SALT',        'e|8 *&dcQ|<}T0&LK%w@h&n(_.bSyl,uY&g=jI;|Jt56<O=Ivik_*Aw)[eEzU>m^' );
define( 'SECURE_AUTH_SALT', 'L|;#.&*90az,?_xx?=wrvN2`BIgxIL9n**j81;;94l%WW:svzG<y=qW+B6u}9P}Q' );
define( 'LOGGED_IN_SALT',   'IZ*f![$XO-/&^~IxC1ei!XCaTe5G*pD_p4:x81woX=vj9ttcK-Wqb:B$.Xi#V)&K' );
define( 'NONCE_SALT',       '6qpXVNkP>zc%1hqydiNg#l7&yG[(8jf62@2GR}%#N_g:E@r<nT.a}I;-K+,;$&tR' );
#define('WP_TEMP_DIR', dirname(__FILE__) . '/wp-content/Temp/');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'rentx_';
//set_time_limit(300);
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


// Enable WP_DEBUG mode
define( 'WP_DEBUG', false );
 
// Enable Debug logging to the /wp-content/debug.log file
define( 'WP_DEBUG_LOG', true );
 
// Disable display of errors and warnings
define( 'WP_DEBUG_DISPLAY', true );
@ini_set( 'display_errors', 0 );
 
// Use dev versions of core JS and CSS files (only needed if you are modifying these core files)
define( 'SCRIPT_DEBUG', true );


//define('WPFC_AUTOMATIC_CACHE', true);

/* That's all, stop editing! Happy publishing. */



/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
