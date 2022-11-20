<?php
/**
 * Displays the PHP error template and sends the HTTP status code, typically 500.
 *
 * A drop-in 'php-error.php' can be used as a custom template. This drop-in should control the HTTP status code and
 * print the HTML markup indicating that a PHP error occurred. Note that this drop-in may potentially be executed
 * very early in the WordPress bootstrap process, so any core functions used that are not part of
 * `wp-includes/load.php` should be checked for before being called.
 *
 * If no such drop-in is available, this will call {@see WP_Fatal_Error_Handler::display_default_error_template()}.
 *
 * This loads the common error-file, which is used for the maintenance mode,
 * php-errors and db-errors and based on 'Smart_errors'.
 *
 * What it does?
 *
 * 1. Send a 500 server response to let the client know that they should try back later
 * 2. Send a 500 status response to let the client know that they should try back later
 * 3. Send a Retry-After header to tell the client when to try again (specified in seconds)
 * 4. (Suppress Email)
 *
 * @package Figuren_Theater
 * @since   2.10.17
 * @author  Carsten Bach  <mail@carsten-bach.de>
 */

namespace Figuren_Theater\Maintenance\Mode;

const TEMPLATE = __DIR__ . '/mu-plugins/FT/ft-maintenance/inc/mode/error-template.php';

define( 'FT_ERROR_MAIL_TO', 'f.t web-Crew <' . getenv( 'FT_ERROR_MAIL_TO' ) . '>' );
define( 'FT_ERROR_MAIL_FROM', getenv( 'FT_ERROR_MAIL_TO' ) );
define( 'FT_ERROR_MAIL_INTERVAL', 300 );
define( 'FT_ERROR_SUPPRESS_EMAIL', true );

define( 'FT_ERROR_STATUS', '500' );

require TEMPLATE;
