<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some functionality here could be replaced by core features.
 *
 * @package Archdiocese_of_Baltimore
 */

if ( ! function_exists( 'balt_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 */
	function balt_posted_on() {
		$time_string = '<time class="published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);

		printf(
			'<a href="%1$s" rel="bookmark">%2$s</a>',
			esc_url( get_permalink() ),
			$time_string // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	}
endif;

if ( ! function_exists( 'balt_posted_by' ) ) :
	/**
	 * Prints HTML with meta information about theme author.
	 */
	function balt_posted_by() {
		printf(
		/* translators: 1: posted by label, only visible to screen readers. 2: author link. 3: post author. */
			'<span class="sr-only">%1$s</span><span class="author vcard"><a class="url fn n" href="%2$s">%3$s</a></span>',
			esc_html__( 'Posted by', 'baltimore' ),
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_html( get_the_author() )
		);
	}
endif;

if ( ! function_exists( 'balt_entry_meta' ) ) :
	/**
	 * Prints HTML with post meta information.
	 * This template tag is used in the entry header.
	 */
	function balt_entry_meta() {

		// Hide author, post date, category and tag text for pages.
		if ( 'post' === get_post_type() ) {

			// Posted by.
			balt_posted_by();

			// Posted on.
			balt_posted_on();

			/* translators: used between list items, there is a space after the comma. */
			$categories_list = get_the_category_list( __( ', ', 'baltimore' ) );
			if ( $categories_list ) {
				printf(
				/* translators: 1: posted in label, only visible to screen readers. 2: list of categories. */
					'<span><span class="sr-only">%1$s</span>%2$s</span>',
					esc_html__( 'Posted in', 'baltimore' ),
					$categories_list // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			}

			/* translators: used between list items, there is a space after the comma. */
			$tags_list = get_the_tag_list( '', __( ', ', 'baltimore' ) );
			if ( $tags_list ) {
				printf(
				/* translators: 1: tags label, only visible to screen readers. 2: list of tags. */
					'<span><span class="sr-only">%1$s</span>%2$s</span>',
					esc_html__( 'Tags:', 'baltimore' ),
					$tags_list // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			}
		}

		// Edit post link.
		edit_post_link(
			sprintf(
				wp_kses(
				/* translators: %s: Name of current post. Only visible to screen readers. */
					__( 'Edit <span class="sr-only">%s</span>', 'baltimore' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			)
		);
	}
endif;

if ( ! function_exists( 'balt_entry_footer' ) ) :
	/**
	 * Prints HTML with post meta information.
	 */
	function balt_entry_footer() {

		// Hide author, post date, category and tag text for pages.
		if ( 'post' === get_post_type() ) {

			// Posted by.
			balt_posted_by();

			// Posted on.
			balt_posted_on();

			/* translators: used between list items, there is a space after the comma. */
			$categories_list = get_the_category_list( __( ', ', 'baltimore' ) );
			if ( $categories_list ) {
				printf(
				/* translators: 1: posted in label, only visible to screen readers. 2: list of categories. */
					'<span><span class="sr-only">%1$s</span>%2$s</span>',
					esc_html__( 'Posted in', 'baltimore' ),
					$categories_list // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			}

			/* translators: used between list items, there is a space after the comma. */
			$tags_list = get_the_tag_list( '', __( ', ', 'baltimore' ) );
			if ( $tags_list ) {
				printf(
				/* translators: 1: tags label, only visible to screen readers. 2: list of tags. */
					'<span><span class="sr-only">%1$s</span>%2$s</span>',
					esc_html__( 'Tags:', 'baltimore' ),
					$tags_list // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				);
			}
		}

		// Edit post link.
		edit_post_link(
			sprintf(
				wp_kses(
				/* translators: %s: Name of current post. Only visible to screen readers. */
					__( 'Edit <span class="sr-only">%s</span>', 'baltimore' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			)
		);
	}
endif;

if ( ! function_exists( 'balt_post_thumbnail' ) ) :
	/**
	 * Displays an optional post thumbnail, wrapping the post thumbnail in an
	 * anchor element except when viewing a single post.
	 */
	function balt_post_thumbnail() {
		if ( ! balt_can_show_post_thumbnail() ) {
			return;
		}

		if ( is_singular() ) :
			?>

			<figure>
				<?php the_post_thumbnail(); ?>
			</figure><!-- .post-thumbnail -->

			<?php
		else :
			?>

			<figure>
				<a href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
					<?php the_post_thumbnail(); ?>
				</a>
			</figure>

			<?php
		endif; // End is_singular().
	}
endif;

if ( ! function_exists( 'balt_the_posts_navigation' ) ) :
	/**
	 * Wraps `the_posts_pagination` for use throughout the theme.
	 */
	function balt_the_posts_navigation() {
		the_posts_pagination(
			array(
				'mid_size'  => 2,
				'prev_text' => __( 'Newer posts', 'baltimore' ),
				'next_text' => __( 'Older posts', 'baltimore' ),
			)
		);
	}
endif;

if ( ! function_exists( 'balt_content_class' ) ) :
	/**
	 * Displays the class names for the post content wrapper.
	 *
	 * This allows us to add Tailwind Typography’s modifier classes throughout
	 * the theme without repeating them in multiple files. (They can be edited
	 * at the top of the `../functions.php` file via the
	 * BALT_TYPOGRAPHY_CLASSES constant.)
	 *
	 * Based on WordPress core’s `body_class` and `get_body_class` functions.
	 *
	 * @param string|string[] $classes Space-separated string or array of class
	 *                                 names to add to the class list.
	 */
	function balt_content_class( $classes = '' ) {
		$all_classes = array( $classes, BALT_TYPOGRAPHY_CLASSES );

		foreach ( $all_classes as &$class_groups ) {
			if ( ! empty( $class_groups ) ) {
				if ( ! is_array( $class_groups ) ) {
					$class_groups = preg_split( '#\s+#', $class_groups );
				}
			} else {
				// Ensure that we always coerce class to being an array.
				$class_groups = array();
			}
		}

		$combined_classes = array_merge( $all_classes[0], $all_classes[1] );
		$combined_classes = array_map( 'esc_attr', $combined_classes );

		// Separates class names with a single space, preparing them for the
		// post content wrapper.
		echo 'class="' . esc_attr( implode( ' ', $combined_classes ) ) . '"';
	}
endif;
