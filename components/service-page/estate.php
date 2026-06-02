<section class="estate-section">
    <div class="estate-section__info">
        <?php lazy_attachment(get_field('estate_img'), 'full'); ?>
        <div class="container">
            <h2 class="section-title section-title_developers capitalize"><?php the_field('estate_title'); ?></h2>
            <?php if (get_field('estate_desc')): ?>
                <div class="section-desc"><?php the_field('estate_desc'); ?></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <div class="estate-section__items">
            <?php
                while( have_rows('estate_items')) : the_row();
                    $title = get_sub_field('title');
                    $description = get_sub_field('desc');
            ?>
                <div class="estate-item">
                    <p class="estate-item__title">
                        <span>
                            <?php echo $title; ?>
                        </span>
                    </p>
                    <p class="estate-item__desc">
                        <?php echo $description; ?>
                    </p>
                </div>
            <?php
                endwhile; 
            ?>
        </div>

        <div class="estate-section__slider">
            <div class="js-estate-slider splide" role="group" aria-label="<?php the_field('estate_title'); ?>">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php
                            while( have_rows('estate_items')) : the_row();
                                $title = get_sub_field('title');
                                $description = get_sub_field('desc');
                        ?>
                            <?php if (get_row_index() % 2 !== 0): ?>
                                <li class="splide__slide">
                            <?php endif; ?>
                                <div class="estate-item">
                                    <p class="estate-item__title">
                                        <span>
                                            <?php echo $title; ?>
                                        </span>
                                    </p>
                                    <p class="estate-item__desc">
                                        <?php echo $description; ?>
                                    </p>
                                </div>
                            <?php if (get_row_index() % 2 === 0): ?>
                                </li>
                            <?php endif; ?>
                        <?php
                            endwhile; 
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>