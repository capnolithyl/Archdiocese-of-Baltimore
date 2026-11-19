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
$layout = function_exists( 'get_field' ) ? (string) get_field( 'layout' ) : 'row';
$layout = in_array( $layout, array( 'row', 'cards' ), true ) ? $layout : 'row';
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
			get_block_wrapper_attributes( array( 'class' => 'dpi-block dpi-stats dpi-block--placeholder border border-dashed border-current p-6 text-center' ) ),
			esc_html__( 'Add at least one statistic.', 'dpi-blocks' )
		);
	}

	return;
}

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'dpi-block dpi-stats dpi-stats--' . $layout . ' py-12!',
	)
);
?>
<section <?php echo $wrapper_attributes;?>>
	<ul class="dpi-stats__list grid grid-cols-[repeat(auto-fit,minmax(min(100%,10rem),1fr))] gap-6">
		<?php foreach ( $items as $item ) : ?>
			<li class="dpi-stats__item grid gap-2 text-center <?php echo esc_attr( 'cards' === $layout ? 'rounded-xl border border-current/20 bg-surface p-6' : '' ); ?>">
				<?php
				if ( $item['value'] ) :
					?>
					<strong class="dpi-stats__value font-heading text-[clamp(2rem,7vw,4rem)] font-semibold leading-none"><?php echo esc_html( $item['value'] ); ?></strong><?php endif; ?>
				<?php
				if ( $item['label'] ) :
					?>
					<span class="dpi-stats__label font-semibold"><?php echo esc_html( $item['label'] ); ?></span><?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
