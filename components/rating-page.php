<div class="inner-page rating-page">
    <div class="container">

        <section class="rating-page__hero">
            <h1 class="rating-page__title"><span class="number"><?php echo get_field('number'); ?></span><span class="title"><?php echo get_field('title'); ?></span></h1>
        </section>
    
        <div class="rating-page__items">
            <?php
                while( have_rows('studios')) : the_row();
                    $title = get_sub_field('title');
                    $description = get_sub_field('description');
                    $img = get_sub_field('image');
                    $link = get_sub_field('link');
                ?>
                <section class="rating-item">
                    <div class="rating-item__main">
                        <h2 class="rating-item__title"><?php echo $title; ?></h2>

                        <div class="rating-item__desc">
                            <?php echo $description; ?>
                        </div>
                    </div>
    
                    <div class="rating-item__img">
                        <?php lazy_attachment($img, 'full'); ?>
                        <?php if(!empty($link)): ?>
                        <a href="<?php echo $link; ?>" class="btn rating-item__link">
                            <span>VIEW PORTFOLIO</span>
                        </a>
                        <?php endif; ?>
                    </div>
                </section>
            <?php
                endwhile; 
            ?>
        </div>
    </div>
</div>