<section class="challenges">
    <div class="container container_small">
        <div class="challenges__info">
            <p class="section-subtitle"><?php the_field('subtitle'); ?></p>
            <h2><?php the_field('title'); ?></h2>  
            <?php if(get_field('description')): ?>
                <p class="p1"><?php the_field('description'); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="challenges__items js-reveal">
        <div class="mfs-snap" data-mfs-snap-multiup role="group" aria-label="<?php the_field('title'); ?> Slider">
            <ul class="mfs-snap__track">
                <?php
                    while( have_rows('challenges')) : the_row();
                        $title = get_sub_field('title');
                        $desc = get_sub_field('description');
                        $quote = get_sub_field('quote');
                        $icon = get_sub_field('icon');
                ?>
                    <li class="mfs-snap__item">
                        <div class="challenges-item">
                            <?php if ($icon) : ?>
                                <?php lazy_attachment($icon, 'full'); ?>
                            <?php else: ?>
                                <?php echo inline_svg('icons/awards.svg'); ?>
                            <?php endif; ?>

                            <h3><?php echo $title; ?></h3>

                            <p class="challenges-item__desc">
                                <span>
                                    <?php echo $desc; ?>
                                </span>
                            </p>

                            <p class="challenges-item__quote">
                                <?php echo inline_svg('icons/check.svg'); ?><?php echo $quote; ?>
                            </p>
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
