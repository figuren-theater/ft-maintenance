<?php
/**
 * Figuren_Theater Maintenance Mode.
 *
 * @package figuren-theater/ft-maintenance
 */

namespace Figuren_Theater\Maintenance\Mode;

use FT_MAINTENANCE_MODE;

use function add_action;
use function add_filter;
use function current_user_can;
use function esc_html__;
use function wp_kses;
use function __;

const TEMPLATE = __DIR__ . '/error-template.php';

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap() :void {

	if ( defined( 'FT_MAINTENANCE_MODE' ) && FT_MAINTENANCE_MODE ) {
		add_action( 'set_current_user', __NAMESPACE__ . '\\load', -1000 );
	}

	add_action( 'load-plugins.php', __NAMESPACE__ . '\\load_plugins' );
}

/**
 * Activate WordPress Maintenance Mode
 *
 * ...if the currently logged-in user is not allowed
 * to switch themes or let's say is not an administrator.
 *
 * @return void
 */
function load() :void {

	if ( current_user_can( 'switch_themes' ) ) {
		return;
	}

	define( 'FT_ERROR_MAIL_TO', 'f.t web-Crew <' . getenv( 'FT_ERROR_MAIL_TO' ) . '>' );
	define( 'FT_ERROR_MAIL_FROM', getenv( 'FT_ERROR_MAIL_TO' ) );
	define( 'FT_ERROR_MAIL_INTERVAL', 300 );
	define( 'FT_ERROR_SUPPRESS_EMAIL', true );

	require TEMPLATE; // phpcs:ignore WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant
	die();
}
/**
 * Load the plugin itself and its modifications.
 *
 * @return void
 */
function load_plugins() {

	add_filter( 'plugin_row_meta', __NAMESPACE__ . '\\output_dropin_note', -10, 4 );
}

/**
 * Add note to [maintenance, php-error, db-error].php on dropins list
 *
 * @param array  $meta Meta links
 * @param string $file Plugin filename (sunrise.php for sunrise)
 * @param array  $data Data from the plugin header
 * @param string $status Status of the plugin
 *
 * @return array Modified meta links
 */
function output_dropin_note( $meta, $file, $data, $status ) {
	if ( 'dropins' !== $status ) {
		return $meta;
	}

	if ( ! in_array( $file, [ 'maintenance.php', 'php-error.php', 'db-error.php' ], true ) ) {
		return $meta;
	}

	$note = '<em>' . wp_kses(
		sprintf(
			__( 'Enhanced by <a href="%1$s" title="%2$s">%3$s</a>', 'figurentheater' ),
			'https://github.com/figuren-theater/ft-maintenance',
			sprintf(
				esc_html__( 'Version %s', 'figurentheater' ),
				FT_PLATTFORM_VERSION
			),
			__NAMESPACE__
		),
		[
			'a' => [
				'href'  => [],
				'title' => [],
			],
		]
	) . '</em>';
	array_unshift( $meta, $note );

	return $meta;
}
