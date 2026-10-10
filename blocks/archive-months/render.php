<?php
/**
 * gexis/archive-months — the current listing grouped by month. A Query Loop
 * cannot insert a heading where the month changes, so this block walks the
 * main query itself.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;
if ( ! $wp_query instanceof WP_Query || ! $wp_query->posts ) {
	printf( '<p %1$s>%2$s</p>', get_block_wrapper_attributes( array( 'class' => 'gx-empty' ) ), esc_html__( 'Nothing published yet.', 'gexis' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}

$gexis_months = array();
foreach ( $wp_query->posts as $gexis_post ) {
	$gexis_key = get_the_date( 'Y-m', $gexis_post );
	if ( ! isset( $gexis_months[ $gexis_key ] ) ) {
		$gexis_months[ $gexis_key ] = array(
			'label' => get_the_date( 'M Y', $gexis_post ),
			'posts' => array(),
		);
	}
	$gexis_months[ $gexis_key ]['posts'][] = $gexis_post;
}

$gexis_out = '';
foreach ( $gexis_months as $gexis_month ) {
	$gexis_count = '<span class="gx-section-label__count">' . esc_html( gexis_count_posts( count( $gexis_month['posts'] ) ) ) . '</span>';
	$gexis_out  .= '<section class="gx-month">' . gexis_section_label_html( $gexis_month['label'], $gexis_count );
	foreach ( $gexis_month['posts'] as $gexis_post ) {
		$gexis_out .= gexis_row_html( $gexis_post, array( 'compact' => true ) );
	}
	$gexis_out .= '</section>';
}

printf(
	'<div %1$s>%2$s</div>',
	get_block_wrapper_attributes( array( 'class' => 'gx-months' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$gexis_out // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts.
);
