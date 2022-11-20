<?php
/**
 * Figuren_Theater Maintenance Dashboard_Widget.
 *
 * @package figuren-theater/maintenance/dashboard_widget
 */

namespace Figuren_Theater\Maintenance\Dashboard_Widget;

use WP_DEBUG;
use WP_CONTENT_DIR;

use function add_action;
use function admin_url;
use function balanceTags;
use function current_user_can;
use function wp_add_dashboard_widget;

const VIEW = ( WP_DEBUG ) ? 'debug' : 'error';
const LOG  = WP_CONTENT_DIR . '/logs/php.' . VIEW . '.log';

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	// 
	add_action( 'wp_dashboard_setup', __NAMESPACE__ . '\\add_widget' );
	add_action( 'wp_network_dashboard_setup', __NAMESPACE__ . '\\add_widget' );
}

function add_widget() {

	if ( ! current_user_can( 'manage_sites' ))
		return;

	wp_add_dashboard_widget( 
		'cbstdsys-php-errorlog',
		// 'Debug Log (/wp-content/logs/php.debug.log)',
		sprintf(
			'%s Log (%s)',
			ucfirst( VIEW ),
			'<code>' . LOG . '</code>'
		),
		__NAMESPACE__ . '\\render_widget'
	);
}


/**
 *  @todo  clean this up even more. This is still a mess from 1995.
 *  
 *  server monitoring as dashboard widget
 *  reads php_error.log or debug.log if WP_DEBUG is TRUE
 *  
 *  @since 0.0.1
 */
function render_widget() {

	// The maximum number of errors to display in the widget
	$displayErrorsLimit = 1000;
	
	// The maximum number of characters to display for each error
	$errorLengthLimit = 1000;
	
	$fileCleared = false;

	// Clear file?
	if ( isset( $_GET["cbstdsys-php-errorlog"] ) && $_GET["cbstdsys-php-errorlog"]=="clear" ) {
		$handle = fopen( LOG, "w" );
		fclose( $handle );
		$fileCleared = true;
	}
	// Read file
	if ( ! file_exists( LOG ) ) {
		echo '<p><em>There was a problem reading the error log file.</em></p>';
	}

	$errors = file( LOG );
	$errors = array_reverse( $errors );
	
	if ( $fileCleared )
		echo '<p><em>File cleared.</em></p>';

	if ( ! $errors )
		echo '<p>No errors currently logged.</p>';

	echo '<p>'.count( $errors ).' error';
	if ( $errors != 1 )
		echo 's';
	echo '.';

	// echo ' [ <b><a href="'.admin_url('?cbstdsys-php-errorlog=clear').'" onclick="return;">CLEAR LOG FILE</a></b> ]';
	echo ' [ <b><a href="?cbstdsys-php-errorlog=clear" onclick="return;">CLEAR LOG FILE</a></b> ]';
	echo '</p>';
	
	echo '<div id="cbstdsys-php-errorlog" style="height:250px;overflow-y:scroll;padding:2px;background-color:#faf9f7;border:1px solid #ccc;">';
	echo '<ol style="padding:0;margin:0;">';
	
	$i = 0;
	foreach ( $errors as $error ) {
		echo '<li style="padding:2px 4px 6px;border-bottom:1px solid #ececec;">';
		$errorOutput = preg_replace( '/\[([^\]]+)\]/', '<b>[$1]</b>', $error, 1 );
		if ( strlen( $errorOutput ) > $errorLengthLimit ) {
			$errorOutput = substr( $errorOutput, 0, $errorLengthLimit ).' [&hellip;]';
		}
		echo balanceTags( $errorOutput, true );
		echo '</li>';
		$i++;
		if ( $i > $displayErrorsLimit ) {
			echo '<li class="howto">More than '.$displayErrorsLimit.' errors in log...</li>';
			break;
		}
	}
	echo '</ol></div>';
}
