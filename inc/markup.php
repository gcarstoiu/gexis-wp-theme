<?php
/**
 * Block markup shared by several patterns, so a row or a card is written
 * once. Each function returns serialized core blocks.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;

/**
 * Section label: mono label, a rule filling the width, optional trailing link.
 *
 * @param string      $label Label.
 * @param string|null $href  Trailing link URL.
 * @param string      $text  Trailing link text.
 * @return string
 */
function gexis_m_label( $label, $href = null, $text = '' ) {
	$out  = '<!-- wp:group {"className":"gx-section-label","layout":{"type":"flex","flexWrap":"nowrap"}} -->' . "\n";
	$out .= '<div class="wp-block-group gx-section-label"><!-- wp:paragraph {"className":"is-style-gx-label"} -->' . "\n";
	$out .= '<p class="is-style-gx-label">' . esc_html( $label ) . '</p>' . "\n" . '<!-- /wp:paragraph -->';
	if ( $href ) {
		$out .= "\n\n" . '<!-- wp:paragraph {"className":"gx-section-label__link"} -->' . "\n";
		$out .= '<p class="gx-section-label__link"><a href="' . esc_url( $href ) . '">' . esc_html( $text ) . '</a></p>' . "\n" . '<!-- /wp:paragraph -->';
	}
	return $out . '</div>' . "\n" . '<!-- /wp:group -->';
}

/**
 * A row inside a post template: 72px thumbnail, title, then diamonds and date.
 *
 * @param array $opt { excerpt: one-line excerpt instead of the meta line;
 *                     guide: a page's row - no thumbnail, title and excerpt }.
 * @return string
 */
function gexis_m_row( $opt = array() ) {
	$meta = ! empty( $opt['excerpt'] )
		? '<!-- wp:post-excerpt {"excerptLength":24,"className":"gx-row__excerpt"} /-->'
		: '<!-- wp:group {"className":"gx-row__meta","layout":{"type":"flex","flexWrap":"nowrap"}} -->' . "\n"
			. '<div class="wp-block-group gx-row__meta"><!-- wp:post-terms {"term":"post_tag","separator":"","className":"is-style-gx-diamonds"} /-->' . "\n\n"
			. '<!-- wp:post-date {"format":"d M Y"} /--></div>' . "\n" . '<!-- /wp:group -->';
	if ( ! empty( $opt['guide'] ) ) {
		return '<!-- wp:group {"className":"gx-row gx-row--guide","layout":{"type":"default"}} -->' . "\n"
			. '<div class="wp-block-group gx-row gx-row--guide"><!-- wp:group {"className":"gx-row__body","layout":{"type":"default"}} -->' . "\n"
			. '<div class="wp-block-group gx-row__body"><!-- wp:post-title {"level":0,"isLink":true,"className":"gx-row__title"} /-->' . "\n\n"
			. '<!-- wp:post-excerpt {"excerptLength":24,"className":"gx-row__excerpt"} /--></div>' . "\n" . '<!-- /wp:group --></div>' . "\n" . '<!-- /wp:group -->';
	}
	$title = empty( $opt['plain'] ) ? $meta : '';
	return '<!-- wp:group {"className":"gx-row","layout":{"type":"default"}} -->' . "\n"
		. '<div class="wp-block-group gx-row"><!-- wp:post-featured-image {"aspectRatio":"1","width":"72px","height":"72px","sizeSlug":"gexis-thumb","className":"gx-row__thumb"} /-->' . "\n\n"
		. '<!-- wp:group {"className":"gx-row__body","layout":{"type":"default"}} -->' . "\n"
		. '<div class="wp-block-group gx-row__body"><!-- wp:post-title {"level":0,"isLink":true,"className":"gx-row__title"} /-->' . "\n\n"
		. $title . '</div>' . "\n" . '<!-- /wp:group --></div>' . "\n" . '<!-- /wp:group -->';
}

/**
 * A Query Loop of rows, tiles or cards.
 *
 * @param array  $query  Query attributes (merged over defaults).
 * @param string $inner  Item markup (gexis_m_row(), gexis_m_tile(), ...).
 * @param string $class  Class on the post template.
 * @param int    $id     queryId.
 * @return string
 */
function gexis_m_query( $query, $inner, $class, $id ) {
	$query = array_merge(
		array(
			'perPage'  => 3,
			'pages'    => 0,
			'offset'   => 0,
			'postType' => 'post',
			'order'    => 'desc',
			'orderBy'  => 'date',
			'inherit'  => false,
		),
		$query
	);
	$attrs = wp_json_encode( array( 'queryId' => $id, 'query' => $query ), JSON_UNESCAPED_SLASHES );
	return '<!-- wp:query ' . $attrs . ' -->' . "\n"
		. '<div class="wp-block-query"><!-- wp:post-template {"className":"' . esc_attr( $class ) . '"} -->' . "\n"
		. $inner . "\n"
		. '<!-- /wp:post-template --></div>' . "\n" . '<!-- /wp:query -->';
}

