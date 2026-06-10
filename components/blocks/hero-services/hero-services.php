<section class="hero">
    <div class="hero__media">
        <?php
            $poster_id = '';
            $poster_mobile_id = get_field('background_mobile');

            if(get_field('video')) {
                $video_url = get_field('video');
                $video_id  = attachment_url_to_postid($video_url);
                $poster_id    = $video_id ? get_post_thumbnail_id($video_id) : '';
            } elseif(get_field('background')) {
                $poster_id = get_field('background');
            }
        ?>

        <?php if($poster_id || $poster_mobile_id): ?>
        <picture>
            <?php if($poster_mobile_id): ?>
                <source 
                    media="(max-width: 768px)" 
                    srcset="<?php echo wp_get_attachment_image_url($poster_mobile_id, 'full'); ?>"
                >
            <?php endif; ?>

            <?php echo wp_get_attachment_image($poster_id, 'full', false, [
                'fetchpriority' => 'high'
            ] ); ?>
        </picture>
        <?php endif; ?>

        <?php if(get_field('video')): ?>
            <video
                class="js-video-autoplay"
                muted
                loop
                playsinline
                preload="none"
                data-src="<?php echo get_field('video'); ?>"
            ></video>
        <?php endif; ?>
    </div>

    <div class="container container_small">
        <div class="hero__main js-reveal">
            <h1 class="hero__title"><?php the_field('title'); ?></h1>

            <div class="hero__main-info">
                <?php if (get_field('small_description') || have_rows('hero_list')): ?>
                    <div class="hero__small-desc"><?php the_field('small_description'); ?></div>
    
                    <div class="hero__list">
                        <?php
                            while( have_rows('hero_list')) : the_row();
                                $icon = get_sub_field('icon'); 
                                $title = get_sub_field('title'); 
                        ?>
                            <li>
                                <?php lazy_attachment($icon, 'full'); ?>
                                <p><?php echo $title; ?></p>
                            </li>
                        <?php
                            endwhile; 
                        ?>
                    </div>
                <?php else: ?>
                    <div class="hero__desc"><?php the_field('description'); ?></div>
                <?php endif; ?>
    
                <?php // todo: common ?>
                <div class="hero__reviews">
                    <div class="review-item">
                        <?php echo inline_svg('icons/google.svg'); ?>
                        <span>4.8</span>
                        <?php echo inline_svg('icons/star.svg'); ?>
                    </div>
                    <div class="review-item">
                        <?php echo inline_svg('icons/trustpilot-white.svg'); ?>
                        <span>4.9</span>
                        <?php echo inline_svg('icons/star.svg'); ?>
                    </div>
                </div>
            </div>

            <div class="hero__btns">
                <button class="btn-main fill js-modal-open" data-modal="book" type="button"><?php echo mfs_t('Book a call', 'Reservar una llamada'); ?></button>
                <button class="btn-secondary fill js-modal-open" data-modal="download" type="button"><?php echo mfs_t('Download Catalog', 'Descargar catálogo'); ?></button>
            </div>
        </div>

        <?php
            $get_in_touch = get_field('get_in_touch');
            if ($get_in_touch) :
                $git_title = !empty($get_in_touch['title']) ? $get_in_touch['title'] : 'Get In Touch';
                $git_media = $get_in_touch['media'] ?? null;
        ?>
            <?php if ($git_media || $git_title) : ?>
            <div class="hero__cta">
                <?php if ($git_media) : ?>
                    <?php
                        $mime = $git_media['mime_type'];

                        if (strpos($mime, 'image/') === 0) :
                            lazy_attachment($git_media['id'], 'full');
                        elseif (strpos($mime, 'video/') === 0) :
                            $poster = get_the_post_thumbnail_url($git_media['id'], 'large');
                    ?>
                        <video
                            class="js-video-item-hover lazyload"
                            muted
                            loop
                            playsinline
                            preload="none"
                            poster="<?php echo esc_url($poster); ?>"
                            data-src="<?php echo esc_url($git_media['url']); ?>"
                        ></video>
                    <?php endif; ?>
                <?php endif; ?>

                <button class="btn-main fill js-modal-open" data-modal="book" type="button">
                    <?php echo esc_html($git_title); ?>
                    <?php echo inline_svg('icons/arrow-up.svg'); ?>
                </button>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>