<?php
define( 'WP_CACHE', true );
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
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'shop' );

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
define( 'AUTH_KEY',         '4BE{S8^xpU1[W zPZCZqLxrS)+M=VrakA-_)1-TqiBIZ+{U7JW}(lnW_IXjbe S]' );
define( 'SECURE_AUTH_KEY',  'R7R4>YQ&iPQ7O/Z==(3!Idq(a*b%&&JwUb)A*M[.^MG+oh6RM 82P!~n~oM+/)Pp' );
define( 'LOGGED_IN_KEY',    '!F`p/}4v1zmZR>;33x%X}!X}8qTWGjKue86BCfG}s3]Y?@{YxblXugQR6t3uZsf7' );
define( 'NONCE_KEY',        'Q#MF>s@E),`,bKd6ji?j:GELO-4X1lXJ-L*r->%)O9y_c~!*^2H$K6sk1bpjIcY,' );
define( 'AUTH_SALT',        '^VG-&&0[cb}c]S#1r$Juei{I|X{,;p0KeN~G[rk7+K`)!So yxx$fm3j`NwTs~=k' );
define( 'SECURE_AUTH_SALT', 'W(|qhveL3ZdH ,ZGY&>rM7UAA;b CI_EPBq*gMrW,kDp0YAoxUh[UKgH&eE[hdf?' );
define( 'LOGGED_IN_SALT',   'hSeKuTvQ%g1DX&,Jak0ab=&k.0#=E =N0VGq^m&,i6yRhRLxD@(b/+Rs:`D++9<;' );
define( 'NONCE_SALT',       '<-+olhQ)40DV-U5)ZI{@}5o}HJamj Wt*Wr&FU5_HQrs(dQ:h[=IAj2(M-/v=x&y' );

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
