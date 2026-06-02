<?php 
$categories = get_field('categories');
$currentCat = array_key_exists('cat', $_GET) ? $_GET['cat'] : 'all';
?>
<section class="inner-page portfolio-page">
    <div class="container portfolio-page__container">
        <aside class="portfolio-page__sidebar">
            <h1 class="section-title section-title_portfolio-page">Portfolio</h1>

            
            <div class="portfolio-page__filter-mobile">
                <select class="js-portfolio-group-mobile" aria-label="Select Portfolio Category">
                    <option value="all" <?php if($currentCat == 'all'):?>selected<?php endif; ?>>All</option>
                    <?php 
                        foreach ($categories as $c):
                            $cat = get_category( $c );
                    ?>
                        <option value="<?php echo esc_html( $cat->term_id ); ?>" <?php if($currentCat == $cat->term_id):?>selected<?php endif; ?>><?php echo esc_html( $cat->name ); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="portfolio-page__filter-desktop">
                <ul class="page-nav js-portfolio-filter-desktop">
                    <li class="page-nav__item">
                        <button class="btn page-nav__btn <?php if($currentCat == 'all'):?>active<?php endif; ?>" type="button" data-cat="all">All</button>
                    </li>
                    <?php 
                        foreach ($categories as $c):
                            $cat = get_category( $c );
                    ?>
                        <li class="page-nav__item">
                            <button class="btn page-nav__btn <?php if($currentCat == $cat->term_id):?>active<?php endif; ?>" type="button" data-cat="<?php echo esc_html( $cat->term_id ); ?>"><?php echo esc_html( $cat->name ); ?></button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>

        <div class="portfolio-page__main">
            <div class="portfolio-page__items js-portfolio-items">
                <?php
                    $cat = isset($_GET['cat']) && $_GET['cat'] != 'all' ? urlencode($_GET['cat']) : null;
                    $args = array(
                        'post_type' => 'portfolio',
                        'posts_per_page' => 12,
                        'paged' => 1,
                        'post_status' => 'publish',
                        'cat' => $cat,
                        'meta_query' => array(
                            array(
                                'key'     => 'portfolio_type',
                                'value'   => '"'.get_field('portfolio_type').'"',
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
                        $post_idx = 0; 
                        while ( $query->have_posts() ) {
                            $query->the_post();
                            
                            echo get_template_part( 'components/portfolio-item', null, array( 
                                'class' => 'portfolio-item_page',
                                'index' => $post_idx
                            ) );
                            $post_idx++; 
                        }
                    }
                    wp_reset_postdata();
                ?>
            </div>
    
            <?php if (  $query->max_num_pages > 1 ): ?>
                <button class="btn portfolio-page__btn js-portfolio-more" type="button">
                    Show more
                </button>
            <?php endif; ?>
        </div>
    </div>
</section>