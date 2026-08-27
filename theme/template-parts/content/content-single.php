<?php
/**
 * Template part for displaying single posts and generic single content types.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Archdiocese_of_Baltimore
 */

$post_type  = get_post_type();
$image_id   = balt_get_featured_image_id();
$categories = 'post' === $post_type ? get_the_category() : array();
$eyebrow    = ! empty( $categories ) ? $categories[0]->name : balt_get_content_type_label();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'template-single' ); ?>>
	<header class="template-single__hero">
		<div class="template-single__hero-inner">
			<?php if ( $eyebrow ) : ?>
				<p class="template-single__eyebrow eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<?php the_title( '<h1 class="template-single__title">', '</h1>' ); ?>

			<?php if ( 'post' === $post_type ) : ?>
				<div class="template-single__meta">
					<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
					<span aria-hidden="true">•</span>
					<span><?php echo esc_html( get_the_author() ); ?></span>
				</div>
			<?php endif; ?>
		</div>
	</header>

	<?php if ( $image_id ) : ?>
		<figure class="template-single__featured-image">
			<?php
			echo wp_get_attachment_image(
				$image_id,
				'full',
				false,
				array( 'class' => 'template-single__featured-image-element' )
			); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</figure>
	<?php endif; ?>

	<div class="template-single__content-wrap">
		<div <?php balt_content_class( 'entry-content template-single__content' ); ?>>
			<?php
			the_content(
				sprintf(
					wp_kses(
						/* translators: %s: Name of current post. Only visible to screen readers. */
						__( 'Continue reading<span class="sr-only"> "%s"</span>', 'baltimore' ),
						array(
							'span' => array( 'class' => array() ),
						)
					),
					get_the_title()
				)
			);

			wp_link_pages(
				array(
					'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Page', 'baltimore' ) . '">' . esc_html__( 'Pages:', 'baltimore' ),
					'after'  => '</nav>',
				)
			);
			?>
		</div>

		<?php if ( 'post' === $post_type || get_edit_post_link() ) : ?>
			<footer class="entry-footer template-single__footer">
				<?php balt_entry_footer(); ?>
			</footer>
		<?php endif; ?>
	</div>
</article>
