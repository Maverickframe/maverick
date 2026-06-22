<section class="service-page-solutions <?php echo $args['classes'] ?? null; ?>">
    <div class="service-page-solutions__main">
        <div class="service-page-solutions__img">
            <?php $video = get_field('solutions_video'); ?>
            
            <?php if($video): ?>
                <video height="100%" width="100%" class="lazyload js-video" autoplay muted loop preload playsinline>
                    <source src="<?php echo $video; ?>" type="video/mp4">
                    Ваш браузер не поддерживает видео, обновите
                </video>
            <?php else: ?>
                <div class="desktop">
                    <?php lazy_attachment(get_field('solutions_image_desktop'), 'full'); ?>
                </div>
                
                <div class="mobile">
                    <?php lazy_attachment(get_field('solutions_image_mobile'), 'full'); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="container service-page-solutions__container">
            <h2 class="service-page-solutions__title"><?php the_field('solutions_title'); ?></h2>

            <div class="service-page-solutions__desc">
                <?php the_field('solutions_subtitle'); ?>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="service-page-solutions__items-container">
            <div class="service-page-solutions__items">
                <div class="js-solutions-slider splide" role="group" aria-label="<?php the_field('solutions_title'); ?>">
                    <div class="splide__track">
                        <ul class="splide__list <?php if (!have_rows('solutions_images')):?>three-rows<?php endif; ?>">
                            <?php
                                while( have_rows('solutions_items')) : the_row();
                                    $title = get_sub_field('title');
                                    $description = get_sub_field('description');
                                    $calendly = get_sub_field('calendly');
                                    $link = get_sub_field('read_more');
                            ?>
                                <li class="splide__slide">
                                    <div class="service-page-solution js-animate">
                                        <h3 class="service-page-solution__title"><?php echo $title; ?></h3>
                                        <div class="service-page-solution__desc">
                                            <?php echo $description; ?>
                                        </div>
            
                                        <?php if(isset($args['download']) && $args['download']): ?>
                                            <button class="service-page-solution__link js-modal-open" data-modal="download" type="button">
                                                <svg width="14.5625rem" height="3rem" viewBox="0 0 233 48" class="border">
                                                    <polyline points="232,1 232,47 1,47 1,1 232,1" class="bg-line" />
                                                    <polyline points="232,1 232,47 1,47 1,1 232,1" class="hl-line" />
                                                </svg>
                                                <?php echo $args['download']; ?>
                                            </button>
                                        <?php elseif($calendly): ?>
                                            <button class="service-page-solution__link js-modal-open" data-modal="book" type="button">
                                                <svg width="14.5625rem" height="3rem" viewBox="0 0 233 48" class="border">
                                                    <polyline points="232,1 232,47 1,47 1,1 232,1" class="bg-line" />
                                                    <polyline points="232,1 232,47 1,47 1,1 232,1" class="hl-line" />
                                                </svg>
                                                <?php echo mfs_t('Discover More', 'Descubre más', 'Mehr entdecken'); ?>
                                            </button>
                                        <?php elseif ($link): ?>
                                            <a href="<?php echo $link; ?>" class="service-page-solution__link">
                                                <svg width="14.5625rem" height="3rem" viewBox="0 0 233 48" class="border">
                                                    <polyline points="232,1 232,47 1,47 1,1 232,1" class="bg-line" />
                                                    <polyline points="232,1 232,47 1,47 1,1 232,1" class="hl-line" />
                                                </svg>
                                                <?php echo mfs_t('Discover More', 'Descubre más', 'Mehr entdecken'); ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="service-page-solution__link">
                                                <svg width="14.5625rem" height="3rem" viewBox="0 0 233 48" class="border">
                                                    <polyline points="232,1 232,47 1,47 1,1 232,1" class="bg-line" />
                                                    <polyline points="232,1 232,47 1,47 1,1 232,1" class="hl-line" />
                                                </svg>
                                                <?php echo mfs_t('Discover More', 'Descubre más', 'Mehr entdecken'); ?>
                                            </span>
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

            <?php if (have_rows('solutions_images')):?>
                <div class="service-page-solutions__imgs">
                    <?php
                        while( have_rows('solutions_images')) : the_row();
                            $img = get_sub_field('img');
                    ?>
                        <?php lazy_attachment($img, 'full'); ?>
                    <?php
                        endwhile; 
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>