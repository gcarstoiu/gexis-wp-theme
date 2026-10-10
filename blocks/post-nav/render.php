<?php
/**
 * gexis/post-nav — Older and Newer as rows with a thumbnail. Core's post
 * navigation link has no thumbnail and no label-above-title layout.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_singular( 'post' ) && ! ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
	return;
}

$gexis_older = get_previous_post();
$gexis_newer = get_next_post();
if ( ! $gexis_older && ! $gexis_newer ) {
	return;
}

$gexis_rows = '';
if ( $gexis_older ) {
	$gexis_rows .= gexis_row_html( $gexis_older, array( 'label' => __( 'Older', 'gexis' ), 'rel' => 'prev' ) );
}
if ( $gexis_newer ) {
	$gexis_rows .= gexis_row_html( $gexis_newer, array( 'label' => __( 'Newer', 'gexis' ), 'rel' => 'next' ) );
}

printf(
	'<nav %1$s aria-label="%2$s">%3$s</nav>',
	get_block_wrapper_attributes( array( 'class' => 'gx-post-nav' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_attr__( 'Older and newer posts', 'gexis' ),
	$gexis_rows // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts.
);
