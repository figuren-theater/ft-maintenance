<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols
/**
 * Smart WP db-error.php, php-error.php and maintenance.php
 *
 * (Based on, but heavily modified)
 *
 * @source Smart_errors https://github.com/agkozak/smart-wp-db-error/
 * @version 1.0.6
 * @copyright 2017-2018 Alexandros Kozak
 * @license GPLv2 (or later)
 *
 * @package figuren-theater/ft-maintenance
 */

namespace Figuren_Theater\Maintenance\Mode; // phpcs:ignore HM.Files.NamespaceDirectoryName.NameMismatch

use WPMU_PLUGIN_URL;
use function esc_url;

// @todo #38 Replace hard-coded path
const ASSETS = WPMU_PLUGIN_URL . '/FT/ft-maintenance/assets/';

// Die silently if error-template.php has been accessed directly.
if ( ! defined( 'FT_ERROR_MAIL_FROM' )
	|| ! defined( 'FT_ERROR_MAIL_TO' )
	|| ! defined( 'FT_ERROR_MAIL_INTERVAL' ) ) {
	die();
}

/**
 * Send headers and sometimes emails ;)
 *
 * @return void
 */
function ft_maintenance_mode_error_setup(): void {
		
	$lock            = __DIR__ . DIRECTORY_SEPARATOR . 'smart-errors.lock';
	$server_protocol = getenv( 'SERVER_PROTOCOL' );

	// Information protocol of incoming request.
	if ( empty( $server_protocol ) ) {
		$server_protocol = 'HTTP/1.1';
	}
	defined( 'FT_ERROR_STATUS' ) || define( 'FT_ERROR_STATUS', '503 Service Temporarily Unavailable' );

	header( $server_protocol . ' ' . FT_ERROR_STATUS );
	header( 'Status: ' . FT_ERROR_STATUS );
	header( 'Retry-After: 600' );

	// When db-error.php is accessed directly, only show the message; do not e-mail.
	if ( ! defined( 'ABSPATH' ) ) {
		return;
	}
	
	if ( defined( 'FT_SUPPRESS_ERROR_EMAIL' ) && \constant( 'FT_SUPPRESS_ERROR_EMAIL' ) ) {
		return;
	}

	// If lock exists and is older than the alert interval, delete it.
	if ( file_exists( $lock ) ) {
		if ( time() - filectime( $lock ) > FT_ERROR_MAIL_INTERVAL ) {
			unlink( $lock ); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_unlink
		}

		// Otherwise try to create the lock; if successful, send the alert e-mail.
		// phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_touch
	} elseif ( touch( $lock ) ) {
		// Send alert email.
		ft_maintenance_mode_send_alert_email();
	}
}

/**
 * Send alert email
 *
 * @return void
 */
function ft_maintenance_mode_send_alert_email(): void {

	$request_uri = getenv( 'REQUEST_URI' );
	$server_name = getenv( 'SERVER_NAME' );

	$headers = 'From: ' . FT_ERROR_MAIL_FROM . "\n"
	. 'X-Mailer: PHP/' . PHP_VERSION . "\n"
	. 'X-Priority: 1 (High)';

	// Encrypted vs. non-encrypted connection.
	if ( ! empty( $_SERVER['HTTPS'] ) && 'off' !== $_SERVER['HTTPS'] ) {
		$web_protocol = 'https';
	} else {
		$web_protocol = 'http';
	}

	// Server name.
	if ( ! empty( $server_name ) ) {
		$server_name = filter_var(
			stripslashes( $server_name ),
			FILTER_SANITIZE_URL
		);
	} else {
		$server_name = '';
	}

	// Request URI.
	if ( ! empty( $request_uri ) ) {
		$request_uri = filter_var(
			stripslashes( $request_uri ),
			FILTER_SANITIZE_URL
		);
	} else {
		$request_uri = '';
	}

	// The e-mail alert.
	$message = 'Database Error on ' . $server_name . "\n"
		. 'The database error occurred when someone tried to open this page: '
		. $web_protocol . '://' . $server_name . $request_uri . "\n";
	$subject = 'Database error at ' . $server_name;
	mail( // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.wp_mail_mail
		FT_ERROR_MAIL_TO,
		$subject,
		$message,
		$headers
	);
}
ft_maintenance_mode_error_setup();
?>

