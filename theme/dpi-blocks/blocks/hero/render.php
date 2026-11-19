<?php

if (! defined('ABSPATH')) {
	exit;
}
?>
<section data-dpi-hero
	<?php echo get_block_wrapper_attributes(); ?>>
	<?php
	if (get_field('media_type')) {
		require __DIR__ . '/templates/video.php';
	} else {
		require __DIR__ . '/templates/slider.php';
	}
	?>
</section>