<section class="team-awards">
    <div class="container container_small">
        <div class="team-awards__info">
            <p class="section-subtitle"><?php the_field('awards_subtitle'); ?></p>
            <h2 class="team-awards__title"><?php the_field('awards_title'); ?></h2>
            <p class="team-awards__description"><?php the_field('awards_description'); ?></p>
        </div>
        
        <div class="team-awards__items">
            <?php
                while( have_rows('awards_items')) : the_row();
                    $years = get_sub_field('years');
                    $image = get_sub_field('image');
                    $title = get_sub_field('title');
                    $nomination = get_sub_field('nomination');
                    $description = get_sub_field('description');
            ?>
                <div class="team-award js-reveal">
                    <span class="team-award__year">
                        <?php echo $years; ?>
                    </span>

                    <div class="team-award__img">
                        <?php echo lazy_attachment($image, 'full'); ?>
                    </div>

                    <h3 class="team-award__title">
                        <?php echo $title; ?>
                    </h3>

                    <p class="team-award__nomination">
                        <?php echo $nomination; ?>
                    </p>

                    <p class="team-award__description">
                        <?php echo $description; ?>
                    </p>
                </div>
            <?php
                endwhile; 
            ?>
        </div>
    </div>
</section>