<?php
/**
 * gexis/pager — "Newer · 1 / 4 · Older" for the main query. Core's
 * pagination block has page numbers, not a fraction.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;
$gexis_total = $wp_query instanceof WP_Query ? (int) $wp_query->max_num_pages : 0;
if ( $gexis_total < 2 ) {
	return;
}
$gexis_paged = max( 1, (int) get_query_var( 'paged' ) );

$gexis_btn = static function ( $label, $page, $rel ) use ( $gexis_total ) {
	if ( $page < 1 || $page > $gexis_total ) {
		return '<span class="gx-pager__btn is-disabled" aria-disabled="true">' . esc_html( $label ) . '</span>';
	}
	return '<a class="gx-pager__btn" href="' . esc_url( get_pagenum_link( $page ) ) . '" rel="' . esc_attr( $rel ) . '">' . esc_html( $label ) . '</a>';
};

printf(
	'<nav %1$s aria-label="%2$s">%3$s<span class="gx-pager__at">%4$d / %5$d</span>%6$s</nav>',
	get_block_wrapper_attributes( array( 'class' => 'gx-pager' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_attr__( 'Pages', 'gexis' ),
	$gexis_btn( __( 'Newer', 'gexis' ), $gexis_paged - 1, 'prev' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	(int) $gexis_paged,
	(int) $gexis_total,
	$gexis_btn( __( 'Older', 'gexis' ), $gexis_paged + 1, 'next' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
