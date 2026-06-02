<section class="service-page-ceo">
    <div class="service-page-ceo__img">
        <?php lazy_attachment(get_field('ceo_img'), 'full'); ?>
        <div class="service-page-ceo__img-overlay">
            <ul class="service-page-ceo__socials">
                <?php
                    while( have_rows('ceo_socials')) : the_row();
                        $title = get_sub_field('title');
                        $link = get_sub_field('link');
                ?>
                    <li>
                        <a href="<?php echo $link; ?>" target="_blank" rel="nofollow noopener"><?php echo $title; ?></a>
                    </li>
                <?php
                    endwhile; 
                ?>
            </ul>
            <div>
                <a href="<?php the_permalink(2016); ?>" class="service-page-ceo__img-name"><?php the_field('ceo_name'); ?></a>
                <p class="service-page-ceo__img-position"><?php the_field('ceo_position'); ?></p>
            </div>
        </div>
    </div>

    <div class="service-page-ceo__main">
        <h2 class="service-page-ceo__title"><?php the_field('ceo_title'); ?></h2>
    
        <div class="service-page-ceo__info mobile">
            <p class="service-page-ceo__position"><?php the_field('ceo_position'); ?></p>
            <p class="service-page-ceo__name"><?php the_field('ceo_name'); ?></p>
        </div>
    </div>

    <div class="service-page-ceo__desc">
        <?php the_field('ceo_text'); ?>
    </div>
    <div class="service-page-ceo__info desktop">
        <p class="service-page-ceo__position"><?php the_field('ceo_position'); ?></p>
        <p class="service-page-ceo__name"><?php the_field('ceo_name'); ?></p>
    </div>

    <button type="button" class="btn service-page-btn service-page-ceo__btn js-modal-open" data-modal="book">Contact CEO</button>
</section>