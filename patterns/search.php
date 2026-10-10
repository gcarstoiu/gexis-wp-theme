<?php
/**
 * Title: Search
 * Slug: gexis/search
 * Inserter: no
 * Description: The large search field; results as rows with a one-line excerpt; suggestions or tag chips when there is nothing to list.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"tagName":"main","className":"gx-main gx-search","layout":{"type":"default"}} -->
<main class="wp-block-group gx-main gx-search"><!-- wp:paragraph {"className":"is-style-gx-label"} -->
<p class="is-style-gx-label"><?php echo esc_html__( 'Search', 'gexis' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:search {"label":"<?php echo esc_attr__( 'Search posts', 'gexis' ); ?>","showLabel":false,"placeholder":"<?php echo esc_attr__( 'Search posts', 'gexis' ); ?>","buttonText":"<?php echo esc_attr__( 'Search', 'gexis' ); ?>","buttonPosition":"no-button","className":"gx-search-field"} /-->

<!-- wp:gexis/search-state /-->

<!-- wp:query {"queryId":40,"query":{"perPage":20,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":true}} -->
<div class="wp-block-query"><!-- wp:post-template {"className":"gx-rows gx-rows--search"} -->
<?php echo gexis_m_row( array( 'excerpt' => true ) ); // phpcs:ignore ?>
<!-- /wp:post-template --></div>
<!-- /wp:query -->

<!-- wp:gexis/pager /--></main>
<!-- /wp:group -->
