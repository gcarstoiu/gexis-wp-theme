<?php
/**
 * gexis/ground — the background behind every page.
 *
 * Weave, then the page's image bled 120px and blurred, then a veil, then a
 * fade to solid ground at 800px. The image is chosen per page:
 *   home        the lead post's featured image
 *   single post its featured image
 *   tag archive the newest post with that tag
 *   otherwise   the Customizer's default ground; empty means weave alone.
 * The blur is static. It is never animated.
 *
 * @package gexis
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$gexis_light = gexis_is_light_veil_page();
$gexis_veil  = $attributes['veil'] ?? 'auto';
if ( 'light' === $gexis_veil ) {
	$gexis_light = true;
} elseif ( 'dense' === $gexis_veil ) {
	$gexis_light = false;
}

$gexis_image = gexis_ground_image_id();
$gexis_img   = '';
if ( $gexis_image ) {
	$gexis_size = image_get_intermediate_size( $gexis_image, 'gexis-ground' ) ? 'gexis-ground' : 'medium';
	$gexis_src  = wp_get_attachment_image_url( $gexis_image, $gexis_size );
	if ( $gexis_src ) {
		$gexis_img = sprintf( '<img class="gx-ground__image" src="%s" alt="" loading="eager" decoding="async" fetchpriority="low">', esc_url( $gexis_src ) );
	}
}

$gexis_letter = gexis_current_lead();
$gexis_bar    = '' !== $gexis_letter ? '<div class="gx-ground__bar"' . gexis_tag_style_attr( $gexis_letter ) . '></div>' : '';

printf(
	'<div %1$s aria-hidden="true"><div class="gx-ground__weave"></div>%2$s<div class="gx-ground__veil gx-ground__veil--%3$s"></div><div class="gx-ground__fade"></div><div class="gx-ground__solid"></div>%4$s</div>',
	get_block_wrapper_attributes( array( 'class' => 'gx-ground' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$gexis_img, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
	$gexis_light ? 'light' : 'dense',
	$gexis_bar // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper.
);
