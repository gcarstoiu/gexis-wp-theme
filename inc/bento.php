<?php
/**
 * Bento grids: posts as tiles of different sizes on a 12-column grid.
 *
 * Core's Post Template gives every item the same markup and no way to size
 * one item differently from the next, so the size and look of each cell are
 * set here: the Post Template's class names a cycle, and each <li> gets the
 * next step of that cycle as `--span` / `--rows` and a look class. A post with
 * no image is always a text card, whatever its step asked for. A one-row cell
 * also gets `gx-cell--short`.
 *
 * Looks: `overlay` (the picture fills the cell, the text sits on it),
 * `photo` (the picture above, the text under it), `text` (a card with the
 * tag's colour along its top).
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;

/**
 * The cycles: each step is [ columns of 12, rows, look ]. A list longer than
 * its cycle starts the cycle again.
 *
 * @return array<string, array<int, array{0: int, 1: int, 2: string}>>
 */
function gexis_bento_cycles() {
	return apply_filters(
		'gexis_bento_cycles',
		array(
			/* Home, after the lead: a wide cell, a tall one, two short ones; every post with a picture shows it with its title on it. */
			'home'  => array( array( 6, 2, 'overlay' ), array( 3, 2, 'overlay' ), array( 3, 1, 'overlay' ), array( 3, 1, 'overlay' ) ),
			/* Home, next to Guides. */
			'more'  => array( array( 4, 2, 'overlay' ), array( 4, 1, 'overlay' ), array( 4, 1, 'overlay' ) ),
			/* A tag's posts, seven to a cycle. */
			'tag'   => array( array( 8, 2, 'overlay' ), array( 4, 2, 'overlay' ), array( 4, 1, 'overlay' ), array( 4, 1, 'overlay' ), array( 4, 2, 'overlay' ), array( 4, 1, 'overlay' ), array( 4, 1, 'overlay' ) ),
			/* Three: About me's Lately, the 404's Latest. */
			'trio'  => array( array( 6, 2, 'overlay' ), array( 3, 2, 'photo' ), array( 3, 2, 'photo' ) ),
		)
	);
}

/**
 * Give each item of a bento Post Template its cell.
 *
 * @param string $html   Rendered Post Template.
 * @param array  $parsed Parsed block.
 * @return string
 */
function gexis_bento_cells( $html, $parsed ) {
	$class = $parsed['attrs']['className'] ?? '';
	if ( ! preg_match( '/\bgx-cycle-([a-z]+)\b/', $class, $m ) ) {
		return $html;
	}
	$cycles = gexis_bento_cycles();
	$cycle  = $cycles[ $m[1] ] ?? null;
	if ( ! $cycle ) {
		return $html;
	}
	$n = 0;
	return preg_replace_callback(
		'/<li class="([^"]*\bwp-block-post\b[^"]*)"/',
		static function ( $li ) use ( $cycle, &$n ) {
			$step = $cycle[ $n % count( $cycle ) ];
			++$n;
			$id   = preg_match( '/\bpost-(\d+)\b/', $li[1], $p ) ? (int) $p[1] : 0;
			$look = $step[2];
			if ( 'text' !== $look && ! has_post_thumbnail( $id ) ) {
				$look = 'text';
			}
			$letter = '';
			foreach ( gexis_post_tags( $id ) as $tag ) {
				if ( '' !== $tag['letter'] ) {
					$letter = $tag['letter'];
					break;
				}
			}
			$style = sprintf( '--span:%d;--rows:%d', $step[0], $step[1] );
			if ( '' !== $letter ) {
				$style .= ';--tag:var(--logo-' . $letter . ');--tag-tx:var(--tx-' . $letter . ')';
			}
			$short = 1 === (int) $step[1] ? ' gx-cell--short' : '';
			return sprintf( '<li class="%1$s gx-cell gx-cell--%2$s%3$s" style="%4$s"', esc_attr( $li[1] ), esc_attr( $look ), $short, esc_attr( $style ) );
		},
		$html
	);
}
add_filter( 'render_block_core/post-template', 'gexis_bento_cells', 10, 2 );
