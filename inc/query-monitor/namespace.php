<?php
/**
 * Figuren_Theater Maintenance Query_Monitor.
 *
 * @package figuren-theater/maintenance/query_monitor
 */

namespace Figuren_Theater\Maintenance\Query_Monitor;

use FT_VENDOR_DIR;

use Figuren_Theater;
use function Figuren_Theater\get_config;

use function add_action;

const BASENAME   = 'query-monitor/query-monitor.php';
const PLUGINPATH = FT_VENDOR_DIR . '/johnbillion/' . BASENAME;


// defined( 'QM_SHOW_ALL_HOOKS' ) ?: define( 'QM_SHOW_ALL_HOOKS', true );
// defined( 'QM_ENABLE_CAPS_PANEL' ) ?: define( 'QM_ENABLE_CAPS_PANEL', true );

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin', 0 );
}

function load_plugin() {

	$config = Figuren_Theater\get_config()['modules']['maintenance'];
	if ( ! $config['query-monitor'] )
		return; // early

	require_once PLUGINPATH;
}
