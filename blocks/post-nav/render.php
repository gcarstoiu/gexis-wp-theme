<?php
/**
 * gexis/post-nav — Older and Newer as two cards, the post's picture behind
 * its title. Core's post navigation link has no picture and no card layout.
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

/* One side as a card: the post's picture with its title on it, or, with no
   picture, a text card. The side with no post says so and offers the archive. */
$gexis_side = static function ( $post, $label, $rel, $end ) {
	if ( ! $post ) {
		return sprintf(
			'<div class="gx-nav-card gx-nav-card--end"><span class="gx-label">%1$s</span><span class="gx-nav-card__title">%2$s</span><a class="gx-button-quiet" href="%3$s">%4$s</a></div>',
			esc_html( $label ),
			esc_html( $end ),
			esc_url( gexis_archive_url() ),
			esc_html__( 'The whole archive', 'gexis' )
		);
	}
	$image = has_post_thumbnail( $post ) ? get_the_post_thumbnail( $post, 'gexis-wide', array( 'alt' => '', 'loading' => 'lazy', 'class' => 'gx-nav-card__image' ) ) : '';
	$tags  = '';
	foreach ( gexis_post_tags( $post ) as $tag ) {
		$tags .= sprintf( '<span class="gx-tag"%1$s>%2$s<span class="gx-tag__name">%3$s</span></span>', gexis_tag_style_attr( $tag['letter'] ), gexis_diamond( $tag['letter'] ), esc_html( $tag['term']->name ) );
	}
	return sprintf(
		'<a class="gx-nav-card%1$s" href="%2$s" rel="%3$s">%4$s<span class="gx-nav-card__text"><span class="gx-label">%5$s</span><span class="gx-nav-card__title">%6$s</span><span class="gx-nav-card__tags">%7$s</span></span></a>',
		$image ? ' has-image' : '',
		esc_url( get_permalink( $post ) ),
		esc_attr( $rel ),
		$image,
		esc_html( $label ),
		esc_html( get_the_title( $post ) ),
		$tags
	);
};

printf(
	'<nav %1$s aria-label="%2$s">%3$s%4$s</nav>',
	get_block_wrapper_attributes( array( 'class' => 'gx-post-nav' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_attr__( 'Older and newer posts', 'gexis' ),
	$gexis_side( $gexis_older, __( '← Older', 'gexis' ), 'prev', __( 'This is the first post.', 'gexis' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$gexis_side( $gexis_newer, __( 'Newer →', 'gexis' ), 'next', __( 'This is the newest post.', 'gexis' ) ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
);
