<?php $has_featured_video = get_field('has_featured_video'); ?>
<section class="key-visuals">
    <div class="container">
        <div class="key-visuals__info">
            <p class="section-subtitle">Key Visual Decisions</p>
            <h2><?php the_field('title'); ?></h2>
        </div>

        <?php if(have_rows('items')): ?>
            <div class="key-visuals__items js-reveal <?php if($has_featured_video): ?>has-featured<?php endif; ?>">
                <?php
                    while( have_rows('items')) : the_row();
                        $title = get_sub_field('title');
                        $description = get_sub_field('description');
                        $media = get_sub_field('media');
                        $custom_class = get_sub_field('custom_class');

                        $is_featured_row = $has_featured_video && get_row_index() === 1;
                        $is_last_row = $has_featured_video && get_row_index() === count(get_field('items'));
                ?>
                    <?php echo get_template_part( 'components/common/key-visual-item', null, [
                        'title' => $title,
                        'description' => $description,
                        'media' => $media,
                        'custom_class' => $custom_class . ' mobile',
                        'is_featured_row' => $is_featured_row
                    ] ); ?>
                    <?php if($is_featured_row): ?>
                        <div class="key-visuals__scroll-items">
                            <?php echo get_template_part( 'components/common/key-visual-item', null, [
                                'title' => $title,
                                'description' => $description,
                                'media' => $media,
                                'custom_class' => $custom_class . ' desktop',
                                'is_featured_row' => $is_featured_row
                            ] ); ?>
                    <?php endif; ?>
                    <?php if($is_last_row): ?>
                        </div>
                    <?php endif; ?>
                <?php
                    endwhile; 
                ?>
            </div>
        <?php
            endif; 
        ?>

        <?php if(have_rows('rows')): ?>
            <div class="key-visuals__rows">
                <?php
                    while( have_rows('rows')) : the_row();
                        $title = get_sub_field('title');
                        $category = get_sub_field('category');
                ?>
                    <div class="key-visuals__row js-reveal">
                        <div class="key-visuals__row-header">
                            <h3>
                                <?php echo $title; ?>
                            </h3>

                            <span>
                                <?php echo $category; ?>
                            </span>
                        </div>

                        <?php if(have_rows('items')): ?>
                            <div class="key-visuals__items <?php if($has_featured_video): ?>has-featured<?php endif; ?>">
                                <?php
                                    while( have_rows('items')) : the_row();
                                        $title = get_sub_field('title');
                                        $description = get_sub_field('description');
                                        $media = get_sub_field('media');
                                        $custom_class = get_sub_field('custom_class');

                                        $is_featured_row = $has_featured_video && get_row_index() === 1;
                                        $is_last_row = $has_featured_video && get_row_index() === count(get_field('items'));
                                ?>
                                    <?php echo get_template_part( 'components/common/key-visual-item', null, [
                                        'title' => $title,
                                        'description' => $description,
                                        'media' => $media,
                                        'custom_class' => $custom_class . ' mobile',
                                        'is_featured_row' => $is_featured_row
                                    ] ); ?>
                                    <?php if($is_featured_row): ?>
                                        <div class="key-visuals__scroll-items">
                                            <?php echo get_template_part( 'components/common/key-visual-item', null, [
                                                'title' => $title,
                                                'description' => $description,
                                                'media' => $media,
                                                'custom_class' => $custom_class . ' desktop',
                                                'is_featured_row' => $is_featured_row
                                            ] ); ?>
                                    <?php endif; ?>
                                    <?php if($is_last_row): ?>
                                        </div>
                                    <?php endif; ?>
                                <?php
                                    endwhile; 
                                ?>
                            </div>
                        <?php
                            endif; 
                        ?>
                    </div>
                <?php
                    endwhile; 
                ?>
            </div>
        <?php
            endif; 
        ?>
    </div>
</section>