<section class="portfolio-section portfolio-section_developers">
    <div class="container">
        <div class="portfolio-section__info">
            <h2 class="section-title section-title_portfolio-front"><?php the_field('portfolio-dev_title'); ?></h2>
            <div class="portfolio-section__desc">
                <?php the_field('portfolio-dev_desc'); ?>
            </div>
        </div>

        <div class="portfolio-section__items">
            <?php
                while( have_rows('portfolio_items')) : the_row();
                    $img = get_sub_field('img'); 
            ?>
                <a href="<?php echo home_url( '/gallery/' ); ?>" class="portfolio-front-item js-dev-portfolio-item">
                    <?php lazy_attachment($img, 'large'); ?>
                </a>
            <?php
                endwhile; 
            ?>
        </div>

        <button type="button" class="btn hero-section__link portfolio-section__loadmore js-dev-portfolio-more">
            <svg width="13.125rem" height="3rem" viewBox="0 0 210 48" class="border">
                <polyline points="209,1 209,47 1,47 1,1 209,1" class="bg-line" />
                <polyline points="209,1 209,47 1,47 1,1 209,1" class="hl-line" />
            </svg>
            Show more
        </button>
    </div>
</section>