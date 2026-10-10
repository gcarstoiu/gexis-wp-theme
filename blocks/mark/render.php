<?php
/**
 * gexis/mark — the mark as HTML and CSS, ported from reference/Logo.jsx.
 *
 * One tile edge drives everything:
 *   tile = size / 2.235   gap = tile × .235   radius = tile × .265
 *   glyph = tile × .53    box = √2 × (2 × tile + gap)
 * The 2×2 grid is rotated 45°, so the gap between the tiles is the x. Letters
 * are dropped below 28px: four colours read at favicon size, letterforms do
 * not. `lead` lights one tile; the other three drop to the unlit slate.
 *
 * @package gexis
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$gexis_size = max( 12, min( 400, (float) ( $attributes['size'] ?? 32 ) ) );
$gexis_lead = (string) ( $attributes['lead'] ?? '' );
if ( 'auto' === $gexis_lead ) {
	$gexis_lead = gexis_current_lead();
}
if ( ! in_array( $gexis_lead, array( 'g', 'e', 'i', 's' ), true ) ) {
	$gexis_lead = '';
}
$gexis_tile = $gexis_size / 2.235;

$gexis_style = sprintf(
	'--mk-size:%1$.2fpx;--mk-tile:%2$.2fpx;--mk-gap:%3$.2fpx;--mk-radius:%4$.2fpx;--mk-glyph:%5$.2fpx;--mk-box:%6$.2fpx',
	$gexis_size,
	$gexis_tile,
	$gexis_tile * 0.235,
	$gexis_tile * 0.265,
	$gexis_tile * 0.53,
	M_SQRT2 * $gexis_tile * 2.235
);

$gexis_label = 'gexis';
if ( '' !== $gexis_lead ) {
	$gexis_label .= ' · ' . gexis_tag_name( $gexis_lead );
}

$gexis_tiles = '';
foreach ( array( 'g', 'e', 'i', 's' ) as $gexis_letter ) {
	$gexis_lit    = '' === $gexis_lead || $gexis_letter === $gexis_lead;
	$gexis_tiles .= sprintf(
		'<span class="gx-mark__tile gx-mark__tile--%1$s%2$s">%3$s</span>',
		$gexis_letter,
		$gexis_lit ? ' is-lit' : '',
		$gexis_size >= 28 ? '<span class="gx-mark__glyph">' . $gexis_letter . '</span>' : ''
	);
}

$gexis_inner = '<span class="gx-mark__box" aria-hidden="true"><span class="gx-mark__tiles">' . $gexis_tiles . '</span></span>';
if ( ! empty( $attributes['wordmark'] ) ) {
	$gexis_inner .= '<span class="gx-mark__word" aria-hidden="true">gexis</span>';
}

$gexis_wrapper = get_block_wrapper_attributes(
	array(
		'class' => 'gx-mark',
		'style' => $gexis_style,
	)
);

if ( ! empty( $attributes['isLink'] ) ) {
	printf(
		'<div %1$s><a class="gx-mark__link" href="%2$s" rel="home" aria-label="%3$s">%4$s</a></div>',
		$gexis_wrapper, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		esc_url( home_url( '/' ) ),
		esc_attr( $gexis_label ),
		$gexis_inner // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup.
	);
} else {
	printf(
		'<div %1$s role="img" aria-label="%2$s">%3$s</div>',
		$gexis_wrapper, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		esc_attr( $gexis_label ),
		$gexis_inner // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed markup.
	);
}
