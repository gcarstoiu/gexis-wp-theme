<?php
/**
 * Title: Home
 * Slug: gexis/home
 * Inserter: no
 * Description: The front page: the lead post as a wide picture, the four tags, then the next posts as a bento grid with the Guides beside them. No post appears twice.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"tagName":"main","className":"gx-main gx-home","layout":{"type":"default"}} -->
<main class="wp-block-group gx-main gx-home"><?php echo gexis_m_query( array( 'perPage' => 1, 'gexis' => 'lead' ), gexis_m_hero(), 'gx-hero-list', 1 ); // phpcs:ignore ?>

<!-- wp:gexis/tag-cards {"variant":"strip"} /-->

<!-- wp:group {"className":"gx-bento","layout":{"type":"default"}} -->
<div class="wp-block-group gx-bento"><?php echo gexis_m_bento( 'home', array( 'perPage' => 4, 'gexis' => 'cards' ), 2 ); // phpcs:ignore ?>

<!-- wp:group {"className":"gx-guides","layout":{"type":"default"}} -->
<div class="wp-block-group gx-guides"><?php echo gexis_m_label( __( 'Guides', 'gexis' ) ); // phpcs:ignore ?>

<?php echo gexis_m_query( array( 'gexis' => 'guides', 'postType' => 'page', 'perPage' => 5 ), gexis_m_row( array( 'guide' => true ) ), 'gx-rows gx-rows--guides', 3 ); // phpcs:ignore ?></div>
<!-- /wp:group -->

<?php echo gexis_m_bento( 'more', array( 'perPage' => 3, 'gexis' => 'latest' ), 4 ); // phpcs:ignore ?></div>
<!-- /wp:group -->

<!-- wp:paragraph {"className":"gx-more"} -->
<p class="gx-more"><a href="<?php echo esc_url( gexis_archive_url() ); ?>"><?php echo esc_html__( 'The whole archive', 'gexis' ); ?></a></p>
<!-- /wp:paragraph --></main>
<!-- /wp:group -->
