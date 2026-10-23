<?php
/**
 * Title: Everything
 * Slug: gexis/everything
 * Inserter: no
 * Description: Every post, grouped by month. The posts page, or /archive/ when the front page shows posts.
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

<!-- wp:heading {"level":1,"className":"gx-head__title"} -->
<h1 class="wp-block-heading gx-head__title"><?php echo esc_html__( 'Everything', 'gexis' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:gexis/found-count /-->

<!-- wp:gexis/tag-chips {"excludeCurrent":false,"counts":true} /--></div>
<!-- /wp:group -->

<!-- wp:gexis/archive-months /-->

<!-- wp:gexis/pager /--></main>
<!-- /wp:group -->
