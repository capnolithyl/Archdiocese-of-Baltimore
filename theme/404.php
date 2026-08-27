<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#404-not-found
 *
 * @package Archdiocese_of_Baltimore
 */

get_header();
?>

<section id="primary" class="template-page template-404">
	<main id="main" class="template-404__main">
		<div class="template-404__inner">
			<p class="template-404__code" aria-hidden="true">404</p>
			<p class="template-404__eyebrow eyebrow"><?php esc_html_e( 'Page Not Found', 'baltimore' ); ?></p>
			<h1><?php esc_html_e( 'We could not find that page.', 'baltimore' ); ?></h1>
			<p class="template-404__intro"><?php esc_html_e( 'The page may have moved, the address may have changed, or the link may be out of date. Try searching the site or return to the homepage.', 'baltimore' ); ?></p>

			<div class="template-404__actions">
				<a class="template-button template-button--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Go to Homepage', 'baltimore' ); ?></a>
			</div>

			<div class="template-404__search">
				<?php get_search_form(); ?>
			</div>
		</div>
	</main><!-- #main -->
</section><!-- #primary -->

<?php
get_footer();
