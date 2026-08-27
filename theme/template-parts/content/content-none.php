<?php
/**
 * Template part for displaying a message when posts are not found.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Archdiocese_of_Baltimore
 */
?>

<section class="template-empty-state">
	<p class="template-empty-state__eyebrow eyebrow"><?php esc_html_e( 'Nothing Found', 'baltimore' ); ?></p>

	<?php if ( is_search() ) : ?>
		<h2 class="template-empty-state__title"><?php esc_html_e( 'Try another search', 'baltimore' ); ?></h2>
		<p><?php esc_html_e( 'We could not find anything matching those terms. Try a broader phrase, a different spelling, or another keyword.', 'baltimore' ); ?></p>
	<?php elseif ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
		<h2 class="template-empty-state__title"><?php esc_html_e( 'No news has been published yet', 'baltimore' ); ?></h2>
		<p><?php esc_html_e( 'Published posts will appear here automatically.', 'baltimore' ); ?></p>
		<p><a class="template-button template-button--primary" href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>"><?php esc_html_e( 'Add a Post', 'baltimore' ); ?></a></p>
	<?php else : ?>
		<h2 class="template-empty-state__title"><?php esc_html_e( 'There is nothing here yet', 'baltimore' ); ?></h2>
		<p><?php esc_html_e( 'No published content matched this archive. You can search the site or return to the homepage.', 'baltimore' ); ?></p>
		<div class="template-empty-state__actions">
			<a class="template-button template-button--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Go to Homepage', 'baltimore' ); ?></a>
		</div>
		<div class="template-empty-state__search">
			<?php get_search_form(); ?>
		</div>
	<?php endif; ?>
</section>
