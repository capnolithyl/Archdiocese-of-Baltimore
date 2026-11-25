<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow    = trim( (string) get_field( 'eyebrow' ) );
$heading    = trim( (string) get_field( 'heading' ) );
$subheading = trim( (string) get_field( 'subheading' ) );
$source     = get_field( 'source' ) ?: 'manual';
$show_date  = (bool) get_field( 'show_date' );
$cards = match ( $source ) {
	'selected' => balt_content_grid_post_cards( (array) get_field( 'selected_posts' ), $show_date ),
	'latest'   => balt_content_grid_latest_cards( (string) ( get_field( 'post_type' ) ?: 'post' ), (int) ( get_field( 'post_count' ) ?: 6 ), (int) get_field( 'category' ), $show_date ),
	default    => balt_content_grid_manual_cards( (array) get_field( 'cards' ) ),
};
if ( ! $cards && empty( $is_preview ) ) {
	return;
}
$view_all = get_field( 'view_all_link' );
$left_heading = 'left' === get_field( 'heading_alignment' );
$header_link = $left_heading && is_array( $view_all ) && ! empty( $view_all['url'] ) && '' !== esc_url( $view_all['url'] ) && '' !== trim( (string) ( $view_all['title'] ?? '' ) );
$heading_id = wp_unique_id( 'balt-content-card-grid-' );

