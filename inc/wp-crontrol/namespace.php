<?php
/**
 * Figuren_Theater Maintenance WP_Crontrol.
 *
 * @package figuren-theater/ft-maintenance
 */

namespace Figuren_Theater\Maintenance\WP_Crontrol;

use Figuren_Theater;

use FT_VENDOR_DIR;
use function add_action;

use function current_user_can;
use function is_admin;
use function remove_action;
use function wp_doing_ajax;
use function wp_doing_cron;

const BASENAME   = 'wp-crontrol/wp-crontrol.php';
const PLUGINPATH = '/johnbillion/' . BASENAME;

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
	if ( ! $config['wp-crontrol'] ) {
		return;
	}

	if ( ! is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
		return;
	}

	require_once FT_VENDOR_DIR . PLUGINPATH; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant

	add_action( 'admin_menu', __NAMESPACE__ . '\\remove_admin_menus', 0 );
}

/**
 * Remove 'wp-crontrol' Submenu from 'Settings' and Submenu from 'Tools'
 *
 * @return void
 */
function remove_admin_menus() {
	if ( current_user_can( 'manage_sites' ) ) {
		return;
	}

	remove_action( 'admin_menu', 'Crontrol\\action_admin_menu' );
}
