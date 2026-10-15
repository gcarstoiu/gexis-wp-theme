<?php
/**
 * gexis/tag-cards — the four gexis tags as cards, each with the mark lit on
 * its letter. Name and count come from the tag; the description from the
 * tag's own description, else the default line.
 *
 * @package gexis
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$gexis_bench = 'bench' === ( $attributes['variant'] ?? 'about' );
$gexis_cards = '';
foreach ( array_keys( gexis_tags() ) as $gexis_letter ) {
	$gexis_term  = gexis_tag_term( $gexis_letter );
	$gexis_mark  = render_block(
		array(
			'blockName' => 'gexis/mark',
			'attrs'     => array(
				'size' => $gexis_bench ? 40 : 56,
				'lead' => $gexis_letter,
			),
		)
	);
	$gexis_count = $gexis_bench ? '' : '<span class="gx-tag-card__count">' . esc_html( gexis_count_posts( gexis_tag_post_count( $gexis_term ) ) ) . '</span>';

	$gexis_cards .= sprintf(
		'<a class="gx-tag-card" href="%1$s"%2$s>%3$s<span class="gx-tag-card__text"><span class="gx-tag-card__name">%4$s</span><span class="gx-tag-card__blurb">%5$s</span>%6$s</span></a>',
		esc_url( gexis_tag_url( $gexis_letter ) ),
		gexis_tag_style_attr( $gexis_letter ),
		$gexis_mark,
		esc_html( gexis_tag_name( $gexis_letter ) ),
		esc_html( gexis_tag_blurb( $gexis_letter ) ),
		$gexis_count
	);
}

printf(
	'<div %1$s>%2$s</div>',
	get_block_wrapper_attributes( array( 'class' => 'gx-tag-cards gx-tag-cards--' . ( $gexis_bench ? 'bench' : 'about' ) ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$gexis_cards // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts.
);
