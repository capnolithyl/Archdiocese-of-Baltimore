<?php
/**
 * Stats block renderer.
 *
 * @package DPI_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$stats  = function_exists( 'get_field' ) ? get_field( 'stats' ) : array();
$stats  = is_array( $stats ) ? $stats : array();
$items  = array();

foreach ( $stats as $stat ) {
	$value = is_array( $stat ) ? trim( (string) ( $stat['value'] ?? '' ) ) : '';
	$label = is_array( $stat ) ? trim( (string) ( $stat['label'] ?? '' ) ) : '';

	if ( $value || $label ) {
		$items[] = array(
			'value' => $value,
			'label' => $label,
		);
	}
}

if ( ! $items ) {
	if ( ! empty( $is_preview ) ) {
		printf(
			'<div %1$s><p>%2$s</p></div>',
			get_block_wrapper_attributes(),
			esc_html__( 'Add at least one statistic.', 'dpi-blocks' )
		);
	}

	return;
}


$wrapper_attributes = get_block_wrapper_attributes();
?>
<section <?php echo $wrapper_attributes;?>>
	<ul>
		<?php foreach ( $items as $item ) : ?>
			<li>
				<?php
				if ( $item['value'] ) :
					?>
					<strong><?php echo esc_html( $item['value'] ); ?></strong><?php endif; ?>
				<?php
				if ( $item['label'] ) :
					?>
					<span><?php echo esc_html( $item['label'] ); ?></span><?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
