<?php
/**
 * gexis/author-cv — the CV-style author page: head, then a main column (In
 * short, Work, On the bench) and an aside (skill groups, Also). Every field
 * comes from the user profile (inc/author-meta.php); a section with nothing
 * in it is left out rather than shown empty.
 *
 * @package gexis
 */

defined( 'ABSPATH' ) || exit;

$gexis_user = is_author() ? get_queried_object() : null;
if ( ! $gexis_user instanceof WP_User ) {
	/* In the editor there is no queried author: preview the first author. */
	$gexis_users = get_users( array( 'who' => 'authors', 'number' => 1 ) );
	$gexis_user  = $gexis_users ? $gexis_users[0] : null;
}
if ( ! $gexis_user ) {
	return;
}

$gexis_cv = gexis_author_cv( $gexis_user->ID );
$gexis_n  = (int) count_user_posts( $gexis_user->ID, 'post', true );

$gexis_meta = array();
if ( $gexis_cv['location'] ) {
	$gexis_meta[] = esc_html( $gexis_cv['location'] );
}
$gexis_meta[] = esc_html( gexis_count_posts( $gexis_n ) );

ob_start();
?>
<header class="gx-cv__head">
	<?php echo gexis_avatar_html( $gexis_user->ID, $gexis_user->display_name, 120 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	<div class="gx-cv__intro">
		<span class="gx-cv__who"><span class="gx-cv__name"><?php echo esc_html( $gexis_user->display_name ); ?></span><span class="gx-cv__where"><?php echo implode( ' · ', $gexis_meta ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span></span>
		<?php if ( $gexis_cv['lede'] ) : ?>
			<h1 class="gx-cv__lede"><?php echo esc_html( $gexis_cv['lede'] ); ?></h1>
		<?php else : ?>
			<h1 class="gx-cv__lede"><?php echo esc_html( $gexis_user->display_name ); ?></h1>
		<?php endif; ?>
		<?php if ( $gexis_cv['now'] ) : ?>
			<span class="gx-cv__now"><span class="gx-cv__dot" aria-hidden="true"></span><span><?php /* translators: %s: what the author is doing now. */ echo esc_html( sprintf( __( 'Now: %s', 'gexis' ), $gexis_cv['now'] ) ); ?></span></span>
		<?php endif; ?>
		<?php if ( $gexis_cv['links'] || $gexis_cv['pdf'] ) : ?>
			<div class="gx-cv__links">
				<?php foreach ( $gexis_cv['links'] as $gexis_link ) : ?>
					<a class="gx-cv__link" href="<?php echo esc_url( $gexis_link['url'] ); ?>"><?php echo esc_html( $gexis_link['label'] ); ?></a>
				<?php endforeach; ?>
				<?php if ( $gexis_cv['pdf'] ) : ?>
					<a class="gx-cv__link gx-cv__link--pdf" href="<?php echo esc_url( $gexis_cv['pdf'] ); ?>" download data-gexis-toast="<?php esc_attr_e( 'PDF downloaded', 'gexis' ); ?>"><?php esc_html_e( 'CV as PDF', 'gexis' ); ?></a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</header>

<div class="gx-cv__cols">
	<div class="gx-cv__main">
		<?php if ( $gexis_cv['summary'] ) : ?>
			<section class="gx-cv__section">
				<?php echo gexis_section_label_html( __( 'In short', 'gexis' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<p class="gx-cv__summary"><?php echo esc_html( $gexis_cv['summary'] ); ?></p>
			</section>
		<?php endif; ?>

		<?php if ( $gexis_cv['jobs'] ) : ?>
			<section class="gx-cv__section gx-cv__section--work">
				<?php echo gexis_section_label_html( __( 'Work', 'gexis' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php foreach ( $gexis_cv['jobs'] as $gexis_job ) : ?>
					<div class="gx-job">
						<span class="gx-job__years"><?php echo esc_html( $gexis_job['years'] ); ?></span>
						<div class="gx-job__body">
							<span class="gx-job__role"><strong><?php echo esc_html( $gexis_job['role'] ); ?></strong><?php if ( $gexis_job['org'] ) : ?><span class="gx-job__org"> · <?php echo esc_html( $gexis_job['org'] ); ?></span><?php endif; ?></span>
							<?php if ( $gexis_job['note'] ) : ?>
								<span class="gx-job__note"><?php echo esc_html( $gexis_job['note'] ); ?></span>
							<?php endif; ?>
						</div>
					</div>
				<?php endforeach; ?>
			</section>
		<?php endif; ?>

		<section class="gx-cv__section">
			<?php echo gexis_section_label_html( __( 'On the bench', 'gexis' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php echo render_block( array( 'blockName' => 'gexis/tag-cards', 'attrs' => array( 'variant' => 'bench' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</section>
	</div>

	<?php if ( $gexis_cv['skills'] || $gexis_cv['facts'] ) : ?>
		<aside class="gx-cv__aside">
			<?php foreach ( $gexis_cv['skills'] as $gexis_group ) : ?>
				<section class="gx-cv__section">
					<?php echo gexis_section_label_html( $gexis_group['label'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<div class="gx-skills">
						<?php foreach ( $gexis_group['items'] as $gexis_item ) : ?>
							<span class="gx-skill"><?php echo esc_html( $gexis_item ); ?></span>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endforeach; ?>
			<?php if ( $gexis_cv['facts'] ) : ?>
				<section class="gx-cv__section gx-cv__section--facts">
					<?php echo gexis_section_label_html( __( 'Also', 'gexis' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php foreach ( $gexis_cv['facts'] as $gexis_fact ) : ?>
						<div class="gx-fact"><span class="gx-fact__k"><?php echo esc_html( $gexis_fact['label'] ); ?></span><span class="gx-fact__v"><?php echo esc_html( $gexis_fact['value'] ); ?></span></div>
					<?php endforeach; ?>
				</section>
			<?php endif; ?>
		</aside>
	<?php endif; ?>
</div>
<?php
$gexis_html = ob_get_clean();

printf(
	'<div %1$s>%2$s</div>',
	get_block_wrapper_attributes( array( 'class' => 'gx-cv' ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	$gexis_html // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
);
