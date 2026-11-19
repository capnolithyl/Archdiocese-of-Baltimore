<?php
/**
 * Community Slider card.
 *
 * Expects $community_post, $show_date, and $is_preview from the parent renderer.
 *
 * @package DPI_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_link = array(
	'url'    => get_permalink( $community_post ),
	'title'  => get_the_title( $community_post ),
	'target' => '',
);
?>
<article>
	<?php if ( has_post_thumbnail( $community_post ) ) : ?>
		<?php echo dpi_blocks_link_open( $post_link, 'dpi-community-slider__image block w-full', ! empty( $is_preview ) );?>
			<?php echo get_the_post_thumbnail( $community_post, 'large' );?>
		<?php echo dpi_blocks_link_close( ! empty( $is_preview ) );?>
	<?php endif; ?>
	<h3><?php echo dpi_blocks_link_open( $post_link, 'dpi-community-slider__title-link font-semibold text-foreground no-underline hover:underline', ! empty( $is_preview ) );?><?php echo esc_html( get_the_title( $community_post ) ); ?><?php echo dpi_blocks_link_close( ! empty( $is_preview ) );?></h3>
	<?php if ( $show_date ) : ?>
		<time datetime="<?php echo esc_attr( get_the_date( 'c', $community_post ) ); ?>">
			<?php echo esc_html( get_the_date( 'M j', $community_post ) ); ?>
		</time>
	<?php endif; ?>
</article>
