<?php
/**
 * Plugin Name:     figuren.theater | Maintenance
 * Plugin URI:      https://github.com/figuren-theater/ft-maintenance
 * Description:     Everything you need to maintain and maybe debug a running WordPress Multisite like figuren.theater.
 * Author:          figuren.theater
 * Author URI:      https://figuren.theater
 * Text Domain:     figurentheater
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         figuren-theater/maintenance
 */

namespace Figuren_Theater\Maintenance;

const DIRECTORY = __DIR__;

add_action( 'altis.modules.init', __NAMESPACE__ . '\\register' );
