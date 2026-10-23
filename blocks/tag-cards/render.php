<?php
/**
 * gexis/tag-cards: the four gexis tags as cards.
 *
 * Variants: `about` (This site: the mark lit on the tag's letter, name, line,
 * post count), `bench` (the author page: the mark, name, line), `strip` (the
 * home page: a row of four, the tag's colour along the top) and `post` (a
 * post's aside: only that post's gexis tags, with counts).
 *
 * @package gexis
 *
 * @var array    $attributes Block attributes.
 * @var WP_Block $block      Block instance.
 */

defined( 'ABSPATH' ) || exit;

$gexis_variant = (string) ( $attributes['variant'] ?? 'about' );
if ( ! in_array( $gexis_variant, array( 'about', 'bench', 'strip', 'post' ), true ) ) {
	$gexis_variant = 'about';
}

$gexis_letters = array_keys( gexis_tags() );
if ( 'post' === $gexis_variant ) {
	$gexis_letters = array();
	foreach ( gexis_post_tags( $block->context['postId'] ?? get_the_ID() ) as $gexis_tag ) {
		if ( '' !== $gexis_tag['letter'] ) {
			$gexis_letters[] = $gexis_tag['letter'];
		}
	}
	if ( ! $gexis_letters ) {
		return;
	}
}

$gexis_cards = '';
foreach ( $gexis_letters as $gexis_letter ) {
	$gexis_term = gexis_tag_term( $gexis_letter );
	$gexis_mark = '';
	if ( 'about' === $gexis_variant || 'bench' === $gexis_variant ) {
		$gexis_mark = render_block(
			array(
				'blockName' => 'gexis/mark',
				'attrs'     => array(
					'size' => 'about' === $gexis_variant ? 64 : 40,
					'lead' => $gexis_letter,
				),
			)
		);
	}
	$gexis_name  = esc_html( gexis_tag_name( $gexis_letter ) );
	$gexis_count = '';
	if ( 'about' === $gexis_variant || 'post' === $gexis_variant ) {
		$gexis_count = '<span class="gx-tag-card__count">' . esc_html( gexis_count_posts( gexis_tag_post_count( $gexis_term ) ) ) . '</span>';
	}
	if ( 'strip' === $gexis_variant || 'post' === $gexis_variant ) {
		$gexis_name = gexis_diamond( $gexis_letter ) . $gexis_name;
	}

	$gexis_cards .= sprintf(
		'<a class="gx-tag-card" href="%1$s"%2$s>%3$s<span class="gx-tag-card__text"><span class="gx-tag-card__name">%4$s</span><span class="gx-tag-card__blurb">%5$s</span>%6$s</span></a>',
		esc_url( gexis_tag_url( $gexis_letter ) ),
		gexis_tag_style_attr( $gexis_letter ),
		$gexis_mark,
		$gexis_name,
		esc_html( gexis_tag_blurb( $gexis_letter ) ),
		$gexis_count
	);
}

printf(
	'<nav %1$s aria-label="%2$s">%3$s</nav>',
	get_block_wrapper_attributes( array( 'class' => 'gx-tag-cards gx-tag-cards--' . $gexis_variant ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_attr__( 'Tags', 'gexis' ),
	$gexis_cards // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts.
);