<!doctype html>
<html>
<head>
	<meta name="robots" content="noindex">
	<title>Umbaupause</title>
	<?php // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet, WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<link rel="stylesheet" type="text/css" href="<?php echo ASSETS . 'css/twentytwenty-style.min.css'; ?>">
	<style>
		body {
			background-color: #000;
			font-family: "Courier New", Courier, monospace;
		}

		#wrapper {
			display: grid;
			place-items: center;  /* https://1linelayouts.glitch.me/ */
			height: 95vh;
		}

		#error {
			width: clamp(30ch, 50%, 76ch);  /* https://1linelayouts.glitch.me/ */
			padding: 5%;
			background-color: #fff;
			font-size: large;
			text-align: center;
			border-radius: 3px;
		}

		#error h1 {
			text-transform: uppercase;
			text-shadow: .04em .04em 0 #d20394;
		}

		#credits {
			padding: 10px 5% 10px 5%;
		}
		.social-menu {
			justify-content: center;
		}
		.footer-social a {
			background-color: #d20394;
		}
		</style>

	</head>

	<body>
		<div id="wrapper">
			<div id="error">
				<h1>Umbaupause</h1>
				<p>Hinter den Kulissen wird gerade alles auf den Kopf gestellt.</p>
				<p><a href="https://status.figuren.theater" rel="nofollow, noindex">status.figuren.theater</a> zeigt an, wenn es weiter geht.</p>
				<p>Bitte gedulden Sie sich einen Augenblick, abonnieren Sie doch derweilen unseren Newsletter und besuchen Sie uns anschließend im sozialen Netz!</p>

				<div id="credits">

					<nav aria-label="Social-Media-Links" class="footer-social-wrapper">
						<ul class="social-menu footer-social reset-list-style social-icons fill-children-current-color">

							<li class=""><a href="https://eepurl.com/gUFba9"><span class="screen-reader-text">Email-Newsletter</span><svg class="svg-icon" aria-hidden="true" role="img" focusable="false" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M20,4H4C2.895,4,2,4.895,2,6v12c0,1.105,0.895,2,2,2h16c1.105,0,2-0.895,2-2V6C22,4.895,21.105,4,20,4z M20,8.236l-8,4.882 L4,8.236V6h16V8.236z"></path></svg></a></li>
							<li class=""><a href="https://twitter.com/figuren_theater"><span class="screen-reader-text">twitter</span><svg class="svg-icon" aria-hidden="true" role="img" focusable="false" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22.23,5.924c-0.736,0.326-1.527,0.547-2.357,0.646c0.847-0.508,1.498-1.312,1.804-2.27 c-0.793,0.47-1.671,0.812-2.606,0.996C18.324,4.498,17.257,4,16.077,4c-2.266,0-4.103,1.837-4.103,4.103 c0,0.322,0.036,0.635,0.106,0.935C8.67,8.867,5.647,7.234,3.623,4.751C3.27,5.357,3.067,6.062,3.067,6.814 c0,1.424,0.724,2.679,1.825,3.415c-0.673-0.021-1.305-0.206-1.859-0.513c0,0.017,0,0.034,0,0.052c0,1.988,1.414,3.647,3.292,4.023 c-0.344,0.094-0.707,0.144-1.081,0.144c-0.264,0-0.521-0.026-0.772-0.074c0.522,1.63,2.038,2.816,3.833,2.85 c-1.404,1.1-3.174,1.756-5.096,1.756c-0.331,0-0.658-0.019-0.979-0.057c1.816,1.164,3.973,1.843,6.29,1.843 c7.547,0,11.675-6.252,11.675-11.675c0-0.178-0.004-0.355-0.012-0.531C20.985,7.47,21.68,6.747,22.23,5.924z"></path></svg></a></li>
							<!-- <li class=""><a href="https://instagram.com/figuren.theater"><span class="screen-reader-text">instagram</span><svg class="svg-icon" aria-hidden="true" role="img" focusable="false" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12,4.622c2.403,0,2.688,0.009,3.637,0.052c0.877,0.04,1.354,0.187,1.671,0.31c0.42,0.163,0.72,0.358,1.035,0.673 c0.315,0.315,0.51,0.615,0.673,1.035c0.123,0.317,0.27,0.794,0.31,1.671c0.043,0.949,0.052,1.234,0.052,3.637 s-0.009,2.688-0.052,3.637c-0.04,0.877-0.187,1.354-0.31,1.671c-0.163,0.42-0.358,0.72-0.673,1.035 c-0.315,0.315-0.615,0.51-1.035,0.673c-0.317,0.123-0.794,0.27-1.671,0.31c-0.949,0.043-1.233,0.052-3.637,0.052 s-2.688-0.009-3.637-0.052c-0.877-0.04-1.354-0.187-1.671-0.31c-0.42-0.163-0.72-0.358-1.035-0.673 c-0.315-0.315-0.51-0.615-0.673-1.035c-0.123-0.317-0.27-0.794-0.31-1.671C4.631,14.688,4.622,14.403,4.622,12 s0.009-2.688,0.052-3.637c0.04-0.877,0.187-1.354,0.31-1.671c0.163-0.42,0.358-0.72,0.673-1.035 c0.315-0.315,0.615-0.51,1.035-0.673c0.317-0.123,0.794-0.27,1.671-0.31C9.312,4.631,9.597,4.622,12,4.622 M12,3 C9.556,3,9.249,3.01,8.289,3.054C7.331,3.098,6.677,3.25,6.105,3.472C5.513,3.702,5.011,4.01,4.511,4.511 c-0.5,0.5-0.808,1.002-1.038,1.594C3.25,6.677,3.098,7.331,3.054,8.289C3.01,9.249,3,9.556,3,12c0,2.444,0.01,2.751,0.054,3.711 c0.044,0.958,0.196,1.612,0.418,2.185c0.23,0.592,0.538,1.094,1.038,1.594c0.5,0.5,1.002,0.808,1.594,1.038 c0.572,0.222,1.227,0.375,2.185,0.418C9.249,20.99,9.556,21,12,21s2.751-0.01,3.711-0.054c0.958-0.044,1.612-0.196,2.185-0.418 c0.592-0.23,1.094-0.538,1.594-1.038c0.5-0.5,0.808-1.002,1.038-1.594c0.222-0.572,0.375-1.227,0.418-2.185 C20.99,14.751,21,14.444,21,12s-0.01-2.751-0.054-3.711c-0.044-0.958-0.196-1.612-0.418-2.185c-0.23-0.592-0.538-1.094-1.038-1.594 c-0.5-0.5-1.002-0.808-1.594-1.038c-0.572-0.222-1.227-0.375-2.185-0.418C14.751,3.01,14.444,3,12,3L12,3z M12,7.378 c-2.552,0-4.622,2.069-4.622,4.622S9.448,16.622,12,16.622s4.622-2.069,4.622-4.622S14.552,7.378,12,7.378z M12,15 c-1.657,0-3-1.343-3-3s1.343-3,3-3s3,1.343,3,3S13.657,15,12,15z M16.804,6.116c-0.596,0-1.08,0.484-1.08,1.08 s0.484,1.08,1.08,1.08c0.596,0,1.08-0.484,1.08-1.08S17.401,6.116,16.804,6.116z"></path></svg></a></li> -->
							<li class=""><a href="https://www.facebook.com/figuren.theater.dach/"><span class="screen-reader-text">facebook</span><svg class="svg-icon" aria-hidden="true" role="img" focusable="false" width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 2C6.5 2 2 6.5 2 12c0 5 3.7 9.1 8.4 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.3v7C18.3 21.1 22 17 22 12c0-5.5-4.5-10-10-10z"></path></svg></a></li>

						</ul><!-- .footer-social -->

					<div></div></nav>

				</div>
			</div>
		</div>
	</body>
</html>
<?php
die();
