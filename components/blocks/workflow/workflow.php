<section class="workflow <?php if(get_field('show_on_mobile')): ?>workflow_mobile<?php endif; ?>">
    <div class="container">
        <p class="workflow__subtitle"><?php the_field('subtitle'); ?></p>
        <h2><?php echo get_field('title'); ?></h2>

        <div class="workflow__start">
            <?php echo get_field('description'); ?>
        </div>

         <div class="workflow__items">
            <?php
                while( have_rows('flow')) : the_row();
                    $title = get_sub_field('title');
                    $description = get_sub_field('desc');
                    $side = get_sub_field('side') ?: 'left';
                    $offset_md = get_sub_field('offset_md');
                    $offset_lg = get_sub_field('offset_lg');
                    $mt_md = get_sub_field('mt_md');
                    $mt_lg = get_sub_field('mt_lg');
                    $connector_md = get_sub_field('connector_md');
                    $connector_lg = get_sub_field('connector_lg');
                    $max_width_md = get_sub_field('max_width_md');
                    $max_width_lg = get_sub_field('max_width_lg');
            ?>
                <div 
                    class="workflow-item js-workflow-item"
                    style="
                        --offset-md: <?php echo $offset_md; ?>px;
                        --offset-lg: <?php echo $offset_lg; ?>px;
                        --mt-md: <?php echo $mt_md; ?>px;
                        --mt-lg: <?php echo $mt_lg; ?>px;
                        --connector-md: <?php echo $connector_md; ?>px;
                        --connector-lg: <?php echo $connector_lg; ?>px;
                        --max-width-md: <?php echo $max_width_md; ?>px;
                        --max-width-lg: <?php echo $max_width_lg; ?>px;
                    "
                    data-side="<?php echo $side; ?>"
                >
                    <span class="dot"></span>
                    <span class="connector"></span>

                    <div class="workflow-item__content">
                        <h3><?php echo $title; ?></h3>
                        <p><?php echo $description; ?></p>
                    </div>
                </div>
            <?php
                endwhile; 
            ?>

            <div class="workflow__line">
                <span class="desktop-big">
                    <?php echo inline_svg('icons/line-big.svg'); ?>
                </span>

                <span class="desktop">
                    <?php echo inline_svg('icons/line.svg'); ?>
                </span>

                <span class="mobile">
                    <?php echo inline_svg('icons/line-mob.svg'); ?>
                </span>
            </div>
         </div>
    </div>
</section>