<?php
/**
 * Customizer: the default ground image, the owner "About me" points to, and
 * the suggested searches.
 *
 * PHP because: a theme setting holding an image has no theme.json form, and
 * the design names the Customizer for both.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;

/**
 * @param WP_Customize_Manager $wp_customize Manager.
 */
function gexis_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'gexis',
		array(
			'title'    => __( 'gexis', 'gexis' ),
			'priority' => 30,
		)
	);

	$wp_customize->add_setting(
		'gexis_default_ground',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'gexis_default_ground',
			array(
				'section'     => 'gexis',
				'label'       => __( 'Default background image', 'gexis' ),
				'description' => __( 'Shown blurred behind pages with no image of their own: author, archive, search, pages, 404, and posts without a featured image. Empty means the weave alone.', 'gexis' ),
				'mime_type'   => 'image',
			)
		)
	);

	$owners = array( 0 => __( 'First administrator', 'gexis' ) );
	foreach ( get_users( array( 'capability' => array( 'edit_posts' ), 'orderby' => 'display_name' ) ) as $user ) {
		$owners[ $user->ID ] = $user->display_name;
	}
	$wp_customize->add_setting(
		'gexis_owner',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'gexis_owner',
		array(
			'section'     => 'gexis',
			'type'        => 'select',
			'choices'     => $owners,
			'label'       => __( 'Owner', 'gexis' ),
			'description' => __( 'Whose author page "About me" opens.', 'gexis' ),
		)
	);

	$wp_customize->add_setting(
		'gexis_search_suggestions',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'gexis_search_suggestions',
		array(
			'section'     => 'gexis',
			'type'        => 'text',
			'label'       => __( 'Suggested searches', 'gexis' ),
			'description' => __( 'Comma separated. Shown under "Try" on the search screen before anything is typed. Optional.', 'gexis' ),
		)
	);
}
add_action( 'customize_register', 'gexis_customize_register' );
