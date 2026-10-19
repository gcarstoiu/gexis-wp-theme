<?php
/**
 * Development seed: the handoff prototype's sample site — 15 posts across
 * the four tags, the comment thread, the author CV, an About page and the
 * default ground. Placeholder images are generated (dev/images), not the
 * prototype's Unsplash photos.
 *
 * Run inside a throwaway WordPress only (dev/docker.sh up does this):
 *   wp eval-file wp-content/themes/gexis/dev/seed.php
 *
 * @package gexis
 */

if ( ! defined( 'ABSPATH' ) ) {
	require_once '/var/www/html/wp-load.php';
}
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$dir = get_template_directory() . '/dev/images/';

function gexis_seed_image( $file ) {
	$tmp = wp_tempnam( $file );
	copy( $file, $tmp );
	$id = media_handle_sideload( array( 'name' => basename( $file ), 'tmp_name' => $tmp ), 0 );
	return is_wp_error( $id ) ? 0 : $id;
}

/* WordPress's own sample post and page. */
foreach ( array( 'hello-world' => 'post', 'sample-page' => 'page' ) as $slug => $type ) {
	$sample = get_page_by_path( $slug, OBJECT, $type );
	if ( $sample ) {
		wp_delete_post( $sample->ID, true );
	}
}

update_option( 'blogname', 'gexis' );
update_option( 'blogdescription', '' );
update_option( 'permalink_structure', '/%year%/%monthnum%/%postname%/' );
update_option( 'thread_comments', 1 );
update_option( 'show_avatars', 1 );
update_option( 'comment_moderation', 0 );
update_option( 'comment_previously_approved', 0 );
update_option( 'posts_per_page', 10 );

wp_update_user( array( 'ID' => 1, 'display_name' => 'gexis', 'nickname' => 'gexis', 'user_email' => 'gexis@example.invalid' ) );

$tags = array(
	'g' => array( 'Gazette', 'gazette', 'Articles on whatever is worth writing down.' ),
	'e' => array( 'Engine room', 'engine-room', 'Homelab and tinkering. Small servers, networking, fixes.' ),
	'i' => array( 'Inbox', 'inbox', 'Email templates and how clients render them.' ),
	's' => array( 'Sound', 'sound', 'The gexis DAC player: builds, decisions, measurements.' ),
);
$term_ids = array();
foreach ( $tags as $k => $t ) {
	$term = term_exists( $t[1], 'post_tag' );
	if ( ! $term ) {
		$term = wp_insert_term( $t[0], 'post_tag', array( 'slug' => $t[1], 'description' => $t[2] ) );
	}
	$term_ids[ $k ] = (int) ( is_array( $term ) ? $term['term_id'] : $term );
}

