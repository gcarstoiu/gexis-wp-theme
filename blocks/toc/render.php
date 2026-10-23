<?php
/**
 * gexis/toc: "On this page", the post's level-2 headings as links. The
 * headings get matching ids as they render (gexis_heading_anchor in
 * inc/blocks.php). With fewer than two sections there is nothing to jump
 * between, and the block prints nothing.
 *
 * @package gexis
 *
 * @var array    $attributes Block attributes.
 * @var WP_Block $block      Block instance.
 */

defined( 'ABSPATH' ) || exit;

$gexis_post = get_post( $block->context['postId'] ?? null );
if ( ! $gexis_post ) {
	return;
}
$gexis_heads = gexis_post_sections( $gexis_post );
if ( count( $gexis_heads ) < 2 ) {
	return;
}

$gexis_links = '';
foreach ( $gexis_heads as $gexis_head ) {
	$gexis_links .= sprintf( '<a class="gx-toc__link" href="#%1$s">%2$s</a>', esc_attr( $gexis_head['id'] ), esc_html( $gexis_head['text'] ) );
}
if ( comments_open( $gexis_post ) || get_comments_number( $gexis_post ) ) {
	$gexis_links .= sprintf(
		'<a class="gx-toc__link gx-toc__link--comments" href="#comments">%1$s<span class="gx-meta">%2$s</span></a>',
		esc_html__( 'Comments', 'gexis' ),
		esc_html( number_format_i18n( (int) get_comments_number( $gexis_post ) ) )
	);
}

printf(
	'<nav %1$s aria-label="%2$s"><span class="gx-label">%2$s</span>%3$s</nav>',
	get_block_wrapper_attributes( array( 'class' => 'gx-toc' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_html__( 'On this page', 'gexis' ),
	$gexis_links // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
);
