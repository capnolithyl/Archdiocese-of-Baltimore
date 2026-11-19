<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow    = trim( (string) get_field( 'eyebrow' ) );
$heading    = trim( (string) get_field( 'heading' ) );
$types      = balt_media_types();
$instance   = wp_unique_id( 'balt-media-center-' );
$heading_id = $instance . '-heading';
?>
<section data-balt-media-center <?php echo get_block_wrapper_attributes( array( 'class' => 'not-prose relative left-1/2 mt-0! w-screen max-w-none -translate-x-1/2 bg-white py-section' ) ); ?><?php if ( $heading ) : ?> aria-labelledby="<?php echo esc_attr( $heading_id ); ?>"<?php endif; ?>>
	<div class="mx-auto w-full max-w-wide px-gutter">
		<header>
			<?php if ( $eyebrow ) : ?>
				<p class="font-body text-lg font-semibold leading-[27px] tracking-[0.16em] text-tertiary uppercase"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>
			<?php if ( $heading ) : ?>
				<h2 id="<?php echo esc_attr( $heading_id ); ?>" class="mt-3 font-heading text-[clamp(2.5rem,5vw,4.8rem)] font-black leading-tight text-foreground"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>
		</header>

		<div class="mt-8 flex overflow-x-auto border-b border-[#E5E7EB]" role="tablist" aria-label="<?php esc_attr_e( 'Media categories', 'baltimore' ); ?>">
			<?php foreach ( $types as $type => $data ) : ?>
				<?php $selected = $type === array_key_first( $types ); ?>
				<button
					type="button"
					id="<?php echo esc_attr( $instance . '-tab-' . $type ); ?>"
					role="tab"
					data-balt-media-tab
					aria-controls="<?php echo esc_attr( $instance . '-panel-' . $type ); ?>"
					aria-selected="<?php echo $selected ? 'true' : 'false'; ?>"
					tabindex="<?php echo $selected ? '0' : '-1'; ?>"
					class="shrink-0 cursor-pointer border-x-0 border-t-0 border-b-2 border-transparent bg-transparent px-5 py-3 font-body text-sm font-semibold tracking-[0.105em] text-[#99A1AF] uppercase transition-colors hover:text-dark aria-selected:border-primary aria-selected:text-dark focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-primary"
				>
					<?php echo esc_html( $data['plural'] ); ?>
				</button>
			<?php endforeach; ?>
		</div>

		<?php foreach ( $types as $type => $data ) : ?>
			<?php
			$selected = $type === array_key_first( $types );
			$posts    = get_posts(
				array(
					'post_type'        => $type,
					'post_status'      => 'publish',
					'numberposts'     => 3,
					'orderby'         => 'date',
					'order'           => 'DESC',
					'suppress_filters' => false,
				)
			);
			$archive_url = get_post_type_archive_link( $type );
			?>
			<div
				id="<?php echo esc_attr( $instance . '-panel-' . $type ); ?>"
				role="tabpanel"
				data-balt-media-panel
				aria-labelledby="<?php echo esc_attr( $instance . '-tab-' . $type ); ?>"
				tabindex="0"
				class="mt-10 [&[hidden]]:hidden!"
				<?php if ( ! $selected ) : ?>hidden<?php endif; ?>
			>
				<?php if ( $posts ) : ?>
					<div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
						<?php foreach ( $posts as $item ) : ?>
							<article>
								<a href="<?php echo esc_url( get_permalink( $item->ID ) ); ?>" class="group block text-inherit no-underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary">
									<div class="h-60 overflow-hidden rounded-control bg-surface sm:h-[290px] lg:h-[340px]">
										<?php if ( has_post_thumbnail( $item->ID ) ) : ?>
											<?php echo get_the_post_thumbnail( $item->ID, 'large', array( 'alt' => '', 'class' => 'size-full object-cover transition-transform duration-300 group-hover:scale-105 motion-reduce:transition-none' ) ); ?>
										<?php endif; ?>
									</div>
									<p class="mt-4 flex items-center gap-2 font-body text-xs font-medium leading-4 tracking-[0.025em] text-[#99A1AF]">
										<time datetime="<?php echo esc_attr( get_the_date( 'Y-m-d', $item->ID ) ); ?>"><?php echo esc_html( get_the_date( 'n.j.y', $item->ID ) ); ?></time>
										<span aria-hidden="true">|</span>
										<span class="uppercase"><?php echo esc_html( $data['singular'] ); ?></span>
									</p>
									<h3 class="mt-2 mb-3 font-body text-lg font-bold leading-[1.375] text-dark group-hover:text-primary"><?php echo esc_html( get_the_title( $item->ID ) ); ?></h3>
									<span class="inline-flex items-center gap-1.5 font-body text-sm font-semibold leading-5 tracking-[0.025em] text-dark uppercase">
										<?php esc_html_e( 'View', 'baltimore' ); ?>
										<svg aria-hidden="true" viewBox="0 0 13 13" width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.08333" stroke-linecap="round" stroke-linejoin="round">
											<path d="M2.7083 6.5h7.5834M6.5 2.7083l3.7917 3.7917-3.7917 3.7917"/>
										</svg>
									</span>
								</a>
							</article>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<p class="text-body"><?php echo esc_html( sprintf( __( 'No %s published yet.', 'baltimore' ), strtolower( $data['plural'] ) ) ); ?></p>
				<?php endif; ?>

				<?php if ( $archive_url ) : ?>
					<div class="mt-12">
						<a href="<?php echo esc_url( $archive_url ); ?>" class="btn btn-primary py-3!">
							<?php echo esc_html( sprintf( __( 'View All %s', 'baltimore' ), $data['plural'] ) ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</section>
