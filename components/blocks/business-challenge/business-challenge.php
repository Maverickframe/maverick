<div class="container">
    <section class="business-challenge js-reveal">
        <div class="business-challenge__info">
            <p class="section-subtitle"><?php echo mfs_t('Business Challenge', 'Reto de negocio'); ?></p>
            <h2><?php the_field('title'); ?></h2>
            <div class="p1">
                <?php the_field('description'); ?>
            </div>

            <p class="business-challenge__strong">
                <?php the_field('big_text'); ?>
            </p>
        </div>

        <div class="business-challenge__media">
            <?php $media = get_field('video'); ?>
            <?php if($media): ?>
                <?php $poster = get_the_post_thumbnail_url($media['id'], 'large'); ?>
                <video playsinline preload="none" muted loop class="js-video-item-hover lazyload" poster="<?php echo $poster; ?>" data-src="<?php echo esc_url($media['url']); ?>"></video>
            <?php elseif(get_field('image')): ?>
                <?php lazy_attachment(get_field('image'), 'full'); ?>
            <?php endif; ?>
        </div>
    </section>
</div>