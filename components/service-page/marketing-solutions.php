<section class="marketing-section">
    <div class="container">
        <div class="marketing-section__info">
            <h2 class="section-title section-title_developers"><?php the_field('marketing_title'); ?></h2>
            <?php if (get_field('marketing_desc')): ?>
                <div class="section-desc"><?php the_field('marketing_desc'); ?></div>
            <?php endif; ?>
        </div>

        <div class="marketing-section__items">
            <?php
                while( have_rows('marketing_items')) : the_row();
                    $title = get_sub_field('title');
                    $description = get_sub_field('description');
                    // $link = get_sub_field('link');
            ?>
                <div class="marketing-item">
                    <h3 class="marketing-item__title">
                        <span>
                            <?php echo $title; ?>
                        </span>
                    </h3>
                    <div class="marketing-item__desc">
                        <?php echo $description; ?>
                    </div>
                </div>
            <?php
                endwhile; 
            ?>
        </div>
    </div>
</section>