<section class="design-services">
    <div class="container">
        <div class="design-services__info">
            <h2><?php the_field('title'); ?></h2>  
            <p class="p1"><?php the_field('description'); ?></p>
        </div>

        <div class="design-services__items">
            <?php
                while( have_rows('services')) : the_row();
                    $title = get_sub_field('title');
                    $desc = get_sub_field('desc');
                    $image = get_sub_field('image');
            ?>
                <div class="design-services-item js-reveal">
                    <?php lazy_attachment($image, 'full'); ?>

                    <div class="design-services-item__info">
                        <h3>
                            <?php echo $title; ?>
                        </h3>

                        <div class="p1 design-services-item__text">
                            <?php echo $desc; ?>
                        </div>
                    </div>
                </div>
            <?php
                endwhile; 
            ?>
        </div>
    </div>
</section>