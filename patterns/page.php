<?php
/**
 * Title: Page
 * Slug: gexis/page
 * Inserter: no
 * Description: Label, title, then the page body. The first paragraph is the 22px intro.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"tagName":"main","className":"gx-main gx-page","layout":{"type":"default"}} -->
<main class="wp-block-group gx-main gx-page"><!-- wp:paragraph {"className":"is-style-gx-label"} -->
<p class="is-style-gx-label"><?php echo esc_html__( 'Page', 'gexis' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:post-terms {"term":"post_tag","separator":"","className":"is-style-gx-pill"} /-->

<!-- wp:post-title {"level":1,"className":"gx-head__title"} /-->

<!-- wp:post-content {"className":"gx-prose gx-prose--page","layout":{"type":"default"}} /--></main>
<!-- /wp:group -->
