<?php

/** 
 * Configuración básica de WordPress.
 *
 * Este archivo contiene las siguientes configuraciones: ajustes de MySQL, prefijo de tablas,
 * claves secretas, idioma de WordPress y ABSPATH. Para obtener más información,
 * visita la página del Codex{@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} . Los ajustes de MySQL te los proporcionará tu proveedor de alojamiento web.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** Ajustes de MySQL. Solicita estos datos a tu proveedor de alojamiento web. ** //
/** El nombre de tu base de datos de WordPress */
define('DB_NAME', 'guigovia_galicia');

/** Tu nombre de usuario de MySQL */
define('DB_USER', 'guigovia_web');

/** Tu contraseña de MySQL */
define('DB_PASSWORD', 'do~??9^V!}o=');

/** Host de MySQL (es muy probable que no necesites cambiarlo) */
define('DB_HOST', 'localhost');

/** Codificación de caracteres para la base de datos. */
define('DB_CHARSET', 'utf8mb4');

/** Cotejamiento de la base de datos. No lo modifiques si tienes dudas. */
define('DB_COLLATE', '');

/**#@+
 * Claves únicas de autentificación.
 *
 * Define cada clave secreta con una frase aleatoria distinta.
 * Puedes generarlas usando el {@link https://api.wordpress.org/secret-key/1.1/salt/ servicio de claves secretas de WordPress}
 * Puedes cambiar las claves en cualquier momento para invalidar todas las cookies existentes. Esto forzará a todos los usuarios a volver a hacer login.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', '/{v^.I~e7&~3_23o4vHY?,~vo(]XbQ[X#7<)Ghy$c+YTz%o |SbvAv$(=e1v}AC%');
define('SECURE_AUTH_KEY', 'CyqM*m!j#{Z];EAWwG4i3K0EY_~}%rD5*Z*Y~eDTLKy>;N3$JDO~SWcC@d=FtO+a');
define('LOGGED_IN_KEY', '9M[6TTy~<CYUBU*pDsAxeZ`$Sz$I!3BdH0Us!vaO+TVAZ)OllN SinxHB3#0Ug<@');
define('NONCE_KEY', 'b<<dr74{Yd2B{MMwVuTl;>*Z^V!iF)=7}Y>znN#B;)mPrtnyiw|ay$QyYi#dV5%1');
define('AUTH_SALT', '^c]Te?oO`]V%w/B +lI9)D*l2n[8k1>lJ$b}rz,DZ:YebIFaa7@gZ#[e0];ERb/d');
define('SECURE_AUTH_SALT', '#8rMhfZ@CW!yt^S:-5`_C.!iMpX9(=jb0-G8l1=s%nZd.x.9,m2dP@[!I6a6*gp(');
define('LOGGED_IN_SALT', '41R!u.QZLl?D6syLbnS#ZUzDkKlH<Os*cW7L=sW5+J9FDq7=;!j7q1!o>V;,skAJ');
define('NONCE_SALT', 'K RYEsJ5rUiUJr7hFHu[8(KltcP_3^ivnUYXEaMDZ6FfbP-t%zp`qh3-}[1-,2ZK');

/**#@-*/

/**
 * Prefijo de la base de datos de WordPress.
 *
 * Cambia el prefijo si deseas instalar multiples blogs en una sola base de datos.
 * Emplea solo números, letras y guión bajo.
 */
$table_prefix  = 'berlin_';


/**
 * Para desarrolladores: modo debug de WordPress.
 *
 * Cambia esto a true para activar la muestra de avisos durante el desarrollo.
 * Se recomienda encarecidamente a los desarrolladores de temas y plugins que usen WP_DEBUG
 * en sus entornos de desarrollo.
 */
define('WP_DEBUG', false);

/* ¡Eso es todo, deja de editar! Feliz blogging */

/** WordPress absolute path to the Wordpress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

ini_set( 'upload_max_filesize' , '256M' );
@ini_set( 'post_max_size', '256M');
@ini_set( 'memory_limit', '256M' );
@ini_set( 'max_execution_time', '300' );
@ini_set( 'max_input_time', '300' );
