<?php
/**
 * Figuren_Theater Maintenance Health_Checks.
 * 
 * Modify some stuff related to the Site Health Checks.
 *
 * Handles hooking in and proper callbacks to enable and disable 
 * some health checks based on the current environment.
 *
 * @package figuren-theater/ft-maintenance
 */

namespace Figuren_Theater\Maintenance\Health_Checks;

use add_action;
use add_filter;

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap(): void {

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load' );
}

/**
 * Load all modifications.
 *
 * @return void
 */
function load(): void {
	// Add or modify which site status tests are run on a site.
	add_filter( 'site_status_tests', __NAMESPACE__ . '\\site_status_tests' );
	// Remove the REST API endpoints related to the site-health checks from the public.
	add_filter( 'Figuren_Theater\\Routes\\Disable_Public_JSON_REST_API\\endpoints_to_remove', __NAMESPACE__ . '\\remove_public_restroute_endpoints' );
}

/**
 * Add or modify which site status tests are run on a site.
 *
 * The site health is determined by a set of tests based on best practices from
 * both the WordPress Hosting Team and web standards in general.
 *
 * Some sites may not have the same requirements, for example the automatic update
 * checks may be handled by a host, and are therefore disabled in core.
 * Or maybe you want to introduce a new test, is caching enabled/disabled/stale for example.
 *
 * Tests may be added either as direct, or asynchronous ones. Any test that may require some time
 * to complete should run asynchronously, to avoid extended loading periods within wp-admin.
 *
 * @wp 5.2.0
 * @wp 5.6.0 Added the `async_direct_test` array key for asynchronous tests.
 *              Added the `skip_cron` array key for all tests.
 *
 * @param array[] $tests {
 *     An associative array of direct and asynchronous tests.
 *
 *     @type array[] $direct {
 *         An array of direct tests.
 *
 *         @type array ...$identifier {
 *             `$identifier` should be a unique identifier for the test. Plugins and themes are encouraged to
 *             prefix test identifiers with their slug to avoid collisions between tests.
 *
 *             @type string   $label     The friendly label to identify the test.
 *             @type callable $test      The callback function that runs the test and returns its result.
 *             @type bool     $skip_cron Whether to skip this test when running as cron.
 *         }
 *     }
 *     @type array[] $async {
 *         An array of asynchronous tests.
 *
 *         @type array ...$identifier {
 *             `$identifier` should be a unique identifier for the test. Plugins and themes are encouraged to
 *             prefix test identifiers with their slug to avoid collisions between tests.
 *
 *             @type string   $label             The friendly label to identify the test.
 *             @type string   $test              An admin-ajax.php action to be called to perform the test, or
 *                                               if `$has_rest` is true, a URL to a REST API endpoint to perform
 *                                               the test.
 *             @type bool     $has_rest          Whether the `$test` property points to a REST API endpoint.
 *             @type bool     $skip_cron         Whether to skip this test when running as cron.
 *             @type callable $async_direct_test A manner of directly calling the test marked as asynchronous,
 *                                               as the scheduled event can not authenticate, and endpoints
 *                                               may require authentication.
 *         }
 *     }
 * }
 */
function site_status_tests( array $tests ): array {
		
	// ///////////////////////////////
	// Important for all environments.
	// ///////////////////////////////
	
	// Disable Test for: available disk space for updates.
	if ( isset( $tests['direct']['available_updates_disk_space'] ) ) {
			unset( $tests['direct']['available_updates_disk_space'] );
	}
	
	// Disable Test for: automatic background updates.
	if ( isset( $tests['async']['background_updates'] ) ) {
			unset( $tests['async']['background_updates'] );
	}

	// Disable Test for: Plugin Updates.
	if ( isset( $tests['direct']['plugin_version'] ) ) {
		unset( $tests['direct']['plugin_version'] );
	}
	
	// Disable Test for: Theme Updates.
	if ( isset( $tests['direct']['theme_version'] ) ) {
		unset( $tests['direct']['theme_version'] );
	}
	
	// Disable Test for: Automatic Theme & Plugin Updates.
	if ( isset( $tests['direct']['plugin_theme_auto_updates'] ) ) {
		unset( $tests['direct']['plugin_theme_auto_updates'] );
	}

	// Chance to run away?
	if ( 'production' !== \wp_get_environment_type() && 'staging' !== \wp_get_environment_type() ) {
		return $tests;
	}

	// ////////////////////////////////////////////////////
	// Important for 'production' & 'staging' environments.
	// ////////////////////////////////////////////////////
	
	// Disable Test for: Reaching api.wordpress.org !
	if ( isset( $tests['async']['dotorg_communication'] ) ) {
			unset( $tests['async']['dotorg_communication'] );
	}

	return $tests;
}

/**
 * Remove the REST API endpoints related to the site-health checks from the public.
 * 
 * This filter is documented at: https://github.com/figuren-theater/ft-routes/blob/e4b14fb21f10edf6cd40a6882230b45cc5961c86/inc/disable-public-json-rest-api/namespace.php#L228
 * 
 * @param  string[] $endpoints List of REST API endpoints that will be made un-available to the public.
 *
 * @return string[]
 */
function remove_public_restroute_endpoints( array $endpoints ): array {

	return array_merge( 
		$endpoints,
		[
			'/wp-site-health/v1/tests',
			'/wp-site-health/v1/directory-sizes',
		]
	);
}
