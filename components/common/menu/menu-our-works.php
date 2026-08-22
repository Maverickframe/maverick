<?php
    $mfs_ow_lang = mfs_lang();
    if ( $mfs_ow_lang === 'de' && function_exists('mfs_menu_ow_de') ) {
        $our_works = mfs_menu_ow_de(); // DE menu is code-driven (no menu_our_works_de ACF)
    } else {
        $ow_field = ( $mfs_ow_lang !== 'en' && get_field('menu_our_works_' . $mfs_ow_lang, 'options') ) ? 'menu_our_works_' . $mfs_ow_lang : 'menu_our_works';
        $our_works = get_field($ow_field, 'options');
    }
?>
<li>                                
    <div class="menu__post-items">
        <?php
            // These case cards render ONLY in the mobile Portfolio accordion
            // (hidden on desktop). Show the 3 most recently published cases — fresh,
            // compact, auto-updating. (No post__in, so no risk of dumping all cases.)
            $args = [
                'post_type'      => 'success-stories',
                'post_status'    => 'publish',
                'posts_per_page' => 3,
                'orderby'        => 'date',
                'order'          => 'DESC',
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

                    // NB: `wp_get_attachment_url($hero_data['video']) ?? ''` evaluated the
                    // array access eagerly, so every case without a hero video logged
                    // "Undefined array key video" (~3-4k warnings/day). Guard the key first.
                    $video_id  = ! empty($hero_data['video']) ? $hero_data['video'] : 0;
                    $video_url = $video_id ? ( wp_get_attachment_url($video_id) ?: '' ) : '';
                    $poster_id = get_post_thumbnail_id();
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
    <?php // Thumbnails are decorative: the link label sits next to them as text, so
          // alt="" is set explicitly here and never inherited from the media library
          // (otherwise the link's accessible name doubles: "Gallery Gallery"). ?>
    <?php foreach ($our_works['items'] as $link): ?>
        <a href="<?php echo get_permalink($link['link']); ?>" class="menu__desktop-link">
            <?php lazy_attachment($link['image'], 'full', 'lazy', '', 'auto', ''); ?>
            <?php echo $link['title']; ?>
        </a>
    <?php endforeach; ?>

    <button class="menu-desktop-links__download js-modal-open" data-modal="download" type="button">
        <?php lazy_attachment($our_works['download_image'], 'full', 'lazy', '', 'auto', ''); ?>
        <span><?php echo $our_works['download_title']; ?></span>
    </button>
</li>