<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$slides = get_field( 'slides' ) ?: array();

if ( ! $slides ) {
	if ( ! empty( $is_preview ) ) {
		echo '<p>' . esc_html__( 'Add at least one hero slide.', 'baltimore' ) . '</p>';
	}
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
<div class="relative [&_.slick-arrow]:absolute [&_.slick-arrow]:top-1/2 [&_.slick-arrow]:z-20 [&_.slick-arrow]:-translate-y-1/2 [&_.slick-arrow]:text-white [&_.slick-prev]:left-4 [&_.slick-next]:right-4 [&_.slick-dots]:absolute [&_.slick-dots]:bottom-5 [&_.slick-dots]:left-1/2"<?php if ( count( $slides ) > 1 ) : ?> data-dpi-slick="<?php echo esc_attr( wp_json_encode( $slick ) ); ?>"<?php endif; ?>>
	<?php foreach ( $slides as $index => $slide ) : ?>
		<?php
		$image_id    = $slide['image']['ID'] ?? 0;
		$heading     = trim( (string) ( $slide['heading'] ?? '' ) );
		$subheading  = $slide['subheadings'][0]['subheading'] ?? '';
		$buttons     = array_slice( $slide['buttons'] ?? array(), 0, 2 );
		$heading_tag = is_front_page() && 0 === $index ? 'h1' : 'h2';
		?>
		<article class="relative h-[865px] overflow-hidden bg-foreground">
			<div class="absolute inset-0">
				<?php if ( $image_id ) : ?>
					<?php echo wp_get_attachment_image( $image_id, 'full', false, array( 'alt' => '', 'class' => 'h-full w-full object-cover' ) ); ?>
				<?php elseif ( ! empty( $is_preview ) ) : ?>
					<p><?php esc_html_e( 'Choose an image for this slide.', 'baltimore' ); ?></p>
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
						<?php foreach ( $buttons as $button_index => $row ) : ?>
							<?php
							$link = $row['link'] ?? null;
							if ( ! empty( $link['url'] ) ) {
								echo wp_kses_post( dpi_blocks_render_link( $link, 0 === $button_index ? 'btn btn-outline' : 'btn btn-gold', ! empty( $is_preview ) ) );
							}
							?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</article>
	<?php endforeach; ?>
</div>
