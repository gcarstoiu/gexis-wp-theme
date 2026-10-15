<?php
/**
 * gexis — a block theme.
 *
 * Templates, parts and patterns are block markup built from core blocks. PHP
 * is here only for what core blocks cannot do; each file under inc/ says what
 * that is.
 *
 * @package gexis
 * @license GPL-3.0-or-later
 */

defined( 'ABSPATH' ) || exit;

define( 'GEXIS_VERSION', wp_get_theme( get_template() )->get( 'Version' ) );
define( 'GEXIS_DIR', get_template_directory() );
define( 'GEXIS_URI', get_template_directory_uri() );

require GEXIS_DIR . '/inc/tags.php';         // The four gexis tags: letter, colour, name.
require GEXIS_DIR . '/inc/helpers.php';      // Formatting: dates, counts, read time, URLs.
require GEXIS_DIR . '/inc/markup.php';       // Shared block markup for the patterns.
require GEXIS_DIR . '/inc/setup.php';        // Supports, image sizes, styles, head script.
require GEXIS_DIR . '/inc/blocks.php';       // gexis/* blocks and filters on core blocks.
require GEXIS_DIR . '/inc/queries.php';      // Home de-duplication and query roles.
require GEXIS_DIR . '/inc/comments.php';     // Comment form, avatars, toasts.
require GEXIS_DIR . '/inc/routes.php';       // /archive/ and the empty search screen.
require GEXIS_DIR . '/inc/customizer.php';   // Default ground image, search suggestions.
require GEXIS_DIR . '/inc/author-meta.php';  // CV fields on the user profile.
require GEXIS_DIR . '/inc/repo-doc.php';     // gexis/repo-doc: Markdown from GitHub, cached.
