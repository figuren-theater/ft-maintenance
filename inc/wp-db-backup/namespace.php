<?php
/**
 * Figuren_Theater Maintenance WP_DB_Backup.
 *
 * @package figuren-theater/ft-maintenance
 */

namespace Figuren_Theater\Maintenance\WP_DB_Backup;

use Exception;
use Figuren_Theater;
use Figuren_Theater\Options;
use FT_VENDOR_DIR;
use function add_action;

use function add_filter;
use function apply_filters;
use function current_user_can;
use function do_action;
use function get_blog_details;
use function get_option;
use function is_main_site;
use function remove_action;
use function wp_clear_scheduled_hook;
use function wp_schedule_event;

const BASENAME   = 'wp-db-backup/wp-db-backup.php';
const PLUGINPATH = '/wpackagist-plugin/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap() :void {

	add_action( 'Figuren_Theater\loaded', __NAMESPACE__ . '\\filter_options', 11 );

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin', 9 );
}

/**
 * Conditionally load the plugin itself and its modifications.
 *
 * @return void
 */
function load_plugin() :void {

	$config = Figuren_Theater\get_config()['modules']['maintenance'];
	if ( ! $config['wp-db-backup'] ) {
		return;
	}

	require_once FT_VENDOR_DIR . PLUGINPATH; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant

	add_action( 'admin_menu', __NAMESPACE__ . '\\remove_menu', 0 );

	add_filter( 'pre_option_wp_cron_backup_tables', __NAMESPACE__ . '\\get_prefixed_table_names', 20 );

	// Run only when visiting the "Impressum" Settings page.
	add_action( 'admin_head-settings_page_impressum', __NAMESPACE__ . '\\save_backup_time' );
}

/**
 * Handle options
 *
 * @return void
 */
function filter_options() :void {
	global $wpdb;

	$_options = [
		'wp_db_backup_excs'        => [
			'revisions' => [ $wpdb->prefix . 'posts' ],
			'spam'      => [ $wpdb->prefix . 'comments' ],
		],

		// only needed for on-demand backup recipient email.
		'wpdb_backup_recip'        => getenv( 'FT_MAINTAINANCE_WPDBBACKUP_EMAIL' ),
		'wp_cron_backup_schedule'  => ( is_main_site() ) ? 'daily' : 'weekly',

		// Disabled for PRIVACY concerns
		//
		// By default, the Plugins sends
		// all relevant tables of each site
		// including the '_users'- and '_usermeta'-tables
		// WHICH IS A PROBLEM FOR PRIVACY
		// so we can only accept this ourselves.
		//
		// 'wp_cron_backup_recipient'  => ( is_main_site() ) ? self::RECIPIENT_EMAIL : \get_bloginfo( 'admin_email' ), !
		'wp_cron_backup_recipient' => getenv( 'FT_MAINTAINANCE_WPDBBACKUP_EMAIL' ),
		'wp_cron_backup_tables'    => [], // Will be set during admin-load, @see get_prefixed_table_names().
	];

	/*
	 * Gets added to the 'OptionsCollection'
	 * from within itself on creation.
	 */
	new Options\Factory(
		$_options,
		'Figuren_Theater\Options\Option',
		BASENAME
	);
}

/**
 * Show the admin-menu, only:
 * - to super-administrators
 *
 * @return void
 */
function remove_menu() :void {
	if ( current_user_can( 'manage_sites' ) ) {
		return;
	}

	global $mywpdbbackup;

	/*
	 * Remove Submenu from 'Settings' and
	 * Submenu from 'Tools'
	 */
	remove_action( 'admin_menu', [ $mywpdbbackup, 'admin_menu' ] );
}

/**
 * Get the names of all (relevant) tables.
 *
 * @return string[]
 */
function get_prefixed_table_names() : array {

	if ( is_main_site() ) {
		global $mywpdbbackup;

		$tables_to_save = $mywpdbbackup->get_tables();
	}

	$tablenames = apply_filters(
		__NAMESPACE__ . '\\tablenames_to_backup',
		[
			'eo_events',
			'eo_venuemeta',

			'koko_analytics_post_stats',
			'koko_analytics_referrer_stats',
			'koko_analytics_referrer_urls',
			'koko_analytics_site_stats',

			'yoast_indexable',
			'yoast_indexable_hierarchy',
			'yoast_migrations',
			'yoast_primary_term',
			'yoast_seo_links',
		]
	);

	$tables_to_save = array_map(
		function( string $tablename ) : string {
			global $wpdb;
			return $wpdb->prefix . $tablename;
		},
		$tablenames
	);

	return (array) apply_filters( __NAMESPACE__ . '\\tables_to_backup', $tables_to_save );
}

/**
 * Shedule WP_Cron for DB backups
 *
 * Because none of the normal Admins is
 * (per design of the plugin)
 * allowed to do backups manually.
 * So nobody will be able to reach
 * the settings screen at Tools->Backups.
 *
 * We need to start thoose automatically.
 *
 * Mainly cloned from the inside of
 * wpdbBackup->save_backup_time()
 * located at ...plugins\wp-db-backup\wp-db-backup.php
 */
function save_backup_time() {
	// Unschedule the previous cron.
	wp_clear_scheduled_hook( 'wp_db_backup_cron' );

	$tomorrow_date       = date( 'Y-m-d', strtotime( 'tomorrow' ) );
	$registered_datetime = get_blog_details( null, false )->registered;

	$registered_datetime = explode( ' ', $registered_datetime );

	$timestamp  = strtotime( $tomorrow_date . $registered_datetime[1] );
	$recurrence = get_option( 'wp_cron_backup_schedule' );

	try {
		return wp_schedule_event( $timestamp, $recurrence, 'wp_db_backup_cron' );

	} catch ( Exception $wp_error ) {
		do_action( 'qm/error', $wp_error ); // See https://querymonitor.com/docs/logging-variables/ for more examples.
	}

}
