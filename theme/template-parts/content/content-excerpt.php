<?php
/**
 * Template part for displaying post archives and search results.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Archdiocese_of_Baltimore
 */

$image_id   = balt_get_featured_image_id();
$post_type  = get_post_type();
$type_label = balt_get_content_type_label();
$categories = 'post' === $post_type ? get_the_category() : array();
$eyebrow    = ! empty( $categories ) ? $categories[0]->name : $type_label;
$excerpt    = has_excerpt()
	? get_the_excerpt()
	: wp_trim_words( wp_strip_all_tags( strip_shortcodes( get_the_content() ) ), 28, '&hellip;' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'template-card' ); ?>>
	<?php if ( $image_id ) : ?>
		<a class="template-card__media" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
			<?php
			echo wp_get_attachment_image(
				$image_id,
				'large',
				false,
				array(
					'class'   => 'template-card__image',
					'loading' => 'lazy',
				)
			); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</a>
	<?php endif; ?>

	<div class="template-card__body">
		<div class="template-card__meta">
			<?php if ( $eyebrow ) : ?>
				<span class="template-card__eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
			<?php endif; ?>

			<?php if ( 'post' === $post_type ) : ?>
				<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
			<?php endif; ?>
		</div>

		<header class="template-card__header">
			<?php the_title( sprintf( '<h2 class="template-card__title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
		</header>

		<?php if ( $excerpt ) : ?>
			<div class="template-card__excerpt">
				<p><?php echo wp_kses_post( $excerpt ); ?></p>
			</div>
		<?php endif; ?>

		<a class="template-card__link" href="<?php the_permalink(); ?>">
			<?php esc_html_e( 'Read More', 'baltimore' ); ?>
			<span aria-hidden="true">&rarr;</span>
		</a>
	</div>
</article>
