<section class="portfolio-service-section">
    <div class="container">
        <div class="portfolio-service-section__items">
            <div class="portfolio-item portfolio-item_main">
                <h2 class="section-title section-title_portfolio"><?php the_field('portfolio_title'); ?></h2>
                <p class="portfolio-service-section__desc">
                    <?php the_field('portfolio_desc'); ?>
                </p>

                <a href="<?php echo home_url( '/gallery/' ); ?>" class="btn portfolio-service-section__link">
                    Show more
                </a>
            </div>
            
            <div class="portfolio-item portfolio-item_projects portfolio-service-section__desc">
                <?php the_field('portfolio_desc_2'); ?>
            </div>

            <?php
                    $args = array(
                        'post_type' => 'portfolio',
                        'posts_per_page' => 7,
                        'cat' => get_field('portfolio_cat') ?? 3,
                        'meta_query' => array(
                            array(
                                'key'     => 'portfolio_type',
                                'value'   => '"common"',
                                'compare' => 'LIKE'
                            )
                        )
                    );

                    $query = new WP_Query( $args );

                    if ( $query->have_posts() ) {
                        while ( $query->have_posts() ) {
                            $query->the_post();

                            echo get_template_part( 'components/portfolio-item', null, array( 
                                'class' => 'portfolio-item_section',
                            ) );
                        }
                    }
                    wp_reset_postdata();
                ?>
       
        </div>
    </div>
</section>