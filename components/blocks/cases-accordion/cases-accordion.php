<?php
    $link = get_field('link');
?>
<section class="cases-accordion">
    <div class="container">
        <div class="cases-accordion__info">
            <h2><?php the_field('title'); ?></h2>
            <?php if(get_field('description')): ?>
                <div class="cases-accordion__description">
                    <?php the_field('description'); ?>
                </div>
            <?php endif; ?>

            <?php if ( is_array($link) && ! empty($link['url']) ) : ?>
                <a href="<?php echo $link['url']; ?>" target="<?php echo $link['target'] ?: '_self'; ?>" class="btn-main"><?php echo $link['title']; ?></a>
            <?php endif; ?>
        </div>
    </div>

    <div class="js-reveal cases-accordion__items">
        <div class="mfs-snap" role="group" aria-label="<?php the_field('title'); ?> Slider">
            <ul class="mfs-snap__track">
                <?php
                    while( have_rows('items')) : the_row();
                        $case = get_sub_field('case');
                        $case = ( $case instanceof WP_Post ) ? $case : null;
                        $title = get_sub_field('title') ?: ( $case ? $case->post_title : '' );
                        $description = get_sub_field('description') ?: ( $case ? get_the_excerpt($case->ID) : '' );
                        $image = get_sub_field('image') ?: ( $case ? get_post_thumbnail_id($case->ID) : 0 );
                        $numbers = get_sub_field('numbers');
                        $case_id = $case ? $case->ID : 0;
                        if ( ! $case_id ) {
                            // Polylang returns null for untranslated cases; fall back to the
                            // raw stored post ID so the link points to the EN case.
                            $raw_case = get_sub_field('case', false);
                            if ( is_numeric($raw_case) ) {
                                $case_id = (int) $raw_case;
                            } elseif ( is_array($raw_case) && ! empty($raw_case[0]) && is_numeric($raw_case[0]) ) {
                                $case_id = (int) $raw_case[0];
                            }
                        }
                        $case_url = $case_id ? get_permalink($case_id) : '';
                ?>
                    <li class="mfs-snap__item">
                        <div class="cases-accordion-item">
                            <?php lazy_attachment($image, 'large'); ?>

                            <div class="cases-accordion-item__info">
                                <h3 class="cases-accordion-item__title">
                                    <?php echo $title; ?>
                                </h3>

                                <div class="cases-accordion-item__desc">
                                    <?php echo $description; ?>
                                </div>

                                <ul class="cases-accordion-item__numbers">
                                    <?php
                                        if($numbers) :
                                            foreach($numbers as $number_item) :
                                                $number = $number_item['number'];
                                                $description = $number_item['description'];
                                    ?>
                                        <li>
                                            <p class="cases-accordion-item__numbers-num">
                                                <?php echo $number; ?>
                                            </p>
                                            <p class="cases-accordion-item__numbers-desc">
                                                <?php echo $description; ?>
                                            </p>
                                        </li>
                                    <?php
                                            endforeach; 
                                        endif;
                                    ?>
                                </ul>

                                <?php if ( $case_url ) : ?>
                                    <a href="<?php echo esc_url($case_url); ?>" class="cases-accordion-item__link btn-main fill" target="_blank">
                                        <?php echo mfs_t('Read the story', 'Ver el caso', 'Zur Fallstudie'); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>

            <div class="mfs-snap__dots"></div>
        </div>
    </div>
</section>
