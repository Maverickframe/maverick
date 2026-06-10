<section class="hero-block">
    <div class="hero-block__media">
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

    <div class="container">
        <div class="hero-block__main">
            <?php // todo: common ?>
            <ul class="hero-block__breadcrumbs">
                <li><a href="<?php echo home_url(); ?>">Home</a></li>
                <li><a href="<?php echo home_url('/success-stories/'); ?>">Success stories</a></li> 
                <li><span><?php the_title(); ?></span></li>
            </ul>

            <h1 class="hero-block__title"><?php the_field('title'); ?></h1>
            <p class="hero-block__desc"><?php the_field('description'); ?></p>

            <button class="btn-main fill hero-block__cta js-modal-open" data-modal="book" type="button">
                <?php echo mfs_t('Book a call', 'Reservar una llamada'); ?>
            </button>

            <?php if (have_rows('tags')): ?>
            <ul class="hero-block__items js-reveal js-reveal-init" data-anim="up">
                <?php
                    while( have_rows('tags')) : the_row();
                        $tag = get_sub_field('tag');
                ?>
                    <li>
                        <?php echo $tag; ?>
                    </li>
                <?php
                    endwhile; 
                ?>
            </ul>
            <?php endif; ?>
        </div>
    </div>
</section>