<?php
/**
 * Figuren_Theater Maintenance WP_Cron_Runner.
 *
 * @package figuren-theater/ft-maintenance
 */

namespace Figuren_Theater\Maintenance\WP_Cron_Runner;

use Figuren_Theater;

use FT_VENDOR_DIR;

use function add_action;

use function remove_submenu_page;

const BASENAME   = 'wp-cron-runner/plugin.php';
const PLUGINPATH = '/devgeniem/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	add_action( 'muplugins_loaded', __NAMESPACE__ . '\\load_plugin', 0 );
}

/**
 * Conditionally load the plugin itself and its modifications.
 *
 * @return void
 */
function load_plugin() {

	$config = Figuren_Theater\get_config()['modules']['maintenance'];
	if ( ! $config['wp-cron-runner'] ) {
		return;
	}

	defined( 'WP_ALLOW_MULTISITE' ) || define( 'WP_ALLOW_MULTISITE', true ); // Needed by devgeniem/wp-cron-runner.

	require_once FT_VENDOR_DIR . PLUGINPATH; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant

	add_action( 'network_admin_menu', __NAMESPACE__ . '\\remove_menu', 11 );
}

/**
 * Remove native WPs "Setup a Network"-Menu
 * which is populated by the existence of WP_ALLOW_MULTISITE.
 *
 * @return void
 */
function remove_menu() : void {
	remove_submenu_page( 'settings.php', 'setup.php' );
}
