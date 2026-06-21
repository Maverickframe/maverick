<?php
    $desktop_label = $args['desktop_label'] ?? '';
    $permalink = $args['permalink'] ?? '';
    $mfs_res_lang = mfs_lang();
    $res_field = ( $mfs_res_lang !== 'en' && get_field('menu_resources_' . $mfs_res_lang, 'options') ) ? 'menu_resources_' . $mfs_res_lang : 'menu_resources';
    $resources = get_field($res_field, 'options');
?>
<li class="menu__icons">
    <div class="menu__icons-links">
        <?php foreach ($resources as $resource): ?>
            <?php if (isset($resource['link'])): ?><a href="<?php echo $resource['link']; ?>" class="menu__icons-link">
            <?php else: ?><span class="menu__icons-link"><?php endif; ?>
                <?php if (isset($resource['icon_title'])): ?><?php echo inline_svg('icons/' . $resource['icon_title'] . '.svg'); ?><?php endif; ?>
                <strong><?php echo $resource['title']; ?></strong>
                <span><?php echo $resource['description']; ?></span>
            <?php echo isset($resource['link']) ? '</a>' : '</span>'; ?>
        <?php endforeach; ?>
    </div>

    <a href="<?php echo $permalink; ?>" class="menu__desktop-link"><?php echo $desktop_label; ?></a>
</li>

<li>
    <div class="menu__post-items resources">
        <?php
            $args = [
                'post_type' => 'blog',
                'post_status' => 'publish',
                'posts_per_page' => 4,
                'orderby' => 'date',
                'order' => 'DESC',
            ];

            $query = new WP_Query($args);

            if ($query->have_posts()) :
                while ($query->have_posts()) : $query->the_post();
        ?>
            <a class="menu__post-item resources" href="<?php the_permalink(); ?>">
                <?php if (has_post_thumbnail()) : ?>
                    <?php lazy_attachment(get_post_thumbnail_id(), 'large'); ?>
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