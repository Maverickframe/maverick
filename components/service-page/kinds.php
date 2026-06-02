<section class="kinds-section">
    <div class="container">
        <h2 class="section-title section-title_developers"><?php the_field('kinds_title'); ?></h2>
        <?php if (get_field('kinds_desc')): ?>
            <div class="section-desc"><?php the_field('kinds_desc'); ?></div>
        <?php endif; ?>
    </div>

    <div class="js-kinds-slider splide" role="group" aria-label="<?php the_field('kinds_title'); ?>">
        <div class="splide__track">
            <ul class="splide__list">
                <?php
                    while( have_rows('kinds_items')) : the_row();
                        $title = get_sub_field('title');
                        $description = get_sub_field('description');
                        $img = get_sub_field('img'); 
                        $link = get_sub_field('link');
                ?>
                    <li class="splide__slide">
                        <div class="kinds-item">
                            <?php lazy_attachment($img, 'large'); ?>
                            <div class="kinds-item__info">
                                <p class="kinds-item__title">
                                    <?php echo $title; ?>
                                </p>
                                <div class="kinds-item__desc">
                                    <div class="kinds-item__desc-inner">
                                        <?php echo $description; ?>
                                    </div>
                                </div>
                                <?php if($link): ?>
                                    <a href="<?php echo $link; ?>" class="btn hero-section__link kinds-item__btn js-modal-open">
                                        <svg width="13.125rem" height="3rem" viewBox="0 0 210 48" class="border">
                                            <polyline points="209,1 209,47 1,47 1,1 209,1" class="bg-line" />
                                            <polyline points="209,1 209,47 1,47 1,1 209,1" class="hl-line" />
                                        </svg>
                                        EXPLORE
                                    </a>
                                <?php else: ?>
                                    <button type="button" class="btn hero-section__link kinds-item__btn js-modal-open" data-modal="download">
                                        <svg width="13.125rem" height="3rem" viewBox="0 0 210 48" class="border">
                                            <polyline points="209,1 209,47 1,47 1,1 209,1" class="bg-line" />
                                            <polyline points="209,1 209,47 1,47 1,1 209,1" class="hl-line" />
                                        </svg>
                                        EXPLORE
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                <?php
                    endwhile; 
                ?>
            </ul>
        </div>
    </div>
</section>