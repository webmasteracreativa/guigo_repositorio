<?php
define('WP_CACHE', false);
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
define('DB_NAME', 'guigovia_website');

/** MySQL database username */
define('DB_USER', 'guigovia_website');

/** MySQL database password */
define('DB_PASSWORD', '25yS][p00F');

/** MySQL hostname */
define('DB_HOST', 'localhost');

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
define('AUTH_KEY',         'undkzypwpyyifi0rykf0y9whz2fbqdmqgleezfwzniw92gzowmwav9vldfphnsr5');
define('SECURE_AUTH_KEY',  'wprrusap9nwc14ezdojule9cbns69g48du7s4jdq8oehlapawxbvafotfmrhuxjp');
define('LOGGED_IN_KEY',    'ip1hqaxatsybms0jpuzsanbzghy531tx086d7yaqryxrkmkpu54pddujkxcewhyy');
define('NONCE_KEY',        'psqlbzb4okvzwlh1ts1xniuwnocicko77uraml1wqzpqfz9ldux6h6geehpga2lb');
define('AUTH_SALT',        'c89opfuhe2qajassixilyaqbk6supbdca0pfylpo1mae6zxg5hnlq7jgr3dsjypq');
define('SECURE_AUTH_SALT', 'szzs8ovrydikdfmcil5fcq6blkkyfqvyor4oblyhbannug9njsqaddcdkkrz2kox');
define('LOGGED_IN_SALT',   '8flkt8vpizxtihz4vjirxtwdcxbi6lwgopj4vhfoipubo8nxxskusyg67roxeor3');
define('NONCE_SALT',       'gviuuxvmv3col3fb99qxtarv3ru5weermonoyo0e3hyndtk2eidvtjbgd5s8prw6');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'guigo_';

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
