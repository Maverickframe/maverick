<section class="visuals-slider">
    <div class="container container_small">
        <div class="visuals-slider__info">
            <p class="section-subtitle"><?php the_field('subtitle'); ?></p>
            <h2><?php the_field('title'); ?></h2>  
            <p class="p1"><?php the_field('description'); ?></p>
        </div>
    </div>

    <div class="visuals-slider__items js-reveal">
        <div class="mfs-snap" data-mfs-snap-multiup role="group" aria-label="Visual Items Slider">
            <ul class="mfs-snap__track">
                <?php
                    while( have_rows('clients')) : the_row();
                        $title = get_sub_field('title');
                        $desc = get_sub_field('description');
                        $image_id = get_sub_field('image');
                        $image_mob_id = get_sub_field('image_mob');
                ?>
                    <li class="mfs-snap__item">
                        <div class="visuals-slider-item">
                            <picture>
                                <?php if ($image_mob_id): ?>
                                    <source media="(max-width: 768px)" srcset="<?php echo wp_get_attachment_image_url($image_mob_id, 'full'); ?>">
                                <?php endif; ?>

                                <?php lazy_attachment($image_id, 'full'); ?>
                            </picture>

                            <div class="visuals-slider-item__info">
                                <h3><?php echo $title; ?></h3>
                                <p><?php echo $desc; ?></p>
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
</section>
