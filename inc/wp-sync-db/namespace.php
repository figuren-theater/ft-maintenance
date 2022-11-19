<?php
/**
 * Figuren_Theater Maintenance WP_Sync_DB.
 *
 * @package figuren-theater/maintenance/wp_sync_db
 */

namespace Figuren_Theater\Maintenance\WP_Sync_DB;

use FT_VENDOR_DIR;

use function add_action;

const BASENAME   = 'wp-sync-db/wp-sync-db.php';
const PLUGINPATH = FT_VENDOR_DIR . '/pixelstudio/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin', 0 );
}

function load_plugin() {

	require_once PLUGINPATH;
}