$raw = array(
	array( 'Build 0.8: handoff between Spotify and LMS', 'Until 0.7, switching from Spotify Connect to Lyrion stopped one renderer, started the other, and lost whatever was queued. Build 0.8 keeps the queue and hands over at the same position.', '2026-09-28', array( 's', 'e' ), array(
		array( 'p', 'The change is mostly in the state machine. Each renderer now reports a position and a track identity on every tick, and the handoff waits for the incoming renderer to confirm both before the outgoing one is stopped. If confirmation does not arrive within 1.5 seconds, the handoff is abandoned and the original renderer keeps playing.' ),
		array( 'h', 'What the panel shows' ),
		array( 'p', 'During the handoff the top hairline crossfades from green to teal over 260ms. Nothing else moves. The times stay hidden until the new renderer reports a position, rather than showing 0:00.' ),
		array( 'f', 'Test rig: the panel, the DAC and a phone running Spotify.' ),
		array( 'h', 'The confirm step' ),
		array( 'c', "await incoming.seek(track, position)\nconst ok = await incoming.confirm({ timeout: 1500 })\nif (!ok) return abandon()\noutgoing.stop()" ),
		array( 'p', 'Two cases are still owed a decision: a handoff requested while the incoming renderer is buffering, and Bluetooth, which does not report position at all.' ),
	) ),
	array( 'The case for slow software', 'Tools that take a second to start and then stay out of the way for a year. Notes on choosing them over the fast-moving alternative.', '2026-09-24', array( 'g' ), array(
		array( 'p', 'A tool that changes every month asks for attention every month. The ones that last tend to have a small surface, a stable file format and a changelog that is mostly bug fixes.' ),
		array( 'h', 'Three tests' ),
		array( 'p', 'Can the data be read without the tool? Has the interface been stable for two years? Does an update ever need to be scheduled? Two yeses is usually enough.' ),
	) ),
	array( 'A dark-mode receipt that survives Outlook 2019', 'One table, two colour schemes, no media queries Outlook can see. Tested in 14 clients.', '2026-09-21', array( 'i' ), array(
		array( 'p', 'Outlook 2019 on Windows ignores prefers-color-scheme and inverts some colours on its own. The receipt survives by using mid-tone backgrounds that read correctly whether or not they are inverted.' ),
		array( 'c', '<td bgcolor="#2e4453" style="background:#2e4453;color:#e9eef2">' ),
		array( 'p', 'The bgcolor attribute is the part Outlook respects. The inline style is for everything else.' ),
	) ),
	array( 'Moving the homelab to a single N100 box', 'Three Raspberry Pis, one NAS and a mini PC became one machine drawing 9 W at idle. Power figures before and after.', '2026-09-17', array( 'e' ), array(
		array( 'p', 'Before: 34 W at idle across five devices. After: 9 W for one. The NAS disks moved into a two-bay enclosure that spins down after 20 minutes.' ),
		array( 'h', 'What did not move' ),
		array( 'p', 'The Pi 4 driving the gexis panel stays where it is. It is the one device that has to be next to the DAC.' ),
	) ),
	array( 'Why the panel never shows 0:00', 'When there is no position, the times are hidden rather than faked. A short note on a rule that shaped the whole interface.', '2026-09-12', array( 's', 'g' ), array( array( 'p', 'A 0:00 that never moves reads as a stalled stream. A hidden time reads as a source that does not report one, which is what Bluetooth is.' ) ) ),
	array( 'Reading list: September', 'Six books and two long essays, with a line on each.', '2026-09-08', array( 'g' ), array( array( 'p', 'Mostly about tools and the people who make them, with one novel that has nothing to do with either.' ) ) ),
	array( 'Caddy, Tailscale and one wildcard certificate', 'Internal services on real hostnames with valid TLS, and no ports open to the internet.', '2026-09-02', array( 'e' ), array(
		array( 'p', 'Caddy obtains the wildcard certificate with a DNS challenge, so nothing needs to be reachable from outside. Tailscale handles who can reach what.' ),
		array( 'c', "*.home.example.net {\n  tls { dns cloudflare {env.CF_TOKEN} }\n}" ),
	) ),
	array( 'Table layouts are still the only layout', 'Flex and grid in email, client by client, in 2026. The short answer has not changed.', '2026-08-27', array( 'i' ), array( array( 'p', 'Grid works in Apple Mail and a handful of webmail clients. Flex works in slightly more. Neither works in Outlook on Windows, which is still a large share of opens.' ) ) ),
	array( 'Measuring boot volume on three renderers', 'The restore floor was a placeholder. Readings from LMS, Spotify Connect and Bluetooth, and why Bluetooth needs its own.', '2026-08-20', array( 's' ), array( array( 'p', 'At the same nominal level, Bluetooth measured 6 dB quieter than LMS. A single floor for all three renderers was never going to work.' ) ) ),
	array( 'Why the Pi 4 still runs the panel', 'A faster board was tested and returned. Thermals, boot time and the 70px blur budget.', '2026-08-14', array( 'e', 's' ), array( array( 'p', 'The faster board booted 4 seconds sooner and ran 11 °C hotter in the same enclosure. The panel holds 60 fps on the Pi 4 as long as nothing animates a blur.' ) ) ),
	array( 'Against the dashboard', 'Most screens that show twelve numbers would be better showing one.', '2026-08-09', array( 'g' ), array( array( 'p', 'A dashboard is often a list of everything that could be measured, arranged so that none of it has to be chosen.' ) ) ),
	array( 'Testing 12 clients without a paid service', 'A local setup for screenshots across webmail and desktop clients.', '2026-08-01', array( 'i', 'e' ), array( array( 'p', 'Two virtual machines, a handful of free accounts and a script that sends the same message to all of them.' ) ) ),
	array( 'Headphone output on a DAC that was never meant to have one', 'Adding a headphone stage to the player, and the noise floor it cost.', '2026-07-24', array( 's', 'e' ), array( array( 'p', 'The headphone stage added 3 dB to the noise floor on the line output. Measurable, not audible at normal levels.' ) ) ),
	array( 'A volume curve that matches the knob', 'Linear volume feels wrong. The curve the panel uses now, and how it was chosen.', '2026-07-11', array( 's' ), array( array( 'p', 'The slider maps to decibels, not to amplitude, so the first half of its travel covers the range that is actually used.' ) ) ),
	array( 'Choosing a DAC chip by measurement, not by forum', 'Four chips, one test board, the same output stage.', '2026-07-02', array( 's', 'g' ), array( array( 'p', 'Three of the four measured within 1 dB of each other. The choice came down to supply and documentation.' ) ) ),
);
$counts = array( 12, 18, 7, 23, 5, 3, 14, 9, 8, 11, 21, 6, 10, 4, 16 );

