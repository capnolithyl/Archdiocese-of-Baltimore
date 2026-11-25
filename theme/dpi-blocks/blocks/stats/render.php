<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$stats = get_field( 'stats' ) ?: array();
$items = array();

foreach ( array_slice( (array) $stats, 0, 6 ) as $stat ) {
	$value = trim( (string) ( $stat['value'] ?? '' ) );
	$label = trim( (string) ( $stat['label'] ?? '' ) );

	if ( '' !== $value || '' !== $label ) {
		$items[] = array( 'value' => $value, 'label' => $label );
	}
}

if ( ! $items ) {
	if ( ! empty( $is_preview ) ) {
		echo '<p ' . get_block_wrapper_attributes() . '>' . esc_html__( 'Add at least one statistic.', 'baltimore' ) . '</p>';
	}
	return;
}

// Only the compact style opts out of the existing impact-banner layout.
// Missing or unknown values retain the original homepage behavior.
if ( 'compact' === get_field( 'style' ) ) {
	$compact_columns = match ( count( $items ) ) {
		1       => 'grid-cols-1',
		2       => 'grid-cols-1 min-[360px]:grid-cols-2',
		3       => 'grid-cols-1 min-[360px]:grid-cols-2 sm:grid-cols-3',
		4       => 'grid-cols-1 min-[360px]:grid-cols-2 desktop:grid-cols-4',
		5       => 'grid-cols-1 min-[360px]:grid-cols-2 md:grid-cols-3 desktop:grid-cols-5',
		default => 'grid-cols-1 min-[360px]:grid-cols-2 md:grid-cols-3 desktop:grid-cols-6',
	};
	?>
	<section <?php echo get_block_wrapper_attributes( array( 'class' => 'not-prose balt-full-bleed balt-stats-compact mt-0! w-full max-w-none border-y border-[#E5E7EB] bg-[#F5F3EE] py-10' ) ); ?> aria-label="<?php esc_attr_e( 'Statistics', 'baltimore' ); ?>">
		<div class="mx-auto w-full max-w-wide px-gutter">
			<ul class="m-0 grid list-none items-start gap-6 p-0 text-center <?php echo esc_attr( $compact_columns ); ?>">
				<?php foreach ( $items as $item ) : ?>
					<li class="flex min-w-0 flex-col items-center gap-1 wrap-break-word">
						<?php if ( '' !== $item['value'] ) : ?>
							<strong class="font-heading text-[clamp(2rem,3vw,2.75rem)] font-extrabold leading-none text-primary"><?php echo esc_html( $item['value'] ); ?></strong>
						<?php endif; ?>
						<?php if ( '' !== $item['label'] ) : ?>
							<span class="font-body text-sm leading-5 tracking-[0.025em] text-muted"><?php echo esc_html( $item['label'] ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</section>
	<?php
	return;
}

$eyebrow     = trim( (string) get_field( 'eyebrow' ) );
$heading     = trim( (string) get_field( 'heading' ) );
$description = trim( (string) get_field( 'description' ) );
$image       = get_field( 'background_image' );
$image_id    = absint( is_array( $image ) ? ( $image['ID'] ?? 0 ) : $image );
$buttons     = array_slice( (array) ( get_field( 'buttons' ) ?: array() ), 0, 2 );
$heading_id  = wp_unique_id( 'balt-stats-' );
$tinted      = 'tinted' === get_field( 'item_background' );
$grid_style  = $tinted ? 'gap-px bg-white/15' : 'gap-0 bg-transparent';
$item_style  = $tinted ? 'bg-primary/25' : 'border-r border-b border-white/15 bg-transparent';

$columns = match ( count( $items ) ) {
	1       => 'grid-cols-1 max-w-[280px]',
	2       => 'grid-cols-2 max-w-[560px]',
	3       => 'grid-cols-2 sm:grid-cols-3 max-w-[840px]',
	4       => 'grid-cols-2 lg:grid-cols-4 max-w-[1120px]',
	5       => 'grid-cols-2 sm:grid-cols-3 xl:grid-cols-5',
	default => 'grid-cols-2 sm:grid-cols-3 xl:grid-cols-6',
};
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'not-prose balt-full-bleed relative mt-0! w-full max-w-none overflow-hidden border-t-4 border-[#C5382D] bg-primary py-section text-white' ) ); ?><?php if ( $heading ) : ?> aria-labelledby="<?php echo esc_attr( $heading_id ); ?>"<?php endif; ?>>
	<?php if ( $image_id ) : ?>
		<?php echo wp_get_attachment_image( $image_id, 'full', false, array( 'class' => 'absolute inset-0 size-full object-cover', 'alt' => '' ) ); ?>
	<?php endif; ?>
	<div class="absolute inset-0 bg-linear-[158deg] from-[#001A3D]/95 via-primary/90 to-foreground/95" aria-hidden="true"></div>

	<div class="relative mx-auto w-full max-w-wide px-gutter text-center">
		<?php if ( $eyebrow ) : ?>
			<p class="font-body text-lg font-bold leading-[27px] tracking-[0.19em] text-white/60 uppercase"><?php echo esc_html( $eyebrow ); ?></p>
		<?php endif; ?>

		<?php if ( $heading ) : ?>
			<h2 id="<?php echo esc_attr( $heading_id ); ?>" class="mt-4 font-heading text-[clamp(2.75rem,5vw,4.8rem)] font-extrabold leading-tight text-white"><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<?php if ( $description ) : ?>
			<p class="mx-auto mt-3 max-w-[580px] font-body text-lg leading-[1.625] text-white/75"><?php echo esc_html( $description ); ?></p>
		<?php endif; ?>

		<ul class="mx-auto mt-14 grid w-full overflow-hidden rounded-card border-2 border-white/15 p-0 list-none <?php echo esc_attr( $columns . ' ' . $grid_style ); ?>">
			<?php foreach ( $items as $item ) : ?>
				<li class="flex min-h-[160px] flex-col items-center justify-center gap-3 px-3 py-7 text-center sm:px-4 <?php echo esc_attr( $item_style ); ?>">
					<?php if ( '' !== $item['value'] ) : ?>
						<strong class="font-body text-[clamp(1.75rem,2.9vw,3rem)] font-extrabold leading-none tracking-[-0.025em] text-white"><?php echo esc_html( $item['value'] ); ?></strong>
					<?php endif; ?>
					<?php if ( '' !== $item['label'] ) : ?>
						<span class="font-body text-sm leading-[1.375] text-white/60"><?php echo esc_html( $item['label'] ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ( $buttons ) : ?>
			<div class="mt-10 flex flex-wrap items-center justify-center gap-3">
				<?php foreach ( $buttons as $index => $row ) : ?>
					<?php
					$link = $row['link'] ?? null;
					if ( is_array( $link ) && ! empty( $link['url'] ) ) {
						echo wp_kses_post( dpi_blocks_render_link( $link, 0 === $index ? 'btn btn-outline' : 'btn btn-gold', ! empty( $is_preview ) ) );
					}
					?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
