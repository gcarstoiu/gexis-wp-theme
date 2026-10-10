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
 * @param array $opt { excerpt: one-line excerpt instead of the meta line }.
 * @return string
 */
function gexis_m_row( $opt = array() ) {
	$meta = ! empty( $opt['excerpt'] )
		? '<!-- wp:post-excerpt {"excerptLength":24,"className":"gx-row__excerpt"} /-->'
		: '<!-- wp:group {"className":"gx-row__meta","layout":{"type":"flex","flexWrap":"nowrap"}} -->' . "\n"
			. '<div class="wp-block-group gx-row__meta"><!-- wp:post-terms {"term":"post_tag","separator":"","className":"is-style-gx-diamonds"} /-->' . "\n\n"
			. '<!-- wp:post-date {"format":"d M Y"} /--></div>' . "\n" . '<!-- /wp:group -->';
	$title = empty( $opt['plain'] ) ? $meta : '';
	return '<!-- wp:group {"className":"gx-row","layout":{"type":"default"}} -->' . "\n"
		. '<div class="wp-block-group gx-row"><!-- wp:post-featured-image {"aspectRatio":"1","width":"72px","height":"72px","sizeSlug":"gexis-thumb","className":"gx-row__thumb"} /-->' . "\n\n"
		. '<!-- wp:group {"className":"gx-row__body","layout":{"type":"default"}} -->' . "\n"
		. '<div class="wp-block-group gx-row__body"><!-- wp:post-title {"level":0,"isLink":true,"className":"gx-row__title"} /-->' . "\n\n"
		. $title . '</div>' . "\n" . '<!-- /wp:group --></div>' . "\n" . '<!-- /wp:group -->';
}

/**
 * A large card: 4:3 image, tag labels, title.
 *
 * @return string
 */
function gexis_m_card() {
	return '<!-- wp:group {"className":"gx-card","layout":{"type":"default"}} -->' . "\n"
		. '<div class="wp-block-group gx-card"><!-- wp:post-featured-image {"aspectRatio":"4/3","sizeSlug":"gexis-wide","className":"gx-card__image"} /-->' . "\n\n"
		. '<!-- wp:post-terms {"term":"post_tag","separator":"","className":"is-style-gx-tags"} /-->' . "\n\n"
		. '<!-- wp:post-title {"level":3,"isLink":true,"className":"gx-card__title"} /--></div>' . "\n" . '<!-- /wp:group -->';
}

/**
 * A Query Loop of rows or cards.
 *
 * @param array  $query  Query attributes (merged over defaults).
 * @param string $inner  Item markup (gexis_m_row() or gexis_m_card()).
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
