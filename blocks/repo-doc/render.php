<?php
/**
 * gexis/repo-doc — a Markdown document from a public GitHub repository, then
 * a mono line saying where it comes from. Fetching, caching and link
 * rewriting are in inc/repo-doc.php.
 *
 * @package gexis
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$gexis_src = gexis_repo_doc_source( $attributes );
if ( ! $gexis_src ) {
	printf(
		'<div %1$s><p class="gx-repo-doc__notice">%2$s</p></div>',
		get_block_wrapper_attributes( array( 'class' => 'gx-repo-doc' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		esc_html__( 'Choose a Markdown file (.md) in the repository, such as docs/FAQ.md.', 'gexis' )
	);
	return;
}

$gexis_doc  = gexis_repo_doc_render( $gexis_src );
$gexis_blob = gexis_repo_doc_blob_url( $gexis_src['repo'], $gexis_src['branch'], $gexis_src['path'] );
$gexis_name = 'gcarstoiu/gexis-player' === $gexis_src['repo'] ? 'Gexis Player' : $gexis_src['repo'];

if ( $gexis_doc['ok'] ) {
	$gexis_body = '<div class="gx-repo-doc__body">' . $gexis_doc['html'] . '</div>';
} else {
	$gexis_body = '<p class="gx-repo-doc__notice">' . sprintf(
		/* translators: %s: link to the file on GitHub. */
		esc_html__( 'This document could not be loaded just now. It can be read %s.', 'gexis' ),
		'<a href="' . esc_url( $gexis_blob ) . '">' . esc_html__( 'on GitHub', 'gexis' ) . '</a>'
	) . '</p>';
}

$gexis_meta = array(
	/* translators: %s: repository name. */
	esc_html( sprintf( __( 'From the %s repository', 'gexis' ), $gexis_name ) ),
);
if ( $gexis_doc['saved'] ) {
	/* translators: %s: date the copy was fetched. */
	$gexis_meta[] = esc_html( sprintf( __( 'copy of %s', 'gexis' ), wp_date( 'd M Y', $gexis_doc['saved'] ) ) );
}
$gexis_meta[] = '<a href="' . esc_url( $gexis_blob ) . '">' . esc_html__( 'view on GitHub', 'gexis' ) . '</a>';

printf(
	'<div %1$s>%2$s<p class="gx-meta gx-repo-doc__meta">%3$s</p></div>',
	get_block_wrapper_attributes( array( 'class' => 'gx-repo-doc' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$gexis_body, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtered by wp_kses_post in gexis_repo_doc_html().
	implode( ' · ', $gexis_meta ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
);
