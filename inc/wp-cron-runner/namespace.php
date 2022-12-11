<?php
/**
 * Figuren_Theater Maintenance WP_Cron_Runner.
 *
 * @package figuren-theater/maintenance/wp_cron_runner
 */

namespace Figuren_Theater\Maintenance\WP_Cron_Runner;

use FT_VENDOR_DIR;

use Figuren_Theater;
use function Figuren_Theater\get_config;

use function add_action;

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

	require_once PLUGINPATH;
}
