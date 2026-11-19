<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$slides = get_field( 'slides' ) ?: array();

if ( ! $slides ) {
	if ( ! empty( $is_preview ) ) {
		echo '<p class="dpi-block--placeholder">' . esc_html__( 'Add at least one hero slide.', 'baltimore' ) . '</p>';
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
<div class="dpi-hero__slides"<?php if ( count( $slides ) > 1 ) : ?> data-dpi-slick="<?php echo esc_attr( wp_json_encode( $slick ) ); ?>"<?php endif; ?>>
	<?php foreach ( $slides as $index => $slide ) : ?>
		<?php
		$image_id    = $slide['image']['ID'] ?? 0;
		$heading     = trim( (string) ( $slide['heading'] ?? '' ) );
		$subheading  = $slide['subheadings'][0]['subheading'] ?? '';
		$buttons     = array_slice( $slide['buttons'] ?? array(), 0, 2 );
		$heading_tag = is_front_page() && 0 === $index ? 'h1' : 'h2';
		?>
		<article class="dpi-hero__slide">
			<div class="dpi-hero__media">
				<?php if ( $image_id ) : ?>
					<?php echo wp_get_attachment_image( $image_id, 'full', false, array( 'alt' => '' ) ); ?>
				<?php elseif ( ! empty( $is_preview ) ) : ?>
					<p class="dpi-block--placeholder"><?php esc_html_e( 'Choose an image for this slide.', 'baltimore' ); ?></p>
				<?php endif; ?>
				<span class="dpi-hero__overlay" aria-hidden="true"></span>
			</div>

			<div class="dpi-hero__content">
				<?php if ( $heading ) : ?>
					<<?php echo $heading_tag; ?> class="dpi-hero__title"><?php echo esc_html( $heading ); ?></<?php echo $heading_tag; ?>>
				<?php endif; ?>

				<?php if ( $subheading ) : ?>
					<p class="dpi-hero__subheading"><?php echo esc_html( $subheading ); ?></p>
				<?php endif; ?>

				<?php if ( $buttons ) : ?>
					<div class="dpi-hero__actions">
						<?php foreach ( $buttons as $row ) : ?>
							<?php
							$link = $row['link'] ?? null;
							if ( ! empty( $link['url'] ) ) {
								echo wp_kses_post( dpi_blocks_render_link( $link, 'dpi-button', ! empty( $is_preview ) ) );
							}
							?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</article>
	<?php endforeach; ?>
</div>
