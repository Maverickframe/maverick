<section class="packages-section">
    <div class="container">
        <h2 class="section-title section-title_developers capitalize"><?php the_field('packages_title'); ?></h2>
        <div class="section-desc"><?php the_field('packages_desc'); ?></div>

        <div class="packages-section__slider">
            <div class="js-packages-slider splide" role="group" aria-label="<?php the_field('packages_title'); ?>">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php
                            while( have_rows('packages_items')) : the_row();
                                $title = get_sub_field('title');
                                $description = get_sub_field('description');
                                $img = get_sub_field('img'); 
                        ?>
                            <li class="splide__slide">
                                <div class="packages-item">
                                    <?php lazy_attachment($img, 'large'); ?>
                                    <span class="packages-item__num">0<?php echo get_row_index(); ?></span>
                                    <div class="packages-item__info">
                                        <div class="packages-item__title">
                                            <?php echo $title; ?>
                                        </div>
                                        <div class="packages-item__desc">
                                            <?php echo $description; ?>
                                        </div>
                                        <button type="button" class="btn hero-section__link js-modal-open" data-modal="download">
                                            <svg width="13.125rem" height="3rem" viewBox="0 0 210 48" class="border">
                                                <polyline points="209,1 209,47 1,47 1,1 209,1" class="bg-line" />
                                                <polyline points="209,1 209,47 1,47 1,1 209,1" class="hl-line" />
                                            </svg>
                                            EXPLORE PACK
                                        </button>
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