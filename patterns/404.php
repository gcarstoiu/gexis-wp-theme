<?php
/**
 * Title: Not found
 * Slug: gexis/404
 * Inserter: no
 * Description: The master mark, 404, Home and Search, and the three latest posts.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"tagName":"main","className":"gx-main gx-404","layout":{"type":"default"}} -->
<main class="wp-block-group gx-main gx-404"><!-- wp:gexis/mark {"size":96,"align":"center"} /-->

<!-- wp:paragraph {"className":"gx-404__code"} -->
<p class="gx-404__code">404</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1,"className":"gx-head__title"} -->
<h1 class="wp-block-heading gx-head__title"><?php echo esc_html__( 'Page not found', 'gexis' ); ?></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"gx-404__line"} -->
<p class="gx-404__line"><?php echo esc_html__( 'The address may be mistyped, or the post was moved.', 'gexis' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"className":"gx-404__buttons","layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons gx-404__buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html__( 'Home', 'gexis' ); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-gx-quiet"} -->
<div class="wp-block-button is-style-gx-quiet"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( gexis_search_url() ); ?>"><?php echo esc_html__( 'Search', 'gexis' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->

<!-- wp:group {"className":"gx-404__latest","layout":{"type":"default"}} -->
<div class="wp-block-group gx-404__latest"><?php echo gexis_m_label( __( 'Latest', 'gexis' ) ); // phpcs:ignore ?>

<?php echo gexis_m_query( array( 'perPage' => 3 ), gexis_m_row( array( 'plain' => true ) ), 'gx-rows gx-rows--grid', 50 ); // phpcs:ignore ?></div>
<!-- /wp:group --></main>
<!-- /wp:group -->
