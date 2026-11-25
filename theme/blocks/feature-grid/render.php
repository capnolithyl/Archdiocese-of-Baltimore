<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow    = (string) get_field( 'eyebrow' );
$heading    = (string) get_field( 'heading' );
$subheading = (string) get_field( 'subheading' );
$items      = array_slice( (array) ( get_field( 'items' ) ?: array() ), 0, 12 );
$count      = count( $items );
$alignment  = get_field( 'heading_alignment' );

if ( ! $count && empty( $is_preview ) ) {
	return;
}

// Compact Links is another presentation of the same repeater, not a separate block.
if ( 'compact' === get_field( 'style' ) ) {
	$heading_id = wp_unique_id( 'balt-feature-grid-' );
	$view_all   = get_field( 'view_all' );
	$view_url   = is_array( $view_all ) ? esc_url( $view_all['url'] ?? '' ) : '';
	$view_label = is_array( $view_all ) ? trim( (string) ( $view_all['title'] ?? '' ) ) : '';
	$preview    = ! empty( $is_preview );
	?>
	<section <?php echo get_block_wrapper_attributes( array( 'class' => 'not-prose balt-full-bleed balt-feature-grid--compact relative mt-0! w-full max-w-none bg-[#F5F3EE] py-[clamp(3rem,5.62vw,6rem)]' ) ); ?><?php if ( $heading ) : ?> aria-labelledby="<?php echo esc_attr( $heading_id ); ?>"<?php endif; ?>>
		<div class="mx-auto w-full max-w-wide px-gutter">
			<?php if ( $eyebrow || $heading || $subheading || ( $view_url && $view_label ) ) : ?>
				<header class="<?php echo 'center' === $alignment ? 'flex flex-col items-center gap-4 text-center' : 'flex flex-wrap items-end justify-between gap-4 text-left'; ?>">
					<div class="min-w-0">
						<?php if ( $eyebrow ) : ?>
							<p class="font-body text-sm font-bold leading-5 tracking-[3px] text-tertiary uppercase"><?php echo esc_html( $eyebrow ); ?></p>
						<?php endif; ?>
						<?php if ( $heading ) : ?>
							<h2 id="<?php echo esc_attr( $heading_id ); ?>" class="mt-3 font-heading text-[clamp(2rem,3vw,2.5rem)] font-extrabold leading-[1.5] text-foreground"><?php echo esc_html( $heading ); ?></h2>
						<?php endif; ?>
						<?php if ( $subheading ) : ?>
							<p class="<?php echo 'center' === $alignment ? 'mx-auto ' : ''; ?>mt-3 max-w-[700px] font-body text-base leading-[26px] text-muted"><?php echo esc_html( $subheading ); ?></p>
						<?php endif; ?>
					</div>
					<?php if ( $view_url && $view_label ) : ?>
						<?php $view_tag = $preview ? 'span' : 'a'; ?>
						<<?php echo $view_tag; ?> class="inline-flex items-center gap-2 font-body text-sm font-bold leading-5 tracking-[0.025em] text-primary uppercase no-underline hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"<?php if ( ! $preview ) : ?> href="<?php echo $view_url; ?>"<?php if ( '_blank' === ( $view_all['target'] ?? '' ) ) : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?><?php endif; ?>>
							<?php echo esc_html( $view_label ); ?>
							<svg aria-hidden="true" viewBox="0 0 13 13" width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.08333" stroke-linecap="round" stroke-linejoin="round"><path d="M2.7083 6.5h7.5834M6.5 2.7083l3.7917 3.7917-3.7917 3.7917"/></svg>
						</<?php echo $view_tag; ?>>
					<?php endif; ?>
				</header>
			<?php endif; ?>
			<?php if ( $items ) : ?>
				<div class="mt-12 grid grid-cols-1 gap-5 sm:grid-cols-2 desktop:grid-cols-4">
					<?php foreach ( $items as $item ) :
						if ( ! is_array( $item ) ) { continue; }
						$title = trim( (string) ( $item['heading'] ?? '' ) );
						if ( '' === $title && ! $preview ) { continue; }
						$icon = is_string( $item['icon'] ?? null ) ? $item['icon'] : '';
						$label = trim( (string) ( $item['eyebrow'] ?? '' ) );
						$description = trim( (string) ( $item['description'] ?? '' ) );
						$link = balt_feature_grid_destination( $item );
						$url = esc_url( $link['url'] );
						$tag = $url && ! $preview ? 'a' : 'div';
						$color = match ( $item['icon_color'] ?? 'primary' ) {
							'tertiary' => 'bg-tertiary text-white',
							'secondary' => 'bg-secondary text-primary',
							default => 'bg-primary text-white',
						};
						?>
						<<?php echo $tag; ?> class="group flex min-h-[196px] min-w-0 flex-col items-start gap-4 rounded-[14px] border border-[#E5E7EB] bg-white p-6 text-foreground no-underline transition-colors duration-300 hover:border-secondary focus-visible:border-secondary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary motion-reduce:transition-none"<?php if ( 'a' === $tag ) : ?> href="<?php echo $url; ?>"<?php if ( $link['download'] ) : ?> download<?php endif; ?><?php if ( '_blank' === $link['target'] ) : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?><?php endif; ?>>
							<?php if ( $icon ) : ?>
								<span class="flex size-12 shrink-0 items-center justify-center rounded-control text-xl leading-none <?php echo esc_attr( $color ); ?>" aria-hidden="true"><i class="<?php echo esc_attr( $icon ); ?>"></i></span>
							<?php endif; ?>
							<div class="w-full min-w-0">
								<?php if ( $label ) : ?>
									<p class="pt-1.5 font-body text-[10px] font-bold leading-[15px] tracking-[2px] text-tertiary uppercase"><?php echo esc_html( $label ); ?></p>
								<?php endif; ?>
								<?php if ( $title ) : ?>
									<h3 class="<?php echo $label ? 'mt-1 ' : ''; ?>font-body text-base font-bold leading-[22px] text-foreground wrap-break-word"><?php echo esc_html( $title ); ?></h3>
								<?php endif; ?>
								<?php if ( $description ) : ?>
									<p class="mt-2 font-body text-sm leading-5 text-muted wrap-break-word"><?php echo esc_html( $description ); ?></p>
								<?php endif; ?>
							</div>
							<?php if ( $url ) : ?>
								<span class="mt-auto inline-flex items-center gap-1.5 font-body text-xs font-semibold leading-4 tracking-[0.025em] text-primary uppercase group-hover:underline group-focus-visible:underline">
									<?php echo esc_html( $link['title'] ); ?>
									<svg width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true"><path d="M2.2917 5.5H8.7083M5.5 2.2917L8.7083 5.5L5.5 8.7083" stroke="currentColor" stroke-width="0.91667"/></svg>
								</span>
							<?php endif; ?>
						</<?php echo $tag; ?>>
					<?php endforeach; ?>
				</div>
			<?php elseif ( $preview ) : ?>
				<p class="mt-8"><?php esc_html_e( 'Add at least one feature to populate this grid.', 'baltimore' ); ?></p>
			<?php endif; ?>
		</div>
	</section>
	<?php
	return;
}

