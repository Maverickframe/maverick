<section class="service-page-clients js-animate">
    <div class="container">
        <h2 class="service-page-clients__title"><?php the_field('clients_title'); ?></h2>
    </div>

    <div class="service-page-clients__items clients-section__items js-animate">
        <div class="container">
            <div class="js-clients-slider splide" role="group" aria-label="<?php the_field('clients_title'); ?>">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php
                            while( have_rows('clients', 'options')) : the_row();
                                $img = get_sub_field('image');
                        ?>
                            <li class="splide__slide">
                                <?php lazy_attachment($img, 'large'); ?>
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