<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$slides = array_slice( get_field( 'slides' ) ?: array(), 0, 5 );

if ( ! $slides ) {
	if ( ! empty( $is_preview ) ) {
		echo '<p ' . get_block_wrapper_attributes() . '>' . esc_html__( 'Add at least one featured slide.', 'baltimore' ) . '</p>';
	}
	return;
}

$slick = array(
	'arrows'         => false,
	'dots'           => true,
	'infinite'       => true,
	'autoplay'       => false,
	'speed'          => 500,
	'pauseOnHover'   => true,
	'pauseOnFocus'   => true,
	'slidesToShow'   => 1,
	'slidesToScroll' => 1,
);
?>
<section data-dpi-feature-banner <?php echo get_block_wrapper_attributes( array( 'class' => 'not-prose relative left-1/2 mt-0! w-screen max-w-none -translate-x-1/2 overflow-hidden bg-darkest' ) ); ?>>
	<div class="relative [&_.slick-list]:mt-0! [&_.slick-track]:mt-0! [&_.slick-slide]:mt-0!
		[&_.slick-dots]:absolute [&_.slick-dots]:bottom-10 [&_.slick-dots]:left-1/2
		[&_.slick-dots]:z-20 [&_.slick-dots]:m-0 [&_.slick-dots]:flex
		[&_.slick-dots]:-translate-x-1/2 [&_.slick-dots]:items-center
		[&_.slick-dots]:gap-2 [&_.slick-dots]:p-0 [&_.slick-dots]:list-none
		[&_.slick-dots_li]:flex [&_.slick-dots_li]:items-center
		[&_.slick-dots_button]:block [&_.slick-dots_button]:size-2
		[&_.slick-dots_button]:cursor-pointer [&_.slick-dots_button]:rounded-full
		[&_.slick-dots_button]:border-0 [&_.slick-dots_button]:bg-white/50
		[&_.slick-dots_button]:p-0 [&_.slick-dots_button]:text-[0px]
		[&_.slick-dots_button]:transition-all
		[&_.slick-dots_li.slick-active_button]:size-2.5! [&_.slick-dots_li.slick-active_button]:bg-secondary!
		[&_.slick-dots_button:focus-visible]:outline-2
		[&_.slick-dots_button:focus-visible]:outline-offset-2
		[&_.slick-dots_button:focus-visible]:outline-secondary"
		<?php if ( count( $slides ) > 1 ) : ?>
			data-dpi-slick="<?php echo esc_attr( wp_json_encode( $slick ) ); ?>"
		<?php endif; ?>
	>
		<?php foreach ( $slides as $slide ) : ?>
			<?php
			$image      = $slide['image'] ?? 0;
			$image_id   = is_array( $image ) ? absint( $image['ID'] ?? 0 ) : absint( $image );
			$eyebrow    = trim( (string) ( $slide['eyebrow'] ?? '' ) );
			$heading    = trim( (string) ( $slide['headline'] ?? '' ) );
			$subheading = trim( (string) ( $slide['content'] ?? '' ) );
			$buttons    = array_slice( (array) ( $slide['buttons'] ?? array() ), 0, 2 );
			?>
			<article class="relative m-0! h-[748px] overflow-hidden bg-darkest text-white">
				<?php if ( $image_id ) : ?>
					<?php echo wp_get_attachment_image( $image_id, 'full', false, array( 'class' => 'absolute inset-0 size-full object-cover', 'alt' => '' ) ); ?>
				<?php endif; ?>

				<div class="absolute inset-0 bg-darkest/20" aria-hidden="true"></div>
				<div class="absolute inset-y-0 left-0 w-full bg-linear-to-r from-darkest via-darkest/90 to-transparent md:w-[52%]" aria-hidden="true"></div>
				<div class="absolute inset-x-0 bottom-0 h-36 bg-linear-to-t from-darkest/65 to-transparent" aria-hidden="true"></div>

				<div class="relative z-10 mx-auto flex h-full max-w-wide items-center px-gutter md:pl-[clamp(40px,calc(848px_-_50vw),96px)]">
					<div class="max-w-[620px]">
						<?php if ( $eyebrow ) : ?>
							<p class="font-body text-lg font-semibold leading-[27px] tracking-[0.22em] text-secondary uppercase"><?php echo esc_html( $eyebrow ); ?></p>
						<?php endif; ?>

						<?php if ( $heading ) : ?>
							<h2 class="mt-5 font-heading text-[clamp(2.75rem,4vw,4rem)] font-black leading-[1.25] text-white text-balance"><?php echo esc_html( $heading ); ?></h2>
						<?php endif; ?>

						<?php if ( $subheading ) : ?>
							<p class="mt-5 font-body text-lg leading-[1.625] text-white/75"><?php echo esc_html( $subheading ); ?></p>
						<?php endif; ?>

						<?php if ( $buttons ) : ?>
							<div class="mt-7 flex flex-wrap items-center gap-4">
								<?php foreach ( $buttons as $index => $row ) : ?>
									<?php
									$link = $row['link'] ?? null;
									if ( is_array( $link ) && ! empty( $link['url'] ) ) {
										echo wp_kses_post( dpi_blocks_render_link( $link, 0 === $index ? 'btn btn-gold' : 'btn btn-outline', ! empty( $is_preview ) ) );
									}
									?>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</article>
		<?php endforeach; ?>
	</div>

	<?php if ( count( $slides ) > 1 ) : ?>
		<button type="button" data-dpi-feature-prev aria-label="<?php esc_attr_e( 'Previous slide', 'baltimore' ); ?>" class="absolute bottom-4 left-4 z-30 flex size-12 cursor-pointer items-center justify-center rounded-full border border-white/20 bg-white/10 text-white transition-colors hover:bg-white/25 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-secondary md:top-1/2 md:bottom-auto md:-translate-y-1/2 lg:left-10">
			<svg aria-hidden="true" viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
		</button>
		<button type="button" data-dpi-feature-next aria-label="<?php esc_attr_e( 'Next slide', 'baltimore' ); ?>" class="absolute right-4 bottom-4 z-30 flex size-12 cursor-pointer items-center justify-center rounded-full border border-white/20 bg-white/10 text-white transition-colors hover:bg-white/25 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-secondary md:top-1/2 md:bottom-auto md:-translate-y-1/2 lg:right-10">
			<svg aria-hidden="true" viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
		</button>
	<?php endif; ?>
</section>
