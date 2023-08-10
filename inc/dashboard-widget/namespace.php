<?php
/**
 * Figuren_Theater Maintenance Dashboard_Widget.
 *
 * @package figuren-theater/ft-maintenance
 */

namespace Figuren_Theater\Maintenance\Dashboard_Widget;

use function add_action;

use function balanceTags;
use function current_user_can;
use function wp_add_dashboard_widget;
use WP_DEBUG_LOG;

// const VIEW = ( defined( 'WP_DEBUG' ) ) ? 'debug' : 'error';
const VIEW = 'error';
// const VIEW = 'debugORerror';
const FILE = '/logs/php.' . VIEW . '.log';

// const LOG = constant( 'WP_CONTENT_DIR' ) . FILE;
const LOG = \WP_DEBUG_LOG;

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap() :void {

	add_action( 'wp_dashboard_setup', __NAMESPACE__ . '\\add_widget' );
	add_action( 'wp_network_dashboard_setup', __NAMESPACE__ . '\\add_widget' );
}

function add_widget() {

	if ( ! current_user_can( 'manage_sites' ) ) {
		return;
	}

	/*
	 * Wrap title in sourrounding span
	 *
	 * which helps preventing a layout bug with WordPress
	 * default '.postbox-header .hndle' class
	 * which sets: "justify-content: space-between;"
	 */
	wp_add_dashboard_widget(
		'cbstdsys-php-errorlog',
		sprintf(
			'<span>%s Log (%s)</span>',
			ucfirst( VIEW ),
			'<abbr title="' . LOG . '">...' . FILE . '</abbr>'
		),
		__NAMESPACE__ . '\\render_widget'
	);
}

/**
 *
 * Log-file monitoring as dashboard widget
 *
 * Reads and displays the first x (1000 by default) lines from php_error.log
 * or debug.log if WP_DEBUG is enabled.
 *
 *  @todo  clean this up even more. This is still a mess from 1995.
 *
 *  @since 0.0.1
 */
function render_widget() {

	// The maximum number of errors to display in the widget.
	$error_display_limit = 1000;

	// The maximum number of characters to display for each error.
	$error_length_limit = 1000;

	$file_cleared = false;

	// Clear file?
	if ( isset( $_GET['cbstdsys-php-errorlog'] ) && $_GET['cbstdsys-php-errorlog'] === 'clear' ) {
		$handle = fopen( LOG, 'w' );
		fclose( $handle );
		$file_cleared = true;
	}
	// Read from file.
	if ( ! file_exists( LOG ) ) {
		echo '<p><em>There was a problem reading the error log file.</em></p>';
	}

	$errors = file( LOG );
	$errors = array_reverse( $errors );

	if ( $file_cleared ) {
		echo '<p><em>File cleared.</em></p>';
	}

	if ( ! $errors ) {
		echo '<p>No errors currently logged.</p>';
	}

	echo '<p>' . count( $errors ) . ' error';
	if ( $errors !== 1 ) {
		echo 's';
	}
	echo '.';

	echo ' [ <b><a href="?cbstdsys-php-errorlog=clear" onclick="return;">CLEAR LOG FILE</a></b> ]';
	echo '</p>';

	echo '<div id="cbstdsys-php-errorlog" style="height:250px;overflow-y:scroll;padding:2px;background-color:#faf9f7;border:1px solid #ccc;">';
	echo '<ol style="padding:0;margin:0;">';

	$i = 0;
	foreach ( $errors as $error ) {
		$error = esc_html( $error );
		echo '<li style="padding:2px 4px 6px;border-bottom:1px solid #ececec;">';
		$error_output = preg_replace( '/\[([^\]]+)\]/', '<b>[$1]</b>', $error, 1 );
		if ( strlen( $error_output ) > $error_length_limit ) {
			$error_output = substr( $error_output, 0, $error_length_limit ) . ' [&hellip;]';
		}
		echo esc_html( balanceTags( $error_output, true ) );
		echo '</li>';
		$i++;
		if ( $i > $error_display_limit ) {
			echo esc_html( '<li class="howto">More than ' . $error_display_limit . ' errors in log...</li>' );
			break;
		}
	}
	echo '</ol></div>';
}
