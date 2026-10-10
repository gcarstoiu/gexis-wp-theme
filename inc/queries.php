<?php
/**
 * Query roles for core/query blocks, and main-query sizes.
 *
 * PHP because: the home page must never show a post twice across its five
 * loops, and "Most discussed" orders by comment count over 90 days. Core's
 * Query Loop can do neither.
 *
 * A template opts in by adding a `gexis` key to the Query Loop's query:
 *   <!-- wp:query {"query":{"perPage":3,"gexis":"cards",...}} -->
 * Roles, in the order the home page renders them:
 *   lead       1 post: the newest sticky post, else the newest post
 *   cards      the next 3
 *   latest     the next 3
 *   discussed  3 by comment count over the last 90 days, not already shown
 *   earlier    the next 3 by date, not already shown
 *   author     the queried author's 6 latest (author page, "Lately")
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;

/**
 * Post IDs already shown on this request, by the role loops.
 *
 * @param int[]|null $add IDs to add.
 * @return int[]
 */
function gexis_shown( $add = null ) {
	static $shown = array();
	if ( $add ) {
		$shown = array_values( array_unique( array_merge( $shown, array_map( 'intval', $add ) ) ) );
	}
	return $shown;
}

/**
 * The lead post: the newest sticky post, else the newest post.
 *
 * @return int Post ID or 0.
 */
function gexis_lead_post_id() {
	static $lead = null;
	if ( null !== $lead ) {
		return $lead;
	}
	$sticky = array_filter( array_map( 'intval', (array) get_option( 'sticky_posts', array() ) ) );
	$args   = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 1,
		'fields'              => 'ids',
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
	);
	$ids = $sticky ? get_posts( array_merge( $args, array( 'post__in' => $sticky ) ) ) : array();
	if ( ! $ids ) {
		$ids = get_posts( $args );
	}
	$lead = $ids ? (int) $ids[0] : 0;
	return $lead;
}

/**
 * IDs for a role, excluding everything already shown.
 *
 * @param string $role  Role.
 * @param int    $count How many.
 * @return int[]
 */
function gexis_role_ids( $role, $count ) {
	$base = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'fields'              => 'ids',
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
		'posts_per_page'      => $count,
		'post__not_in'        => gexis_shown(),
	);

	if ( 'lead' === $role ) {
		$lead = gexis_lead_post_id();
		return $lead ? array( $lead ) : array();
	}
	if ( 'discussed' === $role ) {
		$recent = get_posts(
			array_merge(
				$base,
				array(
					'orderby'    => array( 'comment_count' => 'DESC', 'date' => 'DESC' ),
					'date_query' => array( array( 'after' => '90 days ago' ) ),
				)
			)
		);
		if ( count( $recent ) < $count ) {
			/* A quiet season: top up from all time rather than show a short column. */
			$base['post__not_in'] = array_merge( $base['post__not_in'], $recent );
			$base['posts_per_page'] = $count - count( $recent );
			$recent = array_merge( $recent, get_posts( array_merge( $base, array( 'orderby' => array( 'comment_count' => 'DESC', 'date' => 'DESC' ) ) ) ) );
		}
		return $recent;
	}
	if ( 'author' === $role ) {
		$author = is_author() ? get_queried_object_id() : 0;
		if ( $author ) {
			$base['author'] = $author;
		}
		return get_posts( $base );
	}
	/* cards, latest, earlier: the next posts by date. */
	return get_posts( $base );
}

/**
 * Point a role's Query Loop at exactly the IDs chosen above.
 *
 * @param array    $query Query vars.
 * @param WP_Block $block The post-template block.
 * @return array
 */
function gexis_query_loop_vars( $query, $block ) {
	$role = $block->context['query']['gexis'] ?? '';
	if ( '' === $role ) {
		return $query;
	}
	$count = max( 1, (int) ( $block->context['query']['perPage'] ?? 3 ) );
	$ids   = gexis_role_ids( $role, $count );
	if ( 'author' !== $role ) {
		gexis_shown( $ids );
	}
	$query['post__in']            = $ids ? $ids : array( 0 );
	$query['orderby']             = 'post__in';
	$query['posts_per_page']      = $count;
	$query['ignore_sticky_posts'] = true;
	$query['offset']              = 0;
	unset( $query['post__not_in'], $query['order'] );
	return $query;
}
add_filter( 'query_loop_block_query_vars', 'gexis_query_loop_vars', 10, 2 );

/**
 * Main-query page sizes: a tag archive's first page is 3 cards plus 12 rows;
 * archives list by month; search lists rows.
 *
 * @param WP_Query $q Query.
 */
function gexis_main_query_size( $q ) {
	if ( is_admin() || ! $q->is_main_query() ) {
		return;
	}
	if ( $q->is_tag() ) {
		$q->set( 'posts_per_page', 15 );
	} elseif ( $q->is_home() || $q->is_date() || $q->is_category() || gexis_is_everything() ) {
		$q->set( 'posts_per_page', 40 );
		$q->set( 'ignore_sticky_posts', true );
	} elseif ( $q->is_search() ) {
		$q->set( 'posts_per_page', 20 );
		$q->set( 'post_type', 'post' );
	}
}
add_action( 'pre_get_posts', 'gexis_main_query_size' );
