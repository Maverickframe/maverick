<section class="strategic-cgi">
    <div class="container">
        <div class="strategic-cgi__main js-reveal">
            <div class="strategic-cgi__info">
                <p class="section-subtitle"><?php echo mfs_t('Strategic CGI Approach', 'Enfoque estratégico de CGI', 'Strategischer CGI-Ansatz'); ?></p>
                <h2><?php the_field('title'); ?></h2>
                <div class="p1"><?php the_field('description'); ?></div>
            </div>

            <div class="strategic-cgi__media">
                <?php
                    $media = get_field('media');

                    if ($media) {
                        $mime = $media['mime_type'];

                        if (strpos($mime, 'image/') === 0) :
                            echo lazy_attachment($media['id'], 'large');
                        ?>
                        <?php elseif (strpos($mime, 'video/') === 0) : ?>
                            <?php $poster = get_the_post_thumbnail_url($media['id'], 'large'); ?>
                            <video playsinline preload="none" muted loop class="js-video-item-hover lazyload" poster="<?php echo $poster; ?>" data-src="<?php echo esc_url($media['url']); ?>"></video>
                        <?php endif;
                    }
                ?>
            </div>
        </div>

        <div class="strategic-cgi__quote js-highlight text-highlight">
            <?php the_field('quote'); ?>
        </div>
    </div>
</section>