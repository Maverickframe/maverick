<section class="workflow">
    <h2 class="section-title section-title_workflow"><?php the_field('workflow_title'); ?></h2>

    <div class="workflow__container">
        <div class="workflow__items">
            <div class="js-workflow-slider splide" role="group" aria-label="<?php the_field('workflow_title'); ?>">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php
                            while( have_rows('workflow_items')) : the_row();
                                $title = get_sub_field('title');
                                $description = get_sub_field('description');
                                $img = get_sub_field('img');
                        ?>
                            <li class="splide__slide">
                                <div class="workflow-item">
                                    <div class="workflow-item__info">
                                        <h3 class="workflow-item__title"><?php echo $title; ?></h3>
                                        <div class="workflow-item__desc">
                                            <?php echo $description; ?>
                                        </div>
                                    </div>
            
                                    <?php if ($img): ?>
                                    <div class="workflow-item__img"><?php lazy_attachment($img, 'full'); ?></div>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php
                            endwhile; 
                        ?>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="workflow__desktop-imgs">
            <?php
                while( have_rows('workflow_desktop_imgs')) : the_row();
                    $img = get_sub_field('img');
            ?>
                <div class="workflow__desktop-img"><?php lazy_attachment($img, 'full'); ?></div>
            <?php
                endwhile; 
            ?>
        </div>
    </div>
</section>