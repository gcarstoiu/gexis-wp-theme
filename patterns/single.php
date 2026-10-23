<?php
/**
 * Title: Single post
 * Slug: gexis/single
 * Inserter: no
 * Description: The post's picture header, the body with On this page and its tags beside it, older and newer, and comments.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"tagName":"main","className":"gx-main gx-single","layout":{"type":"default"}} -->
<main class="wp-block-group gx-main gx-single"><?php echo gexis_m_hero( array( 'post' => true ) ); // phpcs:ignore ?>

<!-- wp:group {"className":"gx-post-body","layout":{"type":"default"}} -->
<div class="wp-block-group gx-post-body"><!-- wp:group {"tagName":"article","className":"gx-article","layout":{"type":"default"}} -->
<article class="wp-block-group gx-article"><!-- wp:post-content {"className":"gx-prose","layout":{"type":"default"}} /-->

<!-- wp:post-terms {"term":"post_tag","separator":"","className":"is-style-gx-chip"} /--></article>
<!-- /wp:group -->

<!-- wp:group {"tagName":"aside","className":"gx-post-aside","layout":{"type":"default"}} -->
<aside class="wp-block-group gx-post-aside"><!-- wp:gexis/toc /-->

<!-- wp:gexis/tag-cards {"variant":"post"} /--></aside>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:gexis/post-nav /-->

<!-- wp:comments {"className":"gx-comments"} -->
<div class="wp-block-comments gx-comments"><!-- wp:group {"className":"gx-section-label","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group gx-section-label"><!-- wp:paragraph {"anchor":"comments","className":"is-style-gx-label"} -->
<p id="comments" class="is-style-gx-label"><?php echo esc_html__( 'Comments', 'gexis' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:gexis/post-meta {"bare":true,"className":"gx-section-label__count"} /--></div>
<!-- /wp:group -->

<!-- wp:post-comments-form /-->

<!-- wp:comment-template {"className":"gx-thread"} -->
<!-- wp:group {"className":"gx-comment","layout":{"type":"default"}} -->
<div class="wp-block-group gx-comment"><!-- wp:avatar {"size":44} /-->

<!-- wp:group {"className":"gx-comment__body","layout":{"type":"default"}} -->
<div class="wp-block-group gx-comment__body"><!-- wp:group {"className":"gx-comment__head","layout":{"type":"flex","flexWrap":"wrap"}} -->
<div class="wp-block-group gx-comment__head"><!-- wp:comment-author-name /-->

<!-- wp:comment-date {"format":"d M Y · H:i","isLink":false} /--></div>
<!-- /wp:group -->

<!-- wp:comment-content /-->

<!-- wp:comment-reply-link /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<!-- /wp:comment-template -->

<!-- wp:comments-pagination {"className":"gx-comments-pager"} -->
<!-- wp:comments-pagination-previous /-->

<!-- wp:comments-pagination-next /-->
<!-- /wp:comments-pagination --></div>
<!-- /wp:comments --></main>
<!-- /wp:group -->
