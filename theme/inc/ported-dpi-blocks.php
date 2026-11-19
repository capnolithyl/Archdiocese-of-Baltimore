<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function balt_ported_dpi_block_slugs(): array {
	return array( 'hero', 'five-pillars', 'feature-banner', 'community-slider', 'stats' );
}

function balt_register_ported_dpi_assets(): void {
	$root = get_template_directory_uri() . '/assets/ported-dpi';

	if ( ! defined( 'DPI_BLOCKS_VERSION' ) ) {
		wp_register_style( 'balt-ported-dpi-slick', $root . '/vendor/slick/slick.css', array(), '1.8.1' );
		wp_register_script( 'balt-ported-dpi-slick', $root . '/vendor/slick/slick.min.js', array( 'jquery' ), '1.8.1', true );
	}

	$slick_handle = defined( 'DPI_BLOCKS_VERSION' ) ? 'dpi-blocks-slick' : 'balt-ported-dpi-slick';
	wp_register_script( 'balt-ported-dpi-interactions', $root . '/js/blocks.js', array( 'jquery', $slick_handle ), BALT_VERSION, true );
	wp_localize_script(
		'balt-ported-dpi-interactions',
		'DPIBlocksConfig',
		array(
			'previousLabel' => __( 'Previous', 'baltimore' ),
			'nextLabel'     => __( 'Next', 'baltimore' ),
			'previousIcon'  => '<svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>',
			'nextIcon'      => '<svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>',
		)
	);
}
add_action( 'init', 'balt_register_ported_dpi_assets', 2 );

function balt_register_ported_dpi_category( array $categories ): array {
	foreach ( $categories as $category ) {
		if ( ( $category['slug'] ?? '' ) === 'baltimore' ) {
			return $categories;
		}
	}
	$categories[] = array(
		'slug'  => 'baltimore',
		'title' => __( 'Baltimore Blocks', 'baltimore' ),
	);
	return $categories;
}
add_filter( 'block_categories_all', 'balt_register_ported_dpi_category', 20 );

function balt_register_ported_dpi_blocks(): void {
	if ( defined( 'DPI_BLOCKS_VERSION' ) ) {
		return;
	}

	foreach ( balt_ported_dpi_block_slugs() as $slug ) {
		$name = 'dpi/' . $slug;
		if ( WP_Block_Type_Registry::get_instance()->is_registered( $name ) ) {
			continue;
		}
		register_block_type( get_template_directory() . '/dpi-blocks/blocks/' . $slug );
	}
}
add_action( 'init', 'balt_register_ported_dpi_blocks', 4 );

function balt_render_ported_dpi_block( $block, $content = '', $is_preview = false, $post_id = 0, $wp_block = null, $context = null ): void {
	$name = is_array( $block ) ? ( $block['name'] ?? '' ) : '';
	$slug = is_string( $name ) && str_starts_with( $name, 'dpi/' ) ? substr( $name, 4 ) : '';

	if ( ! in_array( $slug, balt_ported_dpi_block_slugs(), true ) ) {
		return;
	}

	$template = get_template_directory() . '/dpi-blocks/blocks/' . $slug . '/render.php';
	if ( is_readable( $template ) ) {
		require $template;
	}
}

function balt_ported_dpi_editor_assets(): void {
	wp_enqueue_style( defined( 'DPI_BLOCKS_VERSION' ) ? 'dpi-blocks-slick' : 'balt-ported-dpi-slick' );
	wp_enqueue_script( 'balt-ported-dpi-interactions' );
}
add_action( 'enqueue_block_editor_assets', 'balt_ported_dpi_editor_assets' );

if ( ! function_exists( 'dpi_blocks_link_open' ) ) {
	/**
	 * Return an opening tag for a link-like element.
	 *
	 * In ACF block previews, real anchors are replaced with inert spans so
	 * Gutenberg cannot open the link when an editor clicks the preview.
	 *
	 * @param mixed               $link       ACF link array or URL.
	 * @param string              $class_name CSS class list.
	 * @param bool                $is_preview Whether this is an editor preview.
	 * @param array<string,string> $attrs      Additional safe attributes.
	 */
	function dpi_blocks_link_open( mixed $link, string $class_name = '', bool $is_preview = false, array $attrs = array() ): string {
		if ( is_string( $link ) ) {
			$link = array(
				'url'    => $link,
				'title'  => '',
				'target' => '',
			);
		}

		if ( ! is_array( $link ) || empty( $link['url'] ) ) {
			return '';
		}

		$url = esc_url( (string) $link['url'] );
		if ( '' === $url ) {
			return '';
		}

		$classes = trim( $class_name );
		$output  = '<' . ( $is_preview ? 'span' : 'a' );

		if ( '' !== $classes ) {
			$output .= ' class="' . esc_attr( $classes ) . '"';
		}

		if ( $is_preview ) {
			$output .= ' data-dpi-editor-link data-dpi-href="' . esc_attr( $url ) . '"';
		} else {
			$target = isset( $link['target'] ) && '_blank' === $link['target'] ? '_blank' : '';
			$output .= ' href="' . $url . '"';

			if ( $target ) {
				$output .= ' target="_blank" rel="noopener noreferrer"';
			}
		}

		foreach ( $attrs as $name => $value ) {
			$name = sanitize_key( (string) $name );
			if ( '' === $name || '' === (string) $value ) {
				continue;
			}
			$output .= ' ' . esc_attr( $name ) . '="' . esc_attr( (string) $value ) . '"';
		}

		return $output . '>';
	}
}

