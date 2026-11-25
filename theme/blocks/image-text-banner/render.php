<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow    = trim( (string) get_field( 'eyebrow' ) );
$heading    = trim( (string) get_field( 'heading' ) );
$subheading = trim( (string) get_field( 'subheading' ) );
$quote      = trim( (string) get_field( 'quote' ) );
$image      = get_field( 'image' );
$image_id   = absint( is_array( $image ) ? ( $image['ID'] ?? 0 ) : $image );
$buttons    = array_slice( (array) ( get_field( 'buttons' ) ?: array() ), 0, 2 );
$heading_id = wp_unique_id( 'balt-image-text-banner-' );

// Missing or unknown styles retain the original split homepage layout.
if ( 'background' === get_field( 'style' ) ) {
	$description = trim( (string) get_field( 'description' ) );
	?>
	<section <?php echo get_block_wrapper_attributes( array( 'class' => 'not-prose balt-full-bleed balt-image-text-banner--background relative isolate mt-0! w-full max-w-none overflow-hidden bg-primary text-white' ) ); ?><?php if ( $heading ) : ?> aria-labelledby="<?php echo esc_attr( $heading_id ); ?>"<?php endif; ?>>
		<?php if ( $image_id ) : ?>
			<?php echo wp_get_attachment_image( $image_id, 'full', false, array( 'class' => 'pointer-events-none absolute inset-0 size-full object-cover object-center', 'alt' => '', 'sizes' => '100vw' ) ); ?>
		<?php endif; ?>
		<div class="pointer-events-none absolute inset-0 bg-linear-to-r from-primary/95 via-primary/80 via-50% to-primary/0" aria-hidden="true"></div>
		<div class="pointer-events-none absolute inset-0 bg-primary/70 desktop:hidden" aria-hidden="true"></div>
		<div class="pointer-events-none absolute inset-x-0 top-0 z-10 h-1 bg-secondary" aria-hidden="true"></div>
		<div class="relative mx-auto flex w-full max-w-wide items-center px-gutter py-[clamp(4rem,7.49vw,8rem)] desktop:min-h-[694px]">
			<div class="w-full max-w-[600px] min-w-0 wrap-break-word">
				<?php if ( $eyebrow ) : ?>
					<p class="font-body text-sm font-bold leading-5 tracking-[3px] text-secondary uppercase"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>
				<?php if ( $heading ) : ?>
					<h2 id="<?php echo esc_attr( $heading_id ); ?>" class="<?php echo $eyebrow ? 'mt-4 ' : ''; ?>font-heading text-[clamp(2.5rem,4vw,3.5rem)] font-extrabold leading-[1.25] text-white"><?php echo esc_html( $heading ); ?></h2>
				<?php endif; ?>
				<?php if ( $description ) : ?>
					<p class="<?php echo $heading || $eyebrow ? 'mt-6 ' : ''; ?>whitespace-pre-line font-body text-lg leading-[29.25px] text-white/80"><?php echo esc_html( $description ); ?></p>
				<?php endif; ?>
				<?php
				$links = array();
				foreach ( $buttons as $row ) {
					$link = $row['link'] ?? null;
					if ( is_array( $link ) && ! empty( $link['url'] ) && esc_url( $link['url'] ) && '' !== trim( (string) ( $link['title'] ?? '' ) ) ) {
						$links[] = $link;
					}
				}
				?>
				<?php if ( $links ) : ?>
					<div class="mt-8 flex flex-wrap items-start gap-3">
						<?php foreach ( $links as $index => $link ) : ?>
							<?php
							$classes = 0 === $index ? 'btn btn-gold rounded-[4px] min-h-12' : 'btn btn-outline-subtle rounded-[4px] min-h-12';
							echo wp_kses_post( dpi_blocks_render_link( $link, $classes, ! empty( $is_preview ) ) );
							?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
	return;
}
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'not-prose balt-full-bleed relative mt-0! w-full max-w-none overflow-hidden bg-foreground text-white' ) ); ?><?php if ( $heading ) : ?> aria-labelledby="<?php echo esc_attr( $heading_id ); ?>"<?php endif; ?>>
	<div class="mx-auto grid w-full max-w-wide grid-cols-1 lg:min-h-[632px] lg:grid-cols-2">
		<?php if ( $image_id ) : ?>
			<div class="relative order-1 h-[390px] overflow-hidden sm:h-[360px] lg:order-2 lg:h-auto">
				<?php echo wp_get_attachment_image( $image_id, 'full', false, array( 'class' => 'absolute inset-0 size-full object-cover object-center', 'alt' => '' ) ); ?>
				<span class="absolute inset-x-0 bottom-0 h-32 bg-linear-to-t from-foreground to-transparent lg:h-24" aria-hidden="true"></span>
				<span class="absolute inset-y-0 left-0 hidden w-[55%] bg-linear-to-r from-foreground to-transparent lg:block" aria-hidden="true"></span>
			</div>
		<?php endif; ?>

		<div class="order-2 flex min-w-0 flex-col justify-center px-10 py-12 sm:px-12 sm:py-16 lg:order-1 lg:px-20 lg:py-20<?php echo $image_id ? '' : ' lg:col-span-2'; ?>">
			<?php if ( $eyebrow ) : ?>
				<p class="mb-5 font-body text-lg font-semibold leading-[27px] tracking-[0.22em] text-secondary uppercase"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<?php if ( $heading ) : ?>
				<h2 id="<?php echo esc_attr( $heading_id ); ?>" class="font-heading text-[clamp(2.5rem,4.25vw,4rem)] font-extrabold leading-[1.25] text-white text-balance"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>

			<?php if ( $subheading ) : ?>
				<p class="mt-2 font-body text-sm font-medium leading-5 tracking-[0.14em] text-white/45 uppercase"><?php echo esc_html( $subheading ); ?></p>
			<?php endif; ?>

			<?php if ( $quote ) : ?>
				<blockquote class="mt-8 border-l-4 border-secondary pl-6">
					<p class="font-heading text-[clamp(1.25rem,2vw,1.5rem)] italic leading-[1.625] text-white/85"><?php echo esc_html( $quote ); ?></p>
				</blockquote>
			<?php endif; ?>

			<?php if ( $buttons ) : ?>
				<div class="mt-10 flex flex-col items-start gap-3 sm:flex-row sm:flex-wrap">
					<?php foreach ( $buttons as $index => $row ) : ?>
						<?php
						$link = $row['link'] ?? null;
						if ( ! is_array( $link ) || empty( $link['url'] ) ) {
							continue;
						}

						$tag      = empty( $is_preview ) ? 'a' : 'span';
						$classes  = 0 === $index ? 'btn btn-red' : 'btn btn-outline-subtle';
						$target   = '_blank' === ( $link['target'] ?? '' );
						?>
						<<?php echo $tag; ?> class="<?php echo esc_attr( $classes ); ?>"<?php if ( 'a' === $tag ) : ?> href="<?php echo esc_url( $link['url'] ); ?>"<?php if ( $target ) : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?><?php endif; ?>><?php echo esc_html( $link['title'] ?? '' ); ?></<?php echo $tag; ?>>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
