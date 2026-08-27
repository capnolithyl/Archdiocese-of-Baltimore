<?php
/**
 * The main fallback template file.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Archdiocese_of_Baltimore
 */

get_header();
?>

<section id="primary" class="template-page template-archive template-index">
	<main id="main">
		<?php
		get_template_part(
			'template-parts/layout/archive-header',
			null,
			array(
				'eyebrow' => __( 'Archdiocese of Baltimore', 'baltimore' ),
				'title'   => is_home() && ! is_front_page() ? single_post_title( '', false ) : get_bloginfo( 'name' ),
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