if ( ! function_exists( 'dpi_blocks_link_close' ) ) {
	/** Return the matching closing tag for dpi_blocks_link_open(). */
	function dpi_blocks_link_close( bool $is_preview = false ): string {
		return $is_preview ? '</span>' : '</a>';
	}
}

if ( ! function_exists( 'dpi_blocks_render_link' ) ) {
	/**
	 * Render an ACF link value as an escaped anchor.
	 *
	 * @param mixed  $link  ACF link array or URL.
	 * @param string $class_name CSS class list.
	 */
	function dpi_blocks_render_link( mixed $link, string $class_name = '', bool $is_preview = false ): string {
		if ( is_string( $link ) ) {
			$link = array(
				'url'    => $link,
				'title'  => $link,
				'target' => '',
			);
		}

		if ( ! is_array( $link ) || empty( $link['url'] ) ) {
			return '';
		}

		$url = esc_url( (string) $link['url'] );
		if ( '' === $url ) {
			return '';
		}

		$title  = isset( $link['title'] ) ? (string) $link['title'] : $url;
		$target = isset( $link['target'] ) && '_blank' === $link['target'] ? '_blank' : '';
		$rel    = '_blank' === $target ? ' rel="noopener noreferrer"' : '';

		if ( function_exists( 'dpi_blocks_link_open' ) && function_exists( 'dpi_blocks_link_close' ) ) {
			return dpi_blocks_link_open( $link, $class_name, $is_preview ) . esc_html( $title ) . dpi_blocks_link_close( $is_preview );
		}

		return sprintf(
			'<a class="%1$s" href="%2$s"%3$s%4$s>%5$s</a>',
			esc_attr( $class_name ),
			$url,
			$target ? ' target="_blank"' : '',
			$rel,
			esc_html( $title )
		);
	}
}

function balt_hero_acf_field_group( array $group ): array {
	$path = get_template_directory() . '/acf-json/group_dpi_hero.json';
	$fields = wp_json_file_decode( $path, array( 'associative' => true ) );

	return is_array( $fields ) ? $fields : $group;
}
add_filter( 'dpi_blocks/acf_field_group/key=group_dpi_hero', 'balt_hero_acf_field_group' );

/** Keep Feature Banner fields aligned with the simplified theme-owned slider. */
function balt_feature_banner_acf_field_group( array $group ): array {
	$path   = get_template_directory() . '/acf-json/group_dpi_feature_banner.json';
	$fields = wp_json_file_decode( $path, array( 'associative' => true ) );

	return is_array( $fields ) ? $fields : $group;
}
add_filter( 'dpi_blocks/acf_field_group/key=group_dpi_feature_banner', 'balt_feature_banner_acf_field_group' );


function balt_hero_block_metadata( array $metadata ): array {
	$metadata['supports'] = array(
		'anchor' => true,
		'align'  => array( 'full' ),
		'html'   => false,
	);
	$metadata['attributes']['align'] = array(
		'type'    => 'string',
		'default' => 'full',
	);

	return $metadata;
}
add_filter( 'dpi_blocks/block_metadata/hero', 'balt_hero_block_metadata' );

/**
 * These five blocks use Tailwind from the Baltimore theme. Other DPI plugin
 * blocks continue to load their own styles, scripts and dependencies.
 */
function balt_ported_dpi_style_handles( array $args, string $block_name ): array {
	if ( ! in_array( $block_name, array_map( static fn( $slug ) => 'dpi/' . $slug, balt_ported_dpi_block_slugs() ), true ) ) {
		return $args;
	}

	$needs_slick = in_array( $block_name, array( 'dpi/hero', 'dpi/feature-banner', 'dpi/community-slider' ), true );
	$args['style_handles'] = $needs_slick
		? array( defined( 'DPI_BLOCKS_VERSION' ) ? 'dpi-blocks-slick' : 'balt-ported-dpi-slick' )
		: array();
	$args['editor_style_handles'] = array();

	return $args;
}
add_filter( 'register_block_type_args', 'balt_ported_dpi_style_handles', 100, 2 );

/** Theme-owned blocks do not need the old plugin CSS or its initializer. */
function balt_ported_dpi_asset_requirements( array $requirements, array $context ): array {
	$name = $context['block_name'] ?? '';
	if ( in_array( $name, array( 'dpi/hero', 'dpi/five-pillars', 'dpi/feature-banner', 'dpi/community-slider', 'dpi/stats' ), true ) ) {
		$requirements['styles'] = false;
		if ( in_array( $name, array( 'dpi/hero', 'dpi/feature-banner', 'dpi/community-slider' ), true ) ) {
			$requirements['script'] = false;
			$requirements['slick']  = false;
		}
	}
	return $requirements;
}
add_filter( 'dpi_blocks/block_asset_requirements', 'balt_ported_dpi_asset_requirements', 100, 2 );

/**
 * The plugin can still own block registration while this theme owns the
 * class-free markup and JavaScript selectors for these three carousels.
 */
function balt_enqueue_ported_dpi_interactions( string $html, array $block ): string {
	if (
		defined( 'DPI_BLOCKS_VERSION' )
		&& in_array( $block['blockName'] ?? '', array( 'dpi/hero', 'dpi/feature-banner', 'dpi/community-slider' ), true )
	) {
		wp_enqueue_script( 'balt-ported-dpi-interactions' );
	}

	return $html;
}
add_filter( 'render_block', 'balt_enqueue_ported_dpi_interactions', 30, 2 );

