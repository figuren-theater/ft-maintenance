<?php
/**
 * Figuren_Theater Maintenance WP_Sync_DB.
 *
 * @package figuren-theater/ft-maintenance
 */

namespace Figuren_Theater\Maintenance\WP_Sync_DB;

use Figuren_Theater;

use Figuren_Theater\Options;

use FT_VENDOR_DIR;
use function add_action;
use function add_filter;
use function get_sites;
use function wp_get_environment_type;
use function wp_list_pluck;

const BASENAME   = 'wp-sync-db/wp-sync-db.php';
const PLUGINPATH = '/pixelstudio/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() :void {

	add_action( 'Figuren_Theater\loaded', __NAMESPACE__ . '\\filter_options', 11 );

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin', 9 );
}

/**
 * Conditionally load the plugin itself and its modifications.
 *
 * @todo #29 Make path replacements dynamic.
 *
 * @return void
 */
function load_plugin() :void {

	$config = Figuren_Theater\get_config()['modules']['maintenance'];
	if ( ! $config['wp-sync-db'] ) {
		return;
	}

	require_once FT_VENDOR_DIR . PLUGINPATH; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant

	add_filter( 'wpsdb_domain_replaces', __NAMESPACE__ . '\\replace_tlds_on_migrate' );

	// Remove plugins menu.
	add_action( 'network_admin_menu', __NAMESPACE__ . '\\remove_menu', 11 );
}

/**
 * Handle options
 *
 * @return void
 */
function filter_options() :void {

	$_remote_key = \getenv( 'FT_MAINTAINANCE_WPSDB_REMOTE' );
	$_local_key = ( 'local' === wp_get_environment_type() ) ? \getenv( 'FT_MAINTAINANCE_WPSDB_LOCAL' ) : $_remote_key;

	$_options = [
		'max_request' => 1048576,
		'key'         => $_local_key,
		'allow_pull'  => true,
		'allow_push'  => false,
		'profiles'    => [
			0 => [
				'save_computer'       => '1',
				'gzip_file'           => '1',
				'replace_guids'       => '0',
				'exclude_spam'        => '1',
				'keep_active_plugins' => '0',
				'create_backup'       => '0',
				'exclude_post_types'  => '0',
				'action'              => 'pull',
				'connection_info'     => "https://figuren.theater
				$_remote_key", // keep this CRAZY LINEBREAK !

				// replacements
				// that will be done on many different tables
				//
				// but will not work for the 'blogs' table
				// as here are listed all domains without protocoll
				// e.g 'figuren.theater', which we can't replace by default
				// to not destroy emailadresses.
				//
				// So especially for the 'blogs'-table
				// we have our 'replace_tlds_on_migrate' filter
				//
				// @TODO #29 Make path replacements dynamic.
				'replace_old' => [
					1 => '/srv/www/htdocs/c.bach/www.puppen.theater',
					2 => '//figuren.theater',
					3 => '.figuren.theater',
					4 => '//puppen.theater',
					5 => '.puppen.theater',

					6 => '//katharina-muschiol.de',
				],
				'replace_new' => [
					1 => '/shared/httpd/figuren/htdocs',
					2 => '//figuren.test',
					3 => '.figuren.test',
					4 => '//puppen.test',
					5 => '.puppen.test',

					6 => '//katharina-muschiol.test',
				],
				'table_migrate_option'          => 'migrate_only_with_prefix',
				'exclude_transients'            => '1',
				'backup_option'                 => 'backup_only_with_prefix',
				'save_migration_profile'        => '0',
				'save_migration_profile_option' => '0',
				'create_new_profile'            => 'figuren.theater',
				'name'                          => 'PULL from LIVE @ figuren.theater',
			],
		],
		'verify_ssl'           => true,
		'enable_cdn'           => false,
		'blacklist_plugins'    => [],
		'plugin_compatibility' => false, // Not a real option, but used with in the UI.
	];

	new Options\Option(
		'wpsdb_settings',
		$_options,
		BASENAME
	);
}

/**
 * Show the admin-menu, only:
 * - to super-administrators
 *
 * @return void
 */
function remove_menu() : void {
	if ( ! current_user_can( 'manage_sites' ) ) {
		remove_submenu_page( 'settings.php', 'wp-sync-db' );
	}
}

/**
 * Find only the TLDs and replace them
 * with our (typical) local TLD of '.test'
 *
 * @param string[] $domain_replaces List of domain names and their replacements.
 *
 * @return string[]
 */
function replace_tlds_on_migrate( array $domain_replaces ) : array {

	foreach ( _get_sites() as $site_url ) {
		$domain_replaces[ sprintf( '/%s/', $site_url ) ] = _replace_tld( $site_url );
	}

	return $domain_replaces;
}

/**
 * Replace the production TLD for development puposes.
 *
 * @param string $url Full URL of website.
 * @param string $new_tld New TLD which will replace the production TLD for development puposes.
 *
 * @access private
 *
 * @return string
 */
function _replace_tld( string $url, string $new_tld = 'test' ) : string {
	// Cut url into array pieces.
	$url_parts = explode( '.', $url );

	// remove current tld.
	array_pop( $url_parts );

	// re-glue url with new top level domain.
	return implode( '.', array_merge( $url_parts, [ $new_tld ] ) );
}

/**
 * Get list of domains from WP_Site objects.
 *
 * @access private
 *
 * @return string[]
 */
function _get_sites() : array {

	// List of WP_Site objects,
	// or a list of site IDs when 'fields' is set to 'ids',
	// or the number of sites when 'count' is passed as a query var.
	$site_query_vars = [];

	return wp_list_pluck(
		get_sites( $site_query_vars ),
		'domain'
	);
}
