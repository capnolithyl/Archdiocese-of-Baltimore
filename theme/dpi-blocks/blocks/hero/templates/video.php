<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$video       = get_field( 'video_file' );
$poster      = get_field( 'video_poster' );
$heading     = trim( (string) get_field( 'video_heading' ) );
$subheadings = get_field( 'subheadings' );
$subheading  = $subheadings[0]['video_subheading'] ?? '';
$buttons     = array_slice( get_field( 'video_buttons' ) ?: array(), 0, 2 );
$heading_tag = is_front_page() ? 'h1' : 'h2';
?>
<div class="dpi-hero__slides min-h-svh! [&_.slick-list]:min-h-svh [&_.slick-track]:min-h-svh [&_.slick-slide]:min-h-svh">
	<article class="dpi-hero__slide relative min-h-svh! bg-black text-white">
		<div class="dpi-hero__media absolute inset-0">
			<?php if ( ! empty( $video['url'] ) ) : ?>
				<video
					class="dpi-hero__video size-full object-cover"
					src="<?php echo esc_url( $video['url'] ); ?>"
					<?php if ( ! empty( $poster['url'] ) ) : ?>poster="<?php echo esc_url( $poster['url'] ); ?>"<?php endif; ?>
					autoplay muted loop playsinline preload="metadata" aria-hidden="true"
				></video>
			<?php elseif ( ! empty( $poster['ID'] ) ) : ?>
				<?php echo wp_get_attachment_image( $poster['ID'], 'full', false, array( 'alt' => '', 'class' => 'size-full object-cover' ) ); ?>
			<?php elseif ( ! empty( $is_preview ) ) : ?>
				<p class="dpi-block--placeholder"><?php esc_html_e( 'Choose a video.', 'baltimore' ); ?></p>
			<?php endif; ?>
			<span class="dpi-hero__overlay absolute inset-0 bg-black/50" aria-hidden="true"></span>
		</div>

		<div class="dpi-hero__content relative z-10 flex min-h-svh! flex-col items-center justify-center gap-4 px-6 py-20 text-center md:px-12 lg:py-24">
			<?php if ( $heading ) : ?>
				<<?php echo $heading_tag; ?> class="dpi-hero__title font-heading text-[clamp(2.5rem,5vw,5rem)]! font-semibold leading-tight text-white text-balance"><?php echo esc_html( $heading ); ?></<?php echo $heading_tag; ?>>
			<?php endif; ?>

			<?php if ( $subheading ) : ?>
				<p class="dpi-hero__subheading max-w-3xl text-[clamp(1.125rem,2vw,1.5rem)] text-white"><?php echo esc_html( $subheading ); ?></p>
			<?php endif; ?>

			<?php if ( $buttons ) : ?>
				<div class="dpi-hero__actions flex flex-wrap items-center justify-center gap-4">
					<?php foreach ( $buttons as $row ) : ?>
						<?php
						$link = $row['link'] ?? null;
						if ( ! empty( $link['url'] ) ) {
							echo wp_kses_post( dpi_blocks_render_link( $link, 'inline-flex items-center justify-center rounded-full border border-white/50 bg-white px-6 py-3 font-semibold text-primary no-underline transition hover:bg-primary hover:text-white', ! empty( $is_preview ) ) );
						}
						?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</article>
</div>
