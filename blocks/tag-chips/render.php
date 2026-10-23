<?php
/**
 * gexis/tag-chips — chips to the four gexis tag archives.
 *
 * @package gexis
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$gexis_exclude = array();
if ( ! empty( $attributes['excludeCurrent'] ) && '' !== gexis_current_lead() ) {
	$gexis_exclude[] = gexis_current_lead();
}

printf(
	'<nav %1$s aria-label="%2$s">%3$s</nav>',
	get_block_wrapper_attributes( array( 'class' => 'gx-tag-chips' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_attr__( 'Tags', 'gexis' ),
	gexis_tag_chips_html( $gexis_exclude, ! empty( $attributes['counts'] ) ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper.
);