$fig = gexis_seed_image( $dir . 'p16.jpg' );
$ids = array();
foreach ( $raw as $n => $p ) {
	$content = '';
	foreach ( $p[4] as $b ) {
		if ( 'p' === $b[0] ) {
			$content .= "<!-- wp:paragraph -->\n<p>" . esc_html( $b[1] ) . "</p>\n<!-- /wp:paragraph -->\n\n";
		} elseif ( 'h' === $b[0] ) {
			$content .= "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">" . esc_html( $b[1] ) . "</h2>\n<!-- /wp:heading -->\n\n";
		} elseif ( 'c' === $b[0] ) {
			$content .= "<!-- wp:code -->\n<pre class=\"wp-block-code\"><code>" . esc_html( $b[1] ) . "</code></pre>\n<!-- /wp:code -->\n\n";
		} elseif ( 'f' === $b[0] ) {
			$src      = wp_get_attachment_image_url( $fig, 'large' );
			$content .= '<!-- wp:image {"id":' . $fig . ',"sizeSlug":"large"} -->' . "\n" . '<figure class="wp-block-image size-large"><img src="' . esc_url( $src ) . '" alt="" class="wp-image-' . $fig . '"/><figcaption class="wp-element-caption">' . esc_html( $b[1] ) . "</figcaption></figure>\n<!-- /wp:image -->\n\n";
		}
	}
	$id = wp_insert_post(
		array(
			'post_title'   => $p[0],
			'post_excerpt' => $p[1],
			'post_content' => "<!-- wp:paragraph -->\n<p>" . esc_html( $p[1] ) . "</p>\n<!-- /wp:paragraph -->\n\n" . $content,
			'post_status'  => 'publish',
			'post_author'  => 1,
			'post_date'    => $p[2] . ' 09:00:00',
		)
	);
	wp_set_post_terms( $id, array_map( static function ( $k ) use ( $term_ids ) { return $term_ids[ $k ]; }, $p[3] ), 'post_tag' );
	set_post_thumbnail( $id, gexis_seed_image( $dir . sprintf( 'p%02d.jpg', $n ) ) );
	$ids[] = $id;
}

