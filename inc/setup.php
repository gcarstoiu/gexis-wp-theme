<?php
/**
 * Theme setup, styles and the head.
 *
 * PHP because: image sizes, the colour-mode script that must run before
 * first paint, and inlining the token CSS have no theme.json equivalent.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;

/**
 * Supports and image sizes.
 */
function gexis_setup() {
	load_theme_textdomain( 'gexis', GEXIS_DIR . '/languages' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_post_type_support( 'page', 'excerpt' );
	remove_theme_support( 'core-block-patterns' );

	add_image_size( 'gexis-square', 1000, 1000, true );
	add_image_size( 'gexis-wide', 1400, 900, true );
	add_image_size( 'gexis-thumb', 240, 240, true );
	add_image_size( 'gexis-ground', 400, 400, false );

	add_editor_style( gexis_style_files() );
}
add_action( 'after_setup_theme', 'gexis_setup' );

/**
 * Image sizes offered in the editor.
 *
 * @param array $sizes Sizes.
 * @return array
 */
function gexis_image_size_names( $sizes ) {
	return array_merge(
		$sizes,
		array(
			'gexis-square' => __( 'Square (1000)', 'gexis' ),
			'gexis-wide'   => __( 'Wide (1400×900)', 'gexis' ),
			'gexis-thumb'  => __( 'Thumbnail (240)', 'gexis' ),
		)
	);
}
add_filter( 'image_size_names_choose', 'gexis_image_size_names' );

/**
 * Token files, in cascade order. The design-system files under
 * assets/css/ds/ are copied verbatim from the handoff and keep its names.
 *
 * @return string[] Theme-relative paths.
 */
function gexis_style_files() {
	return array(
		'assets/css/ds/colors.css',
		'assets/css/ds/typography.css',
		'assets/css/ds/metrics.css',
		'assets/css/ds/motion.css',
		'assets/css/ds/elevation.css',
		'assets/css/ds/brand.css',
		'assets/css/modes.css',
		'style.css',
	);
}

/**
 * Tokens and modes are inlined (they are what first paint needs, and small
 * once their comments go); the component CSS is one stylesheet.
 */
function gexis_enqueue_styles() {
	wp_register_style( 'gexis-tokens', false, array(), GEXIS_VERSION );
	wp_enqueue_style( 'gexis-tokens' );
	$css = '';
	foreach ( array_slice( gexis_style_files(), 0, -1 ) as $file ) {
		$css .= (string) file_get_contents( GEXIS_DIR . '/' . $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	}
	$css = preg_replace( '#/\*.*?\*/#s', '', $css );
	$css = preg_replace( '/\s+/', ' ', $css );
	wp_add_inline_style( 'gexis-tokens', trim( $css ) );

	wp_enqueue_style( 'gexis', get_stylesheet_uri(), array( 'gexis-tokens' ), GEXIS_VERSION );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'gexis_enqueue_styles' );

/**
 * Colour mode before first paint: the saved choice, else the OS preference.
 * Sets <html data-theme>. Runs first in <head> so nothing flashes.
 */
function gexis_mode_script() {
	echo "<script>(function(){try{var t=localStorage.getItem('gexis-theme');}catch(e){}if(t!=='light'&&t!=='dark'){t=matchMedia('(prefers-color-scheme: light)').matches?'light':'dark';}document.documentElement.setAttribute('data-theme',t);})();</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_head', 'gexis_mode_script', 0 );

/**
 * Preload the two faces above the fold, and the favicon when no Site Icon is
 * set: the master mark at favicon size, four colours and no letters.
 */
function gexis_head_links() {
	foreach ( array( 'NunitoSans-Variable.woff2', 'BricolageGrotesque-Logo.woff2' ) as $font ) {
		printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( GEXIS_URI . '/assets/fonts/' . $font ) );
	}
	if ( ! has_site_icon() ) {
		printf( '<link rel="icon" type="image/svg+xml" href="%s">' . "\n", esc_url( GEXIS_URI . '/assets/images/favicon.svg' ) );
	}
}
add_action( 'wp_head', 'gexis_head_links', 1 );

/*
 * Speed is a stated requirement: no emoji script or styles.
 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
