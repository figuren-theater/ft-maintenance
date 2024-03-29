<?php
/**
 * Figuren_Theater Maintenance Dashboard_Widget.
 *
 * @package figuren-theater/ft-maintenance
 */

namespace Figuren_Theater\Maintenance\Dashboard_Widget;

use WP_CONTENT_DIR;
use WP_Filesystem;
use function add_action;
use function admin_url;
use function balanceTags;
use function current_user_can;
use function esc_url;
use function request_filesystem_credentials;
use function site_url;
use function wp_add_dashboard_widget;
use function wp_nonce_url;
use function wp_verify_nonce;

/**
 * Get name of log file to show, depending on WP_DEBUG set or not.
 *
 * @return string
 */
function get_logfile_type(): string {
	return ( defined( 'WP_DEBUG' ) ) ? 'debug' : 'error';
}

/**
 * Get location of logfile relative to "wp-content" directory.
 *
 * @return string
 */
function get_logfile_location(): string {
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
function get_logfile_path(): string {
	return WP_CONTENT_DIR . get_logfile_location();
}

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap(): void {

	add_action( 'wp_dashboard_setup', __NAMESPACE__ . '\\add_widget' );
	add_action( 'wp_network_dashboard_setup', __NAMESPACE__ . '\\add_widget' );
}

/**
 * Adds a new dashboard widget with the currently relevant log file.
 *
 * @return void
 */
function add_widget(): void {

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
 * Erases the content of the given file, 
 * without deleting the file itself.
 * 
 * @see https://developer.wordpress.org/apis/filesystem/#tips-and-tricks
 * 
 * @param string $filename Absolute path to the file to erase.
 *
 * @return bool Whether the content-deletion was succesful or not.
 */
function clear_log_file( string $filename ): bool {

	// Don't have direct write access. Maybe prompt user with a notice later ...
	$access_type = get_filesystem_method();
	if ( $access_type !== 'direct' ) {
		return false;   
	}
	
	// We can safely run request_filesystem_credentials() without any issues and don't need to worry about passing in a URL.
	$creds = request_filesystem_credentials( 
		site_url() . '/wp-admin/',
		'',
		false,
		'',
		array()
	);
	
	// Initialize the API. 
	// @phpstan-ignore-next-line Ignore crazy boolean error, propably caused by the core coc-blocks.
	if ( ! WP_Filesystem( $creds ) ) {
		return false;
	}
	global $wp_filesystem;
	
	// Do our file manipulations and erase content of the file.
	return $wp_filesystem->put_contents(
		$filename,
		'',           // Empty content of the file.
		FS_CHMOD_FILE // Predefined mode settings for WP files.
	);
}

/**
 *
 * Log-file monitoring as dashboard widget
 *
 * Reads and displays the first x (1000 by default) lines from php_error.log
 * or debug.log if WP_DEBUG is enabled.
 *
 * @todo #44  clean this up even more. This is still a mess from 1995.
 *
 * @since 0.0.1
 *
 * @return void
 */
function render_widget(): void {

	// The maximum number of errors to display in the widget.
	$error_display_limit = 1000;

	// The maximum number of characters to display for each error.
	$error_length_limit = 1000;

	$file_cleared = false;

	$wp_debug_log = get_logfile_path();

	if ( empty( $wp_debug_log ) ) {
		return;
	}

	/**
	 * Clear the log file.
	 *
	 * Protect nonce with current_user_can() check.
	 *
	 * Make sure current user can delete log files,
	 * verify nonce and then perform action.
	 *
	 * Verifying nonce with sanitizing as per WPCS.
	 */
	if ( current_user_can( 'manage_sites' ) &&
		isset( $_GET['ft_maintenance_dw_cl'] ) &&
		wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['ft_maintenance_dw_cl'] ) ), 'clear-logfile' )
	) {
		$file_cleared = clear_log_file( $wp_debug_log );
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

	// Build URL for clearing the logfile.
	// Add nonce to the URL.
	$clear_logfile_url = wp_nonce_url( admin_url(), 'clear-logfile', 'ft_maintenance_dw_cl' );

	echo '.';

	echo ' [ <b><a href="' . esc_url( $clear_logfile_url ) . '" onclick="return;">CLEAR LOG FILE</a></b> ]';
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

		++$i;
		if ( $i > $error_display_limit ) {
			echo esc_html( '<li class="howto">More than ' . $error_display_limit . ' errors in log...</li>' );
			break;
		}
	}
	echo '</ol></div>';
}
