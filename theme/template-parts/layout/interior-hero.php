<?php
/**
 * Figma 320:2180: centered eyebrow/title over an optional featured image.
 * WordPress's featured image remains the dynamic, responsive asset.
 *
 * @package Archdiocese_of_Baltimore
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Explicit template data avoids reading unrelated post fields on taxonomy archives.
if ( isset( $args['hero_title'] ) ) {
	$hero_title = (string) $args['hero_title'];
	$eyebrow    = (string) ( $args['eyebrow'] ?? '' );
	$image_id   = (int) ( $args['image_id'] ?? 0 );
} else {
	$post_id      = get_the_ID();
	$eyebrow      = function_exists( 'get_field' ) ? trim( (string) get_field( 'balt_interior_eyebrow', $post_id ) ) : '';
	$custom_title = function_exists( 'get_field' ) ? trim( (string) get_field( 'balt_interior_title', $post_id ) ) : '';
	$hero_title   = '' !== $custom_title ? $custom_title : get_the_title( $post_id );
	$image_id     = (int) get_post_thumbnail_id( $post_id );
}
$heading_tag = 'h1' === ( $args['heading_tag'] ?? 'h1' ) ? 'h1' : 'p';
$variant  = $image_id > 0 ? 'image' : 'title';
?>
<header class="balt-interior-hero balt-interior-hero--<?php echo esc_attr( $variant ); ?>">
	<?php if ( $image_id ) : ?>
		<?php
		echo wp_get_attachment_image(
			$image_id,
			'full',
			false,
			array(
				'class'         => 'balt-interior-hero__image',
				'alt'           => '',
				'sizes'         => '100vw',
				'loading'       => 'eager',
				'fetchpriority' => 'high',
			)
		);
		?>
	<?php endif; ?>
	<div class="balt-interior-hero__shade" aria-hidden="true"></div>
	<div class="balt-interior-hero__inner">
		<?php if ( '' !== $eyebrow ) : ?>
			<p class="balt-interior-hero__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
		<?php endif; ?>
		<<?php echo $heading_tag; ?> class="balt-interior-hero__title"><?php echo esc_html( $hero_title ); ?></<?php echo $heading_tag; ?>>
	</div>
</header>
