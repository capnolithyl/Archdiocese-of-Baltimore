<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'dpi-block dpi-hero dpi-hero--overlay alignfull relative left-1/2 w-screen max-w-none -translate-x-1/2 overflow-hidden p-0! [.editor-styles-wrapper_&]:left-auto [.editor-styles-wrapper_&]:w-full [.editor-styles-wrapper_&]:translate-x-0' ) ); ?>>
	<?php
	if ( get_field( 'media_type' ) ) {
		require __DIR__ . '/templates/video.php';
	} else {
		require __DIR__ . '/templates/slider.php';
	}
	?>
</section>
