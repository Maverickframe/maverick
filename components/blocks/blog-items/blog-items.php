<?php
    $link = get_field('link');
?>
<section class="blog-items">
    <div class="container container_small">
        <h2 class="blog-items__title"><?php the_field('title'); ?></h2>

        <?php
            $args = [
                'post_type'      => 'blog',
                'posts_per_page' => 4,
                'post_status'    => 'publish',
            ];
            $posts = get_posts($args);
        ?>
   
        <?php if ($posts): ?>
            <div class="blog-items__grid js-reveal">
                <?php 
                    foreach ($posts as $post): 
                        $id = $post->ID;
                        $articleDate = get_the_date('F j, Y', $id);
                        $articlePermalink = get_permalink($id);
                        $articleReadTime = get_field('read_time', $id);
                        $articleTitle = get_the_title($id);
                ?>
                    <article class="blog-item">
                        <a class="blog-item__img" href="<?php echo $articlePermalink; ?>">
                            <?php lazy_attachment(get_post_thumbnail_id($id), 'large'); ?>
                        </a>

                        <div class="blog-item__info">
                            <h3 class="blog-item__title">
                                <?php echo $articleTitle; ?>
                            </h3>

                            <div class="blog-item__numbers">
                                <time datetime="<?php echo get_the_date('Y-m-d'); ?>" class="blog-item__date"><?php echo $articleDate; ?></time>
                                <?php if($articleReadTime): ?><p class="blog-item__read-time"><?php echo $articleReadTime; ?></p><?php endif; ?>
                            </div>

                            <a class="blog-item__link" href="<?php echo $articlePermalink; ?>">
                                <?php echo mfs_t('Read more', 'Leer más', 'Mehr lesen'); ?>

                                <?php echo inline_svg('icons/arrow-right-accent.svg'); ?>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>

            </div>
        <?php endif; ?>

        <?php if ( is_array($link) && ! empty($link['url']) ) : ?>
            <a href="<?php echo $link['url']; ?>" target="<?php echo $link['target'] ?: '_self'; ?>" class="btn-main fill blog-items__link"><?php echo $link['title']; ?></a>
        <?php endif; ?>
    </div>
</section>