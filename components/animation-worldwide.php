<section class="anim-worldwide">
    <h2 class="section-title section-title_worldwide"><?php the_field('worldwide_title'); ?></h2>

    <div class="anim-worldwide__img <?php echo $args['classes'] ?? null; ?>"><?php lazy_attachment(get_field('worldwide_img'), 'full'); ?></div>
    <div class="anim-worldwide__desc"><?php the_field('worldwide_description'); ?></div>

    <button class="btn hero-section__link js-modal-open anim-worldwide__btn" data-modal="download" type="button">
        <svg width="13.125rem" height="3rem" viewBox="0 0 210 48" class="border">
            <polyline points="209,1 209,47 1,47 1,1 209,1" class="bg-line" />
            <polyline points="209,1 209,47 1,47 1,1 209,1" class="hl-line" />
        </svg>
        <?php if(get_field('worldwide_btn_title')): ?>
            <?php the_field('worldwide_btn_title'); ?>
        <?php else: ?>
            <?php echo mfs_t('Get started', 'Empezar'); ?>
        <?php endif; ?>
    </button>
</section>