$columns = match ( $count ) {
	1       => 'grid-cols-1 max-w-[474px]',
	2, 4    => 'grid-cols-1 sm:grid-cols-2 max-w-[960px]',
	5       => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-6',
	default => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
};

$heading_id = wp_unique_id( 'balt-feature-grid-' );
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'balt-full-bleed relative w-full max-w-none bg-surface py-section' ) ); ?><?php if ( $heading ) : ?> aria-labelledby="<?php echo esc_attr( $heading_id ); ?>"<?php endif; ?>>
	<div class="mx-auto w-full max-w-wide px-gutter">
		<header class="<?php echo 'left' === $alignment ? 'text-left' : 'text-center'; ?>">
			<?php if ( $eyebrow ) : ?>
				<p class="font-body text-lg font-bold tracking-[0.19em] text-tertiary uppercase"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<?php if ( $heading ) : ?>
				<h2 id="<?php echo esc_attr( $heading_id ); ?>" class="mt-4 font-heading text-[clamp(2.5rem,4.5vw,4.8rem)] font-black leading-tight text-foreground"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>

			<?php if ( $subheading ) : ?>
				<p class="<?php echo 'left' === $alignment ? '' : 'mx-auto '; ?>mt-3 max-w-[520px] text-base leading-[26px] text-body"><?php echo esc_html( $subheading ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( $items ) : ?>
			<div class="mx-auto mt-14 grid w-full gap-px overflow-hidden rounded-card border border-border bg-border <?php echo esc_attr( $columns ); ?>">
				<?php foreach ( $items as $index => $item ) : ?>
					<?php
					$icon        = is_string( $item['icon'] ?? null ) ? $item['icon'] : '';
					$title       = (string) ( $item['heading'] ?? '' );
					$description = (string) ( $item['description'] ?? '' );
					$link        = balt_feature_grid_destination( $item );
					$has_link    = '' !== esc_url( $link['url'] );
					$tag         = $has_link && empty( $is_preview ) ? 'a' : 'div';
					$span        = 5 === $count ? ( $index < 3 ? 'lg:col-span-2' : 'lg:col-span-3' ) : '';
					?>
					<<?php echo $tag; ?> class="relative flex min-h-[336px] flex-col items-center bg-white px-6 py-12 text-center no-underline text-foreground before:absolute before:inset-x-0 before:top-0 before:h-[3px] before:origin-center before:scale-x-0 before:bg-secondary before:transition-transform before:duration-300 before:ease-out motion-reduce:before:transition-none md:py-16 <?php echo esc_attr( $span ); ?> <?php echo $has_link ? 'group transition-colors duration-300 ease-out hover:bg-surface hover:before:scale-x-100 focus-visible:bg-surface focus-visible:before:scale-x-100 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-primary motion-reduce:transition-none' : ''; ?>"<?php if ( 'a' === $tag ) : ?> href="<?php echo esc_url( $link['url'] ); ?>"<?php if ( $link['download'] ) : ?> download<?php endif; ?><?php if ( '_blank' === ( $link['target'] ?? '' ) ) : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?><?php endif; ?>>
						<?php if ( $icon ) : ?>
							<span class="mb-7 flex size-[58px] shrink-0 items-center justify-center text-[58px] leading-none text-secondary transition-transform duration-300 ease-out group-hover:scale-110 group-focus-visible:scale-110 motion-reduce:transition-none [&_svg]:size-[58px]" aria-hidden="true">
								<i class="<?php echo esc_attr( $icon ); ?>"></i>
							</span>
						<?php endif; ?>

						<?php if ( $title ) : ?>
							<h3 class="text-base font-bold leading-6 tracking-[0.025em] text-foreground"><?php echo esc_html( $title ); ?></h3>
						<?php endif; ?>

						<span class="my-3 flex items-center gap-1.5" aria-hidden="true">
							<span class="h-px w-5 bg-secondary/50"></span>
							<span class="size-[5px] rotate-45 bg-secondary/60"></span>
							<span class="h-px w-5 bg-secondary/50"></span>
						</span>

						<?php if ( $description ) : ?>
							<p class="max-w-[240px] text-sm leading-[23px] text-muted"><?php echo esc_html( $description ); ?></p>
						<?php endif; ?>
					</<?php echo $tag; ?>>
				<?php endforeach; ?>
			</div>
		<?php elseif ( ! empty( $is_preview ) ) : ?>
			<p class="mt-8 text-center"><?php esc_html_e( 'Add at least one feature to populate this grid.', 'baltimore' ); ?></p>
		<?php endif; ?>
	</div>
</section>
