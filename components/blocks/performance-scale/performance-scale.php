<?php
    $link = get_field('link');
?>
<section class="performance-scale">
    <div class="container container_small">
        <div class="performance-scale__info">
            <p class="section-subtitle"><?php the_field('subtitle'); ?></p>
            <h2><?php the_field('title'); ?></h2>
        </div>

        <div class="performance-scale__items">
            <?php
                while( have_rows('items')) : the_row();
                    $title = get_sub_field('title');
                    $icon = get_sub_field('icon');
                    $number = get_sub_field('number');
                    $review_link = get_sub_field('review_link');
            ?>
                <div class="performance-scale-item js-reveal">
                    <div class="performance-scale-item__info">
                        <p class="performance-scale-item__title">
                            <?php echo $title; ?>
                        </p>

                        <p class="performance-scale-item__num">
                            <?php echo $number; ?>
                        </p>
                    </div>

                    <?php if ($review_link): ?>
                        <a href="<?php echo $review_link; ?>" class="performance-scale-item__review-link" target="_blank" rel="noopener"><?php lazy_attachment($icon, 'full'); ?></a>
                    <?php else: ?>
                        <span class="performance-scale-item__review-link"><?php lazy_attachment($icon, 'full'); ?></span>
                    <?php endif; ?>
                </div>
            <?php
                endwhile; 
            ?>

            <?php
                $cases = get_field('cases');
                if( $cases ):
                    foreach( $cases as $case ):
                        $image_id = get_post_thumbnail_id($case->ID);
                        $blocks = parse_blocks(get_post_field('post_content', $case->ID));
                        $hero_title = '';
                        foreach ($blocks as $block) {
                            if ($block['blockName'] === 'acf/hero-block') {
                                $hero_title = $block['attrs']['data']['title'] ?? '';
                                break;
                            }
                        }
            ?>
                <div class="performance-scale-case js-reveal">
                    <?php if($image_id): ?>
                        <div class="performance-scale-case__img">
                            <?php echo wp_get_attachment_image($image_id, 'large'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="performance-scale-case__info">
                        <p class="performance-scale-case__title">
                            <?php echo $hero_title; ?>
                        </p>

                        <a href="<?php echo get_permalink($case->ID); ?>" class="performance-scale-case__link" target="_blank">
                            <span><?php echo mfs_t('Read the story', 'Ver el caso'); ?></span>
                            <?php echo inline_svg('icons/arrow-right-menu-white.svg'); ?>
                        </a>
                    </div>
                </div>
            <?php
                    endforeach;
                endif;
            ?>
        </div>
    </div>
</section>