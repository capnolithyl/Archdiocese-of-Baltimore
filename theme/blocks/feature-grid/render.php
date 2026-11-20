<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow    = (string) get_field( 'eyebrow' );
$heading    = (string) get_field( 'heading' );
$subheading = (string) get_field( 'subheading' );
$items      = array_slice( get_field( 'items' ) ?: array(), 0, 6 );
$count      = count( $items );

if ( ! $count && empty( $is_preview ) ) {
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
		<header class="text-center">
			<?php if ( $eyebrow ) : ?>
				<p class="font-body text-lg font-bold tracking-[0.19em] text-tertiary uppercase"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>

			<?php if ( $heading ) : ?>
				<h2 id="<?php echo esc_attr( $heading_id ); ?>" class="mt-4 font-heading text-[clamp(2.5rem,4.5vw,4.8rem)] font-black leading-tight text-foreground"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>

			<?php if ( $subheading ) : ?>
				<p class="mx-auto mt-3 max-w-[520px] text-base leading-[26px] text-body"><?php echo esc_html( $subheading ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( $items ) : ?>
			<div class="mx-auto mt-14 grid w-full gap-px overflow-hidden rounded-card border border-border bg-border <?php echo esc_attr( $columns ); ?>">
				<?php foreach ( $items as $index => $item ) : ?>
					<?php
					$icon        = is_string( $item['icon'] ?? null ) ? $item['icon'] : '';
					$title       = (string) ( $item['heading'] ?? '' );
					$description = (string) ( $item['description'] ?? '' );
					$link        = $item['link'] ?? null;
					$has_link    = is_array( $link ) && ! empty( $link['url'] );
					$tag         = $has_link && empty( $is_preview ) ? 'a' : 'div';
					$span        = 5 === $count ? ( $index < 3 ? 'lg:col-span-2' : 'lg:col-span-3' ) : '';
					?>
					<<?php echo $tag; ?> class="relative flex min-h-[336px] flex-col items-center bg-white px-6 py-12 text-center no-underline text-foreground before:absolute before:inset-x-0 before:top-0 before:h-[3px] before:origin-center before:scale-x-0 before:bg-secondary before:transition-transform before:duration-300 before:ease-out motion-reduce:before:transition-none md:py-16 <?php echo esc_attr( $span ); ?> <?php echo $has_link ? 'group transition-colors duration-300 ease-out hover:bg-surface hover:before:scale-x-100 focus-visible:bg-surface focus-visible:before:scale-x-100 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-primary motion-reduce:transition-none' : ''; ?>"<?php if ( 'a' === $tag ) : ?> href="<?php echo esc_url( $link['url'] ); ?>"<?php if ( '_blank' === ( $link['target'] ?? '' ) ) : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?><?php endif; ?>>
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
