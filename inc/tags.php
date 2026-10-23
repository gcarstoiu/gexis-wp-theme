<?php
/**
 * The four gexis tags.
 *
 * The letters of the mark are ordinary WordPress tags, not sections. The theme
 * recognises four slugs and gives each its letter, tile colour and text
 * accent. A post can carry several. Any other tag renders neutral.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;

/**
 * The tag map, in mark order: g e i s.
 *
 * Change slugs with the `gexis_tags` filter. `blurb` is the fallback when the
 * tag has no description of its own in Posts → Tags.
 *
 * @return array<string, array{slug: string, name: string, blurb: string}>
 */
function gexis_tags() {
	static $tags = null;
	if ( null === $tags ) {
		$tags = apply_filters(
			'gexis_tags',
			array(
				'g' => array(
					'slug'  => 'gazette',
					'name'  => __( 'Gazette', 'gexis' ),
					'blurb' => __( 'Articles on whatever is worth writing down.', 'gexis' ),
				),
				'e' => array(
					'slug'  => 'engine-room',
					'name'  => __( 'Engine room', 'gexis' ),
					'blurb' => __( 'Homelab and tinkering. Small servers, networking, fixes.', 'gexis' ),
				),
				'i' => array(
					'slug'  => 'inbox',
					'name'  => __( 'Inbox', 'gexis' ),
					'blurb' => __( 'Feedback and requests: what readers ask me to look into, fix or think about again.', 'gexis' ),
				),
				's' => array(
					'slug'  => 'sound',
					'name'  => __( 'Sound', 'gexis' ),
					'blurb' => __( 'The gexis DAC player: builds, decisions, measurements.', 'gexis' ),
				),
			)
		);
	}
	return $tags;
}

/**
 * The mark letter for a tag.
 *
 * @param WP_Term|string|null $term A tag, or a tag slug.
 * @return string 'g', 'e', 'i', 's', or '' for a neutral tag.
 */
function gexis_tag_letter( $term ) {
	$slug = $term instanceof WP_Term ? $term->slug : (string) $term;
	foreach ( gexis_tags() as $letter => $tag ) {
		if ( $tag['slug'] === $slug ) {
			return $letter;
		}
	}
	return '';
}

/**
 * Inline custom properties that colour a tag: `--tag` is the tile colour
 * (diamonds, the accent bar), `--tag-tx` the text accent, which darkens in
 * light mode. Neutral tags get neither and fall back in CSS.
 *
 * @param string $letter Mark letter or ''.
 * @return string A style attribute, escaped, with a leading space; or ''.
 */
function gexis_tag_style_attr( $letter ) {
	if ( '' === $letter ) {
		return '';
	}
	return sprintf( ' style="--tag:var(--logo-%1$s);--tag-tx:var(--tx-%1$s)"', esc_attr( $letter ) );
}

/**
 * A tag's WP_Term for a mark letter, if the tag exists.
 *
 * @param string $letter Mark letter.
 * @return WP_Term|null
 */
function gexis_tag_term( $letter ) {
	$tags = gexis_tags();
	if ( ! isset( $tags[ $letter ] ) ) {
		return null;
	}
	$term = get_term_by( 'slug', $tags[ $letter ]['slug'], 'post_tag' );
	return $term instanceof WP_Term ? $term : null;
}

/**
 * Display name for a gexis tag: the term's own name if it exists.
 *
 * @param string $letter Mark letter.
 * @return string
 */
function gexis_tag_name( $letter ) {
	$term = gexis_tag_term( $letter );
	return $term ? $term->name : gexis_tags()[ $letter ]['name'];
}

/**
 * Description for a gexis tag: the term description, else the fallback blurb.
 *
 * @param string $letter Mark letter.
 * @return string Plain text.
 */
function gexis_tag_blurb( $letter ) {
	$term = gexis_tag_term( $letter );
	if ( $term && '' !== trim( $term->description ) ) {
		return wp_strip_all_tags( $term->description );
	}
	return gexis_tags()[ $letter ]['blurb'];
}

/**
 * Link to a gexis tag's archive; falls back to the home page if the tag has
 * not been created yet.
 *
 * @param string $letter Mark letter.
 * @return string
 */
