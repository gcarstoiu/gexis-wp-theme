<?php
/**
 * gexis/search-state — the parts of the search screen that depend on the
 * query and cannot be static blocks:
 *   with results   "Results" label, rule, count
 *   none found     No posts match “q”. and the four tag chips
 *   no query       "Try" and the suggested searches from the Customizer
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;
$gexis_q = get_search_query( false );

if ( '' === trim( $gexis_q ) ) {
	$gexis_words = array_filter( array_map( 'trim', explode( ',', (string) get_theme_mod( 'gexis_search_suggestions', '' ) ) ) );
	if ( ! $gexis_words ) {
		return;
	}
	$gexis_links = '';
	foreach ( $gexis_words as $gexis_word ) {
		$gexis_links .= sprintf( '<a class="gx-chip gx-chip--plain" href="%1$s">%2$s</a>', esc_url( add_query_arg( 's', rawurlencode( $gexis_word ), home_url( '/' ) ) ), esc_html( $gexis_word ) );
	}
	printf(
		'<div %1$s><span class="gx-search-state__try">%2$s</span><span class="gx-chips">%3$s</span></div>',
		get_block_wrapper_attributes( array( 'class' => 'gx-search-state gx-search-state--try' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		esc_html__( 'Try', 'gexis' ),
		$gexis_links // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
	);
	return;
}

$gexis_n = $wp_query instanceof WP_Query ? (int) $wp_query->found_posts : 0;
if ( $gexis_n > 0 ) {
	printf(
		'<div %1$s>%2$s</div>',
		get_block_wrapper_attributes( array( 'class' => 'gx-search-state gx-search-state--results' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		gexis_section_label_html( __( 'Results', 'gexis' ), '<span class="gx-section-label__count">' . esc_html( gexis_count_posts( $gexis_n ) ) . '</span>' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	);
	return;
}

printf(
	'<div %1$s><p class="gx-search-state__none">%2$s</p>%3$s</div>',
	get_block_wrapper_attributes( array( 'class' => 'gx-search-state gx-search-state--none' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	/* translators: %s: the search query. */
	esc_html( sprintf( __( 'No posts match “%s”.', 'gexis' ), $gexis_q ) ),
	gexis_tag_chips_html() // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper.
);
