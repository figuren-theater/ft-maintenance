<?php
/**
 * Figuren_Theater Maintenance WP_Crontrol.
 *
 * @package figuren-theater/maintenance/wp_crontrol
 */

namespace Figuren_Theater\Maintenance\WP_Crontrol;

use FT_VENDOR_DIR;

use Figuren_Theater;
use function Figuren_Theater\get_config;

use function add_action;
use function current_user_can;
use function remove_action;

const BASENAME   = 'wp-crontrol/wp-crontrol.php';
const PLUGINPATH = FT_VENDOR_DIR . '/johnbillion/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin', 0 );
}

function load_plugin() {

	$config = Figuren_Theater\get_config()['modules']['maintenance'];
	if ( ! $config['wp-crontrol'] )
		return; // early

	require_once PLUGINPATH;

	add_action( 'admin_menu', __NAMESPACE__ . '\\remove_admin_menus', 0 );
}


function remove_admin_menus() {
	if ( current_user_can( 'manage_sites' ))
		return;

	// Remove Submenu from 'Settings' and 
	// Submenu from 'Tools'
	remove_action( 'admin_menu', 'Crontrol\\action_admin_menu' );
}
