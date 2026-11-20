<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Register the reusable RSS News ACF block. */
function balt_register_rss_news_block(): void {
	register_block_type( get_template_directory() . '/blocks/rss-news' );
}
add_action( 'init', 'balt_register_rss_news_block', 5 );

function balt_render_rss_news_block( $block, $content = '', $is_preview = false, $post_id = 0, $wp_block = null, $context = null ): void {
	require get_template_directory() . '/blocks/rss-news/render.php';
}

/** WordPress/SimplePie caches fetched feed data for 30 minutes. */
function balt_rss_news_cache_lifetime( $lifetime ): int {
	return 30 * MINUTE_IN_SECONDS;
}

/** Pick the first safe image URL from the RSS item, including WP featured-image HTML. */
function balt_rss_news_image( $item ): string {
	$enclosure = $item->get_enclosure();
	if ( $enclosure && str_starts_with( (string) $enclosure->get_type(), 'image/' ) ) {
		$url = wp_http_validate_url( (string) $enclosure->get_link() );
		if ( $url ) {
			return esc_url_raw( $url );
		}
	}

	foreach ( array( $item->get_description(), $item->get_content() ) as $html ) {
		if ( ! is_string( $html ) || '' === $html ) {
			continue;
		}

		$processor = new WP_HTML_Tag_Processor( $html );
		while ( $processor->next_tag( 'img' ) ) {
			$url = $processor->get_attribute( 'src' );
			if ( is_string( $url ) && $url ) {
				$safe_url = wp_http_validate_url( $url );
				if ( $safe_url ) {
					return esc_url_raw( $safe_url );
				}
			}
		}
	}

	return '';
}

/**
 * Fetch feeds separately so one invalid/unavailable source doesn't hide the others.
 * Each source uses WordPress's feed cache; only six items per source are needed.
 */
function balt_rss_news_articles( array $urls ): array {
	if ( ! function_exists( 'fetch_feed' ) ) {
		require_once ABSPATH . WPINC . '/feed.php';
	}

	$articles = array();
	$seen     = array();
	$home_url = '';

	add_filter( 'wp_feed_cache_transient_lifetime', 'balt_rss_news_cache_lifetime' );

	foreach ( array_slice( array_unique( $urls ), 0, 6 ) as $url ) {
		$url = is_string( $url ) ? wp_http_validate_url( trim( $url ) ) : false;
		if ( ! $url ) {
			continue;
		}

		$feed = fetch_feed( $url );
		if ( is_wp_error( $feed ) ) {
			continue;
		}

		if ( ! $home_url ) {
			$home_url = esc_url_raw( (string) $feed->get_link() );
		}

		foreach ( $feed->get_items( 0, 6 ) as $item ) {
			$link = esc_url_raw( (string) $item->get_permalink() );
			$key  = strtolower( untrailingslashit( $link ) );
			if ( ! $link || isset( $seen[ $key ] ) ) {
				continue;
			}

			$seen[ $key ] = true;
			$articles[] = array(
				'title'   => wp_strip_all_tags( (string) $item->get_title() ),
				'link'    => $link,
				'date'    => (int) $item->get_date( 'U' ),
				'excerpt' => wp_trim_words( wp_strip_all_tags( (string) $item->get_description() ), 24, '…' ),
				'image'   => balt_rss_news_image( $item ),
			);
		}
	}

	remove_filter( 'wp_feed_cache_transient_lifetime', 'balt_rss_news_cache_lifetime' );

	usort( $articles, static fn( $a, $b ) => $b['date'] <=> $a['date'] );

	return array(
		'articles' => array_slice( $articles, 0, 6 ),
		'home_url' => $home_url,
	);
}
