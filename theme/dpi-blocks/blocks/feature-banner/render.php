<?php
/**
 * Feature / CTA Banner block renderer.
 *
 * @package DPI_Blocks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$dpi_get_field = static function ( $name, $fallback = null ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $fallback;
	}

	$value = get_field( $name );

	return null === $value || '' === $value ? $fallback : $value;
};

$dpi_get_bool = static function ( $name, $fallback ) use ( $dpi_get_field ) {
	$value = $dpi_get_field( $name, null );

	return null === $value ? (bool) $fallback : (bool) $value;
};

$dpi_normalize_buttons = static function ( $rows ): array {
	$buttons = array();

	foreach ( (array) $rows as $row ) {
		$link = is_array( $row ) && isset( $row['link'] ) ? $row['link'] : $row;

		if ( ! is_array( $link ) || empty( $link['url'] ) || empty( $link['title'] ) ) {
			continue;
		}

		$buttons[] = $link;

		if ( 2 === count( $buttons ) ) {
			break;
		}
	}

	return $buttons;
};

$dpi_normalize_slide = static function ( $slide ) use ( $dpi_normalize_buttons ): array {
	$slide = is_array( $slide ) ? $slide : array();

	$image        = $slide['image'] ?? 0;
	$image_id     = is_array( $image ) ? absint( $image['ID'] ?? $image['id'] ?? 0 ) : absint( $image );
	$image_layout = isset( $slide['image_layout'] ) ? (string) $slide['image_layout'] : 'background';
	$variant      = isset( $slide['visual_variant'] ) ? (string) $slide['visual_variant'] : 'dark';
	$headline_el  = isset( $slide['headline_element'] ) ? (string) $slide['headline_element'] : 'heading';

	return array(
		'eyebrow'          => trim( (string) ( $slide['eyebrow'] ?? '' ) ),
		'headline'         => trim( (string) ( $slide['headline'] ?? '' ) ),
		'headline_element' => in_array( $headline_el, array( 'heading', 'quote' ), true ) ? $headline_el : 'heading',
		'content'          => (string) ( $slide['content'] ?? '' ),
		'image_id'         => $image_id,
		'image_layout'     => in_array( $image_layout, array( 'background', 'left', 'right' ), true ) ? $image_layout : 'background',
		'visual_variant'   => in_array( $variant, array( 'dark', 'light' ), true ) ? $variant : 'dark',
		'buttons'          => $dpi_normalize_buttons( $slide['buttons'] ?? array() ),
	);
};

$dpi_has_content = static function ( array $slide ): bool {
	return (bool) (
		$slide['eyebrow']
		|| $slide['headline']
		|| trim( wp_strip_all_tags( $slide['content'] ) )
		|| $slide['image_id']
		|| $slide['buttons']
	);
};

$content_source = (string) $dpi_get_field( 'content_source', 'manual' );
$content_source = in_array( $content_source, array( 'manual', 'categories' ), true ) ? $content_source : 'manual';
$slides         = array();

if ( 'categories' === $content_source ) {
	$category_value = $dpi_get_field( 'categories', array() );
	$category_ids   = array_values(
		array_unique(
			array_filter(
				array_map(
					'absint',
					is_array( $category_value ) ? $category_value : array( $category_value )
				)
			)
		)
	);

	$posts_per_page = max( 1, min( 20, absint( $dpi_get_field( 'posts_per_page', 5 ) ) ) );
	$orderby        = (string) $dpi_get_field( 'orderby', 'date' );
	$orderby        = in_array( $orderby, array( 'date', 'title', 'menu_order', 'rand' ), true ) ? $orderby : 'date';
	$order          = strtoupper( (string) $dpi_get_field( 'order', 'DESC' ) );
	$order          = in_array( $order, array( 'ASC', 'DESC' ), true ) ? $order : 'DESC';
	$show_excerpt   = $dpi_get_bool( 'post_show_excerpt', true );
	$button_label   = trim( (string) $dpi_get_field( 'post_button_label', 'Read More' ) );
	$image_layout   = (string) $dpi_get_field( 'post_image_layout', 'background' );
	$image_layout   = in_array( $image_layout, array( 'background', 'left', 'right' ), true ) ? $image_layout : 'background';
	$visual_variant = (string) $dpi_get_field( 'post_visual_variant', 'dark' );
	$visual_variant = in_array( $visual_variant, array( 'dark', 'light' ), true ) ? $visual_variant : 'dark';

	if ( $category_ids ) {
		$source_posts = get_posts(
			array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => $posts_per_page,
				'category__in'        => $category_ids,
				'ignore_sticky_posts' => true,
				'orderby'             => $orderby,
				'order'               => $order,
				'no_found_rows'       => true,
			)
		);

		foreach ( $source_posts as $source_post ) {
			$buttons = array();

			if ( $button_label ) {
				$buttons[] = array(
					'url'    => get_permalink( $source_post ),
					'title'  => $button_label,
					'target' => '',
				);
			}

			$slide = $dpi_normalize_slide(
				array(
					'headline'         => get_the_title( $source_post ),
					'headline_element' => 'heading',
					'content'          => $show_excerpt ? get_the_excerpt( $source_post ) : '',
					'image'            => get_post_thumbnail_id( $source_post ),
					'image_layout'     => $image_layout,
					'visual_variant'   => $visual_variant,
					'buttons'          => array_map(
						static fn( $link ) => array( 'link' => $link ),
						$buttons
					),
				)
			);

			if ( $dpi_has_content( $slide ) ) {
				$slides[] = $slide;
			}
		}
	}
} else {
	$raw_slides = $dpi_get_field( 'slides', array() );

	foreach ( (array) $raw_slides as $raw_slide ) {
		$slide = $dpi_normalize_slide( $raw_slide );

		if ( $dpi_has_content( $slide ) ) {
			$slides[] = $slide;
		}
	}
}

/**
 * Backward compatibility for Feature Banner blocks saved before the slider
 * field model was introduced. Old blocks remain visible until an editor saves
 * them using the new Slides repeater.
 */
