<?php
/**
 * gexis/tag-docs — on a tag's archive, the pages that carry the tag: the
 * Documents, as cards above the tag's posts (George, 2026-10-15). Reference
 * pages that stay current (a gexis/repo-doc page, say) are not news, so they
 * are not mixed into the posts by date. First page of the archive only;
 * nothing at all when the tag has no pages. Ordered by the page's Order,
 * then title.
 *
 * @package gexis
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_tag() || get_query_var( 'paged' ) > 1 ) {
	return;
}
$gexis_term = get_queried_object();
if ( ! $gexis_term instanceof WP_Term ) {
	return;
}
$gexis_pages = get_posts(
	array(
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'tag_id'         => $gexis_term->term_id,
		'posts_per_page' => 24,
		'orderby'        => array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		),
	)
);
if ( ! $gexis_pages ) {
	return;
}

$gexis_cards = '';
foreach ( $gexis_pages as $gexis_page ) {
	$gexis_blurb  = has_excerpt( $gexis_page ) ? get_the_excerpt( $gexis_page ) : '';
	$gexis_cards .= sprintf(
		'<a class="gx-doc-card" href="%1$s"><span class="gx-doc-card__name">%2$s</span>%3$s</a>',
		esc_url( get_permalink( $gexis_page ) ),
		esc_html( get_the_title( $gexis_page ) ),
		'' !== $gexis_blurb ? '<span class="gx-doc-card__blurb">' . esc_html( $gexis_blurb ) . '</span>' : ''
	);
}

printf(
	'<section %1$s%2$s><p class="is-style-gx-label gx-doc-cards__label">%3$s</p><div class="gx-doc-cards__row">%4$s</div></section>',
	get_block_wrapper_attributes( array( 'class' => 'gx-doc-cards' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	gexis_tag_style_attr( gexis_tag_letter( $gexis_term ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_attr.
	esc_html__( 'Documents', 'gexis' ),
	$gexis_cards // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts.
);
