<?php
/**
 * Title: Archive
 * Slug: gexis/archive
 * Inserter: no
 * Description: Date and category archives: label, title, count, then posts grouped by month.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"tagName":"main","className":"gx-main gx-archive","layout":{"type":"default"}} -->
<main class="wp-block-group gx-main gx-archive"><!-- wp:group {"className":"gx-head gx-page-head gx-head-card","layout":{"type":"default"}} -->
<div class="wp-block-group gx-head gx-page-head gx-head-card"><!-- wp:paragraph {"className":"is-style-gx-label"} -->
<p class="is-style-gx-label"><?php echo esc_html__( 'Archive', 'gexis' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:query-title {"type":"archive","showPrefix":false,"className":"gx-head__title"} /-->

<!-- wp:gexis/found-count /-->

<!-- wp:gexis/tag-chips {"excludeCurrent":false,"counts":true} /--></div>
<!-- /wp:group -->

<!-- wp:gexis/archive-months /-->

<!-- wp:gexis/pager /--></main>
<!-- /wp:group -->
