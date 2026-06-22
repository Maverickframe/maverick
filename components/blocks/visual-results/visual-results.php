<section class="visual-results">
    <div class="container">
        <div class="visual-results__info">
            <p class="section-subtitle"><?php echo mfs_eyebrow(get_field('subtitle'), 'Visual Results'); ?></p>
            <h2><?php the_field('title'); ?></h2>
            <div class="p1"><?php the_field('description'); ?></div>
        </div>
    </div>

    <?php $showNum = get_field('initial_items') ?? 7; ?>
    <?php if(get_field('is_presentation')):?>
        <?php if( have_rows('items') ): ?>
            <div class="visual-results__slider js-reveal">
                <div class="js-visual-results-slider splide" role="group" aria-label="Visual Results Slider">
                    <div class="splide__track">
                        <ul class="splide__list">
                            <?php
                                while( have_rows('items') ) : the_row();
                                    $title = get_sub_field('title');
                                    $media = get_sub_field('wireframe');
                                    $video_iframe = get_sub_field('video_iframe');      
                                    $test = get_sub_field('test');      
                            ?>
                                <li class="splide__slide">
                                    <div 
                                        class="visual-results-item is-slider"
                                    >
                                    <?php echo $test; ?>
                                        <?php
                                            if ($video_iframe) {
                                                echo $video_iframe;
                                            } elseif ($media) {
                                                $mime = $media['mime_type'];

                                                if (strpos($mime, 'image/') === 0) :
                                                    echo lazy_attachment($media['id'], 'full');
                                                ?>
                                                <?php elseif (strpos($mime, 'video/') === 0) : ?>
                                                    <?php $poster = get_the_post_thumbnail_url($media['id'], 'large'); ?>
                                                    <video playsinline preload="none" muted loop class="js-video-item-hover lazyload" poster="<?php echo $poster; ?>" data-src="<?php echo esc_url($media['url']); ?>"></video>
                                                <?php endif;
                                            }
                                        ?>
                                        
                                        <?php if($title): ?>
                                        <p class="visual-results-item__title"><?php echo esc_html($title); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>

                    <div class="splide__arrows">
                        <button class="splide__arrow splide__arrow--prev">
                            <span class="sr-only">prev slide</span>
                            <?php echo inline_svg('icons/arrow-left-slider.svg'); ?>
                        </button>
                        <button class="splide__arrow splide__arrow--next">
                            <span class="sr-only">
                                Next slide
                            </span>
                            <?php echo inline_svg('icons/arrow-right-slider.svg'); ?>
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <?php if( have_rows('items') ): ?>
            <div class="container">
                <div class="visual-results__items">
                    <?php
                    $i = 0;
                    while( have_rows('items') ) : the_row();
                        $title = get_sub_field('title');
                        $media = get_sub_field('wireframe');
                        $video_iframe = get_sub_field('video_iframe');    
                        $stretch = get_sub_field('stretch');

                        $i++;
                        $classes = '';

                        if ($stretch) {
                            $classes .= ' stretch';
                        }

                        if($i > $showNum) {
                            $classes .= ' extra-item js-extra-item';
                        } 
                    ?>
                        <div 
                            class="js-reveal visual-results-item<?php echo $classes;?>"
                        >
                            <?php
                                if ($video_iframe) {
                                    echo $video_iframe;
                                } elseif ($media) {
                                    $mime = $media['mime_type'];

                                    if (strpos($mime, 'image/') === 0) :
                                        echo lazy_attachment($media['id'], 'full');
                                    ?>
                                    <?php elseif (strpos($mime, 'video/') === 0) : ?>
                                        <?php $poster = get_the_post_thumbnail_url($media['id'], 'large'); ?>
                                        <video playsinline preload="none" muted loop class="js-video-item-hover lazyload" poster="<?php echo $poster; ?>" data-src="<?php echo esc_url($media['url']); ?>"></video>
                                    <?php endif;
                                }
                            ?>
                            
                            <?php if($title): ?>
                            <p class="visual-results-item__title"><?php echo esc_html($title); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>

                <?php if($i > $showNum): ?>
                    <button class="btn-secondary-black visual-results__btn js-show-more-visuals-btn" type="button"><?php echo mfs_t('Show More', 'Ver más', 'Mehr anzeigen'); ?></button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>