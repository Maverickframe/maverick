<section class="production-process">
    <div class="container container_small">
        <div class="production-process__info">
            <p class="section-subtitle"><?php echo mfs_eyebrow(get_field('subtitle'), 'Production Process'); ?></p>
            <h2><?php the_field('title'); ?></h2>
            <?php if(get_field('description')): ?>
                <div class="production-process__desc"><?php the_field('description'); ?></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <?php if(have_rows('items')): ?>
            <div class="production-process__items">
                <div class="js-production-process-slider splide" role="group" aria-label="<?php the_field('title'); ?> Slider">
                    <div class="splide__track">
                        <ul class="splide__list">
                            <?php
                                while( have_rows('items')) : the_row();
                                    $title = get_sub_field('title');
                                    $description = get_sub_field('description');
                                    $image_id = get_sub_field('image');
                            ?>
                                <li class="js-reveal splide__slide">
                                    <div class="production-process-item">
                                        <div>
                                            <p class="production-process-item__title"><?php echo $title; ?></p>
                                            <div class="production-process-item__desc"><?php echo $description; ?></div>
                                        </div>

                                        <div class="production-process-item__img">
                                            <?php lazy_attachment($image_id, 'large'); ?>
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
        <?php
            endif; 
        ?>
    </div>
</section>