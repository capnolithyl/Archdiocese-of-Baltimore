<?php
/**
 * Shared image card grid for RSS News and Content Card Grid.
 * Sources supply data only; card appearance and interactions live here.
 *
 * @package Archdiocese_of_Baltimore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$cards = $args['cards'] ?? array();
$external = ! empty( $args['external'] );
$accent = ! empty( $args['accent'] );
$preview = ! empty( $args['preview'] );
$zoom_class = 'size-full object-cover transition-transform duration-300 ease-out group-hover:scale-105 group-focus-within:scale-105 motion-reduce:transition-none';
?>
<div class="balt-content-card-grid mt-14 grid grid-cols-1 gap-px overflow-hidden rounded-card border border-[#E8E3D8] bg-[#E8E3D8] sm:grid-cols-2 lg:grid-cols-3">
	<?php foreach ( $cards as $card ) :
		$url = esc_url( $card['link'] ?? '' );
		$linked = '' !== $url && ! $preview;
		$target = $external || '_blank' === ( $card['target'] ?? '' );
		$label = trim( (string) ( $card['link_label'] ?? '' ) ) ?: __( 'Learn More', 'baltimore' );
		$date = (int) ( $card['date'] ?? 0 );
		$metadata = trim( (string) ( $card['meta'] ?? '' ) );
		$image_id = (int) ( $card['image_id'] ?? 0 );
		$image_url = esc_url( $card['image'] ?? '' );
		$title = (string) ( $card['title'] ?? '' );
		$excerpt = (string) ( $card['excerpt'] ?? '' );
		$image = $image_id ? wp_get_attachment_image( $image_id, 'large', false, array( 'alt' => '', 'class' => $zoom_class, 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw' ) ) : '';
		?>
		<article class="group relative flex min-w-0 flex-col bg-white transition-colors duration-300 ease-out hover:bg-surface focus-within:bg-surface before:pointer-events-none before:absolute before:inset-x-0 before:top-0 before:z-10 before:h-[3px] before:origin-center before:bg-secondary before:transition-transform before:duration-300 before:ease-out hover:before:scale-x-100 focus-within:before:scale-x-100 motion-reduce:transition-none motion-reduce:before:transition-none <?php echo $accent ? 'before:scale-x-100' : 'before:scale-x-0'; ?>">
			<?php if ( $image || $image_url ) : ?>
				<?php if ( $linked ) : ?>
					<a href="<?php echo $url; ?>"<?php if ( $target ) : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?> tabindex="-1" aria-hidden="true" class="block h-[200px] overflow-hidden bg-surface">
				<?php else : ?>
					<div class="block h-[200px] overflow-hidden bg-surface">
				<?php endif; ?>
				<?php if ( $image ) : ?>
					<?php echo $image; ?>
				<?php else : ?>
					<img src="<?php echo $image_url; ?>" alt="" loading="lazy" decoding="async" class="<?php echo esc_attr( $zoom_class ); ?>">
				<?php endif; ?>
				<?php if ( $linked ) : ?></a><?php else : ?></div><?php endif; ?>
			<?php else : ?>
				<div class="h-[200px] bg-surface" aria-hidden="true"></div>
			<?php endif; ?>

			<div class="flex flex-1 flex-col p-6">
				<?php if ( $date ) : ?>
					<time datetime="<?php echo esc_attr( gmdate( $external ? 'Y-m-d' : 'c', $date ) ); ?>" class="font-body text-xs leading-4 text-[#99A1AF]"><?php echo esc_html( wp_date( $external ? 'F j, Y' : 'F j, Y · g:i A', $date ) ); ?></time>
				<?php elseif ( '' !== $metadata ) : ?>
					<p class="font-body text-xs leading-4 text-muted"><?php echo esc_html( $metadata ); ?></p>
				<?php endif; ?>
				<h3 class="<?php echo $date || '' !== $metadata || $external ? 'mt-2 ' : 'm-0 '; ?>font-body text-base font-bold leading-[22px] text-foreground">
					<?php if ( $linked ) : ?>
						<a href="<?php echo $url; ?>"<?php if ( $target ) : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?> class="text-inherit no-underline hover:text-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"><?php echo esc_html( $title ); ?></a>
					<?php else : ?>
						<?php echo esc_html( $title ); ?>
					<?php endif; ?>
				</h3>
				<?php if ( '' !== $excerpt ) : ?>
					<p class="mt-2 font-body text-sm leading-[22.75px] text-[#6B6B7B]"><?php echo esc_html( $excerpt ); ?></p>
				<?php endif; ?>

				<?php if ( '' !== $url ) : ?>
					<div class="mt-auto pt-4">
						<span class="mb-4 flex items-center gap-1.5 text-secondary/60" aria-hidden="true">
							<span class="h-px w-5 bg-current"></span><span class="size-1.5 rotate-45 bg-current"></span><span class="h-px w-5 bg-current"></span>
						</span>
						<?php if ( $linked ) : ?>
							<a href="<?php echo $url; ?>"<?php if ( $target ) : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?> aria-label="<?php echo esc_attr( $label . ': ' . $title ); ?>" class="inline-flex items-center gap-1.5 font-body text-[11px] font-bold leading-[16.5px] tracking-[0.14em] text-foreground uppercase no-underline hover:text-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
						<?php else : ?>
							<span class="inline-flex items-center gap-1.5 font-body text-[11px] font-bold leading-[16.5px] tracking-[0.14em] text-foreground uppercase">
						<?php endif; ?>
							<?php echo esc_html( $label ); ?>
							<svg width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
								<path d="M2.2917 5.5H8.7083M5.5 2.2917L8.7083 5.5L5.5 8.7083" stroke="currentColor" stroke-width="0.91667"/>
							</svg>
						<?php if ( $linked ) : ?></a><?php else : ?></span><?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</article>
	<?php endforeach; ?>
</div>
