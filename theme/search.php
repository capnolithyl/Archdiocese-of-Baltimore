<?php
/**
 * The template for displaying search results pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Archdiocese_of_Baltimore
 */

global $wp_query;

$query = get_search_query();
$count = isset( $wp_query->found_posts ) ? absint( $wp_query->found_posts ) : 0;

get_header();
?>

<section id="primary" class="template-page template-search">
	<main id="main">
		<?php
		get_template_part(
			'template-parts/layout/archive-header',
			null,
			array(
				'eyebrow' => __( 'Search', 'baltimore' ),
				'title'   => $query
					? sprintf(
						/* translators: %s: search query. */
						esc_html__( 'Search results for “%s”', 'baltimore' ),
						esc_html( $query )
					)
					: __( 'Search the Archdiocese', 'baltimore' ),
				'count'   => $count,
			)
		);
		?>

		<div class="template-search__form-wrap">
			<?php get_search_form(); ?>
		</div>

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
