<?php
/**
 * Title: Search
 * Slug: gexis/search
 * Inserter: no
 * Description: The search card (field, suggestions or the no-results line); results as cards with the words searched for marked; the results by tag beside them.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"tagName":"main","className":"gx-main gx-search","layout":{"type":"default"}} -->
<main class="wp-block-group gx-main gx-search"><!-- wp:group {"className":"gx-search-head gx-head-card","layout":{"type":"default"}} -->
<div class="wp-block-group gx-search-head gx-head-card"><!-- wp:paragraph {"className":"is-style-gx-label"} -->
<p class="is-style-gx-label"><?php echo esc_html__( 'Search', 'gexis' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:search {"label":"<?php echo esc_attr__( 'Search posts', 'gexis' ); ?>","showLabel":false,"placeholder":"<?php echo esc_attr__( 'Search posts', 'gexis' ); ?>","buttonText":"<?php echo esc_attr__( 'Search', 'gexis' ); ?>","buttonPosition":"no-button","className":"gx-search-field"} /-->

<!-- wp:gexis/search-state {"part":"head"} /--></div>
<!-- /wp:group -->

<!-- wp:group {"className":"gx-search-body","layout":{"type":"default"}} -->
<div class="wp-block-group gx-search-body"><!-- wp:group {"className":"gx-search-results","layout":{"type":"default"}} -->
<div class="wp-block-group gx-search-results"><!-- wp:gexis/search-state {"part":"count"} /-->

<!-- wp:query {"queryId":40,"query":{"perPage":20,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":true}} -->
<div class="wp-block-query"><!-- wp:post-template {"className":"gx-results"} -->
<?php echo gexis_m_result(); // phpcs:ignore ?>
<!-- /wp:post-template --></div>
<!-- /wp:query -->

<!-- wp:gexis/pager /--></div>
<!-- /wp:group -->

<!-- wp:group {"tagName":"aside","className":"gx-search-aside","layout":{"type":"default"}} -->
<aside class="wp-block-group gx-search-aside"><!-- wp:gexis/search-state {"part":"tags"} /--></aside>
<!-- /wp:group --></div>
<!-- /wp:group --></main>
<!-- /wp:group -->
