<?php
/**
 * Comments: the form, avatars and the confirmation toast.
 *
 * PHP because: the form's fields, placeholders and button text come from
 * comment_form() arguments; the initials fallback for avatars has to wrap
 * the <img> core prints; and the toast depends on where WordPress redirects
 * after a comment is posted.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;

/**
 * The form as designed: textarea first, then Name, Email and Post comment
 * side by side. Labels stay for screen readers; the placeholders show.
 *
 * @param array $defaults comment_form() defaults.
 * @return array
 */
function gexis_comment_form_defaults( $defaults ) {
	$commenter = wp_get_current_commenter();
	$req       = get_option( 'require_name_email' );
	$aria_req  = $req ? ' required' : '';

	/* Name and Email replace core's; the website field goes; cookie consent stays. */
	unset( $defaults['fields']['url'] );
	$defaults['fields'] = array_merge(
		$defaults['fields'],
		array(
		'author' => sprintf(
			'<p class="comment-form-author"><label class="screen-reader-text" for="author">%1$s</label><input id="author" name="author" type="text" value="%2$s" placeholder="%1$s" autocomplete="name" maxlength="245"%3$s></p>',
			esc_html__( 'Name', 'gexis' ),
			esc_attr( $commenter['comment_author'] ),
			$aria_req
		),
		'email'  => sprintf(
			'<p class="comment-form-email"><label class="screen-reader-text" for="email">%1$s</label><input id="email" name="email" type="email" value="%2$s" placeholder="%1$s" autocomplete="email" maxlength="100"%3$s></p>',
			esc_html__( 'Email, not published', 'gexis' ),
			esc_attr( $commenter['comment_author_email'] ),
			$aria_req
		),
		)
	);

	$defaults['comment_field'] = sprintf(
		'<p class="comment-form-comment"><label class="screen-reader-text" for="comment">%1$s</label><textarea id="comment" name="comment" rows="4" maxlength="65525" placeholder="%1$s" required></textarea></p>',
		esc_html__( 'Comment', 'gexis' )
	);
	$defaults['comment_notes_before'] = '';
	$defaults['comment_notes_after']  = '';
	$defaults['title_reply']          = '';
	/* translators: %s: the name being replied to. */
	$defaults['title_reply_to']       = __( 'Replying to %s', 'gexis' );
	$defaults['title_reply_before']   = '<p id="reply-title" class="comment-reply-title gx-reply-title">';
	$defaults['title_reply_after']    = '</p>';
	$defaults['cancel_reply_link']    = __( 'Cancel', 'gexis' );
	$defaults['label_submit']         = __( 'Post comment', 'gexis' );
	$defaults['class_submit']         = 'submit gx-submit';
	$defaults['class_container']      = 'comment-respond gx-respond';
	return $defaults;
}
add_filter( 'comment_form_defaults', 'gexis_comment_form_defaults' );

/**
 * The design threads one level deep: replies sit under the comment they
 * answer, and a reply to a reply joins the same thread.
 *
 * @return int
 */
function gexis_thread_depth() {
	return 2;
}
add_filter( 'pre_option_thread_comments_depth', 'gexis_thread_depth' );

/**
 * Initials on a tint hashed from the name (reference/ArtistAvatar.jsx), under
 * the Gravatar. The Gravatar is requested with d=blank, so a missing one is
 * transparent and the initials show through. No request is made to find out.
 *
 * @param string $name Name.
 * @return string[] { initials, tint }.
 */
