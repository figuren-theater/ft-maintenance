<?php
/**
 * Figuren_Theater Maintenance Blog_Management.
 *
 * @package figuren-theater/maintenance/blog_management
 */

namespace Figuren_Theater\Maintenance\Blog_Management;

use add_action;
use add_filter;

/**
 * Bootstrap module, when enabled.
 */
function bootstrap() {

	add_action( 'init', __NAMESPACE__ . '\\load' );

}

function load() {

	// 
	add_filter( 'wpmu_drop_tables', __NAMESPACE__ . '\\wpmu_drop_tables', 10, 2 );
}


/**
 * Find and delete orphan DB tables.
 *
 * Gets the blogs table-prefix and searches for all tables with this part of their name.
 *
 * @package [package]
 * @since   2.10
 *
 * @param   array     $tables  [description]
 * @param   int       $site_id [description]
 * 
 * @return  array              Tables to delete (in addition to the WP default).
 */
function wpmu_drop_tables( array $tables, int $site_id ) : array {
	
	global $wpdb;
		
	// drop koko tables
	$tables[] = "{$wpdb->prefix}koko_analytics_site_stats";
	$tables[] = "{$wpdb->prefix}koko_analytics_post_stats";
	$tables[] = "{$wpdb->prefix}koko_analytics_referrer_stats";
	$tables[] = "{$wpdb->prefix}koko_analytics_referrer_urls";

	// drop yoast tables
	$tables[] = "{$wpdb->prefix}yoast_indexable";
	$tables[] = "{$wpdb->prefix}yoast_indexable_hierarchy";
	$tables[] = "{$wpdb->prefix}yoast_migrations";
	$tables[] = "{$wpdb->prefix}yoast_primary_term";
	$tables[] = "{$wpdb->prefix}yoast_seo_links";

	return $tables;
}


