<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wp_forevent' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'FosZ=SlZ|;^Zn3lBwu3K=nnf#II6wNsZeawdYsc[[*y_E,G]y%D( =h/^fy+6Vl=' );
define( 'SECURE_AUTH_KEY',  'xP_9pj;H(ksPO+cHh_1{Z6E>r#m;V]ccO.Lz+?($7t8*bQ3Ut;$M,$tfH+%?NY@J' );
define( 'LOGGED_IN_KEY',    'FBIR$+uzn,qi2-_;((.biF&Hv2gJu<L)c4H5ZB R_w=GnKqUdS{p8&/cqZZnGY*?' );
define( 'NONCE_KEY',        'WH=,r,4-6*aH>J@o?{3p[Lo.6/YQ@0+I<2;PHX9)GrP$R_ON!Q93HkG>cfzV2Kr@' );
define( 'AUTH_SALT',        '!g_pAs{]FZKs&NoN/9(UKZ;X5#%P#ve;XpJ5h@H:=C5X1: J4MGjM-w)9&AuF5CX' );
define( 'SECURE_AUTH_SALT', 'iz-o=[`M]B`IXp24%v?6+`9_)>]9z1*c1{s>R]J0JyN7mrz=(_{VsXp7#<4.`dsE' );
define( 'LOGGED_IN_SALT',   'yGrn?+Sx)@ToH/h4O+R)G G=>.eHJ2(RK[o*:>?,!K@~,#{k+rh9H3DKiHmYUt%t' );
define( 'NONCE_SALT',       'wQL:##~gVz#F3__UB:AT6hhCc#|>-8IQOGxF?DMbP1{KXue[bx]Ww-X/6la&5R[f' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
define('SAVEQUERIES', true);

define('FS_METHOD', 'direct');

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
