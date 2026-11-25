<?php
/**
 * Page-based Resource Directory. No separate post type or rewrite rules.
 *
 * @package Archdiocese_of_Baltimore
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function balt_register_resource_directory_block(): void {
	register_block_type( get_template_directory() . '/blocks/resource-directory' );
}
add_action( 'init', 'balt_register_resource_directory_block', 5 );

function balt_render_resource_directory_block( $block, $content = '', $is_preview = false, $post_id = 0, $wp_block = null, $context = null ): void {
	require get_template_directory() . '/blocks/resource-directory/render.php';
}

/** Do not disclose unpublished or password-protected page titles in the navigation. */
function balt_resource_directory_public_page( $page ): bool {
	return $page && 'page' === $page->post_type && 'publish' === $page->post_status && '' === $page->post_password;
}

function balt_resource_directory_categories( int $page_id, string $mode ): array {
	$root_id = 'category' === $mode ? (int) wp_get_post_parent_id( $page_id ) : $page_id;
	if ( ! $root_id || ! balt_resource_directory_public_page( get_post( $root_id ) ) ) {
		return array();
	}
	$pages = get_pages(
		array(
			'parent'       => $root_id,
			'post_status'  => 'publish',
			'sort_column'  => 'menu_order,post_title',
			'sort_order'   => 'ASC',
			'hierarchical' => false,
		)
	);
	$items = array();
	foreach ( (array) $pages as $page ) {
		if ( ! balt_resource_directory_public_page( $page ) ) {
			continue;
		}
		$url = get_permalink( $page );
		if ( ! $url || ! esc_url( $url ) ) {
			continue;
		}
		$items[] = array(
			'label'   => get_the_title( $page ),
			'url'     => $url,
			'current' => (int) $page->ID === $page_id,
		);
	}
	return $items;
}

function balt_resource_directory_sections( array $rows ): array {
	$sections = array();
	foreach ( $rows as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$heading = trim( (string) ( $row['heading'] ?? '' ) );
		if ( '' === $heading ) {
			continue;
		}
		$links = array();
		foreach ( (array) ( $row['links'] ?? array() ) as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$label  = trim( (string) ( $item['label'] ?? '' ) );
			$target = '';
			if ( 'file' === ( $item['type'] ?? 'link' ) ) {
				$id = absint( $item['file'] ?? 0 );
				$file = $id ? get_post( $id ) : null;
				if ( ! $file || 'attachment' !== $file->post_type || ! in_array( $file->post_status, array( 'inherit', 'publish' ), true ) || '' !== $file->post_password ) {
					continue;
				}
				// Do not list attachments of nonpublic parents.
				if ( $file->post_parent ) {
					$parent = get_post( $file->post_parent );
					if ( ! $parent || 'publish' !== $parent->post_status || '' !== $parent->post_password ) {
						continue;
					}
				}
				$url = wp_get_attachment_url( $id );
				$label = $label ?: get_the_title( $id );
			} else {
				$link = is_array( $item['link'] ?? null ) ? $item['link'] : array();
				$url = $link['url'] ?? '';
				$label = $label ?: trim( (string) ( $link['title'] ?? '' ) );
				$target = '_blank' === ( $link['target'] ?? '' ) ? '_blank' : '';
			}
			if ( ! $url || ! esc_url( $url ) || '' === trim( (string) $label ) ) {
				continue;
			}
			$links[] = array( 'label' => $label, 'url' => $url, 'target' => $target );
		}
		if ( $links ) {
			$sections[] = array(
				'heading' => $heading,
				'column'  => 'right' === ( $row['column'] ?? '' ) ? 'right' : 'left',
				'links'   => $links,
			);
		}
	}
	return $sections;
}
