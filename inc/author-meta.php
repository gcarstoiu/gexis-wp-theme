<?php
/**
 * CV fields on the user profile, for the author page.
 *
 * PHP because: user meta has no block-editor UI. The repeatable fields are
 * plain text, one entry per line, so they need no script and no plugin:
 *
 *   Links    Label | https://…
 *   Work     2024 — now | Role | Organisation | One-line note
 *   Skills   Group: item, item, item
 *   Also     Label | Value
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;

/**
 * Field definitions: key => [label, type, help].
 *
 * @return array
 */
function gexis_cv_fields() {
	return array(
		'location' => array( __( 'Location', 'gexis' ), 'text', __( 'Shown after the name, in mono: "Europe · CET".', 'gexis' ) ),
		'lede'     => array( __( 'Lede', 'gexis' ), 'text', __( 'One sentence. The page heading.', 'gexis' ) ),
		'now'      => array( __( 'Now', 'gexis' ), 'text', __( 'One line, after "Now:".', 'gexis' ) ),
		'summary'  => array( __( 'In short', 'gexis' ), 'textarea', __( 'A short paragraph.', 'gexis' ) ),
		'links'    => array( __( 'Links', 'gexis' ), 'lines', __( 'One per line: Label | URL', 'gexis' ) ),
		'jobs'     => array( __( 'Work', 'gexis' ), 'lines', __( 'One per line: Years | Role | Organisation | Note', 'gexis' ) ),
		'skills'   => array( __( 'Skills', 'gexis' ), 'lines', __( 'One group per line: Label: item, item, item', 'gexis' ) ),
		'facts'    => array( __( 'Also', 'gexis' ), 'lines', __( 'One per line: Label | Value', 'gexis' ) ),
		'pdf'      => array( __( 'CV as PDF', 'gexis' ), 'url', __( 'Upload the PDF to the Media Library and paste its file URL here.', 'gexis' ) ),
	);
}

/**
 * The profile section.
 *
 * @param WP_User $user User.
 */
function gexis_cv_profile_fields( $user ) {
	wp_nonce_field( 'gexis_cv', 'gexis_cv_nonce' );
	echo '<h2>' . esc_html__( 'Author page (gexis)', 'gexis' ) . '</h2><table class="form-table" role="presentation">';
	foreach ( gexis_cv_fields() as $key => $field ) {
		list( $label, $type, $help ) = $field;
		$value = (string) get_user_meta( $user->ID, 'gexis_cv_' . $key, true );
		$id    = 'gexis_cv_' . $key;
		echo '<tr><th><label for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label></th><td>';
		if ( 'text' === $type || 'url' === $type ) {
			printf( '<input type="%1$s" class="regular-text" id="%2$s" name="%2$s" value="%3$s">', esc_attr( $type ), esc_attr( $id ), esc_attr( $value ) );
		} else {
			printf( '<textarea class="large-text" rows="%1$d" id="%2$s" name="%2$s">%3$s</textarea>', 'textarea' === $type ? 3 : 5, esc_attr( $id ), esc_textarea( $value ) );
		}
		echo '<p class="description">' . esc_html( $help ) . '</p></td></tr>';
	}
	echo '</table>';
}
add_action( 'show_user_profile', 'gexis_cv_profile_fields' );
add_action( 'edit_user_profile', 'gexis_cv_profile_fields' );

/**
 * Save.
 *
 * @param int $user_id User.
 */
function gexis_cv_save( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) || ! isset( $_POST['gexis_cv_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['gexis_cv_nonce'] ), 'gexis_cv' ) ) {
		return;
	}
	foreach ( gexis_cv_fields() as $key => $field ) {
		$name = 'gexis_cv_' . $key;
		if ( ! isset( $_POST[ $name ] ) ) {
			continue;
		}
		$raw = wp_unslash( $_POST[ $name ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized per type below.
		if ( 'url' === $field[1] ) {
			$value = esc_url_raw( $raw );
		} elseif ( 'text' === $field[1] ) {
			$value = sanitize_text_field( $raw );
		} else {
			$value = sanitize_textarea_field( $raw );
		}
		update_user_meta( $user_id, $name, $value );
	}
}
add_action( 'personal_options_update', 'gexis_cv_save' );
add_action( 'edit_user_profile_update', 'gexis_cv_save' );

/**
 * Parse "a | b | c" lines.
 *
 * @param string $text  Text.
 * @param int    $parts Fields per line.
 * @return array<int, string[]>
 */
function gexis_cv_lines( $text, $parts ) {
	$out = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $text ) as $line ) {
		if ( '' === trim( $line ) ) {
			continue;
		}
		$cells = array_map( 'trim', explode( '|', $line, $parts ) );
		$out[] = array_pad( $cells, $parts, '' );
	}
	return $out;
}

/**
 * Everything the CV block needs, parsed.
 *
 * @param int $user_id User.
 * @return array
 */
function gexis_author_cv( $user_id ) {
	$get = static function ( $key ) use ( $user_id ) {
		return trim( (string) get_user_meta( $user_id, 'gexis_cv_' . $key, true ) );
	};

	$links = array();
	foreach ( gexis_cv_lines( $get( 'links' ), 2 ) as $cells ) {
		if ( '' !== $cells[1] ) {
			$links[] = array( 'label' => $cells[0], 'url' => $cells[1] );
		}
	}
	$jobs = array();
	foreach ( gexis_cv_lines( $get( 'jobs' ), 4 ) as $cells ) {
		$jobs[] = array( 'years' => $cells[0], 'role' => $cells[1], 'org' => $cells[2], 'note' => $cells[3] );
	}
	$skills = array();
	foreach ( preg_split( '/\r\n|\r|\n/', $get( 'skills' ) ) as $line ) {
		if ( false === strpos( $line, ':' ) ) {
			continue;
		}
		list( $label, $items ) = array_map( 'trim', explode( ':', $line, 2 ) );
		$items                 = array_values( array_filter( array_map( 'trim', explode( ',', $items ) ) ) );
		if ( $label && $items ) {
			$skills[] = array( 'label' => $label, 'items' => $items );
		}
	}
	$facts = array();
	foreach ( gexis_cv_lines( $get( 'facts' ), 2 ) as $cells ) {
		$facts[] = array( 'label' => $cells[0], 'value' => $cells[1] );
	}

	return array(
		'location' => $get( 'location' ),
		'lede'     => $get( 'lede' ),
		'now'      => $get( 'now' ),
		'summary'  => $get( 'summary' ),
		'links'    => $links,
		'jobs'     => $jobs,
		'skills'   => $skills,
		'facts'    => $facts,
		'pdf'      => $get( 'pdf' ),
	);
}
