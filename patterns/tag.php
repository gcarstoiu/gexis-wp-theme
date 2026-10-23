<?php
/**
 * Title: Tag archive
 * Slug: gexis/tag
 * Inserter: no
 * Description: The tag's card (its letter lit, name, line, counts, the other tags), its documents, then its posts as a bento grid.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"tagName":"main","className":"gx-main gx-tag","layout":{"type":"default"}} -->
<main class="wp-block-group gx-main gx-tag"><!-- wp:group {"className":"gx-tag-head gx-head-card","layout":{"type":"default"}} -->
<div class="wp-block-group gx-tag-head gx-head-card"><!-- wp:gexis/mark {"size":96,"lead":"auto"} /-->

<!-- wp:group {"className":"gx-head","layout":{"type":"default"}} -->
<div class="wp-block-group gx-head"><!-- wp:paragraph {"className":"is-style-gx-label"} -->
<p class="is-style-gx-label"><?php echo esc_html__( 'Tag', 'gexis' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:query-title {"type":"archive","showPrefix":false,"className":"gx-head__title"} /-->

<!-- wp:term-description {"className":"gx-tag-head__blurb"} /-->

<!-- wp:group {"className":"gx-tag-head__foot","layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group gx-tag-head__foot"><!-- wp:gexis/found-count /-->

<!-- wp:gexis/tag-chips {"counts":true} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:gexis/tag-docs /-->

<!-- wp:group {"className":"gx-tag-posts","layout":{"type":"default"}} -->
<div class="wp-block-group gx-tag-posts"><?php echo gexis_m_label( __( 'Posts', 'gexis' ) ); // phpcs:ignore ?>

<!-- wp:query {"queryId":20,"query":{"perPage":14,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":true}} -->
<div class="wp-block-query"><!-- wp:post-template {"className":"gx-bento-cells gx-cycle-tag"} -->
<?php echo gexis_m_tile(); // phpcs:ignore ?>
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->

<!-- wp:gexis/pager /--></main>
<!-- /wp:group -->
