<?php
/**
 * The WordPress Core drop-in file db-error.php
 *
 * The pure existence of this file works
 * as a custom WordPress database error page.
 *
 * This loads the common error-file, which is used for the maintenance mode,
 * php-errors and db-errors and based on 'Smart_errors'.
 *
 * What it does?
 *
 * 1. Send a 503 server response to let the client know that they should try back later
 * 2. Send a 503 status response to let the client know that they should try back later
 * 3. Send a Retry-After header to tell the client when to try again (specified in seconds)
 * 4. Send an email to the site admin letting them know that “There is a problem with the database!”
 *
 * @package figuren-theater/ft-maintenance
 * @since   1.0.2
 * @author  Carsten Bach  <mail@carsten-bach.de>
 */

namespace Figuren_Theater\Maintenance\Mode;

define( 'FT_ERROR_MAIL_TO', 'f.t web-Crew <' . getenv( 'FT_ERROR_MAIL_TO' ) . '>' );
define( 'FT_ERROR_MAIL_FROM', getenv( 'FT_ERROR_MAIL_TO' ) );
define( 'FT_ERROR_MAIL_INTERVAL', 300 );
define( 'FT_ERROR_SUPPRESS_EMAIL', false );

require __DIR__ . '/mu-plugins/FT/ft-maintenance/inc/mode/error-template.php';
