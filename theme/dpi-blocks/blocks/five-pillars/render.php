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
			get_block_wrapper_attributes(),
			esc_html__( 'Add at least one pillar.', 'dpi-blocks' )
		);
	}
	return;
}

$has_images         = (bool) array_filter( array_column( $items, 'images' ) );
$instance_id        = wp_unique_id( 'dpi-five-pillars-' );
$heading_id         = $heading ? $instance_id . '-heading' : '';
$wrapper_attributes = get_block_wrapper_attributes();
?>
<section <?php echo $wrapper_attributes;?><?php echo $heading_id ? ' aria-labelledby="' . esc_attr( $heading_id ) . '"' : ''; ?> data-dpi-five-pillars>
	<?php if ( $eyebrow || $heading ) : ?>
		<header>
			<?php if ( $eyebrow ) : ?><p><?php echo esc_html( $eyebrow ); ?></p><?php endif; ?>
			<?php if ( $heading ) : ?><h2 id="<?php echo esc_attr( $heading_id ); ?>"><?php echo esc_html( $heading ); ?></h2><?php endif; ?>
		</header>
	<?php endif; ?>

	<?php if ( $items ) : ?>
		<div>
			<?php if ( $has_images ) : ?>
			<div>
				<?php foreach ( $items as $index => $item ) : ?>
					<div <?php if ( 0 !== $index ) : ?>hidden<?php endif; ?> data-dpi-pillar-media data-dpi-pillar-index="<?php echo esc_attr( (string) $index ); ?>">
						<?php foreach ( $item['images'] as $image_index => $image_id ) : ?>
							<figure>
								<?php echo wp_get_attachment_image( $image_id, 'large');?>
							</figure>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

			<div>
				<div aria-hidden="true">
					<span><?php echo esc_html( (string) count( $items ) ); ?></span>
					<?php if ( $counter_label ) : ?><span><?php echo esc_html( $counter_label ); ?></span><?php endif; ?>
				</div>

				<div role="list">
					<?php foreach ( $items as $index => $item ) : ?>
						<?php
						$trigger_id = $instance_id . '-trigger-' . $index;
						$panel_id   = $instance_id . '-panel-' . $index;
						$link       = $item['link'];
						?>
						<div role="listitem" data-dpi-pillar-item>
							<h3>
								<button id="<?php echo esc_attr( $trigger_id ); ?>" type="button" aria-expanded="true" aria-controls="<?php echo esc_attr( $panel_id ); ?>" data-dpi-pillar-trigger data-dpi-pillar-index="<?php echo esc_attr( (string) $index ); ?>">
									<?php echo esc_html( $item['name'] ); ?>
								</button>
							</h3>
							<div id="<?php echo esc_attr( $panel_id ); ?>" role="region" aria-labelledby="<?php echo esc_attr( $trigger_id ); ?>" data-dpi-pillar-panel data-dpi-pillar-index="<?php echo esc_attr( (string) $index ); ?>" <?php if ( 0 !== $index ) : ?>hidden<?php endif; ?>>
								<?php if ( $item['description'] ) : ?><p><?php echo esc_html( $item['description'] ); ?></p><?php endif; ?>
								<?php if ( ! empty( $link['url'] ) && ! empty( $link['title'] ) ) : ?>
									<?php
									$target = ! empty( $link['target'] ) ? (string) $link['target'] : '_self';
									$rel    = '_blank' === $target ? 'noopener noreferrer' : '';
									?>
									<?php echo dpi_blocks_link_open( $link, '', ! empty( $is_preview ) );?><?php echo esc_html( $link['title'] ); ?><?php echo dpi_blocks_link_close( ! empty( $is_preview ) );?>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</section>
