<section class="service-page-faq">
    <div class="service-page-faq__info">
        <h2 class="service-page-faq__title"><?php the_field('faq_title'); ?></h2>

        <div class="service-page-faq__desc">
            <?php the_field('faq_desc'); ?>
        </div>
    </div>

    <div class="service-page-faq__items js-faq">
        <?php
            while( have_rows('faq_items')) : the_row();
                $title = get_sub_field('title');
                $description = get_sub_field('description');
        ?>
            <div class="service-page-faq-item js-faq-item">
                <button type="button" class="btn service-page-faq-item__btn js-faq-btn">
                    <span>
                        <?php echo $title; ?>
                    </span>
                </button>

                <div class="service-page-faq-item__answer">
                    <div class="service-page-faq-item__answer-inner">
                        <?php echo $description; ?>
                    </div>
                </div>
            </div>
        <?php
            endwhile; 
        ?>
    </div>
</section>