<section class="dev-team-section">
    <div class="container">
        <div class="dev-team-section__info">
            <h2 class="section-title section-title_developers"><?php the_field('dev-team_title'); ?></h2>
            <div class="section-desc"><?php the_field('dev-team_desc'); ?></div>
        </div>

        <div class="dev-team-section__items">
            <?php
                while( have_rows('dev-team_items')) : the_row();
                    $img = get_sub_field('img');
                    $link = get_sub_field('link');
                    $title = get_sub_field('title');
            ?>
                <div class="dev-team-item">
                    <?php lazy_attachment($img, 'large'); ?>
                    <a href="<?php echo $link; ?>" class="dev-team-item__title">
                        <?php echo $title; ?>
                    </a>
                </div>
            <?php
                endwhile; 
            ?>
        </div>

        <div class="dev-team-section__slider">
            <div class="js-dev-team-slider splide" role="group" aria-label="<?php the_field('dev-team_title'); ?>">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php
                            while( have_rows('dev-team_items')) : the_row();
                                $img = get_sub_field('img'); 
                                $link = get_sub_field('link');
                                $title = get_sub_field('title');
                        ?>
                            <?php if (get_row_index() % 2 !== 0): ?>
                                <li class="splide__slide">
                            <?php endif; ?>
                                <div class="dev-team-item">
                                    <?php lazy_attachment($img, 'large'); ?>
                                    <div class="dev-team-item__info">
                                        <a href="<?php echo $link; ?>" class="dev-team-item__title">
                                            <?php echo $title; ?>
                                        </a>
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