<div class="service-page-download">
    <?php if(get_field('download_img')): ?>
        <div class="service-page-download__img">
            <?php lazy_attachment(get_field('download_img'), 'full'); ?>
        </div>
    <?php else: ?>
        <h2 class="service-page-download__title"><?php the_field('download_title'); ?></h2>
    <?php endif; ?>

    <div>
        <div class="service-page-download__desc">
            <?php the_field('download_desc'); ?>
        </div>

        <?php if(get_field('download_link')): ?>
            <button class="btn service-page-btn service-page-download__link js-modal-open" data-modal="download" type="button"><?php the_field('download_link_title'); ?></button>
        <?php endif; ?>
    </div>
</div>