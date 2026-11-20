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
			<div class="mt-14 grid grid-cols-1 gap-px overflow-hidden rounded-card border border-[#E8E3D8] bg-[#E8E3D8] sm:grid-cols-2 lg:grid-cols-3">
				<?php foreach ( $articles as $article ) : ?>
					<article class="group relative flex min-w-0 flex-col bg-white transition-colors duration-300 ease-out hover:bg-surface focus-within:bg-surface before:pointer-events-none before:absolute before:inset-x-0 before:top-0 before:z-10 before:h-[3px] before:origin-center before:scale-x-0 before:bg-secondary before:transition-transform before:duration-300 before:ease-out hover:before:scale-x-100 focus-within:before:scale-x-100 motion-reduce:transition-none motion-reduce:before:transition-none">
						<?php if ( $article['image'] ) : ?>
							<a href="<?php echo esc_url( $article['link'] ); ?>" target="_blank" rel="noopener noreferrer" tabindex="-1" aria-hidden="true" class="block h-[200px] overflow-hidden bg-surface">
								<img src="<?php echo esc_url( $article['image'] ); ?>" alt="" loading="lazy" decoding="async" class="size-full object-cover transition-transform duration-300 ease-out group-hover:scale-105 group-focus-within:scale-105 motion-reduce:transition-none">
							</a>
						<?php else : ?>
							<div class="h-[200px] bg-surface" aria-hidden="true"></div>
						<?php endif; ?>

						<div class="flex flex-1 flex-col p-6">
							<?php if ( $article['date'] ) : ?>
								<time datetime="<?php echo esc_attr( gmdate( 'Y-m-d', $article['date'] ) ); ?>" class="font-body text-xs leading-4 text-[#99A1AF]"><?php echo esc_html( wp_date( 'F j, Y', $article['date'] ) ); ?></time>
							<?php endif; ?>
							<h3 class="mt-2 font-body text-base font-bold leading-[22px] text-foreground">
								<a href="<?php echo esc_url( $article['link'] ); ?>" target="_blank" rel="noopener noreferrer" class="text-inherit no-underline hover:text-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"><?php echo esc_html( $article['title'] ); ?></a>
							</h3>
							<?php if ( $article['excerpt'] ) : ?>
								<p class="mt-2 font-body text-sm leading-[22.75px] text-[#6B6B7B]"><?php echo esc_html( $article['excerpt'] ); ?></p>
							<?php endif; ?>

							<div class="mt-auto pt-4">
								<span class="mb-4 flex items-center gap-1.5 text-secondary/60" aria-hidden="true">
									<span class="h-px w-5 bg-current"></span><span class="size-1.5 rotate-45 bg-current"></span><span class="h-px w-5 bg-current"></span>
								</span>
								<a href="<?php echo esc_url( $article['link'] ); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 font-body text-[11px] font-bold leading-[16.5px] tracking-[0.14em] text-foreground uppercase no-underline hover:text-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
									<?php esc_html_e( 'Learn More', 'baltimore' ); ?>
									<svg width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
										<path d="M2.2917 5.5H8.7083M5.5 2.2917L8.7083 5.5L5.5 8.7083" stroke="currentColor" stroke-width="0.91667"/>
									</svg>
								</a>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
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
