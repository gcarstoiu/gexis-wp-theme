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

/* Its sections, for the list beside it: the level-2 headings, which carry
   GitHub's anchors (inc/repo-doc.php). */
$gexis_toc = '';
if ( $gexis_doc['ok'] && preg_match_all( '#<h2 id="([^"]+)">(.*?)</h2>#s', $gexis_doc['html'], $gexis_heads, PREG_SET_ORDER ) && count( $gexis_heads ) > 1 ) {
	foreach ( $gexis_heads as $gexis_head ) {
		$gexis_toc .= sprintf( '<a class="gx-toc__link" href="#%1$s">%2$s</a>', esc_attr( $gexis_head[1] ), esc_html( wp_strip_all_tags( $gexis_head[2] ) ) );
	}
	$gexis_toc = sprintf( '<nav class="gx-toc" aria-label="%1$s"><span class="gx-label">%1$s</span>%2$s</nav>', esc_attr__( 'Sections', 'gexis' ), $gexis_toc );
}

/* The other guides, as cards under the document. */
$gexis_more = '';
$gexis_here = get_the_ID();
foreach ( gexis_guide_page_ids() as $gexis_page ) {
	if ( $gexis_page === $gexis_here ) {
		continue;
	}
	$gexis_blurb = has_excerpt( $gexis_page ) ? get_the_excerpt( $gexis_page ) : '';
	$gexis_more .= sprintf(
		'<a class="gx-doc-card" href="%1$s"><span class="gx-doc-card__name">%2$s</span>%3$s</a>',
		esc_url( get_permalink( $gexis_page ) ),
		esc_html( get_the_title( $gexis_page ) ),
		'' !== $gexis_blurb ? '<span class="gx-doc-card__blurb">' . esc_html( $gexis_blurb ) . '</span>' : ''
	);
}
if ( '' !== $gexis_more ) {
	$gexis_more = '<section class="gx-doc-cards gx-repo-doc__more">' . gexis_section_label_html( __( 'More guides', 'gexis' ) ) . '<div class="gx-doc-cards__row">' . $gexis_more . '</div></section>';
}

printf(
	'<div %1$s><div class="gx-repo-doc__cols%2$s"><aside class="gx-repo-doc__aside"><p class="gx-meta gx-repo-doc__meta">%3$s</p>%4$s</aside>%5$s</div>%6$s</div>',
	get_block_wrapper_attributes( array( 'class' => 'gx-repo-doc' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	'' !== $gexis_toc ? ' has-toc' : '',
	implode( '<br>', $gexis_meta ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
	$gexis_toc, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
	$gexis_body, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtered by wp_kses_post in gexis_repo_doc_html().
	$gexis_more // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
);
