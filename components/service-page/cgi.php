<section class="cgi-section">
    <div class="container">
        <h2 class="section-title section-title_developers"><?php the_field('cgi_title'); ?></h2>

        <div class="cgi-section__slider">
            <div class="js-cgi-slider desktop splide" role="group" aria-label="<?php the_field('cgi_title'); ?>">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php
                            while( have_rows('cgi_items')) : the_row();
                                $title = get_sub_field('title');
                                $description = get_sub_field('description');
                                $img = get_sub_field('img'); 
                        ?>
                            <li class="splide__slide">
                                <div class="cgi-item">
                                    <?php lazy_attachment($img, 'large'); ?>
                                    <div class="cgi-item__info">
                                        <div class="cgi-item__title">
                                            <?php echo $title; ?>
                                        </div>
                                        <p class="cgi-item__desc">
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

            <div class="js-cgi-slider-mobile mobile splide" role="group" aria-label="<?php the_field('cgi_title'); ?>">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php
                            $items = get_field('cgi_items');
                            if (isset($items) && is_array($items)):
                                $chunks = array_chunk($items, 3);
                        ?>
                            <?php foreach ($chunks as $chunk): ?>
                                <li class="splide__slide">
                                    <div class="cgi-section__group">
                                        <?php foreach ($chunk as $row): ?>
                                            <div class="cgi-item">
                                                <div class="cgi-item__info">
                                                    <div class="cgi-item__title"><?php echo $row['title']; ?></div>
                                                    <p class="cgi-item__desc"><?php echo $row['description']; ?></p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>