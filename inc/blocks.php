<?php
/**
 * The gexis/* blocks, block styles, and filters on core blocks.
 *
 * PHP because: each gexis/* block needs server data core blocks do not
 * expose (see each render.php), and core/post-terms has no way to colour a
 * tag by its slug.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register blocks, the shared editor script and the front-end script.
 */
function gexis_register_blocks() {
	wp_register_script(
		'gexis-blocks-editor',
		GEXIS_URI . '/assets/js/editor-blocks.js',
		array( 'wp-blocks', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-i18n', 'wp-server-side-render' ),
		GEXIS_VERSION,
		true
	);
	wp_register_script(
		'gexis-site',
		GEXIS_URI . '/assets/js/site.js',
		array(),
		GEXIS_VERSION,
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);

	$names = array();
	foreach ( glob( GEXIS_DIR . '/blocks/*/block.json' ) as $file ) {
		$type = register_block_type( dirname( $file ) );
		if ( $type ) {
			$names[] = array(
				'name'       => $type->name,
				'attributes' => $type->attributes,
			);
		}
	}
	wp_add_inline_script( 'gexis-blocks-editor', 'window.gexisBlocks = ' . wp_json_encode( $names ) . ';', 'before' );

	foreach ( array(
		'gx-pill'     => __( 'Pills on a scrim', 'gexis' ),
		'gx-chip'     => __( 'Chips', 'gexis' ),
		'gx-tags'     => __( 'Mono labels with diamonds', 'gexis' ),
		'gx-diamonds' => __( 'Diamonds only', 'gexis' ),
	) as $name => $label ) {
		register_block_style( 'core/post-terms', array( 'name' => $name, 'label' => $label ) );
	}
	register_block_style( 'core/paragraph', array( 'name' => 'gx-label', 'label' => __( 'Section label', 'gexis' ) ) );
	register_block_style( 'core/button', array( 'name' => 'gx-quiet', 'label' => __( 'Quiet', 'gexis' ) ) );

	register_block_pattern_category( 'gexis', array( 'label' => __( 'gexis', 'gexis' ) ) );
}
add_action( 'init', 'gexis_register_blocks' );

/**
 * core/post-terms on tags: give each link its tag's colour and a diamond.
 *
 * A gexis tag gets `--tag` and `--tag-tx`; any other tag stays neutral. The
 * block's style variation (pill, chip, label, diamonds) decides how it looks.
 *
 * @param string   $html   Rendered block.
 * @param array    $parsed Parsed block.
 * @param WP_Block $block  Block instance.
 * @return string
 */
function gexis_filter_post_terms( $html, $parsed, $block ) {
	if ( 'post_tag' !== ( $parsed['attrs']['term'] ?? '' ) || '' === $html ) {
		return $html;
	}
	$post_id = $block->context['postId'] ?? get_the_ID();
	$by_url  = array();
	foreach ( gexis_post_tags( $post_id ) as $tag ) {
		$link = get_term_link( $tag['term'] );
		if ( ! is_wp_error( $link ) ) {
			$by_url[ $link ] = $tag['letter'];
		}
	}
	return preg_replace_callback(
		'#<a href="([^"]+)" rel="tag">(.*?)</a>#',
		static function ( $m ) use ( $by_url ) {
			$url    = html_entity_decode( $m[1] );
			$letter = $by_url[ $url ] ?? '';
			return sprintf(
				'<a class="gx-tag" href="%1$s" rel="tag"%2$s>%3$s<span class="gx-tag__name">%4$s</span></a>',
				$m[1],
				gexis_tag_style_attr( $letter ),
				gexis_diamond( $letter ),
				$m[2]
			);
		},
		$html
	);
}
add_filter( 'render_block_core/post-terms', 'gexis_filter_post_terms', 10, 3 );

/**
 * Home and single posts use the light veil, so the image's colour shows;
 * every other page uses the dense one, for contrast.
 *
 * @return bool
 */
function gexis_is_light_veil_page() {
	return ( is_front_page() && ! gexis_is_everything() ) || is_singular( 'post' );
}

/**
 * The image behind the current page. See blocks/ground/render.php.
 *
 * @return int Attachment ID, or 0 for the weave alone.
 */
function gexis_ground_image_id() {
	$id = 0;
	if ( is_front_page() && ! gexis_is_everything() && ! is_page() ) {
		$lead = gexis_lead_post_id();
		$id   = $lead ? (int) get_post_thumbnail_id( $lead ) : 0;
	} elseif ( is_singular( 'post' ) ) {
		$id = (int) get_post_thumbnail_id( get_queried_object_id() );
	} elseif ( is_tag() ) {
		$newest = get_posts(
			array(
				'tag_id'         => get_queried_object_id(),
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);
		$id = $newest ? (int) get_post_thumbnail_id( $newest[0] ) : 0;
	}
	if ( ! $id ) {
		$id = (int) get_theme_mod( 'gexis_default_ground', 0 );
	}
	return $id;
}

/**
 * Custom navigation links never get core's current-item class. Mark the one
 * whose address is the page being viewed, so it carries the pressed fill.
 *
 * @param string $html   Block HTML.
 * @param array  $parsed Parsed block.
 * @return string
 */
function gexis_nav_link_current( $html, $parsed ) {
	$url = $parsed['attrs']['url'] ?? '';
	if ( '' === $url ) {
		return $html;
	}
	$here = untrailingslashit( (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/', PHP_URL_PATH ) );
	$path = wp_parse_url( $url );
	$is   = isset( $path['path'] ) && ! isset( $path['query'] ) && untrailingslashit( $path['path'] ) === $here && '' !== $here;
	if ( ! $is && is_search() && isset( $path['query'] ) && 0 === strpos( $path['query'], 's=' ) ) {
		$is = true;
	}
	if ( ! $is && ( ( is_home() && ! is_front_page() ) || gexis_is_everything() || is_date() ) && untrailingslashit( $url ) === untrailingslashit( gexis_archive_url() ) ) {
		$is = true;
	}
	if ( ! $is ) {
		return $html;
	}
	$p = new WP_HTML_Tag_Processor( $html );
	if ( $p->next_tag( 'li' ) ) {
		$p->add_class( 'current-menu-item' );
	}
	if ( $p->next_tag( 'a' ) ) {
		$p->set_attribute( 'aria-current', 'page' );
	}
	return $p->get_updated_html();
}
add_filter( 'render_block_core/navigation-link', 'gexis_nav_link_current', 10, 2 );
