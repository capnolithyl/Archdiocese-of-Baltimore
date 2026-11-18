<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function balt_ported_dpi_block_slugs(): array {
	return array( 'hero', 'five-pillars', 'feature-banner', 'community-slider', 'stats' );
}

function balt_register_ported_dpi_assets(): void {
	if ( defined( 'DPI_BLOCKS_VERSION' ) ) {
		return;
	}

	$root = get_template_directory_uri() . '/assets/ported-dpi';

	wp_register_style( 'balt-ported-dpi-layer', false, array(), BALT_VERSION );
	wp_add_inline_style( 'balt-ported-dpi-layer', '@layer dpi-blocks; @layer dpi-blocks.structure, dpi-blocks.defaults;' );
	wp_register_style( 'balt-ported-dpi-structure', $root . '/css/blocks.css', array( 'balt-ported-dpi-layer' ), BALT_VERSION );
	wp_register_style( 'balt-ported-dpi-defaults', $root . '/css/block-defaults.css', array( 'balt-ported-dpi-structure' ), BALT_VERSION );
	wp_register_style( 'balt-ported-dpi-slick', $root . '/vendor/slick/slick.css', array(), '1.8.1' );
	wp_register_script( 'balt-ported-dpi-slick', $root . '/vendor/slick/slick.min.js', array( 'jquery' ), '1.8.1', true );
	wp_register_script( 'balt-ported-dpi-interactions', $root . '/js/blocks.js', array( 'jquery', 'balt-ported-dpi-slick' ), BALT_VERSION, true );
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
	if ( defined( 'DPI_BLOCKS_VERSION' ) ) {
		return;
	}
	wp_enqueue_style( 'balt-ported-dpi-defaults' );
	wp_enqueue_style( 'balt-ported-dpi-slick' );
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

		$classes = trim( $class_name . ( $is_preview ? ' dpi-editor-link' : '' ) );
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

