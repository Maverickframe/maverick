<section class="trusted-section">
    <div class="container container_small">
        <h2 class="section-title section-title_developers"><?php the_field('trusted_title'); ?></h2>
        <?php if (get_field('trusted_desc')): ?>
            <div class="section-desc"><?php the_field('trusted_desc'); ?></div>
        <?php endif; ?>
    </div>

    <div class="trusted-section__slider">
        <div class="js-trusted-slider splide" role="group" aria-label="<?php the_field('trusted_title'); ?>">
            <div class="splide__track">
                <ul class="splide__list">
                    <?php
                        while( have_rows('trusted_items')) : the_row();
                            $img = get_sub_field('img'); 
                    ?>
                        <li class="splide__slide">
                            <div class="trusted-section__slider-item">
                                <?php lazy_attachment($img, 'large'); ?>
                            </div>
                        </li>
                    <?php
                        endwhile; 
                    ?>
                </ul>
            </div>
        </div>
    </div>
</section>