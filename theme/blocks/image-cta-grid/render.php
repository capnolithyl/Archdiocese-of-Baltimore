<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$items = get_field( 'items' ) ?: array();

if ( ! $items ) {
	if ( ! empty( $is_preview ) ) {
		echo '<p ' . get_block_wrapper_attributes() . '>' . esc_html__( 'Add four image cards to complete this section.', 'baltimore' ) . '</p>';
	}
	return;
}

/* Four cards match the current design. Smaller counts stay centered if limits change. */
$grid_classes = match ( count( $items ) ) {
	1       => 'grid-cols-1 max-w-[350px]',
	2       => 'max-w-[708px]',
	3       => 'min-[992px]:grid-cols-3 min-[992px]:max-w-[1066px]',
	default => 'min-[992px]:grid-cols-4',
};

$eyebrow     = trim( (string) get_field( 'eyebrow' ) );
$heading     = trim( (string) get_field( 'heading' ) );
$description = trim( (string) get_field( 'description' ) );
$cta         = get_field( 'cta' );
$cta         = is_array( $cta ) && ! empty( $cta['url'] ) ? $cta : null;
$badge_colors = array( 'bg-[#BE2026]', 'bg-[#B8892B]', 'bg-[#4A90B8]', 'bg-[#4B5563]' );
$heading_id   = wp_unique_id( 'balt-image-cta-grid-' );
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'not-prose relative left-1/2 mt-0! w-screen max-w-none -translate-x-1/2 bg-surface py-section' ) ); ?><?php if ( $heading ) : ?> aria-labelledby="<?php echo esc_attr( $heading_id ); ?>"<?php endif; ?>>
	<div class="mx-auto flex w-full max-w-wide flex-col-reverse px-gutter sm:flex-col">
		<div class="mx-auto grid w-full grid-cols-2 gap-2 <?php echo esc_attr( $grid_classes ); ?>">
			<?php foreach ( $items as $index => $item ) : ?>
				<?php
				$image_id = absint( is_array( $item['image'] ?? null ) ? ( $item['image']['ID'] ?? 0 ) : ( $item['image'] ?? 0 ) );
				$label    = trim( (string) ( $item['label'] ?? '' ) );
				$link     = $item['link'] ?? null;
				$has_link = is_array( $link ) && ! empty( $link['url'] );
				$tag      = $has_link && empty( $is_preview ) ? 'a' : 'div';
				?>
				<<?php echo $tag; ?> class="group relative flex aspect-[3/4] items-center justify-center overflow-hidden rounded-card bg-darkest text-white no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-secondary sm:aspect-auto sm:h-[460px]"<?php if ( 'a' === $tag ) : ?> href="<?php echo esc_url( $link['url'] ); ?>"<?php if ( '_blank' === ( $link['target'] ?? '' ) ) : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?><?php endif; ?>>
					<?php if ( $image_id ) : ?>
						<?php echo wp_get_attachment_image( $image_id, 'full', false, array( 'class' => 'absolute inset-0 size-full object-cover transition-transform duration-500 group-hover:scale-105 group-focus-visible:scale-105 motion-reduce:transition-none', 'alt' => '' ) ); ?>
					<?php endif; ?>

					<span class="absolute inset-0 bg-black/10" aria-hidden="true"></span>
					<?php if ( $label ) : ?>
						<span class="relative z-10 max-w-[calc(100%-2rem)] rounded-[6px] px-5 py-2.5 text-center font-body text-[13px] font-bold leading-[19.5px] tracking-[0.15em] text-white uppercase shadow-md <?php echo esc_attr( $badge_colors[ $index % count( $badge_colors ) ] ); ?>">
							<?php echo esc_html( $label ); ?>
						</span>
					<?php endif; ?>
				</<?php echo $tag; ?>>
			<?php endforeach; ?>
		</div>

		<div class="relative z-10 mx-auto mb-8 w-full max-w-[760px] rounded-card border-2 border-secondary bg-white px-5 py-10 text-center shadow-xl sm:-mt-20 sm:mb-0 sm:px-14">
			<?php if ( $eyebrow ) : ?>
				<p class="font-body text-xl font-semibold leading-[30px] tracking-[0.2em] text-secondary uppercase"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<?php if ( $heading ) : ?>
				<h2 id="<?php echo esc_attr( $heading_id ); ?>" class="mt-3 font-heading text-[clamp(2.5rem,5vw,4rem)] font-black leading-[1.25] text-primary text-balance"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>

			<?php if ( $description ) : ?>
				<p class="mt-4 font-body text-[17px] leading-[1.625] text-[#374151]"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>

			<?php if ( $cta ) : ?>
				<?php
				$cta_tag = empty( $is_preview ) ? 'a' : 'span';
				?>
				<<?php echo $cta_tag; ?> class="mt-6 inline-flex items-center justify-center gap-2 font-body text-[13px] font-bold leading-[19.5px] tracking-[0.15em] text-tertiary uppercase no-underline transition-colors hover:text-primary focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-secondary"<?php if ( 'a' === $cta_tag ) : ?> href="<?php echo esc_url( $cta['url'] ); ?>"<?php if ( '_blank' === ( $cta['target'] ?? '' ) ) : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?><?php endif; ?>>
					<?php echo esc_html( $cta['title'] ?: __( 'Learn More', 'baltimore' ) ); ?>
					<svg aria-hidden="true" width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.166667" stroke-linecap="round" stroke-linejoin="round">
						<path d="M2.9167 7H11.0833M7 2.9167L11.0833 7 7 11.0833"/>
					</svg>
				</<?php echo $cta_tag; ?>>
			<?php endif; ?>
		</div>
	</div>
</section>
