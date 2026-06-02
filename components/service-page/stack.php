<section class="stack-section">
    <div class="container">
        <h2 class="section-title section-title_developers"><?php the_field('stack_title'); ?></h2>
        <div class="section-desc"><?php the_field('stack_desc'); ?></div>
    </div>

    <div class="stack-section__slider">
        <div class="js-stack-slider splide" role="group" aria-label="<?php the_field('stack_title'); ?>">
            <div class="splide__track">
                <ul class="splide__list">
                    <?php
                        while( have_rows('stack_items')) : the_row();
                            $img = get_sub_field('img'); 
                    ?>
                        <li class="splide__slide">
                            <div class="stack-section__slider-item">
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