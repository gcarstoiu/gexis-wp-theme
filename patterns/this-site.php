<?php
/**
 * Title: This site: the four tags
 * Slug: gexis/this-site
 * Categories: gexis, about
 * Post Types: page
 * Description: An intro line, the four gexis tag cards with their counts, and two short sections. Insert it on the This site page (slug `site`).
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:paragraph -->
<p><?php echo esc_html__( 'gexis is a personal site in four parts. Each takes a letter of the mark, and a post can belong to more than one.', 'gexis' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:gexis/tag-cards /-->

<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html__( 'Comments', 'gexis' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html__( 'Comments are open on every post. The first comment from a new address is held for approval; after that, comments appear straight away.', 'gexis' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading"><?php echo esc_html__( 'Elsewhere', 'gexis' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php echo esc_html__( 'An RSS feed covers everything; each tag has its own feed at the same address with the tag name appended.', 'gexis' ); ?></p>
<!-- /wp:paragraph -->