function gexis_initials( $name ) {
	$tints = array(
		'rgba(126,214,188,0.22)',
		'rgba(159,180,232,0.22)',
		'rgba(242,164,143,0.22)',
		'rgba(200,162,216,0.22)',
		'rgba(232,160,180,0.22)',
		'rgba(143,196,216,0.22)',
	);
	$name  = trim( (string) $name );
	$h     = 0;
	foreach ( preg_split( '//u', $name, -1, PREG_SPLIT_NO_EMPTY ) as $ch ) {
		$code = mb_ord( $ch, 'UTF-8' );
		/* JS iterates UTF-16 code units; astral characters are two of them. */
		if ( $code > 0xFFFF ) {
			$code -= 0x10000;
			$h     = ( $h * 31 + ( 0xD800 + ( $code >> 10 ) ) ) % 997;
			$h     = ( $h * 31 + ( 0xDC00 + ( $code & 0x3FF ) ) ) % 997;
		} else {
			$h = ( $h * 31 + $code ) % 997;
		}
	}
	$words    = array_slice( preg_split( '/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY ), 0, 2 );
	$initials = '';
	foreach ( $words as $word ) {
		$initials .= mb_substr( $word, 0, 1 );
	}
	return array(
		'initials' => '' === $initials ? '?' : mb_strtoupper( $initials ),
		'tint'     => $tints[ $h % count( $tints ) ],
	);
}

/**
 * The avatar circle.
 *
 * @param mixed  $who  User ID, email, WP_Comment...
 * @param string $name Name for initials and tint.
 * @param int    $size Pixels.
 * @param string $img  Optional <img> already rendered by core.
 * @return string HTML.
 */
function gexis_avatar_html( $who, $name, $size, $img = null ) {
	$i = gexis_initials( $name );
	if ( null === $img && get_option( 'show_avatars' ) ) {
		$img = get_avatar( $who, $size, 'blank', '', array( 'loading' => 'lazy' ) );
	}
	return sprintf(
		'<span class="gx-avatar" style="--av-size:%1$dpx;--av-tint:%2$s"><span class="gx-avatar__initials" aria-hidden="true">%3$s</span>%4$s</span>',
		(int) $size,
		esc_attr( $i['tint'] ),
		esc_html( $i['initials'] ),
		(string) $img
	);
}

/**
 * Avatars request d=blank on the front end, so a missing Gravatar is
 * transparent and the initials circle shows through.
 *
 * @param array $args Avatar data args.
 * @return array
 */
function gexis_avatar_blank_default( $args ) {
	if ( ! is_admin() ) {
		$args['default'] = 'blank';
	}
	return $args;
}
add_filter( 'pre_get_avatar_data', 'gexis_avatar_blank_default' );

/**
 * Wrap a rendered avatar block in the initials circle.
 *
 * @param string   $html   Block HTML.
 * @param array    $parsed Parsed block.
 * @param WP_Block $block  Instance.
 * @return string
 */
function gexis_wrap_avatar_block( $html, $parsed, $block ) {
	if ( '' === trim( $html ) ) {
		return $html;
	}
	$size = (int) ( $parsed['attrs']['size'] ?? 96 );
	$name = '';
	if ( isset( $block->context['commentId'] ) ) {
		$comment = get_comment( $block->context['commentId'] );
		$name    = $comment ? $comment->comment_author : '';
	} elseif ( isset( $parsed['attrs']['userId'] ) ) {
		$user = get_userdata( (int) $parsed['attrs']['userId'] );
		$name = $user ? $user->display_name : '';
	} elseif ( isset( $block->context['postId'] ) ) {
		$name = get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $block->context['postId'] ) );
	}
	$img = '';
	if ( preg_match( '#<img[^>]*>#', $html, $m ) ) {
		$img = $m[0];
	}
	/* Keep core's wrapper (and its link, if any); replace the image inside. */
	return '' === $img ? $html : str_replace( $img, gexis_avatar_html( 0, $name, $size, $img ), $html );
}
add_filter( 'render_block_core/avatar', 'gexis_wrap_avatar_block', 10, 3 );


/**
 * After a comment is posted, come back with a flag the footer turns into a
 * toast: posted, reply posted, or held for approval.
 *
 * @param string     $location Redirect URL.
 * @param WP_Comment $comment  The new comment.
 * @return string
 */
function gexis_comment_redirect( $location, $comment ) {
	if ( '1' !== (string) $comment->comment_approved ) {
		$flag = 'held';
	} else {
		$flag = $comment->comment_parent ? 'reply' : 'comment';
	}
	$hash = '';
	if ( false !== strpos( $location, '#' ) ) {
		list( $location, $hash ) = explode( '#', $location, 2 );
		$hash                    = '#' . $hash;
	}
	return add_query_arg( 'commented', $flag, $location ) . $hash;
}
add_filter( 'comment_post_redirect', 'gexis_comment_redirect', 10, 2 );

/**
 * The toast: past tense, gone after 2.4s. Drawn by CSS alone, so it needs no
 * script. Also the host for toasts site.js raises (PDF downloaded).
 */
function gexis_toast() {
	$messages = array(
		'comment' => __( 'Comment posted', 'gexis' ),
		'reply'   => __( 'Reply posted', 'gexis' ),
		'held'    => __( 'Comment held for approval', 'gexis' ),
	);
	$flag = isset( $_GET['commented'] ) ? sanitize_key( wp_unslash( $_GET['commented'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( isset( $messages[ $flag ] ) ) {
		printf( '<div class="gx-toast" role="status">%s</div>', esc_html( $messages[ $flag ] ) );
	} else {
		echo '<div class="gx-toast" role="status" hidden></div>';
	}
}
add_action( 'wp_footer', 'gexis_toast' );

/**
 * Replies to a reply join the same thread: on a second-level comment, core
 * prints no Reply link (it is at the maximum depth), so print one that
 * replies to its parent instead.
 *
 * @param string   $html   Block HTML.
 * @param array    $parsed Parsed block.
 * @param WP_Block $block  Instance.
 * @return string
 */
function gexis_reply_to_thread( $html, $parsed, $block ) {
	if ( '' !== trim( $html ) || empty( $block->context['commentId'] ) ) {
		return $html;
	}
	$comment = get_comment( $block->context['commentId'] );
	if ( ! $comment || ! $comment->comment_parent ) {
		return $html;
	}
	$link = get_comment_reply_link(
		array(
			'depth'     => 1,
			'max_depth' => 2,
		),
		(int) $comment->comment_parent,
		(int) $comment->comment_post_ID
	);
	return $link ? '<div class="wp-block-comment-reply-link">' . $link . '</div>' : $html;
}
add_filter( 'render_block_core/comment-reply-link', 'gexis_reply_to_thread', 10, 3 );
