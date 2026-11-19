<?php

if (! defined('ABSPATH')) {
    exit;
}

$video       = get_field('video_file');
$poster      = get_field('video_poster');
$heading     = trim((string) get_field('video_heading'));
$subheadings = get_field('subheadings');
$subheading  = $subheadings[0]['video_subheading'] ?? '';
$buttons     = array_slice(get_field('video_buttons') ?: array(), 0, 2);
$heading_tag = is_front_page() ? 'h1' : 'h2';
?>
<div>
    <article>
        <div>
            <?php if (! empty($video['url'])) : ?>
                <video src="<?php echo esc_url($video['url']); ?>"
                    <?php if (! empty($poster['url'])) : ?>poster="<?php echo esc_url($poster['url']); ?>"
                    <?php endif; ?> autoplay muted loop playsinline preload="metadata" aria-hidden="true"></video>
            <?php elseif (! empty($poster['ID'])) : ?>
                <?php echo wp_get_attachment_image($poster['ID'], 'full', false, array('alt' => '')); ?>
            <?php elseif (! empty($is_preview)) : ?>
                <p><?php esc_html_e('Choose a video.', 'baltimore'); ?></p>
            <?php endif; ?>
            <span aria-hidden="true"></span>
        </div>

        <div>
            <?php if ($heading) : ?>
                <<?php echo $heading_tag; ?>>
                    <?php echo esc_html($heading); ?></<?php echo $heading_tag; ?>>
            <?php endif; ?>

            <?php if ($subheading) : ?>
                <p>
                    <?php echo esc_html($subheading); ?></p>
            <?php endif; ?>

            <?php if ($buttons) : ?>
                <div>
                    <?php foreach ($buttons as $row) : ?>
                        <?php
                        $link = $row['link'] ?? null;
                        if (! empty($link['url'])) {
                            echo wp_kses_post(dpi_blocks_render_link( $link, '', ! empty($is_preview)));
                        }
                        ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </article>
</div>