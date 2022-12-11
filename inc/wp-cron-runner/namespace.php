<?php
/**
 * Figuren_Theater Maintenance WP_Cron_Runner.
 *
 * @package figuren-theater/maintenance/wp_cron_runner
 */

namespace Figuren_Theater\Maintenance\WP_Cron_Runner;

use FT_VENDOR_DIR;

use WP_ALLOW_MULTISITE;

use Figuren_Theater;
use function Figuren_Theater\get_config;

use function add_action;
use function remove_submenu_page;

const BASENAME   = 'wp-cron-runner/plugin.php';
const PLUGINPATH = FT_VENDOR_DIR . '/devgeniem/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	add_action( 'muplugins_loaded', __NAMESPACE__ . '\\load_plugin', 0 );
}

function load_plugin() {

	$config = Figuren_Theater\get_config()['modules']['maintenance'];
	if ( ! $config['wp-cron-runner'] )
		return; // early

	defined( 'WP_ALLOW_MULTISITE' ) || define( 'WP_ALLOW_MULTISITE', true ); // needed by devgeniem/wp-cron-runner

	require_once PLUGINPATH;

	add_action( 'network_admin_menu', __NAMESPACE__ . '\\remove_menu', 11 );
}


function remove_menu() : void {
	// remove native WPs "Setup a Network"-Menu
	// which is populated by the existence of WP_ALLOW_MULTISITE
	remove_submenu_page( 'settings.php', 'setup.php' );
}
