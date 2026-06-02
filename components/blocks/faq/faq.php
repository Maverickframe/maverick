<section class="faq">
    <div class="container container_small">
        <div class="faq__info">
            <h2><?php the_field('title'); ?></h2>
            <?php if(get_field('description')): ?>
                <div class="faq__description">
                    <?php the_field('description'); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="faq__items js-faq">
            <?php
                while( have_rows('faq_items')) : the_row();
                    $title = get_sub_field('title');
                    $description = get_sub_field('description');
            ?>
                <div class="faq-item js-faq-item js-reveal">
                    <button type="button" class="btn faq-item__btn js-faq-btn">
                        <span>
                            <?php echo $title; ?>
                        </span>
                    </button>

                    <div class="faq-item__answer">
                        <div class="faq-item__answer-inner">
                            <?php echo $description; ?>
                        </div>
                    </div>
                </div>
            <?php
                endwhile; 
            ?>
        </div>
    </div>
</section>