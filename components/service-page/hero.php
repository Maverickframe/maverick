<section class="service-page-hero">
    <div class="service-page-hero__bg">
        <div class="desktop">
            <?php echo wp_get_attachment_image(get_field('hero_img_desktop'), 'full', false, [
                 'fetchpriority' => 'high'
            ] ); ?>
        </div>
        <div class="mobile">
            <?php echo wp_get_attachment_image(get_field('hero_img_mobile'), 'full', false, [
                 'fetchpriority' => 'high'
            ] ); ?>
        </div>
    </div>

    <h1 class="service-page-hero__title"><?php the_title(); ?></h1>

    <div class="service-page-hero__subtitle"><?php the_content(); ?></div>

    <button type="button" class="btn service-page-btn service-page-btn_white service-page-hero__btn js-modal-open" data-modal="book"><?php the_field('hero_btn'); ?></button>
</section>