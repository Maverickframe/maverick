<section class="service-page-why-choose <?php echo $args['classes'] ?? null; ?>">
    <div class="service-page-why-choose__img">
        <?php lazy_attachment(get_field('why-choose_img'), 'full'); ?>
    </div>

    <div class="container">
        <div class="service-page-why-choose__container">
            <h2 class="service-page-why-choose__title"><?php the_field('why-choose_title'); ?></h2>
    
            <?php if (get_field('why-choose_desc')): ?>
            <div class="service-page-why-choose__desc">
                <?php the_field('why-choose_desc'); ?>
            </div>
            <?php endif; ?>
        </div>
    
        <div class="service-page-why-choose__items">
            <div class="js-why-choose-slider splide" role="group" aria-label="<?php the_field('why-choose_title'); ?>">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php
                            while( have_rows('why-choose_items')) : the_row();
                                $title = get_sub_field('title');
                                $description = get_sub_field('description');
                        ?>
                            <li class="splide__slide">
                                <div class="service-page-why-choose-item js-animate">
                                    <h3 class="service-page-why-choose-item__title"><?php echo $title; ?></h3>
                    
                                    <div class="service-page-why-choose-item__desc">
                                        <?php echo $description; ?>
                                    </div>
                                </div>
                            </li>
                        <?php
                            endwhile; 
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>