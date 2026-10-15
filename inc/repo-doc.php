<?php
/**
 * gexis/repo-doc: a Markdown document from a public GitHub repository,
 * fetched, converted and shown as a page, so pages that carry the Gexis
 * Player's FAQ, manual or release notes stay current with no manual step.
 *
 * PHP because: the document is fetched and converted on the server, its
 * links are rewritten against the pages that show the other documents, and
 * both the result and the last good copy are kept in WordPress.
 *
 * How it stays current, and what happens when GitHub does not answer:
 * - The rendered HTML is cached in a transient for an hour.
 * - Every good fetch also saves the Markdown in an option (not autoloaded).
 *   When a fetch fails, that copy is rendered instead, and the failure is
 *   cached for ten minutes so GitHub is not asked on every view. With no copy
 *   at all, a short notice links to the file on GitHub. Never an error page.
 *
 * The block can only fetch from raw.githubusercontent.com: the host is fixed,
 * and repository, branch and path are checked before they go into the URL.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;

/** Seconds a rendered document is kept. */
define( 'GEXIS_REPO_DOC_TTL', HOUR_IN_SECONDS );

/** Seconds a failed fetch is remembered before GitHub is asked again. */
define( 'GEXIS_REPO_DOC_RETRY', 10 * MINUTE_IN_SECONDS );

/**
 * The block's attributes, checked. Anything that does not pass leaves the
 * block with nothing to fetch.
 *
 * @param array $attributes Block attributes.
 * @return array|null repo, branch, path, drop_title; null if invalid.
 */
function gexis_repo_doc_source( $attributes ) {
	$repo   = trim( (string) ( $attributes['repo'] ?? '' ) );
	$branch = trim( (string) ( $attributes['branch'] ?? '' ) );
	$path   = ltrim( trim( (string) ( $attributes['path'] ?? '' ) ), '/' );
	$repo   = '' === $repo ? 'gcarstoiu/gexis-player' : $repo;
	$branch = '' === $branch ? 'main' : $branch;

	if ( ! preg_match( '#^[A-Za-z0-9_.-]+/[A-Za-z0-9_.-]+$#', $repo )
		|| ! preg_match( '#^[A-Za-z0-9_.-]+(/[A-Za-z0-9_.-]+)*$#', $branch )
		|| ! preg_match( '#^[A-Za-z0-9_. -]+(/[A-Za-z0-9_. -]+)*\.md$#i', $path ) ) {
		return null;
	}
	foreach ( array( $repo, $branch, $path ) as $value ) {
		foreach ( explode( '/', $value ) as $segment ) {
			if ( '.' === $segment || '..' === $segment ) {
				return null;
			}
		}
	}
	return array(
		'repo'       => $repo,
		'branch'     => $branch,
		'path'       => $path,
		'drop_title' => ! isset( $attributes['dropTitle'] ) || (bool) $attributes['dropTitle'],
	);
}

/**
 * A repository path, URL-encoded segment by segment.
 *
 * @param string $path Path.
 * @return string
 */
function gexis_repo_doc_encode_path( $path ) {
	return implode( '/', array_map( 'rawurlencode', explode( '/', $path ) ) );
}

/**
 * The file's raw address (what is fetched, and where images are served from).
 *
 * @param string $repo   owner/name.
 * @param string $branch Branch.
 * @param string $path   Repository path.
 * @return string
 */
function gexis_repo_doc_raw_url( $repo, $branch, $path ) {
	return 'https://raw.githubusercontent.com/' . $repo . '/' . gexis_repo_doc_encode_path( $branch ) . '/' . gexis_repo_doc_encode_path( $path );
}

/**
 * The file's page on GitHub.
 *
 * @param string $repo   owner/name.
 * @param string $branch Branch.
 * @param string $path   Repository path.
 * @return string
 */
function gexis_repo_doc_blob_url( $repo, $branch, $path ) {
	return 'https://github.com/' . $repo . '/blob/' . gexis_repo_doc_encode_path( $branch ) . '/' . gexis_repo_doc_encode_path( $path );
}

