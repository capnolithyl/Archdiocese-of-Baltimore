<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$video      = get_field( 'video_file' );
$poster     = get_field( 'video_poster' );
$heading    = trim( (string) get_field( 'video_heading' ) );
$subheading = get_field( 'subheadings' )[0]['video_subheading'] ?? '';
$buttons    = array_slice( get_field( 'video_buttons' ) ?: array(), 0, 2 );
$heading_tag = is_front_page() ? 'h1' : 'h2';
?>
<article class="relative h-[865px] overflow-hidden bg-foreground">
	<div class="absolute inset-0">
		<?php if ( ! empty( $video['url'] ) ) : ?>
			<video class="h-full w-full object-cover" src="<?php echo esc_url( $video['url'] ); ?>"<?php if ( ! empty( $poster['url'] ) ) : ?> poster="<?php echo esc_url( $poster['url'] ); ?>"<?php endif; ?> autoplay muted loop playsinline preload="metadata" aria-hidden="true"></video>
		<?php elseif ( ! empty( $poster['ID'] ) ) : ?>
			<?php echo wp_get_attachment_image( $poster['ID'], 'full', false, array( 'alt' => '', 'class' => 'h-full w-full object-cover' ) ); ?>
		<?php elseif ( ! empty( $is_preview ) ) : ?>
			<p><?php esc_html_e( 'Choose a video.', 'baltimore' ); ?></p>
		<?php endif; ?>
		<span class="absolute inset-0 bg-black/45" aria-hidden="true"></span>
		<span class="absolute inset-x-0 top-0 h-[280px] bg-linear-to-b from-black/70 via-black/20 to-transparent" aria-hidden="true"></span>
	</div>

	<div class="relative z-10 flex h-full flex-col items-center justify-end px-6 pb-[60px] text-center">
		<?php if ( $heading ) : ?>
			<<?php echo $heading_tag; ?> class="max-w-[900px] font-heading text-[clamp(3.5rem,6.55vw,7rem)] font-extrabold leading-none tracking-[-0.025em] text-white text-balance"><?php echo esc_html( $heading ); ?></<?php echo $heading_tag; ?>>
		<?php endif; ?>

		<?php if ( $subheading ) : ?>
			<p class="mt-5 font-body text-xl font-medium leading-[30px] tracking-[0.15em] text-white/85 uppercase"><?php echo esc_html( $subheading ); ?></p>
		<?php endif; ?>

		<?php if ( $buttons ) : ?>
			<div class="mt-8 flex flex-wrap items-center justify-center gap-3">
				<?php foreach ( $buttons as $index => $row ) : ?>
					<?php
					$link = $row['link'] ?? null;
					if ( ! empty( $link['url'] ) ) {
						echo wp_kses_post( dpi_blocks_render_link( $link, 0 === $index ? 'btn btn-outline' : 'btn btn-gold', ! empty( $is_preview ) ) );
					}
					?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</article>
