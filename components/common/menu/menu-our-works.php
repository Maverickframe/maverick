<?php
    $our_works = get_field('menu_our_works', 'options'); 
?>
<li>                                
    <div class="menu__post-items">
        <?php
            $args = [
                'post_type' => 'success-stories',
                'post__in' => $our_works['cases'], 
                'orderby' => 'post__in',
                'posts_per_page' => -1,
            ];

            $query = new WP_Query($args);

            if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post();
        ?>
            <a class="menu__post-item" href="<?php the_permalink(); ?>">
                <?php
                    $blocks = parse_blocks(get_post_field('post_content'));

                    $hero_data = null;

                    foreach ($blocks as $block) {
                        if ($block['blockName'] === 'acf/hero-block') {
                            $hero_data = $block['attrs']['data'] ?? [];
                            break;
                        }
                    }

                    $video_url = wp_get_attachment_url($hero_data['video']) ?? '';
                    $poster_id = get_post_thumbnail_id() ?? $hero_data['background'];
                ?>

                <?php if ($poster_id): ?>
                    <?php echo lazy_attachment($poster_id, 'large'); ?>
                <?php endif; ?>

                <?php if ($video_url): ?>
                    <video
                        class="menu__video js-video-item-hover"
                        muted
                        loop
                        playsinline
                        preload="none"
                        src="<?php echo esc_url($video_url); ?>"
                    ></video>
                <?php endif; ?>

                <p><?php the_title(); ?></p>
            </a>
        <?php
                endwhile;
                wp_reset_postdata();
            endif;
        ?>
    </div>
</li>

<li class="menu__desktop-links">
    <?php foreach ($our_works['items'] as $link): ?>
        <a href="<?php echo get_permalink($link['link']); ?>" class="menu__desktop-link">
            <?php lazy_attachment($link['image'], 'full'); ?>
            <?php echo $link['title']; ?>
        </a>
    <?php endforeach; ?>

    <button class="menu-desktop-links__download js-modal-open" data-modal="download" type="button">
        <?php lazy_attachment($our_works['download_image'], 'full'); ?>
        <span><?php echo $our_works['download_title']; ?></span>
    </button>
</li>