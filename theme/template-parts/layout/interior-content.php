<?php
/**
 * Shared article shell for the interior templates.
 *
 * @package Archdiocese_of_Baltimore
 *
 * @var array $args Template arguments.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hero_variant = 'featured' === ( $args['hero'] ?? '' ) ? 'featured' : 'title';
?>
<section id="primary" class="balt-interior-page">
	<main id="main">
		<?php while ( have_posts() ) : ?>
			<?php
			the_post();
			get_template_part( 'template-parts/layout/interior-hero', null, array( 'variant' => $hero_variant ) );
			get_template_part( 'template-parts/layout/section-navigation', null, array( 'post_id' => get_the_ID() ) );
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'balt-interior-page__article' ); ?>>
				<div <?php balt_content_class( 'entry-content balt-interior-page__content' ); ?>>
					<?php
					the_content();
					wp_link_pages(
						array(
							'before' => '<div class="balt-interior-page__pagination">' . esc_html__( 'Pages:', 'baltimore' ),
							'after'  => '</div>',
						)
					);
					?>
				</div>
			</article>
		<?php endwhile; ?>
	</main>
</section>
