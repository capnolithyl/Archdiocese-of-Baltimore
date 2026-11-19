<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_video = (bool) get_field( 'media_type' );

$slides = $is_video
	? array(
		array(
			'heading'     => get_field( 'video_heading' ),
			'subheadings' => get_field( 'subheadings' ),
			'buttons'     => get_field( 'video_buttons' ),
		),
	)
	: (array) get_field( 'slides' );

$video = $is_video ? get_field( 'video_file' ) : null;
$video_url = is_array( $video )
	? ( $video['url'] ?? '' )
	: ( is_numeric( $video ) ? wp_get_attachment_url( absint( $video ) ) : $video );

$poster = $is_video ? get_field( 'video_poster' ) : null;
$poster_id = is_array( $poster ) ? absint( $poster['ID'] ?? 0 ) : absint( $poster );
$poster_url = $poster_id ? wp_get_attachment_image_url( $poster_id, 'full' ) : '';

if ( ! $slides && empty( $is_preview ) ) {
	return;
}

$slick = array(
	'arrows'        => true,
	'dots'          => true,
	'autoplay'      => true,
	'autoplaySpeed' => 6000,
	'pauseOnHover'  => true,
	'pauseOnFocus'  => true,
);
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'dpi-block dpi-hero dpi-hero--overlay alignfull' ) ); ?>>
	<div class="dpi-hero__slides"<?php if ( ! $is_video && count( $slides ) > 1 ) : ?> data-dpi-slick="<?php echo esc_attr( wp_json_encode( $slick ) ); ?>"<?php endif; ?>>
		<?php foreach ( $slides as $index => $slide ) : ?>
			<?php
			if ( ! is_array( $slide ) ) {
				continue;
			}

			$image = $slide['image'] ?? null;
			$image_id = is_array( $image ) ? absint( $image['ID'] ?? $image['id'] ?? 0 ) : absint( $image );
			$heading = trim( (string) ( $slide['heading'] ?? '' ) );
			$subheadings = is_array( $slide['subheadings'] ?? null ) ? $slide['subheadings'] : array();
			$buttons = is_array( $slide['buttons'] ?? null ) ? array_slice( $slide['buttons'], 0, 2 ) : array();
			$heading_tag = is_front_page() && 0 === $index ? 'h1' : 'h2';
			?>
			<article class="dpi-hero__slide">
				<div class="dpi-hero__media">
					<?php if ( $is_video && $video_url ) : ?>
						<video class="dpi-hero__video" src="<?php echo esc_url( $video_url ); ?>"<?php if ( $poster_url ) : ?> poster="<?php echo esc_url( $poster_url ); ?>"<?php endif; ?> autoplay muted loop playsinline preload="metadata" aria-hidden="true"></video>
					<?php elseif ( $is_video && $poster_id ) : ?>
						<?php echo wp_get_attachment_image( $poster_id, 'full', false, array( 'alt' => '' ) ); ?>
					<?php elseif ( $image_id ) : ?>
						<?php echo wp_get_attachment_image( $image_id, 'full', false, array( 'alt' => '' ) ); ?>
					<?php elseif ( ! empty( $is_preview ) ) : ?>
						<p class="dpi-block--placeholder"><?php esc_html_e( 'Choose a video or an image.', 'baltimore' ); ?></p>
					<?php endif; ?>
					<span class="dpi-hero__overlay" aria-hidden="true"></span>
				</div>

				<div class="dpi-hero__content">
					<?php if ( $heading ) : ?>
						<<?php echo $heading_tag; ?> class="dpi-hero__title"><?php echo esc_html( $heading ); ?></<?php echo $heading_tag; ?>>
					<?php endif; ?>

					<?php foreach ( $subheadings as $row ) : ?>
						<?php
						$subheading = is_array( $row )
							? ( $row['subheading'] ?? $row['video_subheading'] ?? '' )
							: $row;
						?>
						<?php if ( trim( (string) $subheading ) ) : ?>
							<p class="dpi-hero__subheading"><?php echo esc_html( $subheading ); ?></p>
						<?php endif; ?>
					<?php endforeach; ?>

					<?php if ( $buttons ) : ?>
						<div class="dpi-hero__actions">
							<?php foreach ( $buttons as $row ) : ?>
								<?php
								$link = is_array( $row ) ? ( $row['link'] ?? null ) : null;
								if ( is_array( $link ) && ! empty( $link['url'] ) ) {
									echo wp_kses_post( dpi_blocks_render_link( $link, 'dpi-button', ! empty( $is_preview ) ) );
								}
								?>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
</section>
