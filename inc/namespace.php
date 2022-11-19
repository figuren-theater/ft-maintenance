<?php
/**
 * Figuren_Theater Maintenance.
 *
 * @package figuren-theater/maintenance
 */

namespace Figuren_Theater\Maintenance;

use Altis;
use function Altis\register_module;


/**
 * Register module.
 */
function register() {

	$default_settings = [
		'enabled' => true, // needs to be set
		'query-monitor' => WP_DEBUG,
		'wp-crontrol' => WP_DEBUG,
		'wp-sync-db' => [
		],
	];
	$options = [
		'defaults' => $default_settings,
	];

	Altis\register_module(
		'maintenance',
		DIRECTORY,
		'Maintenance',
		$options,
		__NAMESPACE__ . '\\bootstrap'
	);
}

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	// Plugins
	Query_Monitor\bootstrap();
	WP_Crontrol\bootstrap();
	// WP_Sync_DB\bootstrap();
	
	// Best practices
	Dashboard_Widget\bootstrap();
}


//////////////////
// UNUSED IDEAS //
//////////////////


function get_log_folder() : string {
	if ( defined('WP_DEBUG_LOG') ) {
		return dirname( WP_DEBUG_LOG );
	}

	return '';
}


function is_log_folder_secured() : bool {
	
	$log_folder_htaccess = get_log_folder() . '/.htaccess';
	if ( file_exists( $log_folder_htaccess ) ) {
		return true;
	}

	return false;
}

function secure_log_folder() : void {
	# code...
}