if ( 'manual' === $content_source && empty( $slides ) && ! empty( $block['data'] ) && is_array( $block['data'] ) ) {
	$legacy_data = $block['data'];
	$legacy_cta  = isset( $legacy_data['cta'] ) && is_array( $legacy_data['cta'] )
		? array( array( 'link' => $legacy_data['cta'] ) )
		: array();

	$legacy_slide = $dpi_normalize_slide(
		array(
			'eyebrow'          => $legacy_data['eyebrow'] ?? '',
			'headline'         => $legacy_data['headline'] ?? '',
			'headline_element' => $legacy_data['headline_element'] ?? 'heading',
			'content'          => $legacy_data['content'] ?? '',
			'image'            => $legacy_data['image'] ?? 0,
			'image_layout'     => $legacy_data['image_layout'] ?? 'background',
			'visual_variant'   => $legacy_data['visual_variant'] ?? 'dark',
			'buttons'          => $legacy_cta,
		)
	);

	if ( $dpi_has_content( $legacy_slide ) ) {
		$slides[] = $legacy_slide;
	}
}

if ( empty( $slides ) ) {
	if ( ! empty( $is_preview ) ) {
		printf(
			'<div %1$s><p>%2$s</p></div>',
			get_block_wrapper_attributes( array( 'class' => 'dpi-block dpi-feature-banner dpi-block--placeholder border border-dashed border-current p-6 text-center' ) ),
			esc_html__( 'Add at least one feature slide.', 'dpi-blocks' )
		);
	}

	return;
}

