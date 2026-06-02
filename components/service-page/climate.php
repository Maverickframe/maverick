<section class="climate-section">
    <div class="container">
        <h2 class="section-title section-title_developers"><?php the_field('climate_title'); ?></h2>
    </div>

    <div class="climate-section__slider">
        <div class="js-climate-slider splide" role="group" aria-label="<?php the_field('climate_title'); ?>">
            <div class="splide__track">
                <ul class="splide__list">
                    <?php
                        while( have_rows('climate_items')) : the_row();
                            $title = get_sub_field('title');
                            $description = get_sub_field('description');
                            $img = get_sub_field('img'); 
                    ?>
                        <li class="splide__slide">
                            <div class="climate-item">
                                <?php lazy_attachment($img, 'large'); ?>
                                <div class="climate-item__info">
                                    <div class="climate-item__title">
                                        <?php echo $title; ?>
                                    </div>
                                    <p class="climate-item__desc">
                                        <?php echo $description; ?>
                                    </p>
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
</section>