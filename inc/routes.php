<?php
/**
 * Two addresses core does not have.
 *
 * /archive/  "Everything", grouped by month, for sites whose front page shows
 *            posts and so have no separate posts page. When a posts page is
 *            set, the nav's Archive goes there instead (templates/home.html).
 * /?s=       the search screen with no query yet. WordPress treats an empty
 *            search as the home page; here it opens the search template.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;

/**
 * Rewrite and query var.
 */
function gexis_routes() {
	add_rewrite_rule( '^archive/?$', 'index.php?gexis_archive=1', 'top' );
	add_rewrite_rule( '^archive/page/([0-9]+)/?$', 'index.php?gexis_archive=1&paged=$matches[1]', 'top' );
}
add_action( 'init', 'gexis_routes' );

/**
 * @param string[] $vars Query vars.
 * @return string[]
 */
function gexis_query_vars( $vars ) {
	$vars[] = 'gexis_archive';
	return $vars;
}
add_filter( 'query_vars', 'gexis_query_vars' );

/**
 * Flush rules once when the theme is switched on, so /archive/ resolves.
 */
function gexis_flush_rules() {
	gexis_routes();
	flush_rewrite_rules( false );
}
add_action( 'after_switch_theme', 'gexis_flush_rules' );

/**
 * Is this the /archive/ listing?
 *
 * @return bool
 */
function gexis_is_everything() {
	return (bool) get_query_var( 'gexis_archive' );
}

/**
 * /archive/ is the posts listing, so WordPress would give it the front page
 * template; send it to the home (Everything) template instead.
 *
 * @param string[] $templates Candidate templates.
 * @return string[]
 */
function gexis_everything_template( $templates ) {
	if ( gexis_is_everything() ) {
		return array( 'home', 'index' );
	}
	return $templates;
}
add_filter( 'frontpage_template_hierarchy', 'gexis_everything_template' );

/**
 * An empty ?s= is the search screen, not the home page. It matches nothing,
 * and the template shows the suggestions instead.
 *
 * @param WP_Query $q Query.
 */
function gexis_empty_search( $q ) {
	if ( is_admin() || ! $q->is_main_query() ) {
		return;
	}
	if ( isset( $_GET['s'] ) && '' === trim( (string) wp_unslash( $_GET['s'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$q->is_search     = true;
		$q->is_home       = false;
		$q->is_front_page = false;
		$q->set( 'post__in', array( 0 ) );
	}
}
add_action( 'parse_query', 'gexis_empty_search' );
