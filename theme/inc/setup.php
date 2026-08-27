<?php

/**
 * Remove featured image support for the front page.
 */
add_action('admin_init', function () {
    if (!is_admin()) {
        return;
    }

    $post_id = isset($_GET['post']) ? absint($_GET['post']) : 0;

    if (!$post_id) {
        return;
    }

    $front_page_id = (int) get_option('page_on_front');

    if ($post_id === $front_page_id) {
        remove_post_type_support('page', 'thumbnail');
    }
});