/**
 * The document, rendered: from the cache, a fresh fetch, or the last good
 * copy, in that order.
 *
 * @param array $src Checked source (gexis_repo_doc_source()).
 * @return array html (body, already filtered), saved (Unix time of the copy
 *               shown when it is not fresh, else 0), ok (false: no copy).
 */
function gexis_repo_doc_render( $src ) {
	$map  = gexis_repo_doc_page_map();
	$id   = $src['repo'] . '|' . $src['branch'] . '|' . $src['path'];
	$key  = 'gexis_rd_' . md5( $id . '|' . (int) $src['drop_title'] . '|' . GEXIS_VERSION . '|' . $map['hash'] );
	$hit  = get_transient( $key );
	if ( is_array( $hit ) && isset( $hit['html'] ) ) {
		return $hit;
	}

	$store    = 'gexis_repo_doc_' . md5( $id );
	$response = wp_safe_remote_get(
		gexis_repo_doc_raw_url( $src['repo'], $src['branch'], $src['path'] ),
		array(
			'timeout'             => 8,
			'redirection'         => 0,
			'limit_response_size' => 2 * MB_IN_BYTES,
		)
	);
	$markdown = '';
	if ( ! is_wp_error( $response ) && 200 === (int) wp_remote_retrieve_response_code( $response ) ) {
		$markdown = (string) wp_remote_retrieve_body( $response );
	}

	if ( '' !== trim( $markdown ) ) {
		update_option( $store, array( 'markdown' => $markdown, 'time' => time() ), false );
		$out = array(
			'html'  => gexis_repo_doc_html( $markdown, $src, $map['pages'] ),
			'saved' => 0,
			'ok'    => true,
		);
		set_transient( $key, $out, GEXIS_REPO_DOC_TTL );
		return $out;
	}

	$copy = get_option( $store );
	if ( is_array( $copy ) && ! empty( $copy['markdown'] ) ) {
		$out = array(
			'html'  => gexis_repo_doc_html( $copy['markdown'], $src, $map['pages'] ),
			'saved' => (int) $copy['time'],
			'ok'    => true,
		);
	} else {
		$out = array(
			'html'  => '',
			'saved' => 0,
			'ok'    => false,
		);
	}
	set_transient( $key, $out, GEXIS_REPO_DOC_RETRY );
	return $out;
}

/**
 * Markdown to the HTML the page shows: converted by Parsedown (GitHub tables
 * included), then the title dropped if asked, headings given GitHub's
 * anchors, Mermaid diagrams pointed at GitHub, links and images rewritten
 * for the blog, and the whole filtered as post content.
 *
 * @param string $markdown Document.
 * @param array  $src      Checked source.
 * @param array  $pages    Page map (gexis_repo_doc_page_map()['pages']).
 * @return string
 */
