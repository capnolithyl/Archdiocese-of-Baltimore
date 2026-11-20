<?php
declare(strict_types=1);

define( 'ABSPATH', __DIR__ . '/' );
function add_filter( ...$args ): void {}

class WP_Block_Type_Registry {
	public static function get_instance(): self { return new self(); }
	public function get_all_registered(): array {
		return array_fill_keys( array( 'core/rss', 'core/paragraph', 'core/image', 'balt/rss-news' ), true );
	}
}

require_once dirname( __DIR__ ) . '/theme/inc/editor-blocks.php';

function check( $actual, $expected, string $case ): void {
	if ( $actual !== $expected ) {
		throw new RuntimeException( $case . ' failed: ' . var_export( $actual, true ) );
	}
}

check( balt_hide_core_rss_block( true ), array( 'core/paragraph', 'core/image', 'balt/rss-news' ), 'Default allowed set' );
check( balt_hide_core_rss_block( array( 'core/rss', 'core/image', 'balt/rss-news' ) ), array( 'core/image', 'balt/rss-news' ), 'Existing restrictions' );
check( balt_hide_core_rss_block( false ), false, 'All blocks forbidden' );
echo "Built-in RSS block hidden; custom RSS News retained\n";
