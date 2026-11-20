<?php
/**
 * Lightweight runtime tests for RSS merging without a WordPress installation.
 * Live source/network behavior is verified separately in WordPress.
 */
declare(strict_types=1);

define( 'ABSPATH', __DIR__ . '/' );
define( 'WPINC', 'mock' );
define( 'MINUTE_IN_SECONDS', 60 );

function add_action( ...$args ): void {}
function add_filter( ...$args ): void {}
function remove_filter( ...$args ): void {}
function is_wp_error( $value ): bool { return $value instanceof WP_Error; }
function wp_http_validate_url( string $url ) { return str_starts_with( $url, 'https://' ) ? $url : false; }
function esc_url_raw( string $url ): string { return $url; }
function wp_strip_all_tags( string $value ): string { return strip_tags( $value ); }
function wp_trim_words( string $value, int $length, string $more ): string {
	$words = preg_split( '/\s+/', trim( $value ) );
	return implode( ' ', array_slice( $words, 0, $length ) ) . ( count( $words ) > $length ? $more : '' );
}
function untrailingslashit( string $value ): string { return rtrim( $value, '/' ); }

class WP_Error {}
class WP_HTML_Tag_Processor {
	private string $html;
	private string $tag = '';
	public function __construct( string $html ) { $this->html = $html; }
	public function next_tag( string $name ): bool {
		if ( preg_match( '/<img\s[^>]*>/i', $this->html, $match ) ) {
			$this->tag  = $match[0];
			$this->html = substr( $this->html, strpos( $this->html, $match[0] ) + strlen( $match[0] ) );
			return true;
		}
		return false;
	}
	public function get_attribute( string $name ): ?string {
		return preg_match( '/\bsrc="([^"]+)"/i', $this->tag, $match ) ? $match[1] : null;
	}
}
class DemoItem {
	public function __construct( public string $title, public string $url, public int $timestamp, public string $description ) {}
	public function get_title(): string { return $this->title; }
	public function get_permalink(): string { return $this->url; }
	public function get_date( string $format ): int { return $this->timestamp; }
	public function get_description(): string { return $this->description; }
	public function get_content(): string { return $this->description; }
	public function get_enclosure() { return null; }
}
class DemoFeed {
	public function __construct( public string $url, public array $items ) {}
	public function get_link(): string { return $this->url; }
	public function get_items( int $offset, int $count ): array { return array_slice( $this->items, $offset, $count ); }
}
function fetch_feed( string $url ) {
	global $mock_feeds;
	return $mock_feeds[ $url ] ?? new WP_Error();
}

require_once dirname( __DIR__ ) . '/theme/inc/rss-news.php';

$first_url  = 'https://example.org/first/feed/';
$second_url = 'https://example.org/second/feed/';
$story_a    = new DemoItem( 'Story A', 'https://example.org/story-a/', 300, '<p>A description</p>' );
$story_b    = new DemoItem( 'Story B', 'https://example.org/story-b/', 100, 'Another excerpt' );
$story_c    = new DemoItem( 'Story C', 'https://example.org/story-c/', 400, '<img src="https://example.org/images/c.jpg"><p>Latest article</p>' );
$story_d    = new DemoItem( 'Story D', 'https://example.org/story-d/', 200, 'Fourth article' );

$mock_feeds = array(
	$first_url  => new DemoFeed( 'https://example.org/first/', array( $story_a, $story_b ) ),
	$second_url => new DemoFeed( 'https://example.org/second/', array( $story_c, $story_a, $story_d ) ),
);

$result = balt_rss_news_articles( array( $first_url, $second_url, 'http://127.0.0.1/private/' ) );
$items  = $result['articles'];
$actual = array_column( $items, 'title' );

if ( $actual !== array( 'Story C', 'Story A', 'Story D', 'Story B' ) ) {
	throw new RuntimeException( 'Articles were not correctly sorted and deduplicated: ' . implode( ', ', $actual ) );
}
if ( 'https://example.org/first/' !== $result['home_url'] ) {
	throw new RuntimeException( 'Unexpected default source archive URL' );
}
if ( 'https://example.org/images/c.jpg' !== $items[0]['image'] ) {
	throw new RuntimeException( 'Failed to extract an image from RSS HTML' );
}
$missing = balt_rss_news_articles( array( 'https://example.org/missing/', $second_url ) );
if ( 3 !== count( $missing['articles'] ) ) {
	throw new RuntimeException( 'A failed source suppressed results from a working source' );
}
if ( 30 * MINUTE_IN_SECONDS !== balt_rss_news_cache_lifetime( 99999 ) ) {
	throw new RuntimeException( 'RSS cache lifetime should be 30 minutes' );
}

echo "RSS News smoke tests passed\n";
