<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'dpi-block dpi-hero dpi-hero--overlay alignfull' ) ); ?>>
	<?php
	if ( get_field( 'media_type' ) ) {
		require __DIR__ . '/templates/video.php';
	} else {
		require __DIR__ . '/templates/slider.php';
	}
	?>
</section>
