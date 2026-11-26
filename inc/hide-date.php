<?php
/**
 * A per-post switch that hides the publish date.
 *
 * The date is drawn in several places (gexis/post-meta, the archive's month
 * cards, core/post-date in rows), so one post meta key is read by all of
 * them. The switch sits in the editor's Summary panel.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the meta key, editable through the REST API by anyone who can edit the post.
 */
function gexis_register_hide_date() {
	register_post_meta(
		'post',
		'gexis_hide_date',
		array(
			'type'          => 'boolean',
			'single'        => true,
			'default'       => false,
			'show_in_rest'  => true,
			'description'   => __( 'Hide the publish date on the post and in lists.', 'gexis' ),
			'auth_callback' => static function ( $allowed, $key, $post_id ) {
				return current_user_can( 'edit_post', $post_id );
			},
		)
	);
}
add_action( 'init', 'gexis_register_hide_date' );

/**
 * Whether a post hides its date.
 *
 * @param int|WP_Post|null $post Post.
 * @return bool
 */
function gexis_hides_date( $post = null ) {
	$post = get_post( $post );
	return $post && (bool) get_post_meta( $post->ID, 'gexis_hide_date', true );
}

/**
 * core/post-date draws nothing for a post that hides its date.
 *
 * @param string   $html     Rendered block.
 * @param array    $parsed   Parsed block.
 * @param WP_Block $instance Block instance.
 * @return string
 */
function gexis_hide_core_date( $html, $parsed, $instance ) {
	$id = $instance->context['postId'] ?? get_the_ID();
	return gexis_hides_date( $id ) ? '' : $html;
}
add_filter( 'render_block_core/post-date', 'gexis_hide_core_date', 10, 3 );

/**
 * The switch in the editor, for posts.
 */
function gexis_hide_date_editor() {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'post' !== $screen->post_type ) {
		return;
	}
	wp_enqueue_script(
		'gexis-hide-date',
		GEXIS_URI . '/assets/js/editor-hide-date.js',
		array( 'wp-plugins', 'wp-editor', 'wp-components', 'wp-data', 'wp-element', 'wp-i18n' ),
		GEXIS_VERSION,
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'gexis_hide_date_editor' );
