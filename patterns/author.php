<?php
/**
 * Title: Author
 * Slug: gexis/author
 * Inserter: no
 * Description: The CV-style author page, then Lately: the author's six latest posts.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"tagName":"main","className":"gx-main gx-author","layout":{"type":"default"}} -->
<main class="wp-block-group gx-main gx-author"><!-- wp:gexis/author-cv /-->

<!-- wp:group {"className":"gx-lately","layout":{"type":"default"}} -->
<div class="wp-block-group gx-lately"><?php echo gexis_m_label( __( 'Lately', 'gexis' ), gexis_archive_url(), __( 'Archive', 'gexis' ) ); // phpcs:ignore ?>

<?php echo gexis_m_query( array( 'perPage' => 6, 'gexis' => 'author' ), gexis_m_row(), 'gx-rows gx-rows--grid', 30 ); // phpcs:ignore ?></div>
<!-- /wp:group --></main>
<!-- /wp:group -->
