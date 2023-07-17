<?php
/**
 * Figuren_Theater Maintenance Multisite_Enhancements.
 *
 * @package figuren-theater/ft-maintenance
 */

namespace Figuren_Theater\Maintenance\Multisite_Enhancements;

use Figuren_Theater\Options;

use FT_VENDOR_DIR;

use function add_action;
use function add_filter;
use function remove_action;

const BASENAME   = 'multisite-enhancements/multisite-enhancements.php';
const PLUGINPATH = '/wpackagist-plugin/' . BASENAME;

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	add_action( 'Figuren_Theater\loaded', __NAMESPACE__ . '\\filter_options', 11 );

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin', 0 );
	add_action( 'plugins_loaded', __NAMESPACE__ . '\\unload_plugin_ui', 20 );
}

/**
 * Conditionally load the plugin itself and its modifications.
 *
 * @return void
 */
function load_plugin() {

	require_once FT_VENDOR_DIR . PLUGINPATH; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant

	/**
	 * I thought about using unload_textdomain( 'multisite-enhancements' )
	 * but this does unfortunately nothing ...
	 */
	add_filter( 'load_textdomain_mofile', __NAMESPACE__ . '\\unload_i18n', 0, 2 );
}

function unload_plugin_ui() {

	remove_action( 'init', [ 'Multisite_Enhancements_Settings', 'init' ] );
}

function unload_i18n( string $mofile, string $domain ) : string {
	if ( 'multisite-enhancements' === $domain ) {
		return '';
	}
	return $mofile;
}

function filter_options() {

	$_options = [
		'remove-logo'         => 1,

		/*
		 * This saves (Websites*2)-DB requests per Admin-Bar-ified page-load
		 * so: 20 Websites * 2 = 40 DB requests saved
		 */
		'add-favicon'         => 0,
		'add-blog-id'         => 1,
		'add-css'             => 1,
		'add-plugin-list'     => 1,
		'add-theme-list'      => 1,
		'add-site-status'     => 1,
		'add-ssl-identifier'  => 1,
		'add-manage-comments' => 1,
		'add-new-plugin'      => 0,
		'filtering-themes'    => 1,
		'change-footer'       => 1,
		'delete-settings'     => 1,
	];

	/*
	 * Gets added to the 'OptionsCollection'
	 * from within itself on creation.
	 */
	new Options\Option(
		'wpme_options',
		$_options,
		BASENAME,
		'site_option'
	);
}
