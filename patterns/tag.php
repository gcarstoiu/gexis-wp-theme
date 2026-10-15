<?php
/**
 * Title: Tag archive
 * Slug: gexis/tag
 * Inserter: no
 * Description: The mark lit on the tag's letter, the tag name and count, the other gexis tags; then three large cards and rows. Later pages are rows only.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;
?>
<?php
$gexis_item = '<!-- wp:group {"className":"gx-item","layout":{"type":"default"}} -->' . "\n"
	. '<div class="wp-block-group gx-item"><!-- wp:post-featured-image {"aspectRatio":"4/3","sizeSlug":"gexis-wide","className":"gx-item__image"} /-->' . "\n\n"
	. '<!-- wp:group {"className":"gx-item__body","layout":{"type":"default"}} -->' . "\n"
	. '<div class="wp-block-group gx-item__body"><!-- wp:post-title {"level":2,"isLink":true,"className":"gx-item__title"} /-->' . "\n\n"
	. '<!-- wp:group {"className":"gx-row__meta","layout":{"type":"flex","flexWrap":"nowrap"}} -->' . "\n"
	. '<div class="wp-block-group gx-row__meta"><!-- wp:post-terms {"term":"post_tag","separator":"","className":"is-style-gx-diamonds"} /-->' . "\n\n"
	. '<!-- wp:post-date {"format":"d M Y"} /-->' . "\n\n"
	. '<!-- wp:gexis/post-meta {"parts":"comments","className":"gx-item__comments"} /--></div>' . "\n" . '<!-- /wp:group --></div>' . "\n" . '<!-- /wp:group --></div>' . "\n" . '<!-- /wp:group -->';
?>
<!-- wp:group {"tagName":"main","className":"gx-main gx-tag","layout":{"type":"default"}} -->
<main class="wp-block-group gx-main gx-tag"><!-- wp:group {"className":"gx-tag-head","layout":{"type":"default"}} -->
<div class="wp-block-group gx-tag-head"><!-- wp:group {"className":"gx-tag-head__id","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group gx-tag-head__id"><!-- wp:gexis/mark {"size":96,"lead":"auto"} /-->

<!-- wp:group {"className":"gx-head","layout":{"type":"default"}} -->
<div class="wp-block-group gx-head"><!-- wp:paragraph {"className":"is-style-gx-label"} -->
<p class="is-style-gx-label"><?php echo esc_html__( 'Tag', 'gexis' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:query-title {"type":"archive","showPrefix":false,"className":"gx-head__title"} /-->

<!-- wp:gexis/found-count /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:gexis/tag-chips /--></div>
<!-- /wp:group -->

<!-- wp:gexis/tag-docs /-->

<!-- wp:query {"queryId":20,"query":{"perPage":15,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","inherit":true}} -->
<div class="wp-block-query"><!-- wp:post-template {"className":"gx-tag-list"} -->
<?php echo $gexis_item; // phpcs:ignore ?>
<!-- /wp:post-template --></div>
<!-- /wp:query -->

<!-- wp:gexis/pager /--></main>
<!-- /wp:group -->
