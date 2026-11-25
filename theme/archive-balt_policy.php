<?php
/** Default category directory when a custom landing page is not configured. */
get_header();
$cards = balt_policy_category_cards();
?>
<main id="main" class="balt-policy-page">
	<?php get_template_part( 'template-parts/layout/interior-hero', null, array( 'hero_title' => __( 'Archdiocese Policies', 'baltimore' ) ) ); ?>
	<div class="balt-policy-index">
		<div class="balt-policy-index__header">
			<h2 class="balt-card-directory__eyebrow"><?php esc_html_e( 'Policy Directory', 'baltimore' ); ?></h2>
			<form class="balt-card-directory__search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">
				<input type="hidden" name="post_type" value="balt_policy">
				<button class="balt-card-directory__search-submit" type="submit" aria-label="<?php esc_attr_e( 'Search policies', 'baltimore' ); ?>"><span class="balt-card-directory__search-icon" aria-hidden="true"></span></button>
				<input type="search" name="s" aria-label="<?php esc_attr_e( 'Search policies', 'baltimore' ); ?>" placeholder="<?php esc_attr_e( 'Search policies...', 'baltimore' ); ?>" required>
			</form>
		</div>
		<?php if ( $cards ) : ?>
			<?php get_template_part( 'template-parts/components/content-card-grid', null, array( 'cards' => $cards, 'accent' => true, 'compact_spacing' => true ) ); ?>
		<?php else : ?>
			<p class="balt-policy-empty"><?php esc_html_e( 'No policy categories available.', 'baltimore' ); ?></p>
		<?php endif; ?>
	</div>
</main>
<?php get_footer(); ?>
