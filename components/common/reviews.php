<div class="container container_small">
    <div class="reviews-section js-reveal">
        <div class="reviews-section__main-slider">
            <div class="js-reviews-slider splide" role="group" aria-label="Maverickframe reviews">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php
                            while( have_rows('reviews_items', 'options')) : the_row();
                                $date = get_sub_field('date');
                                $name = get_sub_field('name');
                                $position = get_sub_field('position');
                                $location = get_sub_field('location');
                                $review = get_sub_field('review');
                                $type = get_sub_field('type');
                                $link = get_sub_field('link');
                                $image = get_sub_field('image');
                        ?>
                            <li class="splide__slide">
                                <div class="js-reviews-item reviews-item">
                                    <div class="reviews-item__main">
                                        <div class="reviews-item__review">
                                            <?php echo inline_svg('icons/quote.svg'); ?>
    
                                            <div>
                                                <div class="js-desc-text reviews-item__review-text">
                                                    <?php echo $review; ?>
                                                </div>
                        
                                                <button class="js-desc-more reviews-item__more-btn" type="button"><?php echo mfs_t('More', 'Más'); ?></button>
                                            </div>

                                            <?php if($link): ?>
                                                <a href="<?php echo $link; ?>" target="_blank" rel="nofollow noopener">More</a>
                                            <?php endif; ?>
                                        </div>
    
                                        <div class="reviews-item__rating-wrapper">
                                            <div class="reviews-item__rating">
                                                5,0
                                                <?php echo inline_svg('icons/rating.svg'); ?>
                                            </div>
    
                                            <div class="reviews-item__place">
                                                <?php 
                                                    if($type === 'Trustpilot') {
                                                        echo inline_svg('icons/trustpilot-dark.svg');
                                                    } else {
                                                        echo inline_svg('icons/google.svg');
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class="reviews-item__info">
                                        <?php echo lazy_attachment($image, 'large'); ?>
    
                                        <div class="reviews-item__info-main">
                                            <time datetime="<?php echo $date; ?>" class="reviews-item__date"><?php echo $date; ?></time>
    
                                            <p class="reviews-item__name">
                                                <?php echo $name; ?>
                                            </p>
                                            <p class="reviews-item__position">
                                                <?php echo $position; ?>
                                            </p>
                                            <p class="reviews-item__location">
                                                <?php echo inline_svg('icons/location.svg'); ?>
                                                <?php echo $location; ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php
                            endwhile; 
                        ?>
                    </ul>
                </div>
    
                <div class="reviews-section__nav">
                    <ul class="splide__pagination"></ul>
                    <div class="splide__arrows">
                        <button class="splide__arrow splide__arrow--prev sr-only">
                            <?php echo mfs_t('Prev Review', 'Reseña anterior'); ?>
                        </button>
                        <button class="splide__arrow splide__arrow--next">
                            <span>
                                <?php echo mfs_t('Next Review', 'Siguiente reseña'); ?>
                            </span>
                            <?php echo inline_svg('icons/arrow-right-accent.svg'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="reviews-section__thumb-slider">
            <div class="js-reviews-thumbnails-slider splide" role="group" aria-label="Maverickframe thumbnails reviews">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php
                            while( have_rows('reviews_items', 'options')) : the_row();
                                $date = get_sub_field('date');
                                $date = DateTime::createFromFormat('d/m/Y', $date);
                                $date = $date->format('d/m/y');
                                $name = get_sub_field('name');
                                $position = get_sub_field('position');
                                $review = get_sub_field('review');
                                $image = get_sub_field('image');
                        ?>
                            <li class="splide__slide">
                                <div class="reviews-item-thumb">
                                    <?php echo lazy_attachment($image, 'large'); ?>

                                    <time datetime="<?php echo $date; ?>" class="reviews-item-thumb__date"><?php echo $date; ?></time>

                                    <div class="reviews-item-thumb__info">

                                        <div class="reviews-item-thumb__title">
                                            <?php echo $name; ?>
                                        </div>

                                        <div class="reviews-item-thumb__position">
                                            <?php echo $position; ?>
                                        </div>

                                        <div class="reviews-item-thumb__review">
                                            <?php echo $review; ?>
                                        </div>
                                    </div>

                                    <button class="reviews-item-thumb__arrow" type="button">
                                        <span><?php echo mfs_t('Next review', 'Siguiente reseña'); ?></span>
                                        <?php echo inline_svg('icons/arrow-right-accent.svg'); ?>
                                    </button>
                                </div>
                            </li>
                        <?php
                            endwhile; 
                        ?>
                    </ul>
                </div>
    
                <div class="reviews-section__nav">
                    <ul class="splide__pagination"></ul>
                    <div class="splide__arrows">
                        <button class="splide__arrow splide__arrow--prev sr-only">
                            <?php echo mfs_t('Prev Review', 'Reseña anterior'); ?>
                        </button>
                        <button class="splide__arrow splide__arrow--next">
                            <span>
                                <?php echo mfs_t('Next Review', 'Siguiente reseña'); ?>
                            </span>
                            <?php echo inline_svg('icons/arrow-right-accent.svg'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>