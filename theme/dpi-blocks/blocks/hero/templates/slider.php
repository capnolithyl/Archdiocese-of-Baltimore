<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$slides = get_field( 'slides' ) ?: array();

if ( ! $slides ) {
	if ( ! empty( $is_preview ) ) {
		echo '<p class="dpi-block--placeholder">' . esc_html__( 'Add at least one hero slide.', 'baltimore' ) . '</p>';
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
<div class="dpi-hero__slides min-h-svh! [&_.slick-list]:min-h-svh [&_.slick-track]:min-h-svh [&_.slick-slide]:min-h-svh [&_.slick-arrow]:absolute! [&_.slick-arrow]:top-1/2 [&_.slick-arrow]:z-20 [&_.slick-arrow]:grid! [&_.slick-arrow]:size-10 [&_.slick-arrow]:-translate-y-1/2 [&_.slick-arrow]:place-items-center [&_.slick-arrow]:rounded-full [&_.slick-arrow]:bg-white/90 [&_.slick-arrow]:text-primary [&_.slick-arrow]:shadow-md [&_.slick-arrow_svg]:size-5 [&_.slick-prev]:left-3 [&_.slick-next]:right-3 [&_.slick-dots]:flex! [&_.slick-dots]:justify-center [&_.slick-dots]:gap-2 [&_.slick-dots]:p-3 [&_.slick-dots]:list-none [&_.slick-dots_button]:size-3 [&_.slick-dots_button]:rounded-full [&_.slick-dots_button]:bg-current/40 [&_.slick-dots_button]:text-[0px] [&_.slick-active_button]:bg-current"<?php if ( count( $slides ) > 1 ) : ?> data-dpi-slick="<?php echo esc_attr( wp_json_encode( $slick ) ); ?>"<?php endif; ?>>
	<?php foreach ( $slides as $index => $slide ) : ?>
		<?php
		$image_id    = $slide['image']['ID'] ?? 0;
		$heading     = trim( (string) ( $slide['heading'] ?? '' ) );
		$subheading  = $slide['subheadings'][0]['subheading'] ?? '';
		$buttons     = array_slice( $slide['buttons'] ?? array(), 0, 2 );
		$heading_tag = is_front_page() && 0 === $index ? 'h1' : 'h2';
		?>
		<article class="dpi-hero__slide relative min-h-svh! bg-black text-white">
			<div class="dpi-hero__media absolute inset-0">
				<?php if ( $image_id ) : ?>
					<?php echo wp_get_attachment_image( $image_id, 'full', false, array( 'alt' => '', 'class' => 'size-full object-cover' ) ); ?>
				<?php elseif ( ! empty( $is_preview ) ) : ?>
					<p class="dpi-block--placeholder"><?php esc_html_e( 'Choose an image for this slide.', 'baltimore' ); ?></p>
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
	<?php endforeach; ?>
</div>
