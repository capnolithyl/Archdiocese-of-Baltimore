<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Archdiocese_of_Baltimore
 */

get_header();
?>

<section id="primary" class="template-page template-single-page">
	<main id="main">
		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content/content', 'single' );

			if ( is_singular( 'post' ) ) {
				the_post_navigation(
					array(
						'next_text' => '<span class="post-navigation__label">' . esc_html__( 'Next Story', 'baltimore' ) . '</span><span class="post-navigation__title">%title</span>',
						'prev_text' => '<span class="post-navigation__label">' . esc_html__( 'Previous Story', 'baltimore' ) . '</span><span class="post-navigation__title">%title</span>',
					)
				);
			}
		endwhile;
		?>
	</main><!-- #main -->
</section><!-- #primary -->

<?php
get_footer();
