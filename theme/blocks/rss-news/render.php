<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow    = trim( (string) get_field( 'eyebrow' ) );
$heading    = trim( (string) get_field( 'heading' ) );
$subheading = trim( (string) get_field( 'subheading' ) );
$feeds      = get_field( 'feeds' ) ?: array();
$feed_urls  = array();

foreach ( array_slice( (array) $feeds, 0, 6 ) as $row ) {
	if ( is_array( $row ) && ! empty( $row['url'] ) ) {
		$feed_urls[] = (string) $row['url'];
	}
}
if ( ! $feed_urls ) {
	$feed_urls = array( 'https://catholicreview.org/category/local-news/feed/' );
}

$news       = balt_rss_news_articles( $feed_urls );
$articles   = $news['articles'];
$view_all   = get_field( 'view_all_link' );
$button_url = is_array( $view_all ) && ! empty( $view_all['url'] ) ? $view_all['url'] : $news['home_url'];
$button_text = is_array( $view_all ) && ! empty( $view_all['title'] ) ? $view_all['title'] : __( 'View All Archdiocese News', 'baltimore' );
$heading_id = wp_unique_id( 'balt-rss-news-' );
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'not-prose balt-full-bleed relative mt-0! w-full max-w-none bg-white py-section text-foreground' ) ); ?><?php if ( $heading ) : ?> aria-labelledby="<?php echo esc_attr( $heading_id ); ?>"<?php endif; ?>>
	<div class="mx-auto w-full max-w-wide px-gutter">
		<header class="text-center">
			<?php if ( $eyebrow ) : ?>
				<p class="font-body text-lg font-bold leading-[27px] tracking-[0.19em] text-tertiary uppercase"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>
			<?php if ( $heading ) : ?>
				<h2 id="<?php echo esc_attr( $heading_id ); ?>" class="mt-4 font-heading text-[clamp(2.75rem,5vw,4.8rem)] font-black leading-tight text-foreground text-balance"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
			<?php if ( $subheading ) : ?>
				<p class="mx-auto mt-3 max-w-[520px] font-body text-base leading-[26px] text-[#6B6B7B]"><?php echo esc_html( $subheading ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( $articles ) : ?>
			<?php get_template_part( 'template-parts/components/content-card-grid', null, array( 'cards' => $articles, 'external' => true ) ); ?>
		<?php elseif ( ! empty( $is_preview ) ) : ?>
			<p class="mt-10 text-center text-body"><?php esc_html_e( 'No RSS articles are available. Check the feed URLs.', 'baltimore' ); ?></p>
		<?php endif; ?>

		<?php if ( $button_url ) : ?>
			<div class="mt-12 text-center">
				<a href="<?php echo esc_url( $button_url ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
					<?php echo esc_html( $button_text ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>
</section>