function gexis_repo_doc_html( $markdown, $src, $pages ) {
	if ( ! class_exists( 'Parsedown' ) ) {
		require_once GEXIS_DIR . '/inc/vendor/Parsedown.php';
	}
	$parser = new Parsedown();
	$html   = $parser->text( $markdown );
	$blob   = gexis_repo_doc_blob_url( $src['repo'], $src['branch'], $src['path'] );

	/* Mermaid is drawn by GitHub's own script; here it would be source text. */
	$html = preg_replace(
		'#<pre><code class="language-mermaid">.*?</code></pre>#s',
		'<p class="gx-repo-doc__diagram">' . sprintf(
			/* translators: %s: link to the file on GitHub. */
			esc_html__( 'This diagram is on %s.', 'gexis' ),
			'<a href="' . esc_url( $blob ) . '">GitHub</a>'
		) . '</p>',
		$html
	);

	/* Headings: GitHub's slugs, numbered on repeats; the first H1 dropped. */
	$seen    = array();
	$dropped = ! $src['drop_title'];
	$html    = preg_replace_callback(
		'#<h([1-6])>(.*?)</h\1>#s',
		static function ( $m ) use ( &$seen, &$dropped ) {
			$slug = gexis_repo_doc_slug( $m[2] );
			$id   = $slug;
			if ( isset( $seen[ $slug ] ) ) {
				$id = $slug . '-' . $seen[ $slug ];
				++$seen[ $slug ];
			} else {
				$seen[ $slug ] = 1;
			}
			if ( ! $dropped && '1' === $m[1] ) {
				$dropped = true;
				return '';
			}
			return sprintf( '<h%1$s id="%2$s">%3$s</h%1$s>', $m[1], esc_attr( $id ), $m[2] );
		},
		$html
	);

	/* Tables scroll inside their own box on a narrow screen. */
	$html = str_replace( array( '<table>', '</table>' ), array( '<div class="gx-repo-doc__table"><table>', '</table></div>' ), $html );

	$dir = dirname( $src['path'] );
	$dir = '.' === $dir ? '' : $dir;
	$p   = new WP_HTML_Tag_Processor( $html );
	while ( $p->next_tag() ) {
		$tag = $p->get_tag();
		if ( 'A' === $tag ) {
			$href = $p->get_attribute( 'href' );
			if ( is_string( $href ) ) {
				$p->set_attribute( 'href', gexis_repo_doc_link( $href, $dir, $src, $pages ) );
			}
		} elseif ( 'IMG' === $tag ) {
			$url = $p->get_attribute( 'src' );
			if ( is_string( $url ) && ! gexis_repo_doc_is_absolute( $url ) ) {
				$file = gexis_repo_doc_resolve( strtok( $url, '?#' ), $dir );
				if ( null !== $file ) {
					$p->set_attribute( 'src', gexis_repo_doc_raw_url( $src['repo'], $src['branch'], $file ) );
				}
			}
			$p->set_attribute( 'loading', 'lazy' );
			$p->set_attribute( 'decoding', 'async' );
		}
	}

	return wp_kses_post( $p->get_updated_html() );
}

/**
 * GitHub's heading anchor: the text, lower case, with punctuation removed
 * and each space a hyphen ("1. The panel at a glance" → "1-the-panel-at-a-glance").
 *
 * @param string $html Heading content.
 * @return string
 */
