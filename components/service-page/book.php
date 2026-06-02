<section class="service-page-book">
    <div class="service-page-book__img">
        <?php lazy_attachment(get_field('book_img'), 'full'); ?>
    </div>

    <h2 class="service-page-book__title"><?php the_field('book_title'); ?></h2>

    <div class="service-page-book__desc">
        <?php the_field('book_desc'); ?>
    </div>

    <button type="button" class="btn service-page-btn service-page-book__btn js-modal-open" data-modal="book"><?php the_field('hero_btn'); ?></button>
</section>