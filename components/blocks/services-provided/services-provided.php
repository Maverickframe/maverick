<div class="container">
    <section class="services-provided js-reveal">
        <div class="services-provided__info">
            <p class="section-subtitle white"><?php echo mfs_t('Services Provided', 'Servicios prestados'); ?></p>
            <h2><?php the_field('title'); ?></h2>

            <?php if(get_field('description')): ?>
                <p class="p1"><?php the_field('description'); ?></p>
            <?php endif; ?>
        </div>

        <?php if(have_rows('items')): ?>
            <div class="services-provided__links">
                <?php
                    while( have_rows('items')) : the_row();
                        $title = get_sub_field('title');
                        $link = get_sub_field('link');
                ?>
                    <a href="<?php echo $link; ?>"><span><?php echo $title; ?></span> <?php echo inline_svg('icons/arrow-right-menu.svg'); ?></a>
                <?php
                    endwhile; 
                ?>
            </div>
        <?php
            endif; 
        ?>
    </section>
</div>