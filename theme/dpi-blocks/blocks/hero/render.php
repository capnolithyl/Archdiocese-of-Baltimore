<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section data-dpi-hero <?php echo get_block_wrapper_attributes( array( 'class' => 'relative left-1/2 w-screen max-w-none -translate-x-1/2 overflow-hidden' ) ); ?>>
	<?php
	if ( get_field( 'media_type' ) ) {
		require __DIR__ . '/templates/video.php';
	} else {
		require __DIR__ . '/templates/slider.php';
	}
	?>
</section>
