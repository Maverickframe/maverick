<section class="service-page-how-create">
    <div class="mobile">
        <h2 class="service-page-how-create__title"><?php the_field('how-create_title'); ?></h2>
        <div class="service-page-how-create__items">
            <div class="js-how-create-slider splide" role="group" aria-label="<?php the_field('how-create_title'); ?>">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php for($i = 1; $i <= 3; $i++): ?>
                            <li class="splide__slide">
                                <div class="service-page-how-create__img">
                                    <?php lazy_attachment(get_field('how-create_image_' . $i), 'full'); ?>
                                </div>
                                <?php
                                    while( have_rows('how-create_items')) : the_row();
                                        if (get_row_index() < (2 * $i - 1)) continue;
                                        if (get_row_index() > (2 * $i)) break;
                                        $description = get_sub_field('description');
                                ?>
                                    <div class="service-page-how-create__item js-animate">
                                        <?php echo $description; ?>
                                    </div>
                                <?php
                                    endwhile; 
                                    reset_rows();
                                ?>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="desktop">
        <div class="service-page-how-create__block js-animate">
            <div class="service-page-how-create__img">
                <?php lazy_attachment(get_field('how-create_image_1'), 'full'); ?>
            </div>
    
            <div>
                <h2 class="service-page-how-create__title"><?php the_field('how-create_title'); ?></h2>
    
                <div class="service-page-how-create__items">
                    <?php
                        while( have_rows('how-create_items')) : the_row();
                            if (get_row_index() > 3) break;
                            $description = get_sub_field('description');
                    ?>
                        <div class="service-page-how-create__item js-animate">
                            <?php echo $description; ?>
                        </div>
                    <?php
                        endwhile; 
                        reset_rows();
                    ?>
                </div>
            </div>
        </div>
    
        <div class="service-page-how-create__block js-animate">
            <div class="service-page-how-create__img ">
                <?php lazy_attachment(get_field('how-create_image_2'), 'full'); ?>
            </div>
    
            <div class="service-page-how-create__items">
                <?php
                    while( have_rows('how-create_items')) : the_row();
                        if (get_row_index() < 4) continue;
                        $description = get_sub_field('description');
                ?>
                    <div class="service-page-how-create__item js-animate">
                        <?php echo $description; ?>
                    </div>
                <?php
                    endwhile; 
                ?>
            </div>
        </div>
    </div>
</section>