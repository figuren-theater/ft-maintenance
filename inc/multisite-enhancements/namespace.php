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
 *
 * @return void
 */
function bootstrap() :void {

	add_action( 'Figuren_Theater\loaded', __NAMESPACE__ . '\\filter_options', 11 );

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugin', 0 );
	add_action( 'plugins_loaded', __NAMESPACE__ . '\\unload_plugin_ui', 20 );
}

/**
 * Conditionally load the plugin itself and its modifications.
 *
 * @return void
 */
function load_plugin() :void {

	require_once FT_VENDOR_DIR . PLUGINPATH; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant

	/**
	 * I thought about using unload_textdomain( 'multisite-enhancements' )
	 * but this does unfortunately nothing ...
	 */
	add_filter( 'load_textdomain_mofile', __NAMESPACE__ . '\\unload_i18n', 0, 2 );
}

/**
 * Disable the Plugin UI
 *
 * @return void
 */
function unload_plugin_ui() :void {

	remove_action( 'init', [ 'Multisite_Enhancements_Settings', 'init' ] );
}

/**
 * Unloads the specified MO file for localization based on the domain.
 *
 * This function unloads the specified MO file for localization based on the provided domain.
 * If the domain is 'multisite-enhancements', the function returns an empty string, effectively
 * preventing the MO file from being loaded. Otherwise, the function returns the original MO file path.
 *
 * @param string $mofile The path to the MO file for localization.
 * @param string $domain The domain associated with the localization.
 * 
 * @return string The path to the MO file or an empty string if unloading is needed.
 */
function unload_i18n( string $mofile, string $domain ) : string {
	// Check if the domain is 'multisite-enhancements'.
	if ( 'multisite-enhancements' === $domain ) {
		// If the domain is 'multisite-enhancements', prevent loading and return an empty string.
		return '';
	}

	// If the domain is not 'multisite-enhancements', return the original MO file path.
	return $mofile;
}

/**
 * Handle options
 *
 * @return void
 */
function filter_options() :void {

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
