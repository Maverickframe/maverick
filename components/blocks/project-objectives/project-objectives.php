<section class="project-objectives">
    <div class="container">
        <div class="project-objectives__info">
            <p class="section-subtitle"><?php echo mfs_t('Project Objectives', 'Objetivos del proyecto', 'Projektziele'); ?></p>
            <h2><?php the_field('title'); ?></h2>
        </div>

        <?php if(have_rows('items')): ?>
            <ul class="project-objectives__items js-reveal">
                <?php
                    while( have_rows('items')) : the_row();
                        $description = get_sub_field('description');
                ?>
                    <li>
                        <p>
                            <?php echo $description; ?>
                        </p>
                    </li>
                <?php
                    endwhile; 
                ?>
            </ul>
        <?php
            endif; 
        ?>
    </div>
</section>