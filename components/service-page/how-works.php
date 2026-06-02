<section class="service-page-how-works">
    <h2 class="service-page-how-works__title"><?php the_field('how-works_title'); ?></h2>

    <div class="service-page-how-works__items">
        <?php
            while( have_rows('how-works_items')) : the_row();
                $title = get_sub_field('title');
                $description = get_sub_field('description');
                $img = get_sub_field('img');
        ?>
            <div class="service-page-how-works-item">
                <div class="service-page-how-works-item__info">
                    <h3 class="service-page-how-works-item__title"><?php echo $title; ?></h3>
                    <div class="service-page-how-works-item__desc">
                        <?php echo $description; ?>
                    </div>
                </div>

                <div class="service-page-how-works-item__img"><?php lazy_attachment($img, 'full'); ?></div>
            </div>
        <?php
            endwhile; 
        ?>
    </div>
</section>