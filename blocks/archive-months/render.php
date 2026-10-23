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
			'month' => get_the_date( 'M', $gexis_post ),
			'year'  => get_the_date( 'Y', $gexis_post ),
			'posts' => array(),
		);
	}
	$gexis_months[ $gexis_key ]['posts'][] = $gexis_post;
}

/* A month's first post spans the row, its picture behind the title; the rest
   are cards with a small picture, or text cards when there is none. */
$gexis_card = static function ( $post, $first ) {
	$image = has_post_thumbnail( $post );
	$tags  = '';
	$lead  = '';
	foreach ( gexis_post_tags( $post ) as $tag ) {
		$tags .= sprintf( '<span class="gx-tag"%1$s>%2$s<span class="gx-tag__name">%3$s</span></span>', gexis_tag_style_attr( $tag['letter'] ), gexis_diamond( $tag['letter'] ), esc_html( $tag['term']->name ) );
		if ( '' === $lead && '' !== $tag['letter'] ) {
			$lead = $tag['letter'];
		}
	}
	$look = $image ? ( $first ? 'wide' : 'thumb' ) : 'text';
	$pic  = '';
	if ( $image ) {
		$pic = get_the_post_thumbnail( $post, $first ? 'gexis-wide' : 'gexis-thumb', array( 'alt' => '', 'loading' => 'lazy', 'class' => 'gx-month-card__image' ) );
	}
	return sprintf(
		'<a class="gx-month-card gx-month-card--%1$s%2$s" href="%3$s"%4$s>%5$s<span class="gx-month-card__text"><span class="gx-month-card__tags">%6$s</span><span class="gx-month-card__title">%7$s</span><span class="gx-meta">%8$s</span></span></a>',
		$look,
		$first ? ' is-first' : '',
		esc_url( get_permalink( $post ) ),
		gexis_tag_style_attr( $lead ),
		$pic,
		$tags,
		esc_html( get_the_title( $post ) ),
		esc_html( implode( ' · ', array( gexis_date( $post ), gexis_read_time( $post ), gexis_count_comments( $post ) ) ) )
	);
};

$gexis_out = '';
foreach ( $gexis_months as $gexis_month ) {
	$gexis_cards = '';
	foreach ( $gexis_month['posts'] as $gexis_i => $gexis_post ) {
		$gexis_cards .= $gexis_card( $gexis_post, 0 === $gexis_i );
	}
	$gexis_out .= sprintf(
		'<section class="gx-month"><h2 class="gx-month__head"><span class="gx-month__name">%1$s</span><span class="gx-month__year">%2$s</span><span class="gx-meta">%3$s</span></h2><div class="gx-month__posts">%4$s</div></section>',
		esc_html( $gexis_month['month'] ),
		esc_html( $gexis_month['year'] ),
		esc_html( gexis_count_posts( count( $gexis_month['posts'] ) ) ),
		$gexis_cards
	);
}

printf(
	'<div %1$s>%2$s</div>',
	get_block_wrapper_attributes( array( 'class' => 'gx-months' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$gexis_out // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts.
);
