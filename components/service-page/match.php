<section class="match-section">
    <div class="container">
        <h2 class="section-title section-title_developers capitalize"><?php the_field('match_title'); ?></h2>
        <?php if (get_field('match_desc')): ?>
            <div class="section-desc"><?php the_field('match_desc'); ?></div>
        <?php endif; ?>

        <div class="match-section__slider">
            <div class="js-match-slider splide" role="group" aria-label="<?php the_field('match_title'); ?>">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php
                            while( have_rows('match_items')) : the_row();
                                $title = get_sub_field('title');
                                $description = get_sub_field('description');
                                $img = get_sub_field('img'); 
                        ?>
                            <?php if (get_row_index() % 2 !== 0): ?>
                                <li class="splide__slide">
                            <?php endif; ?>
                                <div class="match-item">
                                    <?php lazy_attachment($img, 'large'); ?>
                                    <div class="match-item__info">
                                        <p class="match-item__title">
                                            <?php echo $title; ?>
                                        </p>
                                        <p class="match-item__desc">
                                            <?php echo $description; ?>
                                        </p>
                                    </div>
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