// Directory changes only the section presentation; reuse the same cards and sources.
if ( 'directory' === get_field( 'style' ) ) {
	$preview = ! empty( $is_preview );
	$search_enabled = (bool) get_field( 'enable_card_search' );
	$placeholder = trim( (string) get_field( 'search_placeholder' ) ) ?: __( 'Search resources...', 'baltimore' );
	$search_id = $heading_id . '-search';
	if ( $search_enabled && $cards && ! $preview ) {
		wp_enqueue_script( 'balt-card-directory-filter', get_template_directory_uri() . '/assets/js/card-directory-filter.js', array(), BALT_VERSION, true );
	}
	?>
	<section <?php echo get_block_wrapper_attributes( array( 'class' => 'not-prose balt-full-bleed balt-card-directory relative mt-0! w-full max-w-none bg-white text-foreground' ) ); ?><?php if ( $heading || $eyebrow ) : ?> aria-labelledby="<?php echo esc_attr( $heading_id ); ?>"<?php endif; ?>>
		<div class="balt-card-directory__inner">
			<?php if ( $eyebrow || $heading || ( $search_enabled && $cards ) ) : ?>
				<header class="balt-card-directory__header">
					<div class="min-w-0">
						<?php if ( $eyebrow ) : ?>
							<p class="balt-card-directory__eyebrow"<?php if ( ! $heading ) : ?> id="<?php echo esc_attr( $heading_id ); ?>"<?php endif; ?>><?php echo esc_html( $eyebrow ); ?></p>
						<?php endif; ?>
						<?php if ( $heading ) : ?>
							<h2 id="<?php echo esc_attr( $heading_id ); ?>" class="font-heading text-[clamp(2rem,3vw,2.5rem)] font-extrabold leading-[1.25] text-foreground"><?php echo esc_html( $heading ); ?></h2>
						<?php endif; ?>
					</div>
					<?php if ( $search_enabled && $cards ) : ?>
						<div class="balt-card-directory__search" data-card-search<?php if ( ! $preview ) : ?> hidden<?php endif; ?>>
							<label class="sr-only" for="<?php echo esc_attr( $search_id ); ?>"><?php esc_html_e( 'Filter displayed cards', 'baltimore' ); ?></label>
							<span class="balt-card-directory__search-icon" aria-hidden="true"></span>
							<input type="search" id="<?php echo esc_attr( $search_id ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>" autocomplete="off"<?php if ( $preview ) : ?> disabled<?php endif; ?>>
						</div>
					<?php endif; ?>
				</header>
			<?php endif; ?>
			<?php if ( $subheading ) : ?>
				<p class="balt-card-directory__intro"><?php echo esc_html( $subheading ); ?></p>
			<?php endif; ?>
			<?php if ( $cards ) : ?>
				<?php get_template_part( 'template-parts/components/content-card-grid', null, array( 'cards' => $cards, 'accent' => true, 'preview' => $preview, 'compact_spacing' => true ) ); ?>
				<?php if ( $search_enabled && ! $preview ) : ?>
					<p class="balt-card-directory__empty" data-card-empty hidden><?php esc_html_e( 'No matching cards found.', 'baltimore' ); ?></p>
					<span class="sr-only" role="status" aria-live="polite" data-card-status data-count-template="<?php esc_attr_e( '%1$s of %2$s cards shown.', 'baltimore' ); ?>"></span>
				<?php endif; ?>
			<?php else : ?>
				<p class="mt-6 text-body"><?php esc_html_e( 'Add cards or select published content to display.', 'baltimore' ); ?></p>
			<?php endif; ?>
			<?php if ( is_array( $view_all ) && ! empty( $view_all['url'] ) ) : ?>
				<div class="mt-6 text-right">
					<?php get_template_part( 'template-parts/components/section-heading-link', null, array( 'link' => $view_all, 'preview' => $preview ) ); ?>
				</div>
			<?php endif; ?>
		</div>
	</section>
	<?php
	return;
}
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'not-prose balt-full-bleed relative mt-0! w-full max-w-none border-y border-border bg-white py-[clamp(3rem,5.62vw,6rem)] text-foreground' ) ); ?><?php if ( $heading ) : ?> aria-labelledby="<?php echo esc_attr( $heading_id ); ?>"<?php endif; ?>>
	<div class="mx-auto w-full max-w-wide px-gutter">
		<?php if ( $eyebrow || $heading || $subheading || $header_link ) : ?>
			<header class="<?php echo $left_heading ? 'flex flex-wrap items-end justify-between gap-4 text-left' : 'text-center'; ?>">
				<div class="min-w-0">
				<?php if ( $eyebrow ) : ?>
					<p class="eyebrow leading-5 tracking-[3px]"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>
				<?php if ( $heading ) : ?>
					<h2 id="<?php echo esc_attr( $heading_id ); ?>" class="<?php echo $left_heading ? 'mt-2 text-[clamp(2rem,3vw,2.2rem)] leading-[1.5]' : 'mt-3 text-[clamp(2rem,3vw,2.5rem)] leading-[1.25]'; ?> font-heading font-extrabold text-foreground text-balance"><?php echo esc_html( $heading ); ?></h2>
				<?php endif; ?>
				<?php if ( $subheading ) : ?>
					<p class="<?php echo $left_heading ? '' : 'mx-auto '; ?>mt-3 max-w-[520px] font-body text-base leading-[26px] text-muted"><?php echo esc_html( $subheading ); ?></p>
				<?php endif; ?>
				</div>
				<?php if ( $header_link ) : ?>
					<?php get_template_part( 'template-parts/components/section-heading-link', null, array( 'link' => $view_all, 'preview' => ! empty( $is_preview ) ) ); ?>
				<?php endif; ?>
			</header>
		<?php endif; ?>

		<?php if ( $cards ) : ?>
			<?php get_template_part( 'template-parts/components/content-card-grid', null, array( 'cards' => $cards, 'accent' => true, 'preview' => ! empty( $is_preview ), 'compact_spacing' => $left_heading ) ); ?>
		<?php else : ?>
			<p class="mt-10 text-center text-body"><?php esc_html_e( 'Add cards or select published content to display.', 'baltimore' ); ?></p>
		<?php endif; ?>

		<?php if ( ! $header_link && is_array( $view_all ) && ! empty( $view_all['url'] ) ) : ?>
			<div class="mt-12 text-center">
				<?php echo wp_kses_post( dpi_blocks_render_link( $view_all, 'btn btn-primary', ! empty( $is_preview ) ) ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