function gexis_tag_url( $letter ) {
	$term = gexis_tag_term( $letter );
	if ( $term ) {
		$link = get_term_link( $term );
		if ( ! is_wp_error( $link ) ) {
			return $link;
		}
	}
	return home_url( '/' );
}

/**
 * A post's tags, gexis tags first in mark order, then any others by name.
 *
 * @param int|WP_Post|null $post Post.
 * @return array<int, array{term: WP_Term, letter: string}>
 */
function gexis_post_tags( $post = null ) {
	$terms = get_the_tags( $post );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return array();
	}
	$order = array_flip( array_keys( gexis_tags() ) );
	$out   = array();
	foreach ( $terms as $term ) {
		$out[] = array(
			'term'   => $term,
			'letter' => gexis_tag_letter( $term ),
		);
	}
	usort(
		$out,
		static function ( $a, $b ) use ( $order ) {
			$ka = '' === $a['letter'] ? 99 : $order[ $a['letter'] ];
			$kb = '' === $b['letter'] ? 99 : $order[ $b['letter'] ];
			return $ka === $kb ? strcasecmp( $a['term']->name, $b['term']->name ) : $ka - $kb;
		}
	);
	return $out;
}

/**
 * The 7–8px accent diamond.
 *
 * @param string $letter Mark letter or '' (neutral).
 * @return string HTML.
 */
function gexis_diamond( $letter ) {
	return '<span class="gx-diamond" aria-hidden="true"' . gexis_tag_style_attr( $letter ) . '></span>';
}

/**
 * Chips linking to the gexis tag archives: neutral ink, accent diamond.
 *
 * @param string[] $exclude Letters to leave out.
 * @param bool     $counts  Add each tag's post count.
 * @return string HTML.
 */
function gexis_tag_chips_html( $exclude = array(), $counts = false ) {
	$out = '<span class="gx-chips">';
	foreach ( array_keys( gexis_tags() ) as $letter ) {
		if ( in_array( $letter, $exclude, true ) ) {
			continue;
		}
		$out .= sprintf(
			'<a class="gx-chip" href="%1$s"%2$s>%3$s%4$s%5$s</a>',
			esc_url( gexis_tag_url( $letter ) ),
			gexis_tag_style_attr( $letter ),
			gexis_diamond( $letter ),
			esc_html( gexis_tag_name( $letter ) ),
			$counts ? '<span class="gx-chip__count">' . esc_html( number_format_i18n( gexis_tag_post_count( gexis_tag_term( $letter ) ) ) ) . '</span>' : ''
		);
	}
	return $out . '</span>';
}

/**
 * The letter whose tile the header and footer marks light: only on a gexis
 * tag archive. Everywhere else the master mark.
 *
 * @return string
 */
function gexis_current_lead() {
	if ( is_tag() ) {
		return gexis_tag_letter( get_queried_object() );
	}
	return '';
}

/**
 * Pages take tags too (George, 2026-10-15): a page that shows a document from
 * the repository is tagged with the part of the site it belongs to, and the
 * tag's archive lists it under Documents (gexis/tag-docs), apart from the
 * posts.
 */
function gexis_tags_on_pages() {
	register_taxonomy_for_object_type( 'post_tag', 'page' );
}
add_action( 'init', 'gexis_tags_on_pages' );

/**
 * How many published posts carry a tag - not its pages. WordPress's own
 * count includes every type the tag is registered for, which since pages
 * take tags would count the Documents as posts.
 *
 * @param WP_Term|null $term Tag.
 * @return int
 */
function gexis_tag_post_count( $term ) {
	if ( ! $term instanceof WP_Term ) {
		return 0;
	}
	$key   = 'gexis_tag_posts_' . $term->term_id . '_' . wp_cache_get_last_changed( 'posts' );
	$count = wp_cache_get( $key, 'gexis' );
	if ( false === $count ) {
		$q     = new WP_Query(
			array(
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'tag_id'         => $term->term_id,
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		$count = (int) $q->found_posts;
		wp_cache_set( $key, $count, 'gexis' );
	}
	return (int) $count;
}
