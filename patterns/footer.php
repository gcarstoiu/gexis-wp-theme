<?php
/**
 * Title: Footer
 * Slug: gexis/footer
 * Inserter: no
 * Description: The mark, the four tags, About me, This site and RSS.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;
?>
<!-- wp:group {"tagName":"footer","className":"gx-footer","layout":{"type":"default"}} -->
<footer class="wp-block-group gx-footer"><!-- wp:group {"className":"gx-footer__inner","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
<div class="wp-block-group gx-footer__inner"><!-- wp:gexis/mark {"size":20,"lead":"auto","isLink":true} /-->

<!-- wp:paragraph {"className":"gx-footer__links"} -->
<p class="gx-footer__links"><?php
foreach ( array_keys( gexis_tags() ) as $gexis_letter ) {
	printf( '<a href="%1$s">%2$s</a>', esc_url( gexis_tag_url( $gexis_letter ) ), esc_html( gexis_tag_name( $gexis_letter ) ) );
}
printf(
	'<a href="%1$s">%2$s</a><a href="%3$s">%4$s</a><a href="%5$s">%6$s</a>',
	esc_url( gexis_owner_url() ),
	esc_html__( 'About me', 'gexis' ),
	esc_url( gexis_site_page_url() ),
	esc_html__( 'This site', 'gexis' ),
	esc_url( get_feed_link() ),
	esc_html__( 'RSS', 'gexis' )
);
?></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></footer>
<!-- /wp:group -->