/* The thread on the first post, as in the prototype. */
$thread = array(
	array( 'Mara Lindqvist', '2026-09-28 14:12', 'Does the 1.5 second timeout hold over Wi-Fi? On a congested network the confirm could arrive late and the handoff would abandon when it did not need to.', array(
		array( 'gexis', '2026-09-28 15:40', 'Measured 220 to 480 ms on a busy 2.4 GHz network. 1.5 s leaves room, but the value is a setting and not yet confirmed.' ),
	) ),
	array( 'Tomás Ferreira', '2026-09-28 18:03', 'Hiding the times instead of showing 0:00 is a small thing that makes a big difference.', array() ),
	array( 'Jun Park', '2026-09-29 09:27', 'For Bluetooth, could the handoff fall back to starting the track from the beginning? Losing position seems better than refusing.', array(
		array( 'Mara Lindqvist', '2026-09-29 10:02', 'That would be a silent restart, which feels worse than a refusal with a reason.' ),
		array( 'gexis', '2026-09-29 11:15', 'Both are on the list of open decisions. A toast saying the position could not be carried over is the current favourite.' ),
	) ),
);
$add = static function ( $c, $parent ) use ( $ids ) {
	$is_author = 'gexis' === $c[0];
	return wp_insert_comment(
		array(
			'comment_post_ID'      => $ids[0],
			'comment_author'       => $c[0],
			'comment_author_email' => $is_author ? 'gexis@example.invalid' : sanitize_title( $c[0] ) . '@example.invalid',
			'comment_content'      => $c[2],
			'comment_date'         => $c[1] . ':00',
			'comment_parent'       => $parent,
			'comment_approved'     => 1,
			'user_id'              => $is_author ? 1 : 0,
		)
	);
};
foreach ( $thread as $c ) {
	$parent = $add( $c, 0 );
	foreach ( $c[3] as $r ) {
		$add( $r, $parent );
	}
}
/* The prototype's comment counts (the comments' threads). */
global $wpdb;
foreach ( $ids as $n => $id ) {
	if ( $n > 0 ) {
		$wpdb->update( $wpdb->posts, array( 'comment_count' => $counts[ $n ] ), array( 'ID' => $id ) );
	}
}
clean_post_cache( $ids );

/* Author CV. */
$cv = array(
	'location' => 'Europe · CET',
	'lede'     => 'Builds interfaces and the systems behind them, lately with a soldering iron nearby.',
	'now'      => 'building the gexis network audio player, from the DAC board to the touch panel.',
	'summary'  => 'Fifteen years building interfaces and the systems behind them. Currently designing and building the gexis network audio player end to end: hardware, a Svelte touch interface and the services it talks to.',
	'links'    => "Email | mailto:gexis@example.invalid\nGitHub | https://github.com/gcarstoiu\nRSS | " . get_feed_link(),
	'jobs'     => "2024 — now | Designer and developer | gexis, independent | Network DAC player with a 1280×800 touch panel. Svelte 5 interface, Lyrion, Spotify Connect and Bluetooth renderers on a Raspberry Pi 4.\n2019 — 2024 | Lead front-end engineer | Company name | Led a team of six on a design system used by four product teams. Cut median page weight by 40%.\n2015 — 2019 | Front-end engineer | Company name | Transactional and marketing email platform. Templates tested across 30+ clients.\n2011 — 2015 | Web developer | Agency name | Sites and WordPress themes for small publishers.",
	'skills'   => "Front end: Svelte, TypeScript, CSS, Design systems, Accessibility\nSystems: Linux, Raspberry Pi, Docker, Caddy, Tailscale\nEmail: MJML, Table layouts, Dark mode, Client testing",
	'facts'    => "Studied | BSc Computer Science, University name, 2011\nSpeaks | English, Language name\nBased | Europe, CET. Remote or on site.\nOpen to | Front-end and embedded projects, part time",
	'pdf'      => '',
);
foreach ( $cv as $k => $v ) {
	update_user_meta( 1, 'gexis_cv_' . $k, $v );
}

/* This site, built from the theme's pattern. */
$about = WP_Block_Patterns_Registry::get_instance()->get_registered( 'gexis/this-site' );
wp_insert_post(
	array(
		'post_type'    => 'page',
		'post_title'   => 'This site',
		'post_name'    => 'site',
		'post_status'  => 'publish',
		'post_content' => $about ? $about['content'] : '',
	)
);

set_theme_mod( 'gexis_default_ground', gexis_seed_image( $dir . 'p06.jpg' ) );
set_theme_mod( 'gexis_search_suggestions', 'handoff, Outlook, Tailscale, volume' );

flush_rewrite_rules( false );
echo "seeded\n";