$slide_count = count( $slides );
$has_slider  = $slide_count > 1;
$slick       = array(
	'arrows'         => $dpi_get_bool( 'show_arrows', true ),
	'dots'           => $dpi_get_bool( 'show_dots', true ),
	'infinite'       => true,
	'autoplay'       => $dpi_get_bool( 'autoplay', false ),
	'autoplaySpeed'  => max( 1000, absint( $dpi_get_field( 'autoplay_speed', 6000 ) ) ),
	'speed'          => 500,
	'pauseOnHover'   => true,
	'pauseOnFocus'   => true,
	'slidesToShow'   => 1,
	'slidesToScroll' => 1,
	'rows'           => 0,
);

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'dpi-block dpi-feature-banner relative min-w-0 overflow-hidden p-0! ' . ( $has_slider ? 'dpi-feature-banner--slider' : 'dpi-feature-banner--single' ),
	)
);
?>
<section <?php echo $wrapper_attributes;?>>
	<div
		class="dpi-feature-banner__slides relative [&_.slick-track]:flex! [&_.slick-arrow]:absolute! [&_.slick-arrow]:top-1/2 [&_.slick-arrow]:z-20 [&_.slick-arrow]:grid! [&_.slick-arrow]:size-10 [&_.slick-arrow]:-translate-y-1/2 [&_.slick-arrow]:place-items-center [&_.slick-arrow]:rounded-full [&_.slick-arrow]:bg-white/90 [&_.slick-arrow]:text-primary [&_.slick-arrow]:shadow-md [&_.slick-arrow_svg]:size-5 [&_.slick-prev]:left-3 [&_.slick-next]:right-3 [&_.slick-dots]:flex! [&_.slick-dots]:justify-center [&_.slick-dots]:gap-2 [&_.slick-dots]:p-3 [&_.slick-dots]:list-none [&_.slick-dots_button]:size-3 [&_.slick-dots_button]:rounded-full [&_.slick-dots_button]:bg-current/40 [&_.slick-dots_button]:text-[0px] [&_.slick-active_button]:bg-current [&_.slick-slide]:h-auto [&_.slick-slide>div]:h-full [&_.slick-slide_.dpi-feature-banner__slide]:h-full"
		<?php if ( $has_slider ) : ?>
			data-dpi-slick="<?php echo esc_attr( wp_json_encode( $slick ) ); ?>"
		<?php endif; ?>
	>
		<?php foreach ( $slides as $index => $slide ) : ?>
			<?php
			$heading_id = $slide['headline'] && 'heading' === $slide['headline_element']
				? wp_unique_id( 'dpi-feature-banner-heading-' )
				: '';
			?>
			<article
				class="dpi-feature-banner__slide dpi-feature-banner__slide--<?php echo esc_attr( $slide['image_layout'] ); ?> dpi-feature-banner__slide--<?php echo esc_attr( $slide['visual_variant'] ); ?> relative isolate grid min-h-80 overflow-hidden <?php echo in_array( $slide['image_layout'], array( 'left', 'right' ), true ) ? 'md:grid-cols-2' : ''; ?> <?php echo 'dark' === $slide['visual_variant'] ? 'bg-foreground text-white' : 'bg-background text-foreground'; ?>"
				<?php echo $heading_id ? 'aria-labelledby="' . esc_attr( $heading_id ) . '"' : '';?>
			>
				<?php if ( $slide['image_id'] ) : ?>
					<figure class="dpi-feature-banner__media min-w-0 <?php echo 'background' === $slide['image_layout'] ? 'absolute inset-0 -z-20' : 'min-h-60'; ?> <?php echo 'right' === $slide['image_layout'] ? 'md:order-2' : ''; ?>">
						<?php echo wp_get_attachment_image( $slide['image_id'], 'full', false, array( 'class' => 'size-full object-cover' ) );?>
					</figure>

					<?php if ( 'background' === $slide['image_layout'] ) : ?>
						<div class="dpi-feature-banner__overlay absolute inset-0 -z-10 <?php echo 'dark' === $slide['visual_variant'] ? 'bg-black/70' : 'bg-white/90'; ?>" aria-hidden="true"></div>
					<?php endif; ?>
				<?php endif; ?>

				<div class="dpi-feature-banner__content relative z-10 grid min-w-0 content-center gap-4 p-[clamp(2rem,6vw,5rem)] <?php echo 'background' === $slide['image_layout'] ? 'max-w-[42rem]' : ''; ?>">
					<?php if ( $slide['eyebrow'] ) : ?>
						<p class="dpi-feature-banner__eyebrow text-sm font-bold uppercase tracking-[0.18em]"><?php echo esc_html( $slide['eyebrow'] ); ?></p>
					<?php endif; ?>

					<?php if ( $slide['headline'] ) : ?>
						<?php if ( 'quote' === $slide['headline_element'] ) : ?>
							<blockquote class="dpi-feature-banner__quote m-0 border-l-4 border-current/30 pl-4 font-heading text-[clamp(2rem,4vw,3.5rem)] leading-tight [&>p]:m-0">
								<p><?php echo esc_html( $slide['headline'] ); ?></p>
							</blockquote>
						<?php else : ?>
							<h2 id="<?php echo esc_attr( $heading_id ); ?>" class="dpi-feature-banner__heading font-heading text-[clamp(2rem,3vw,3.5rem)] font-semibold leading-tight text-inherit"><?php echo esc_html( $slide['headline'] ); ?></h2>
						<?php endif; ?>
					<?php endif; ?>

					<?php if ( trim( wp_strip_all_tags( $slide['content'] ) ) ) : ?>
						<div class="dpi-feature-banner__text [&>p:first-child]:mt-0 [&>p:last-child]:mb-0"><?php echo wp_kses_post( wpautop( $slide['content'] ) ); ?></div>
					<?php endif; ?>

					<?php if ( $slide['buttons'] ) : ?>
						<div class="dpi-feature-banner__actions flex flex-wrap items-center gap-4">
							<?php foreach ( $slide['buttons'] as $button ) : ?>
								<?php
								echo dpi_blocks_link_open( $button, 'dpi-feature-banner__cta inline-flex items-center justify-center rounded-full border border-current/30 px-6 py-3 font-semibold text-inherit no-underline transition hover:bg-primary hover:text-white', ! empty( $is_preview ) );
								echo esc_html( $button['title'] );
								echo dpi_blocks_link_close( ! empty( $is_preview ) );
								?>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
</section>
