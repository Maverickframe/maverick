<section class="selective-works">
    <div class="container container_small">
        <div class="selective-works__info">
            <h2><?php the_field('title'); ?></h2>  
            <p class="p1"><?php the_field('description'); ?></p>
        </div>

        <div class="selective-works__items js-reveal">
            <div class="mfs-snap" role="group" aria-label="<?php the_field('title'); ?> Items Slider">
                <ul class="mfs-snap__track">
                    <?php
                        while( have_rows('Items')) : the_row();
                            $image_id = get_sub_field('image');
                            $title = get_sub_field('title');
                            $desc = get_sub_field('description');
                            $case_title = get_sub_field('case_title');
                            $case = get_sub_field('case_link');
                    ?>
                        <li class="mfs-snap__item">
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

                <div class="mfs-snap__arrows">
                    <button class="mfs-snap__arrow mfs-snap__arrow--prev" type="button">
                        <span class="sr-only"><?php echo esc_html( mfs_t('prev slide', 'Diapositiva anterior', 'Vorherige Folie') ); ?></span>
                        <?php echo inline_svg('icons/arrow-left-slider-square.svg'); ?>
                    </button>
                    <button class="mfs-snap__arrow mfs-snap__arrow--next" type="button">
                        <span class="sr-only">
                            <?php echo esc_html( mfs_t('Next slide', 'Siguiente diapositiva', 'Nächste Folie') ); ?>
                        </span>
                        <?php echo inline_svg('icons/arrow-right-slider-square.svg'); ?>
                    </button>
                </div>

                <div class="mfs-snap__dots"></div>
            </div>
        </div>
    </div>
</section>
