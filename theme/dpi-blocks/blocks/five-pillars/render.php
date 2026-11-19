<?php
/**
 * Pillars block renderer.
 *
 * @package DPI_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow       = function_exists( 'get_field' ) ? trim( (string) get_field( 'eyebrow' ) ) : '';
$heading       = function_exists( 'get_field' ) ? trim( (string) get_field( 'heading' ) ) : '';
$counter_label = function_exists( 'get_field' ) ? trim( (string) get_field( 'counter_label' ) ) : '';
$pillars       = function_exists( 'get_field' ) ? get_field( 'pillars' ) : array();
$pillars       = is_array( $pillars ) ? $pillars : array();
$items         = array();

foreach ( $pillars as $pillar ) {
	if ( ! is_array( $pillar ) ) {
		continue;
	}

	$name        = trim( (string) ( $pillar['name'] ?? '' ) );
	$description = trim( (string) ( $pillar['description'] ?? '' ) );
	$link        = isset( $pillar['link'] ) && is_array( $pillar['link'] ) ? $pillar['link'] : array();
	$images      = isset( $pillar['images'] ) && is_array( $pillar['images'] ) ? $pillar['images'] : array();
	$image_ids   = array();

	foreach ( $images as $image ) {
		$image_id = is_array( $image ) ? absint( $image['ID'] ?? $image['id'] ?? 0 ) : absint( $image );
		if ( $image_id ) {
			$image_ids[] = $image_id;
		}
	}
	$image_ids = array_slice( array_values( array_unique( $image_ids ) ), 0, 3 );

	if ( $name || $description || ! empty( $link['url'] ) || $image_ids ) {
		$items[] = array(
			'name'        => $name,
			'description' => $description,
			'link'        => $link,
			'images'      => $image_ids,
		);
	}
}

if ( ! $eyebrow && ! $heading && ! $items ) {
	if ( ! empty( $is_preview ) ) {
		printf(
			'<div %1$s><p>%2$s</p></div>',
			get_block_wrapper_attributes( array( 'class' => 'dpi-block dpi-five-pillars dpi-block--placeholder border border-dashed border-current p-6 text-center' ) ),
			esc_html__( 'Add at least one pillar.', 'dpi-blocks' )
		);
	}
	return;
}

$has_images         = (bool) array_filter( array_column( $items, 'images' ) );
$instance_id        = wp_unique_id( 'dpi-five-pillars-' );
$heading_id         = $heading ? $instance_id . '-heading' : '';
$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => 'dpi-block dpi-five-pillars py-12! md:py-20!' ) );
$image_positions = array( 'top-0 bottom-[6%] left-0 w-[58%]', 'top-[6%] right-0 h-[40%] w-[34%]', 'right-0 bottom-0 h-[45%] w-[52%]' );
?>
<section <?php echo $wrapper_attributes;?><?php echo $heading_id ? ' aria-labelledby="' . esc_attr( $heading_id ) . '"' : ''; ?> data-dpi-five-pillars>
	<?php if ( $eyebrow || $heading ) : ?>
		<header class="dpi-five-pillars__header mb-8 grid gap-3">
			<?php if ( $eyebrow ) : ?><p class="dpi-five-pillars__eyebrow eyebrow"><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
			<?php if ( $heading ) : ?><h2 id="<?php echo esc_attr( $heading_id ); ?>" class="dpi-five-pillars__heading font-heading text-[clamp(1.8rem,3vw,2.5rem)] font-semibold"><?php echo esc_html( $heading ); ?></h2><?php endif; ?>
		</header>
	<?php endif; ?>

	<?php if ( $items ) : ?>
		<div class="dpi-five-pillars__layout grid items-center gap-6 <?php echo $has_images ? 'md:grid-cols-[minmax(0,1.1fr)_minmax(0,0.9fr)]' : ''; ?>">
			<?php if ( $has_images ) : ?>
			<div class="dpi-five-pillars__visuals relative min-h-[clamp(24rem,45vw,42rem)]">
				<?php foreach ( $items as $index => $item ) : ?>
					<div class="dpi-five-pillars__media absolute inset-0 [&[hidden]]:hidden!" <?php if ( 0 !== $index ) : ?>hidden<?php endif; ?> data-dpi-pillar-media data-dpi-pillar-index="<?php echo esc_attr( (string) $index ); ?>">
						<?php foreach ( $item['images'] as $image_index => $image_id ) : ?>
							<figure class="dpi-five-pillars__image absolute m-0 overflow-hidden rounded-xl <?php echo esc_attr( count( $item['images'] ) === 1 ? 'inset-0 size-full' : $image_positions[ $image_index ] ); ?>">
								<?php echo wp_get_attachment_image( $image_id, 'large', false, array( 'class' => 'size-full object-cover' ) );?>
							</figure>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

			<div class="dpi-five-pillars__content grid gap-4">
				<div class="dpi-five-pillars__count-wrap grid grid-cols-[auto_minmax(0,1fr)] items-end gap-4" aria-hidden="true">
					<span class="dpi-five-pillars__count font-heading text-[clamp(4rem,10vw,8rem)] leading-[0.8]"><?php echo esc_html( (string) count( $items ) ); ?></span>
					<?php if ( $counter_label ) : ?><span class="dpi-five-pillars__count-label"><?php echo esc_html( $counter_label ); ?></span><?php endif; ?>
				</div>

				<div class="dpi-five-pillars__items grid gap-4" role="list">
					<?php foreach ( $items as $index => $item ) : ?>
						<?php
						$trigger_id = $instance_id . '-trigger-' . $index;
						$panel_id   = $instance_id . '-panel-' . $index;
						$link       = $item['link'];
						?>
						<div class="dpi-five-pillars__item border-t border-current/25 last:border-b" role="listitem" data-dpi-pillar-item>
							<h3 class="dpi-five-pillars__item-heading m-0">
								<button id="<?php echo esc_attr( $trigger_id ); ?>" class="dpi-five-pillars__trigger block min-h-11 w-full cursor-pointer border-0 bg-transparent py-4 text-left font-semibold text-inherit hover:underline" type="button" aria-expanded="true" aria-controls="<?php echo esc_attr( $panel_id ); ?>" data-dpi-pillar-trigger data-dpi-pillar-index="<?php echo esc_attr( (string) $index ); ?>">
									<?php echo esc_html( $item['name'] ); ?>
								</button>
							</h3>
							<div id="<?php echo esc_attr( $panel_id ); ?>" class="dpi-five-pillars__panel pb-5 [&[hidden]]:hidden!" role="region" aria-labelledby="<?php echo esc_attr( $trigger_id ); ?>" data-dpi-pillar-panel data-dpi-pillar-index="<?php echo esc_attr( (string) $index ); ?>" <?php if ( 0 !== $index ) : ?>hidden<?php endif; ?>>
								<?php if ( $item['description'] ) : ?><p class="dpi-five-pillars__description"><?php echo esc_html( $item['description'] ); ?></p><?php endif; ?>
								<?php if ( ! empty( $link['url'] ) && ! empty( $link['title'] ) ) : ?>
									<?php
									$target = ! empty( $link['target'] ) ? (string) $link['target'] : '_self';
									$rel    = '_blank' === $target ? 'noopener noreferrer' : '';
									?>
									<?php echo dpi_blocks_link_open( $link, 'dpi-five-pillars__link mt-3 inline-flex items-center gap-2 font-semibold text-primary underline underline-offset-4', ! empty( $is_preview ) );?><?php echo esc_html( $link['title'] ); ?><?php echo dpi_blocks_link_close( ! empty( $is_preview ) );?>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</section>
