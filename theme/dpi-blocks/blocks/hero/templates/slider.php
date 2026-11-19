<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$slides = get_field( 'slides' ) ?: array();

if ( ! $slides ) {
	if ( ! empty( $is_preview ) ) {
		echo '<p>' . esc_html__( 'Add at least one hero slide.', 'baltimore' ) . '</p>';
	}
	return;
}

$slick = array(
	'arrows'        => true,
	'dots'          => true,
	'autoplay'      => true,
	'autoplaySpeed' => 6000,
	'pauseOnHover'  => true,
	'pauseOnFocus'  => true,
);
?>
<div<?php if ( count( $slides ) > 1 ) : ?> data-dpi-slick="<?php echo esc_attr( wp_json_encode( $slick ) ); ?>"<?php endif; ?>>
	<?php foreach ( $slides as $index => $slide ) : ?>
		<?php
		$image_id    = $slide['image']['ID'] ?? 0;
		$heading     = trim( (string) ( $slide['heading'] ?? '' ) );
		$subheading  = $slide['subheadings'][0]['subheading'] ?? '';
		$buttons     = array_slice( $slide['buttons'] ?? array(), 0, 2 );
		$heading_tag = is_front_page() && 0 === $index ? 'h1' : 'h2';
		?>
		<article>
			<div>
				<?php if ( $image_id ) : ?>
					<?php echo wp_get_attachment_image( $image_id, 'full', false, array( 'alt' => '' ) ); ?>
				<?php elseif ( ! empty( $is_preview ) ) : ?>
					<p><?php esc_html_e( 'Choose an image for this slide.', 'baltimore' ); ?></p>
				<?php endif; ?>
				<span aria-hidden="true"></span>
			</div>

			<div>
				<?php if ( $heading ) : ?>
					<<?php echo $heading_tag; ?>><?php echo esc_html( $heading ); ?></<?php echo $heading_tag; ?>>
				<?php endif; ?>

				<?php if ( $subheading ) : ?>
					<p><?php echo esc_html( $subheading ); ?></p>
				<?php endif; ?>

				<?php if ( $buttons ) : ?>
					<div>
						<?php foreach ( $buttons as $row ) : ?>
							<?php
							$link = $row['link'] ?? null;
							if ( ! empty( $link['url'] ) ) {
								echo wp_kses_post( dpi_blocks_render_link( $link, '', ! empty( $is_preview ) ) );
							}
							?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</article>
	<?php endforeach; ?>
</div>
