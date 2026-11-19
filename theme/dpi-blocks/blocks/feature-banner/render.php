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
	'arrows'         => true,
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
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'relative left-1/2 w-screen max-w-none -translate-x-1/2 overflow-hidden bg-darkest' ) ); ?>>
	<div class="relative
		[&_.slick-arrow]:absolute [&_.slick-arrow]:bottom-4 [&_.slick-arrow]:z-20 md:[&_.slick-arrow]:bottom-auto md:[&_.slick-arrow]:top-1/2
		[&_.slick-arrow]:flex [&_.slick-arrow]:size-12 md:[&_.slick-arrow]:-translate-y-1/2
		[&_.slick-arrow]:items-center [&_.slick-arrow]:justify-center
		[&_.slick-arrow]:rounded-full [&_.slick-arrow]:border [&_.slick-arrow]:border-white/20
		[&_.slick-arrow]:bg-white/10 [&_.slick-arrow]:text-white [&_.slick-arrow]:transition-colors
		[&_.slick-arrow:hover]:bg-white/25 [&_.slick-arrow:focus-visible]:outline-2
		[&_.slick-arrow:focus-visible]:outline-offset-2 [&_.slick-arrow:focus-visible]:outline-secondary
		[&_.slick-arrow_svg]:size-5 [&_.slick-prev]:left-4 [&_.slick-next]:right-4
		lg:[&_.slick-prev]:left-10 lg:[&_.slick-next]:right-10
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
		[&_.slick-active_button]:size-2.5 [&_.slick-active_button]:bg-secondary
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
			<article class="relative h-[748px] overflow-hidden bg-darkest text-white">
				<?php if ( $image_id ) : ?>
					<?php echo wp_get_attachment_image( $image_id, 'full', false, array( 'class' => 'absolute inset-0 size-full object-cover', 'alt' => '' ) ); ?>
				<?php endif; ?>

				<div class="absolute inset-0 bg-darkest/20" aria-hidden="true"></div>
				<div class="absolute inset-y-0 left-0 w-full bg-linear-to-r from-darkest via-darkest/90 to-transparent md:w-[52%]" aria-hidden="true"></div>
				<div class="absolute inset-x-0 bottom-0 h-36 bg-linear-to-t from-darkest/65 to-transparent" aria-hidden="true"></div>

				<div class="relative z-10 mx-auto flex h-full max-w-wide items-center px-gutter">
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
</section>
