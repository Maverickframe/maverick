<section class="service-page-get-started">
    <div class="service-page-get-started__img">
        <?php lazy_attachment(get_field('get-started_img'), 'full'); ?>
        <h2 class="service-page-get-started__title"><?php the_field('get-started_title'); ?></h2>
    </div>

    <div class="service-page-get-started__main">
        <div class="service-page-get-started__desc">
            <?php the_field('get-started_desc'); ?>
        </div>

        <div class="service-page-get-started__form">
            <?php echo get_template_part( 'components/contacts-form' ); ?>
        </div>
    </div>
</section>