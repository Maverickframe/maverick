<section class="team-performance">
    <div class="container">
        <div class="team-performance__info">
            <p class="section-subtitle"><?php the_field('performance_subtitle'); ?></p>
            <h2><?php the_field('performance_title'); ?></h2>
        </div>

        <ul class="team-performance__items">
            <?php
                while( have_rows('performance_numbers')) : the_row();
                    $number = get_sub_field('number');
                    $description = get_sub_field('description');
            ?>
                <li class="team-perf-item js-reveal">
                    <p class="team-perf-item__num js-counter">
                        <?php echo $number; ?>
                    </p>
                    <p class="team-perf-item__description">
                        <?php echo $description; ?>
                    </p>
                </li>
            <?php
                endwhile; 
            ?>
        </ul>
    </div>
</section>