<?php
/**
 * The template for the posts index.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#home-page-display
 *
 * @package Archdiocese_of_Baltimore
 */

$posts_page_id = absint( get_option( 'page_for_posts' ) );
$title         = $posts_page_id ? get_the_title( $posts_page_id ) : __( 'Archdiocese News', 'baltimore' );
$description   = $posts_page_id ? get_post_field( 'post_excerpt', $posts_page_id ) : '';

get_header();
?>

<section id="primary" class="template-page template-archive template-home">
	<main id="main">
		<?php
		get_template_part(
			'template-parts/layout/archive-header',
			null,
			array(
				'eyebrow'     => __( 'News & Stories', 'baltimore' ),
				'title'       => $title,
				'description' => $description,
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
