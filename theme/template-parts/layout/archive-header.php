<?php
/**
 * Shared hero/header for archive-style WordPress templates.
 *
 * @package Archdiocese_of_Baltimore
 *
 * @var array $args Template arguments.
 */

$eyebrow     = isset( $args['eyebrow'] ) ? trim( (string) $args['eyebrow'] ) : '';
$title       = isset( $args['title'] ) ? trim( (string) $args['title'] ) : '';
$description = isset( $args['description'] ) ? trim( (string) $args['description'] ) : '';
$count       = isset( $args['count'] ) ? absint( $args['count'] ) : null;
?>

<header class="template-hero">
	<div class="template-hero__inner">
		<?php if ( $eyebrow ) : ?>
			<p class="template-hero__eyebrow eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
		<?php endif; ?>

		<?php if ( $title ) : ?>
			<h1 class="template-hero__title"><?php echo wp_kses_post( $title ); ?></h1>
		<?php endif; ?>

		<?php if ( $description ) : ?>
			<div class="template-hero__description"><?php echo wp_kses_post( wpautop( $description ) ); ?></div>
		<?php endif; ?>

		<?php if ( null !== $count ) : ?>
			<p class="template-hero__count">
				<?php
				printf(
					/* translators: %s: number of results. */
					esc_html( _n( '%s result', '%s results', $count, 'baltimore' ) ),
					number_format_i18n( $count )
				);
				?>
			</p>
		<?php endif; ?>
	</div>
</header>
