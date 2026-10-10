<?php
/**
 * gexis/post-meta — "28 Sep 2026 · 7 min · 12 comments" in mono.
 *
 * Core has a date block but no read time or comment count that fits a single
 * mono line, so this one block draws the line.
 *
 * @package gexis
 * @var array    $attributes
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

$gexis_post = get_post( $block->context['postId'] ?? null );
if ( ! $gexis_post ) {
	return;
}

if ( ! empty( $attributes['bare'] ) ) {
	printf(
		'<span %1$s>%2$s</span>',
		get_block_wrapper_attributes( array( 'class' => 'gx-meta' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		esc_html( number_format_i18n( (int) get_comments_number( $gexis_post ) ) )
	);
	return;
}

$gexis_parts = array();
foreach ( array_map( 'trim', explode( ',', (string) ( $attributes['parts'] ?? 'date,time,comments' ) ) ) as $gexis_part ) {
	if ( 'date' === $gexis_part ) {
		$gexis_parts[] = '<time datetime="' . esc_attr( get_the_date( 'c', $gexis_post ) ) . '">' . esc_html( gexis_date( $gexis_post ) ) . '</time>';
	} elseif ( 'time' === $gexis_part ) {
		$gexis_parts[] = esc_html( gexis_read_time( $gexis_post ) );
	} elseif ( 'comments' === $gexis_part ) {
		$gexis_parts[] = esc_html( gexis_count_comments( $gexis_post ) );
	}
}
if ( ! $gexis_parts ) {
	return;
}

printf(
	'<span %1$s>%2$s</span>',
	get_block_wrapper_attributes( array( 'class' => 'gx-meta' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	implode( ' · ', $gexis_parts ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
);
