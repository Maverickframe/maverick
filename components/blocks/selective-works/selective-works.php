<section class="selective-works">
    <div class="container container_small">
        <div class="selective-works__info">
            <h2><?php the_field('title'); ?></h2>  
            <p class="p1"><?php the_field('description'); ?></p>
        </div>

        <div class="selective-works__items js-reveal">
            <div class="js-selective-works-slider splide" role="group" aria-label="<?php the_field('title'); ?> Items Slider">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php
                            while( have_rows('Items')) : the_row();
                                $image_id = get_sub_field('image');
                                $title = get_sub_field('title');
                                $desc = get_sub_field('description');
                                $case_title = get_sub_field('case_title');
                                $case = get_sub_field('case_link');
                        ?>
                            <li class="splide__slide">
                                <div class="selective-works-item js-selective-works-item">
                                    <?php lazy_attachment($image_id, 'full'); ?>

                                    <div class="selective-works-item__info">
                                        <div>
                                            <button class="js-selective-works-item-collapse selective-works-item__collapse-btn" type="button" aria-label="Collapse <?php echo $title; ?>">
                                                <?php echo inline_svg('icons/collapse.svg'); ?>
                                            </button>

                                            <h3><?php echo $title; ?></h3>
    
                                            <div>
                                                <div class="js-desc-text selective-works-item__desc"><?php echo $desc; ?></div>
    
                                                <button class="js-desc-more selective-works-item__more-btn" type="button"><?php echo mfs_t('More', 'Más', 'Mehr'); ?></button>
                                            </div>
                                        </div>

                                        <?php if ($case) : ?>
                                        <a href="<?php echo get_permalink($case->ID); ?>" target="_blank" class="btn-main fill selective-works-item__link" type="button">
                                            <span><?php echo $case_title; ?></span>
                                            <?php echo inline_svg('icons/arrow-open.svg'); ?>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>

                <div class="splide__arrows">
                    <button class="splide__arrow splide__arrow--prev">
                        <span class="sr-only">prev slide</span>
                        <?php echo inline_svg('icons/arrow-left-slider-square.svg'); ?>
                    </button>
                    <button class="splide__arrow splide__arrow--next">
                        <span class="sr-only">
                            Next slide
                        </span>
                        <?php echo inline_svg('icons/arrow-right-slider-square.svg'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>