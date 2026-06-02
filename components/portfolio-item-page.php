<?php 
    $category = get_the_category(); 
    $catNames = array();

    foreach ($category as $cat) {
        if($cat->term_id != 1 ) {
            array_push($catNames, $cat->name);
        }
    }
?>

<div class="inner-page portfolio-item-page">
    <div class="container">
        <section class="portfolio-item-page__hero">
            <?php echo get_the_post_thumbnail(get_the_ID(), 'full', [
                 'fetchpriority' => 'high'
            ]); ?>
            
            <div class="portfolio-item-page__main">
                <div class="portfolio-item-page__info">
                    <h1 class="portfolio-item-page__title"><?php the_title(); ?> | <?php echo implode(', ', $catNames); ?></h1>
                    <?php if(get_field('country')): ?>
                        <div class="portfolio-item-page__row"><h2>Country</h2> <?php the_field('country'); ?></div>
                    <?php endif; ?>

                    <?php if(get_field('area')): ?>
                        <div class="portfolio-item-page__row"><h2>Area</h2> <?php the_field('area'); ?></div>
                    <?php endif; ?>

                    <?php if(get_field('text_field')): ?>
                        <div class="portfolio-item-page__row"><?php the_field('text_field'); ?></div>
                    <?php endif; ?>
                </div>
                <?php 
                    $portfolioType = gettype(get_field('portfolio_type')) === 'array'
                        ? get_field('portfolio_type')
                        : array();
                    if($portfolioType === 'common' || (in_array('common', $portfolioType))): 
                ?>
                    <div class="portfolio-item-page__links pip-next-prev">
                        <?php 
                            $next = get_previous_post();
                            $previous = get_next_post();
                        ?>

                        <?php if ( $next ): ?>
                            <a href="<?php echo get_permalink($next); ?>" class="pip-next-prev__link next">
                                <span>next</span>
                            </a>
                        <?php endif; ?>

                        <?php if ( $previous ): ?>
                            <a href="<?php echo get_permalink($previous); ?>" class="pip-next-prev__link prev">
                                <span>prev</span>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        
        <?php echo get_template_part( 'components/portfolio-item-page-vizuals'); ?>
    </div>
</section>
