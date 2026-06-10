<section class="portfolio-section">
    <div class="container">
        <div class="portfolio-section__info">
            <h2 class="section-title section-title_portfolio-front"><?php the_field('portfolio_title'); ?></h2>
            <div class="portfolio-section__desc">
                <?php the_field('portfolio_desc'); ?>
            </div>

            <a href="<?php echo home_url( '/gallery/' ); ?>" class="btn hero-section__link">
                <svg width="13.125rem" height="3rem" viewBox="0 0 210 48" class="border">
                    <polyline points="209,1 209,47 1,47 1,1 209,1" class="bg-line" />
                    <polyline points="209,1 209,47 1,47 1,1 209,1" class="hl-line" />
                </svg>
                <?php echo mfs_t('Discover more', 'Descubre más'); ?>
            </a>
        </div>

        <div class="portfolio-section__items js-portfolio-front-items">
            <?php
                $args = array(
                    'post_type' => 'portfolio',
                    'posts_per_page' => 6,
                    'cat' => get_field('portfolio_cat') ?? null,
                    'meta_query' => array(
                        array(
                            'key'     => 'portfolio_type',
                            'value'   => '"common"',
                            'compare' => 'LIKE'
                        )
                    )
                );

                $query = new WP_Query( $args );

                wp_localize_script( 'main', 'params', array(
                    'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php',
                    'current_page' => get_query_var( 'paged' ) ? get_query_var('paged') : 1,
                    'max_page' => $query->max_num_pages,
                    'post_id' => get_the_ID()
                ) );

                if ( $query->have_posts() ) {
                    while ( $query->have_posts() ) {
                        $query->the_post();
                        echo get_template_part( 'components/portfolio-front-item');
                    }
                }
                wp_reset_postdata();
            ?>
        </div>

        <button type="button" class="btn hero-section__link portfolio-section__loadmore js-portfolio-front-more">
            <svg width="13.125rem" height="3rem" viewBox="0 0 210 48" class="border">
                <polyline points="209,1 209,47 1,47 1,1 209,1" class="bg-line" />
                <polyline points="209,1 209,47 1,47 1,1 209,1" class="hl-line" />
            </svg>
            <?php echo mfs_t('Show more', 'Ver más'); ?>
        </button>
    </div>
</section>