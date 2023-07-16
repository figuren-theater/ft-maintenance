<?php
/**
 * Figuren_Theater Maintenance Query_Monitor.
 *
 * @package figuren-theater/ft-maintenance
 */

namespace Figuren_Theater\Maintenance\Query_Monitor;

use Figuren_Theater;

use FT_VENDOR_DIR;
use function add_action;

const BASENAME   = 'query-monitor/query-monitor.php';
const PLUGINPATH = '/johnbillion/' . BASENAME;

/**
 * This could be used for debugging
 *
 * Or could be used exclusively via maintenance-mode
 *
 * defined( 'QM_SHOW_ALL_HOOKS' ) ?: define( 'QM_SHOW_ALL_HOOKS', true );
 * defined( 'QM_ENABLE_CAPS_PANEL' ) ?: define( 'QM_ENABLE_CAPS_PANEL', true );
 */

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin', 0 );
}

/**
 * Conditionally load the plugin itself and its modifications.
 *
 * @return void
 */
function load_plugin() {

	$config = Figuren_Theater\get_config()['modules']['maintenance'];
	if ( ! $config['query-monitor'] ) {
		return;
	}

	require_once FT_VENDOR_DIR . PLUGINPATH; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant
}
