<section class="result-business-impact">
    <div class="container">
        <h2 class="section-subtitle">Results & Business Impact</h2>

        <?php if(have_rows('items')): ?>
        <?php $hasNumbers = get_field('items')[2]['number']; ?>
            <ul class="result-business-impact__items js-reveal <?php if($hasNumbers):?>numbers<?php endif; ?>">
                <?php
                    while( have_rows('items')) : the_row();
                        $title = get_sub_field('title');
                        $number = get_sub_field('number');
                ?>
                    <li>
                        <?php if($number): ?>
                            <p class="result-business-impact__number js-counter">
                                <?php echo $number; ?>
                            </p>
                            <p>
                                <?php echo $title; ?>
                            </p>
                        <?php else: ?>
                            <p class="result-business-impact__item-text">
                                <?php echo $title; ?>
                            </p>
                        <?php endif; ?>
                    </li>
                <?php
                    endwhile; 
                ?>
            </ul>
        <?php
            endif; 
        ?>

        <?php 
            $video_iframe = get_field('video_iframe');
            $video_url = get_field('video');
            $photo_id = get_field('photo');
            $photo_mob_id = get_field('photo_mob');
        ?>
        <?php if($video_url): ?>
            <?php
                $video_id = attachment_url_to_postid($video_url);
                $poster   = $video_id ? get_the_post_thumbnail_url($video_id, 'large') : '';
                $video_mime = wp_check_filetype($video_url);
                $video_type = !empty($video_mime['type']) ? $video_mime['type'] : 'video/mp4';

                if ($video_type === 'video/quicktime') {
                    $video_type = 'video/mp4';
                }
            ?>
            <div class="result-business-impact__video js-video-item">
                <div class="js-video-anim">
                    <video height="100%" width="100%" loop muted playsinline class="lazyload" preload="none" poster="<?php echo esc_url($poster); ?>">
                        <source src="<?php echo esc_url($video_url); ?>" type="<?php echo esc_attr($video_type); ?>">
                        Ваш браузер не поддерживает видео, обновите
                    </video>
                </div>
            </div>
        <?php elseif($video_iframe): ?>
            <div class="result-business-impact__video-iframe">
                <div class="js-video-anim">
                    <?php echo $video_iframe; ?>
                </div>
            </div>
        <?php elseif($photo_id): ?>
            <div class="result-business-impact__photo js-reveal">
                <picture>
                    <?php if ($photo_mob_id): ?>
                        <source media="(max-width: 768px)" srcset="<?php echo wp_get_attachment_image_url($photo_mob_id, 'full'); ?>">
                    <?php endif; ?>

                    <?php lazy_attachment($photo_id, 'full'); ?>
                </picture>
            </div>
        <?php endif; ?>
    </div>
</section>