/**
 * A tile in a bento grid (inc/bento.php): image, tag labels, title, date and
 * read time. One markup for every cell; the grid gives each cell its size and
 * look (picture with the text over it, picture with the text under it, or a
 * text card), and a post with no image always becomes a text card.
 *
 * @return string
 */
function gexis_m_tile() {
	return '<!-- wp:group {"className":"gx-tile","layout":{"type":"default"}} -->' . "\n"
		. '<div class="wp-block-group gx-tile"><!-- wp:post-featured-image {"sizeSlug":"gexis-wide","className":"gx-tile__image"} /-->' . "\n\n"
		. '<!-- wp:group {"className":"gx-tile__text","layout":{"type":"default"}} -->' . "\n"
		. '<div class="wp-block-group gx-tile__text"><!-- wp:post-terms {"term":"post_tag","separator":"","className":"is-style-gx-tags"} /-->' . "\n\n"
		. '<!-- wp:post-title {"level":3,"isLink":true,"className":"gx-tile__title"} /-->' . "\n\n"
		. '<!-- wp:gexis/post-meta {"parts":"date,time","className":"gx-tile__meta"} /--></div>' . "\n" . '<!-- /wp:group --></div>' . "\n" . '<!-- /wp:group -->';
}

/**
 * The picture header: the post's image filling a wide card, its tags, title
 * and meta over it. Without an image it is a plain card.
 *
 * @param array $opt { post: true on a single post (h1, no link, the byline
 *                     in place of the excerpt) }.
 * @return string
 */
function gexis_m_hero( $opt = array() ) {
	$post  = ! empty( $opt['post'] );
	$title = $post
		? '<!-- wp:post-title {"level":1,"className":"gx-hero__title"} /-->'
		: '<!-- wp:post-title {"level":1,"isLink":true,"className":"gx-hero__title"} /-->';
	$foot  = $post
		? '<!-- wp:group {"className":"gx-byline","layout":{"type":"flex","flexWrap":"nowrap"}} -->' . "\n"
			. '<div class="wp-block-group gx-byline"><!-- wp:avatar {"size":44,"isLink":true} /-->' . "\n\n"
			. '<!-- wp:group {"className":"gx-byline__text","layout":{"type":"default"}} -->' . "\n"
			. '<div class="wp-block-group gx-byline__text"><!-- wp:post-author-name {"isLink":true} /-->' . "\n\n"
			. '<!-- wp:gexis/post-meta /--></div>' . "\n" . '<!-- /wp:group --></div>' . "\n" . '<!-- /wp:group -->'
		: '<!-- wp:post-excerpt {"excerptLength":36,"className":"gx-hero__excerpt"} /-->' . "\n\n"
			. '<!-- wp:gexis/post-meta {"className":"gx-hero__meta"} /-->';
	return '<!-- wp:group {"className":"gx-hero' . ( $post ? ' gx-hero--post' : '' ) . '","layout":{"type":"default"}} -->' . "\n"
		. '<div class="wp-block-group gx-hero' . ( $post ? ' gx-hero--post' : '' ) . '"><!-- wp:post-featured-image {"sizeSlug":"full","className":"gx-hero__image"} /-->' . "\n\n"
		. '<!-- wp:group {"className":"gx-hero__text","layout":{"type":"default"}} -->' . "\n"
		. '<div class="wp-block-group gx-hero__text"><!-- wp:post-terms {"term":"post_tag","separator":"","className":"is-style-gx-pill"} /-->' . "\n\n"
		. $title . "\n\n" . $foot . '</div>' . "\n" . '<!-- /wp:group --></div>' . "\n" . '<!-- /wp:group -->';
}

/**
 * A search result: picture, tag labels, title, a line of the post, meta.
 *
 * @return string
 */
function gexis_m_result() {
	return '<!-- wp:group {"className":"gx-result","layout":{"type":"default"}} -->' . "\n"
		. '<div class="wp-block-group gx-result"><!-- wp:post-featured-image {"sizeSlug":"gexis-wide","className":"gx-result__image"} /-->' . "\n\n"
		. '<!-- wp:group {"className":"gx-result__body","layout":{"type":"default"}} -->' . "\n"
		. '<div class="wp-block-group gx-result__body"><!-- wp:post-terms {"term":"post_tag","separator":"","className":"is-style-gx-tags"} /-->' . "\n\n"
		. '<!-- wp:post-title {"level":2,"isLink":true,"className":"gx-result__title"} /-->' . "\n\n"
		. '<!-- wp:post-excerpt {"excerptLength":28,"className":"gx-result__excerpt"} /-->' . "\n\n"
		. '<!-- wp:gexis/post-meta {"className":"gx-result__meta"} /--></div>' . "\n" . '<!-- /wp:group --></div>' . "\n" . '<!-- /wp:group -->';
}

/**
 * A Query Loop whose items are tiles laid out by a bento cycle.
 *
 * @param string $cycle Cycle name (gexis_bento_cycles()).
 * @param array  $query Query attributes.
 * @param int    $id    queryId.
 * @return string
 */
function gexis_m_bento( $cycle, $query, $id ) {
	return gexis_m_query( $query, gexis_m_tile(), 'gx-bento-cells gx-cycle-' . $cycle, $id );
}
