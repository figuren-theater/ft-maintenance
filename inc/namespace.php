<?php
/**
 * Figuren_Theater Maintenance.
 *
 * @package figuren-theater/ft-maintenance
 */

namespace Figuren_Theater\Maintenance;

use Altis;
use DISABLE_WP_CRON;

use WP_DEBUG;
use WP_ENVIRONMENT_TYPE;

/**
 * Register module.
 *
 * @return void
 */
function register() :void {

	$default_settings = [
		'enabled'        => true, // Needs to be set.
		'query-monitor'  => WP_DEBUG,
		'wp-crontrol'    => WP_DEBUG,
		'wp-cron-runner' => DISABLE_WP_CRON,
		'wp-db-backup'   => ( 'production' === WP_ENVIRONMENT_TYPE ),
		'wp-sync-db'     => true,
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
 *
 * @return void
 */
function bootstrap() :void {

	// Plugins.
	Multisite_Enhancements\bootstrap();
	Query_Monitor\bootstrap();
	WP_Crontrol\bootstrap();
	WP_Cron_Runner\bootstrap();
	WP_DB_Backup\bootstrap();
	WP_Sync_DB\bootstrap();

	// Best practices.
	Blog_Management\bootstrap();
	Dashboard_Widget\bootstrap();
	Mode\bootstrap();
}
