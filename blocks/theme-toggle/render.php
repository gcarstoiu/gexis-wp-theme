<?php
/**
 * gexis/theme-toggle — switches the colour mode, naming the mode it switches
 * to: "Light" in the header bar, "Light mode" in the open menu. All four
 * labels are in the markup and CSS shows the right one for the mode and the
 * place, so it is correct before any script runs. assets/js/site.js does the
 * switching and stores localStorage['gexis-theme'].
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;

wp_enqueue_script( 'gexis-site' );

printf(
	'<button type="button" %1$s data-gexis-theme-toggle><span class="gx-theme-toggle__to-light"><span class="gx-theme-toggle__short">%2$s</span><span class="gx-theme-toggle__long">%3$s</span></span><span class="gx-theme-toggle__to-dark"><span class="gx-theme-toggle__short">%4$s</span><span class="gx-theme-toggle__long">%5$s</span></span></button>',
	get_block_wrapper_attributes( array( 'class' => 'gx-theme-toggle' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	esc_html__( 'Light', 'gexis' ),
	esc_html__( 'Light mode', 'gexis' ),
	esc_html__( 'Dark', 'gexis' ),
	esc_html__( 'Dark mode', 'gexis' )
);
