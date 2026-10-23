<?php
/**
 * gexis/search-state — the parts of the search screen that depend on the
 * query and cannot be static blocks:
 *   no query       "Try" and the suggested searches from the Customizer
 *   none found     No posts match “q”. and the four tag chips
 *   with results   "Results" label, rule, count; and the results by tag
 *
 * `part` picks one: `head` (suggestions or the no-results line), `count`,
 * `tags`; `all` prints whichever of the first two applies, as before 1.1.
 *
 * @package gexis
 *
 * @var array $attributes Block attributes.
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;
$gexis_part = (string) ( $attributes['part'] ?? 'all' );
$gexis_q    = get_search_query( false );
$gexis_n    = $wp_query instanceof WP_Query ? (int) $wp_query->found_posts : 0;
$gexis_head = in_array( $gexis_part, array( 'all', 'head' ), true );

if ( '' === trim( $gexis_q ) ) {
	if ( ! $gexis_head ) {
		return;
	}
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

if ( 0 === $gexis_n ) {
	if ( ! $gexis_head ) {
		return;
	}
	printf(
		'<div %1$s><p class="gx-search-state__none">%2$s</p>%3$s</div>',
		get_block_wrapper_attributes( array( 'class' => 'gx-search-state gx-search-state--none' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		/* translators: %s: the search query. */
		esc_html( sprintf( __( 'No posts match “%s”.', 'gexis' ), $gexis_q ) ),
		gexis_tag_chips_html() // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper.
	);
	return;
}

if ( 'all' === $gexis_part || 'count' === $gexis_part ) {
	printf(
		'<div %1$s>%2$s</div>',
		get_block_wrapper_attributes( array( 'class' => 'gx-search-state gx-search-state--results' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		gexis_section_label_html( __( 'Results', 'gexis' ), '<span class="gx-section-label__count">' . esc_html( gexis_count_posts( $gexis_n ) ) . '</span>' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	);
	return;
}

if ( 'tags' !== $gexis_part ) {
	return;
}

/* The results by tag: every match, not only this page's, counted by gexis
   tag. Each chip narrows the search to that tag; the chosen one undoes it. */
$gexis_ids = get_posts(
	array(
		's'              => $gexis_q,
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 500,
		'fields'         => 'ids',
		'no_found_rows'  => true,
	)
);
$gexis_counts = array();
foreach ( $gexis_ids as $gexis_id ) {
	foreach ( gexis_post_tags( $gexis_id ) as $gexis_tag ) {
		if ( '' !== $gexis_tag['letter'] ) {
			$gexis_counts[ $gexis_tag['letter'] ] = ( $gexis_counts[ $gexis_tag['letter'] ] ?? 0 ) + 1;
		}
	}
}
if ( ! $gexis_counts ) {
	return;
}
$gexis_now   = (string) get_query_var( 'tag' );
$gexis_chips = '';
foreach ( array_keys( gexis_tags() ) as $gexis_letter ) {
	if ( empty( $gexis_counts[ $gexis_letter ] ) ) {
		continue;
	}
	$gexis_slug  = gexis_tags()[ $gexis_letter ]['slug'];
	$gexis_on    = $gexis_slug === $gexis_now;
	$gexis_url   = add_query_arg( 's', rawurlencode( $gexis_q ), home_url( '/' ) );
	$gexis_url   = $gexis_on ? $gexis_url : add_query_arg( 'tag', $gexis_slug, $gexis_url );
	$gexis_chips .= sprintf(
		'<a class="gx-chip%1$s" href="%2$s"%3$s%4$s>%5$s%6$s<span class="gx-chip__count">%7$s</span></a>',
		$gexis_on ? ' is-on' : '',
		esc_url( $gexis_url ),
		gexis_tag_style_attr( $gexis_letter ),
		$gexis_on ? ' aria-current="true"' : '',
		gexis_diamond( $gexis_letter ),
		esc_html( gexis_tag_name( $gexis_letter ) ),
		esc_html( number_format_i18n( $gexis_counts[ $gexis_letter ] ) )
	);
}

printf(
	'<nav %1$s aria-label="%2$s">%3$s<span class="gx-chips">%4$s</span></nav>',
	get_block_wrapper_attributes( array( 'class' => 'gx-search-state gx-search-state--tags' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_attr__( 'Results by tag', 'gexis' ),
	gexis_section_label_html( __( 'By tag', 'gexis' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$gexis_chips // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts.
);
