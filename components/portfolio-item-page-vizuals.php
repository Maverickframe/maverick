<section class="portfolio-item-page__vizuals pip-vizual js-pip">
    <div class="pip-vizual__container">
        <?php
            while( have_rows('gallery')) : the_row();

                $img = get_sub_field('img');
                $panorama = get_sub_field('panorama');
                $video = get_sub_field('video');

                $is_single = get_sub_field('is_image') != 'Grid' ? true : false;
        ?>

            <?php if($is_single && $img): ?>
                <a href="<?php echo wp_get_attachment_image_url( $img, 'full') ;?>" class="pip-vizual__item" aria-label="Open image in lightbox">  
                    <?php lazy_attachment($img, 'full'); ?>
                </a>
            <?php endif; ?>

            <?php if($is_single && $panorama): ?>
                <div class="pip-vizual__item" style="width: 100%; height: 640px; border: none; max-width: 100%;" >
                    <iframe 
                        style="transform: scale(<?php if(get_sub_field('scale')): ?><?php the_sub_field('scale')?><?php else: ?>1<?php endif; ?>)"
                        width="100%" 
                        height="640" 
                        frameborder="0" 
                        allowfullscreen 
                        allow="xr-spatial-tracking; gyroscope; accelerometer" 
                        scrolling="no" 
                        data-src="<?php echo $panorama; ?>"
                        loading="lazy"
                        class="lazyload">
                    </iframe>
                </div>
            <?php endif; ?>

            <?php if($is_single && $video): ?>
                <div class="pip-vizual__item">
                    <video height="100%" width="100%" class="lazyload" autoplay controls loop preload="none">
                        <source src="<?php echo $video; ?>" type="video/mp4">
                        Ваш браузер не поддерживает видео, обновите
                    </video>
                </div>
            <?php endif; ?>

            <?php if(!$is_single && have_rows('grid')): ?>
                <div class="pip-vizual__grid">
                    <?php
                            while( have_rows('grid')) : the_row();

                                $img = get_sub_field('grid_img');
                                if($img):
                    ?>
                        <a href="<?php echo wp_get_attachment_image_url( $img, 'full') ;?>"  class="pip-vizual__item" aria-label="Open image in lightbox">
                            <?php lazy_attachment($img, 'full'); ?>
                        </a>
                    <?php
                        endif;
                        endwhile; 
                    ?>
                </div>
            <?php 
                endif;
            ?>
        <?php
            endwhile; 
        ?>
    </div>
</section>