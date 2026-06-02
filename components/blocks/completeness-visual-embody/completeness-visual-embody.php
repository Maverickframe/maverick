<section class="completeness-visual-embody">
    <div class="container">
        <div class="completeness-visual-embody__info">
            <h2><?php the_field('title'); ?></h2>  
            <p class="p1"><?php the_field('description'); ?></p>
        </div>

        <div class="completeness-visual-embody__items">
            <?php
                while( have_rows('items')) : the_row();
                    $title = get_sub_field('title');
                    $desc = get_sub_field('desc');
                    $image = get_sub_field('image');
                    $video = get_sub_field('video');
            ?>
                <div class="js-reveal js-completeness-visual-embody-item completeness-visual-embody-item">
                    <div class="completeness-visual-embody-item__media">
                        <?php if($video): ?>
                            <?php $poster = get_the_post_thumbnail_url($video['id'], 'large'); ?>
                            <video playsinline preload="none" muted loop class="js-video-item-hover lazyload" poster="<?php echo $poster; ?>" data-src="<?php echo esc_url($video['url']); ?>"></video>
                        <?php elseif($image): ?>
                            <?php lazy_attachment($image, 'full'); ?>
                        <?php endif; ?>
                    </div>

                    <div class="completeness-visual-embody-item__info">
                        <h3>
                            <?php echo $title; ?>
                        </h3>

                        <div>
                            <div class="js-desc-text p1 completeness-visual-embody-item__text">
                                <?php echo $desc; ?>
                            </div>
    
                            <button class="js-desc-more completeness-visual-embody-item__more-btn" type="button">More</button>
                        </div>
                    </div>
                </div>
            <?php
                endwhile; 
            ?>
        </div>
    </div>
</section>