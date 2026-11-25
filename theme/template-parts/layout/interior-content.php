<?php
/**
 * Shared article shell for the default page template.
 *
 * @package Archdiocese_of_Baltimore
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<section id="primary" class="balt-interior-page">
	<main id="main">
		<?php while ( have_posts() ) : ?>
			<?php
			the_post();
			get_template_part( 'template-parts/layout/interior-hero' );
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
