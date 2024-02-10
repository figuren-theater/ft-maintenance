<?php
/**
 * Figuren_Theater Maintenance Blog_Management.
 *
 * @package figuren-theater/ft-maintenance
 */

namespace Figuren_Theater\Maintenance\Blog_Management;

use add_action;
use add_filter;

/**
 * Bootstrap module, when enabled.
 *
 * @return void
 */
function bootstrap(): void {

	add_action( 'init', __NAMESPACE__ . '\\load' );
}

/**
 * Load all modifications.
 *
 * @return void
 */
function load(): void {

	add_filter( 'wpmu_drop_tables', __NAMESPACE__ . '\\wpmu_drop_tables', 10 );
}

/**
 * Find and delete orphan DB tables.
 *
 * Gets the blogs table-prefix and searches for all tables with this part of their name.
 *
 * @param  string[] $tables Tables to delete (in addition to the WP default).
 *
 * @return string[]
 */
function wpmu_drop_tables( array $tables ): array {

	global $wpdb;

	/**
	 * Drop koko tables.
	 *
	 * @todo #34 Separate into ft-privacy module
	 */
	$tables[] = "{$wpdb->prefix}koko_analytics_site_stats";
	$tables[] = "{$wpdb->prefix}koko_analytics_post_stats";
	$tables[] = "{$wpdb->prefix}koko_analytics_referrer_stats";
	$tables[] = "{$wpdb->prefix}koko_analytics_referrer_urls";

	/**
	 * Drop yoast tables.
	 *
	 * @todo #35 Separate into ft-seo module
	 */
	$tables[] = "{$wpdb->prefix}yoast_indexable";
	$tables[] = "{$wpdb->prefix}yoast_indexable_hierarchy";
	$tables[] = "{$wpdb->prefix}yoast_migrations";
	$tables[] = "{$wpdb->prefix}yoast_primary_term";
	$tables[] = "{$wpdb->prefix}yoast_seo_links";

	return $tables;
}
