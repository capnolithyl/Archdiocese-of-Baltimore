<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$preview = ! empty( $is_preview );
$page_id = is_numeric( $post_id ?? null ) && (int) $post_id > 0 ? (int) $post_id : (int) get_the_ID();
$mode = 'category' === get_field( 'mode' ) ? 'category' : 'landing';
$categories = balt_resource_directory_categories( $page_id, $mode );
$sections = 'category' === $mode ? balt_resource_directory_sections( (array) get_field( 'sections' ) ) : array();
$columns = array( 'left' => array(), 'right' => array() );
$prefix = wp_unique_id( 'balt-resource-' );
foreach ( $sections as $index => $section ) {
	$section['id'] = $prefix . '-' . ( $index + 1 ) . '-' . sanitize_title( $section['heading'] );
	$columns[ $section['column'] ][] = $section;
}
$intro = trim( (string) get_field( 'intro' ) );
if ( ! $intro ) {
	$intro = __( 'Use the navigation to select a resource category. Once selected, you will see its sections and their respective files.', 'baltimore' );
}
if ( $sections && ! $preview ) {
	wp_enqueue_script( 'balt-resource-directory', get_template_directory_uri() . '/assets/js/resource-directory.js', array(), BALT_VERSION, true );
}
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'not-prose balt-full-bleed balt-resource-directory' ) ); ?>>
	<nav class="balt-resource-directory__sidebar" aria-label="<?php esc_attr_e( 'Resource categories', 'baltimore' ); ?>">
		<?php if ( $categories ) : ?>
			<ul>
				<?php foreach ( $categories as $category ) : ?>
					<li>
						<?php if ( $preview ) : ?>
							<span class="balt-resource-directory__category<?php echo $category['current'] ? ' is-current' : ''; ?>"><?php echo esc_html( $category['label'] ); ?></span>
						<?php else : ?>
							<a class="balt-resource-directory__category<?php echo $category['current'] ? ' is-current' : ''; ?>" href="<?php echo esc_url( $category['url'] ); ?>"<?php if ( $category['current'] ) : ?> aria-current="page"<?php endif; ?>><?php echo esc_html( $category['label'] ); ?></a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php elseif ( $preview ) : ?>
			<p class="balt-resource-directory__empty"><?php esc_html_e( 'Publish the directory landing page and its child category pages to populate this navigation.', 'baltimore' ); ?></p>
		<?php endif; ?>
	</nav>
	<div class="balt-resource-directory__content">
		<?php if ( 'landing' === $mode ) : ?>
			<div class="balt-resource-directory__intro">
				<span class="balt-resource-directory__intro-arrow" aria-hidden="true">&larr;</span>
				<p><?php echo esc_html( $intro ); ?></p>
			</div>
		<?php elseif ( $sections ) : ?>
			<details class="balt-resource-directory__jump">
				<summary><?php esc_html_e( 'Jump to Section...', 'baltimore' ); ?><span aria-hidden="true" class="balt-resource-directory__chevron"></span></summary>
				<ul>
					<?php foreach ( array_merge( $columns['left'], $columns['right'] ) as $section ) : ?>
						<li><a href="#<?php echo esc_attr( $section['id'] ); ?>"><?php echo esc_html( $section['heading'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</details>
			<div class="balt-resource-directory__columns">
				<?php foreach ( $columns as $column => $groups ) : ?>
					<div class="balt-resource-directory__column">
						<?php foreach ( $groups as $section ) : ?>
							<section class="balt-resource-directory__section" id="<?php echo esc_attr( $section['id'] ); ?>" tabindex="-1">
								<h2><?php echo esc_html( $section['heading'] ); ?></h2>
								<ul>
									<?php foreach ( $section['links'] as $link ) : ?>
										<li>
											<?php if ( $preview ) : ?>
												<span><?php echo esc_html( $link['label'] ); ?></span>
											<?php else : ?>
												<a href="<?php echo esc_url( $link['url'] ); ?>"<?php if ( '_blank' === $link['target'] ) : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?>><?php echo esc_html( $link['label'] ); ?></a>
											<?php endif; ?>
										</li>
									<?php endforeach; ?>
								</ul>
							</section>
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<p class="balt-resource-directory__empty"><?php esc_html_e( 'No resources are available in this category yet.', 'baltimore' ); ?></p>
		<?php endif; ?>
	</div>
</div>
