<?php

/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default. Please note that
 * this is the WordPress construct of pages: specifically, posts with a post
 * type of `page`.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Archdiocese_of_Baltimore
 */

get_header();
?>

<section id="primary">
	<main id="main">

		<?php
		/* Start the Loop */
		while (have_posts()) :
			the_post(); ?>

			<?php

			/**
			 * Template part for displaying pages
			 *
			 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
			 *
			 * @package Archdiocese_of_Baltimore
			 */

			?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

				<div <?php balt_content_class('entry-content'); ?>>
					<?php
					the_content();

					wp_link_pages(
						array(
							'before' => '<div>' . __('Pages:', 'baltimore'),
							'after'  => '</div>',
						)
					);
					?>
				</div><!-- .entry-content -->


			</article><!-- #post-<?php the_ID(); ?> -->

		<?php endwhile; // End of the loop.
		?>

	</main><!-- #main -->
</section><!-- #primary -->

<?php
get_footer();
