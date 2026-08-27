<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Archdiocese_of_Baltimore
 */

get_header();
?>

<section id="primary" class="template-page template-archive">
	<main id="main">
		<?php
		get_template_part(
			'template-parts/layout/archive-header',
			null,
			array(
				'eyebrow'     => balt_get_archive_eyebrow(),
				'title'       => get_the_archive_title(),
				'description' => get_the_archive_description(),
			)
		);
		?>

		<div class="template-listing">
			<?php if ( have_posts() ) : ?>
				<div class="template-card-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/content/content', 'excerpt' );
					endwhile;
					?>
				</div>

				<?php balt_the_posts_navigation(); ?>
			<?php else : ?>
				<?php get_template_part( 'template-parts/content/content', 'none' ); ?>
			<?php endif; ?>
		</div>
	</main><!-- #main -->
</section><!-- #primary -->

<?php
get_footer();
