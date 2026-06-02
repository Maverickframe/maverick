<section class="cases-section">
    <div class="container">
        <h2 class="section-title section-title_cases"><?php the_field('cases_title'); ?></h2>

        <div class="cases-section__desc">
            <?php the_field('cases_description'); ?>
        </div>

        <button type="button" class="btn hero-section__link cases-section__btn js-modal-open" data-modal="book">
            <svg width="13.125rem" height="3rem" viewBox="0 0 210 48" class="border">
                <polyline points="209,1 209,47 1,47 1,1 209,1" class="bg-line" />
                <polyline points="209,1 209,47 1,47 1,1 209,1" class="hl-line" />
            </svg>
            Get offer
        </button>

        <div class="cases-section__items">
            <div class="js-cases-slider splide" role="group" aria-label="<?php the_field('cases_title'); ?>">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php
                            while( have_rows('cases_items')) : the_row();
                                $title = get_sub_field('title');
                                $description = get_sub_field('description');
                                $case = get_sub_field('case');
                        ?>
                            <li class="splide__slide">
                                <div class="case">
                                    <h3 class="case__title"><?php echo $title; ?></h3>
        
                                    <div class="case__video">
                                        <video height="100%" width="100%" muted preload="none" controls loop playsinline>
                                            <source src="<?php echo $case; ?>" type="video/mp4">
                                            Ваш браузер не поддерживает видео, обновите
                                        </video>
                                    </div>

                                    <div class="case__desc">
                                        <?php echo $description; ?>
                                    </div>
            
                                    <a href="/portfolio/" class="btn hero-section__link case__btn">
                                        <svg width="13.125rem" height="3rem" viewBox="0 0 210 48" class="border">
                                            <polyline points="209,1 209,47 1,47 1,1 209,1" class="bg-line" />
                                            <polyline points="209,1 209,47 1,47 1,1 209,1" class="hl-line" />
                                        </svg>
                                        Show More
                                    </a>
                                </div>
                            </li>
                        <?php
                            endwhile; 
                        ?>
                    </ul>

                    <div class="splide__arrows">
                        <button class="splide__arrow splide__arrow--prev">
                            <span class="sr-only">prev slide</span>
                        </button>
                        <button class="btn splide__arrow splide__arrow--next">
                            <svg width="29" height="28" viewBox="0 0 29 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.13825 13.64L0 13.7709L0.13825 13.9169V13.64ZM14.7876 27.5959L27.8388 15.2391L28.565 14.5516L27.8774 13.8254L15.3603 0.604916L14.7876 0V1.45449L27.1513 14.5129L14.7876 26.2188V27.5959Z" fill="#2C3ADA"/>
                                <polyline points="28,1 28,27 1,27" class="hl-line" stroke="#fff" />
                            </svg>
                            <span class="sr-only">Next client</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>