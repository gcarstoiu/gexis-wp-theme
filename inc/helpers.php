<?php
/**
 * Formatting helpers shared by the gexis/* blocks.
 *
 * Copy rules from the design system: counts are bare numerals with a plain
 * noun, anything measured is mono, sentence case, no emoji.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;

/**
 * "28 Sep 2026".
 *
 * @param int|WP_Post|null $post Post.
 * @return string
 */
function gexis_date( $post = null ) {
	return get_the_date( 'd M Y', $post );
}

/**
 * Reading time, "7 min", at 220 words a minute and never under 1.
 *
 * @param int|WP_Post|null $post Post.
 * @return string
 */
function gexis_read_time( $post = null ) {
	$post  = get_post( $post );
	$words = $post ? str_word_count( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ) ) : 0;
	/* translators: %d: minutes of reading. */
	return sprintf( __( '%d min', 'gexis' ), max( 1, (int) round( $words / 220 ) ) );
}

/**
 * "35 posts", "1 post".
 *
 * @param int $n Count.
 * @return string
 */
function gexis_count_posts( $n ) {
	/* translators: %s: number of posts. */
	return sprintf( _n( '%s post', '%s posts', $n, 'gexis' ), number_format_i18n( $n ) );
}

/**
 * "12 comments", "1 comment".
 *
 * @param int|WP_Post|null $post Post.
 * @return string
 */
function gexis_count_comments( $post = null ) {
	$n = (int) get_comments_number( $post );
	/* translators: %s: number of comments. */
	return sprintf( _n( '%s comment', '%s comments', $n, 'gexis' ), number_format_i18n( $n ) );
}

/**
 * A post's image at a theme size, or the empty well. The design never fakes
 * content to fill a slot; an empty well is honest about a post with no image.
 *
 * @param WP_Post $post Post.
 * @param string  $size Image size.
 * @param array   $attr Extra <img> attributes.
 * @return string HTML.
 */
function gexis_post_image( $post, $size, $attr = array() ) {
	if ( has_post_thumbnail( $post ) ) {
		return get_the_post_thumbnail( $post, $size, wp_parse_args( $attr, array( 'alt' => '', 'loading' => 'lazy' ) ) );
	}
	return '<span class="gx-well" aria-hidden="true"></span>';
}

/**
 * A row: thumbnail, title, tag diamonds and date. The design's most used
 * list item. Rendered here for the blocks that cannot use a core Query Loop
 * (month groups, older / newer).
 *
 * @param WP_Post $post  Post.
 * @param array   $args  { label: string above the title, compact: 56px thumb }.
 * @return string HTML.
 */
function gexis_row_html( $post, $args = array() ) {
	$args = wp_parse_args( $args, array( 'label' => '', 'compact' => false, 'rel' => '' ) );
	ob_start();
	?>
	<a class="gx-row<?php echo $args['compact'] ? ' gx-row--compact' : ''; ?>" href="<?php echo esc_url( get_permalink( $post ) ); ?>"<?php echo $args['rel'] ? ' rel="' . esc_attr( $args['rel'] ) . '"' : ''; ?>>
		<span class="gx-row__thumb"><?php echo gexis_post_image( $post, 'gexis-thumb', array( 'sizes' => '72px' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		<span class="gx-row__body">
			<?php if ( $args['label'] ) : ?>
				<span class="gx-label"><?php echo esc_html( $args['label'] ); ?></span>
			<?php endif; ?>
			<span class="gx-row__title"><?php echo esc_html( get_the_title( $post ) ); ?></span>
			<?php if ( ! $args['label'] ) : ?>
				<span class="gx-row__meta"><?php echo gexis_diamonds_html( $post ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="gx-row__date"><?php echo esc_html( gexis_date( $post ) ); ?></span></span>
			<?php endif; ?>
		</span>
	</a>
	<?php
	return ob_get_clean();
}

/**
 * Section label with its rule, and optional trailing HTML.
 *
 * @param string $label Label.
 * @param string $trail Trailing HTML, already escaped.
 * @return string HTML.
 */
function gexis_section_label_html( $label, $trail = '' ) {
	return sprintf(
		'<div class="gx-section-label"><span class="gx-label">%1$s</span><span class="gx-rule" aria-hidden="true"></span>%2$s</div>',
		esc_html( $label ),
		$trail
	);
}

/**
 * Where "Archive" goes: the posts page when there is one, otherwise the
 * theme's /archive/ listing (inc/routes.php).
 *
 * @return string
 */
function gexis_archive_url() {
	$page_for_posts = (int) get_option( 'page_for_posts' );
	if ( 'page' === get_option( 'show_on_front' ) && $page_for_posts ) {
		return get_permalink( $page_for_posts );
	}
	if ( get_option( 'permalink_structure' ) ) {
		return home_url( '/archive/' );
	}
	return add_query_arg( 'gexis_archive', '1', home_url( '/' ) );
}

/**
 * The search screen with no query.
 *
 * @return string
 */
function gexis_search_url() {
	return home_url( '/?s=' );
}

/**
 * The About page (slug `about`), if it exists.
 *
 * @return string
 */
function gexis_about_url() {
	$page = get_page_by_path( 'about' );
	return $page ? get_permalink( $page ) : home_url( '/about/' );
}
