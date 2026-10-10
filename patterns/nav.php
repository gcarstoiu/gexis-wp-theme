<?php
/**
 * Title: Header navigation
 * Slug: gexis/nav
 * Inserter: no
 * Description: Archive, About, Search and the colour-mode switch. Collapses to a Menu button under 760px.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:navigation {"overlayMenu":"mobile","hasIcon":false,"className":"gx-nav","layout":{"type":"flex","justifyContent":"right"}} -->
<!-- wp:navigation-link {"label":"<?php echo esc_attr__( 'Archive', 'gexis' ); ?>","url":"<?php echo esc_url( gexis_archive_url() ); ?>","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"<?php echo esc_attr__( 'About', 'gexis' ); ?>","url":"<?php echo esc_url( gexis_about_url() ); ?>","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"<?php echo esc_attr__( 'Search', 'gexis' ); ?>","url":"<?php echo esc_url( gexis_search_url() ); ?>","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:gexis/theme-toggle /-->
<!-- /wp:navigation -->
