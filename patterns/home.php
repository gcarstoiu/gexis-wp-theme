<?php
/**
 * Title: Home
 * Slug: gexis/home
 * Inserter: no
 * Description: The front page: the lead post as a cover, three large cards, then Latest, Guides and Earlier. No post appears twice.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;
?>
<?php
$gexis_cover = '<!-- wp:group {"className":"gx-cover","layout":{"type":"default"}} -->' . "\n"
	. '<div class="wp-block-group gx-cover"><!-- wp:post-featured-image {"isLink":true,"aspectRatio":"1","sizeSlug":"gexis-square","className":"gx-cover__image"} /-->' . "\n\n"
	. '<!-- wp:group {"className":"gx-cover__text","layout":{"type":"default"}} -->' . "\n"
	. '<div class="wp-block-group gx-cover__text"><!-- wp:post-terms {"term":"post_tag","separator":"","className":"is-style-gx-pill"} /-->' . "\n\n"
	. '<!-- wp:post-title {"level":1,"isLink":true,"className":"gx-cover__title"} /-->' . "\n\n"
	. '<!-- wp:gexis/post-meta {"className":"gx-cover__meta"} /--></div>' . "\n" . '<!-- /wp:group --></div>' . "\n" . '<!-- /wp:group -->';
$gexis_archive = gexis_archive_url();
$gexis_cols    = array(
	array( __( 'Latest', 'gexis' ), array( 'gexis' => 'latest', 'offset' => 4 ), $gexis_archive, gexis_m_row() ),
	/* The site's guides in place of "Most discussed" (George, 2026-10-19). */
	array( __( 'Guides', 'gexis' ), array( 'gexis' => 'guides', 'postType' => 'page', 'perPage' => 5 ), null, gexis_m_row( array( 'guide' => true ) ) ),
	array( __( 'Earlier', 'gexis' ), array( 'gexis' => 'earlier', 'offset' => 7 ), $gexis_archive, gexis_m_row() ),
);
?>
<!-- wp:group {"tagName":"main","className":"gx-main gx-home","layout":{"type":"default"}} -->
<main class="wp-block-group gx-main gx-home"><?php echo gexis_m_query( array( 'perPage' => 1, 'gexis' => 'lead' ), $gexis_cover, 'gx-cover-list', 1 ); // phpcs:ignore ?>

<?php echo gexis_m_query( array( 'perPage' => 3, 'offset' => 1, 'gexis' => 'cards' ), gexis_m_card(), 'gx-cards', 2 ); // phpcs:ignore ?>

<!-- wp:group {"className":"gx-columns","layout":{"type":"default"}} -->
<div class="wp-block-group gx-columns"><?php foreach ( $gexis_cols as $gexis_i => $gexis_col ) : ?><!-- wp:group {"className":"gx-column","layout":{"type":"default"}} -->
<div class="wp-block-group gx-column"><?php echo gexis_m_label( $gexis_col[0], $gexis_col[2], __( 'Archive', 'gexis' ) ); // phpcs:ignore ?>

<?php echo gexis_m_query( array_merge( array( 'perPage' => 3 ), $gexis_col[1] ), $gexis_col[3], 'gx-rows', 3 + $gexis_i ); // phpcs:ignore ?></div>
<!-- /wp:group -->

<?php endforeach; ?></div>
<!-- /wp:group --></main>
<!-- /wp:group -->
