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

/**
 * Get name of log file to show, depending on WP_DEBUG set or not.
 *
 * @return string
 */
function get_logfile_type() :string {
	return ( defined( 'WP_DEBUG' ) ) ? 'debug' : 'error';
}

/**
 * Get location of logfile relative to "wp-content" directory.
 *
 * @return string
 */
function get_logfile_location() :string {
	return \sprintf(
		'/logs/php.%s.log',
		get_logfile_type()
	);
}

/**
 * Get full absolute path of relevant logfile.
 *
 * @return string
 */
function get_logfile_path() :string {
	return \WP_CONTENT_DIR . get_logfile_location();
}

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap() :void {

	add_action( 'wp_dashboard_setup', __NAMESPACE__ . '\\add_widget' );
	add_action( 'wp_network_dashboard_setup', __NAMESPACE__ . '\\add_widget' );
}

/**
 * Adds a new dashboard widget with the currently relevant log file.
 *
 * @return void
 */
function add_widget() :void {

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
		\sprintf(
			'<span>%s Log (%s)</span>',
			\ucfirst( get_logfile_type() ),
			'<abbr title="' . get_logfile_path() . '">...' . get_logfile_location() . '</abbr>'
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
 * @todo  clean this up even more. This is still a mess from 1995.
 *
 * @since 0.0.1
 *
 * @return void
 */
function render_widget() :void {

	// The maximum number of errors to display in the widget.
	$error_display_limit = 1000;

	// The maximum number of characters to display for each error.
	$error_length_limit = 1000;

	$file_cleared = false;

	$wp_debug_log = get_logfile_path();

	if ( empty( $wp_debug_log ) ) {
		return;
	}

	// Clear file.
	// TODO #39 Fix "Processing form data without nonce verification." (WordPress.Security.NonceVerification.Recommended) !
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $_GET['cbstdsys-php-errorlog'] ) && $_GET['cbstdsys-php-errorlog'] === 'clear' ) {
		$handle = fopen( $wp_debug_log, 'w' );
		if ( false !== $handle ) {
			fclose( $handle );
			$file_cleared = true;
		}
	}
	// Read from file.
	if ( ! file_exists( $wp_debug_log ) ) {
		echo '<p><em>There was a problem reading the error log file.</em></p>';
	}

	$errors = (array) file( $wp_debug_log );
	$errors = array_reverse( $errors );

	if ( $file_cleared ) {
		echo '<p><em>File cleared.</em></p>';
	}

	if ( ! $errors ) {
		echo '<p>No errors currently logged.</p>';
	}

	echo '<p>' . count( $errors ) . ' error';
	if ( count( $errors ) > 1 ) {
		echo 's';
	}

	echo '.';

	echo ' [ <b><a href="?cbstdsys-php-errorlog=clear" onclick="return;">CLEAR LOG FILE</a></b> ]';
	echo '</p>';

	echo '<div id="cbstdsys-php-errorlog" style="height:250px;overflow-y:scroll;padding:2px;background-color:#faf9f7;border:1px solid #ccc;">';
	echo '<ol style="padding:0;margin:0;">';

	$i = 0;
	foreach ( $errors as $error ) {
		$error = esc_html( (string) $error );
		echo '<li style="padding:2px 4px 6px;border-bottom:1px solid #ececec;">';

		$error_output = (string) preg_replace( '/\[([^\]]+)\]/', '<b>[$1]</b>', $error, 1 );
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

