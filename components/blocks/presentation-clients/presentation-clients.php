<section class="presentation-clients">
    <div class="container container_small">
        <div class="presentation-clients__info">
            <h2><?php the_field('title'); ?></h2>
    
            <div class="presentation-clients__desc"><?php the_field('description'); ?></div>

            <div class="presentation-clients__clients js-reveal-group">
                <?php
                    while( have_rows('clients')) : the_row();
                        $image = get_sub_field('image');
                ?>
                    <div class="presentation-clients__clients-img js-reveal-item">
                        <?php lazy_attachment($image, 'full'); ?>
                    </div>
                <?php
                    endwhile; 
                ?>
    
                <p><?php the_field('trusted_by'); ?></p>
            </div>
        </div>
        
        <ul class="presentation-clients__stats js-reveal">
            <?php
                while( have_rows('stats')) : the_row();
                    $title = get_sub_field('title');
                    $number = get_sub_field('number');
            ?>
                <li>
                    <p class="presentation-clients__stats-number"><?php echo $number; ?></p>
                    <p class="presentation-clients__stats-info"><?php echo $title; ?></p>
                </li>
            <?php
                endwhile; 
            ?>
        </ul>
</section>