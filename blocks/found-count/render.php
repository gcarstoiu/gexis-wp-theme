<?php
/**
 * gexis/found-count — "35 posts" for the current listing.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;
if ( is_tag() ) {
	$gexis_n = gexis_tag_post_count( get_queried_object() );
} elseif ( is_author() ) {
	$gexis_n = (int) count_user_posts( get_queried_object_id(), 'post', true );
} else {
	$gexis_n = $wp_query instanceof WP_Query ? (int) $wp_query->found_posts : 0;
}

printf(
	'<p %1$s>%2$s</p>',
	get_block_wrapper_attributes( array( 'class' => 'gx-count' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_html( gexis_count_posts( $gexis_n ) )
);