function gexis_repo_doc_slug( $html ) {
	$text = html_entity_decode( wp_strip_all_tags( $html ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	$text = function_exists( 'mb_strtolower' ) ? mb_strtolower( $text, 'UTF-8' ) : strtolower( $text );
	$text = preg_replace( '/[^\p{L}\p{M}\p{N}\p{Pc} -]/u', '', trim( $text ) );
	return str_replace( ' ', '-', (string) $text );
}

/**
 * Whether a link has its own scheme or host (or is protocol-relative).
 *
 * @param string $url URL.
 * @return bool
 */
function gexis_repo_doc_is_absolute( $url ) {
	return (bool) preg_match( '#^([a-z][a-z0-9+.-]*:|//)#i', $url );
}

/**
 * A link in the document, relative to its directory, as a repository path:
 * "../FAQ.md" from docs/manual → "docs/FAQ.md". A leading slash is the
 * repository root, as on GitHub.
 *
 * @param string $link Relative path, no query or fragment.
 * @param string $dir  The document's directory ('' at the root).
 * @return string|null Null if it climbs out of the repository.
 */
function gexis_repo_doc_resolve( $link, $dir ) {
	$link  = rawurldecode( (string) $link );
	$parts = 0 === strpos( $link, '/' ) ? array() : ( '' === $dir ? array() : explode( '/', $dir ) );
	foreach ( explode( '/', $link ) as $segment ) {
		if ( '' === $segment || '.' === $segment ) {
			continue;
		}
		if ( '..' === $segment ) {
			if ( ! $parts ) {
				return null;
			}
			array_pop( $parts );
			continue;
		}
		$parts[] = $segment;
	}
	return implode( '/', $parts );
}

/**
 * Where a link in the document should go on the blog. In-page anchors and
 * absolute URLs stay. A Markdown file goes to the page that shows it, if
 * there is one, otherwise to the file on GitHub; any other file goes to
 * GitHub. The fragment is kept.
 *
 * @param string $href  Link as written.
 * @param string $dir   The document's directory.
 * @param array  $src   Checked source.
 * @param array  $pages Page map.
 * @return string
 */
function gexis_repo_doc_link( $href, $dir, $src, $pages ) {
	if ( '' === $href || '#' === $href[0] || gexis_repo_doc_is_absolute( $href ) ) {
		return $href;
	}
	$hash = '';
	$pos  = strpos( $href, '#' );
	if ( false !== $pos ) {
		$hash = substr( $href, $pos );
		$href = substr( $href, 0, $pos );
	}
	$file = gexis_repo_doc_resolve( strtok( $href, '?' ), $dir );
	if ( null === $file ) {
		return $href . $hash;
	}
	if ( '' === $file ) {
		return 'https://github.com/' . $src['repo'] . $hash;
	}
	$page = $pages[ $src['repo'] . '|' . $src['branch'] . '|' . $file ] ?? '';
	if ( '' !== $page ) {
		return $page . $hash;
	}
	return gexis_repo_doc_blob_url( $src['repo'], $src['branch'], $file ) . $hash;
}

/**
 * Which published page shows which document: every gexis/repo-doc block in a
 * published page or post, keyed "repo|branch|path", to its address. Cached
 * until a post is saved or deleted. The hash goes into the rendered cache's
 * key, so a new or renamed page re-renders the documents that link to it.
 *
 * @return array pages (key => permalink), hash.
 */
function gexis_repo_doc_page_map() {
	$map = get_transient( 'gexis_repo_doc_pages' );
	if ( is_array( $map ) && isset( $map['pages'], $map['hash'] ) ) {
		return $map;
	}
	global $wpdb;
	$ids = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type IN ('page', 'post') AND post_content LIKE %s ORDER BY post_type = 'page' DESC, ID ASC",
			'%' . $wpdb->esc_like( '<!-- wp:gexis/repo-doc' ) . '%'
		)
	);
	$pages = array();
	foreach ( $ids as $post_id ) {
		gexis_repo_doc_collect( parse_blocks( (string) get_post_field( 'post_content', (int) $post_id ) ), (int) $post_id, $pages );
	}
	$map = array(
		'pages' => $pages,
		'hash'  => md5( wp_json_encode( $pages ) ),
	);
	set_transient( 'gexis_repo_doc_pages', $map, DAY_IN_SECONDS );
	return $map;
}

/**
 * Add the documents shown by a page's blocks, nested ones included, to the
 * map. The first page found for a document keeps it (pages before posts).
 *
 * @param array $blocks  Parsed blocks.
 * @param int   $post_id Post ID.
 * @param array $pages   Map, by reference.
 */
function gexis_repo_doc_collect( $blocks, $post_id, &$pages ) {
	foreach ( $blocks as $block ) {
		if ( 'gexis/repo-doc' === $block['blockName'] ) {
			$src = gexis_repo_doc_source( $block['attrs'] );
			if ( $src ) {
				$key = $src['repo'] . '|' . $src['branch'] . '|' . $src['path'];
				if ( ! isset( $pages[ $key ] ) ) {
					$pages[ $key ] = (string) get_permalink( $post_id );
				}
			}
		}
		if ( ! empty( $block['innerBlocks'] ) ) {
			gexis_repo_doc_collect( $block['innerBlocks'], $post_id, $pages );
		}
	}
}

/**
 * Forget the page map when a post changes, so links follow new, moved or
 * removed pages.
 */
function gexis_repo_doc_forget_pages() {
	delete_transient( 'gexis_repo_doc_pages' );
}
add_action( 'save_post', 'gexis_repo_doc_forget_pages' );
add_action( 'deleted_post', 'gexis_repo_doc_forget_pages' );
add_action( 'trashed_post', 'gexis_repo_doc_forget_pages' );
