<?php
/**
 * Title: Single post
 * Slug: gexis/single
 * Inserter: no
 * Description: Post head with the square image, the body, tags, older and newer, and comments.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"tagName":"main","className":"gx-main gx-single","layout":{"type":"default"}} -->
<main class="wp-block-group gx-main gx-single"><!-- wp:group {"className":"gx-post-head","layout":{"type":"default"}} -->
<div class="wp-block-group gx-post-head"><!-- wp:post-featured-image {"aspectRatio":"1","sizeSlug":"gexis-square","className":"gx-post-head__image"} /-->

<!-- wp:group {"className":"gx-post-head__text","layout":{"type":"default"}} -->
<div class="wp-block-group gx-post-head__text"><!-- wp:post-terms {"term":"post_tag","separator":"","className":"is-style-gx-pill"} /-->

<!-- wp:post-title {"level":1,"className":"gx-post-head__title"} /-->

<!-- wp:group {"className":"gx-byline","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group gx-byline"><!-- wp:avatar {"size":44,"isLink":true} /-->

<!-- wp:group {"className":"gx-byline__text","layout":{"type":"default"}} -->
<div class="wp-block-group gx-byline__text"><!-- wp:post-author-name {"isLink":true} /-->

<!-- wp:gexis/post-meta /--></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:group {"tagName":"article","className":"gx-article","layout":{"type":"default"}} -->
<article class="wp-block-group gx-article"><!-- wp:post-content {"className":"gx-prose","layout":{"type":"default"}} /-->

<!-- wp:post-terms {"term":"post_tag","separator":"","className":"is-style-gx-chip"} /--></article>
<!-- /wp:group -->

<!-- wp:gexis/post-nav /-->

<!-- wp:comments {"className":"gx-comments"} -->
<div class="wp-block-comments gx-comments"><!-- wp:group {"className":"gx-section-label","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group gx-section-label"><!-- wp:paragraph {"className":"is-style-gx-label"} -->
<p class="is-style-gx-label"><?php echo esc_html__( 'Comments', 'gexis' ); ?></p>
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
