<?php $is_featured = $args['is_featured_row']; ?>
<div class="key-visuals-item <?php if($is_featured): ?>is-featured<?php endif; ?> <?php echo $args['custom_class']; ?>">
    <div class="key-visuals-item__img">
        <?php
            $media = $args['media'];
            if ($media) {
                $mime = $media['mime_type'];

                if (strpos($mime, 'image/') === 0) :
                    echo lazy_attachment($media['id'], 'full');
                ?>
                <?php elseif (strpos($mime, 'video/') === 0) : ?>
                    <?php $poster = get_the_post_thumbnail_url($media['id'], 'large'); ?>
                    <video 
                        <?php if($is_featured): ?>autoplay class="js-video-autoplay"<?php else: ?>class="js-video-item-hover lazyload"<?php endif;?>
                        muted 
                        loop
                        playsinline 
                        preload="none" 
                        poster="<?php echo esc_url($poster); ?>"
                        data-src="<?php echo esc_url($media['url']); ?>"
                    ></video>
                <?php endif;
            }
        ?>
    </div>

    <h3 class="key-visuals-item__title"><?php echo $args['title']; ?></h3>
    <p class="key-visuals-item__desc"><?php echo $args['description']; ?></p>
</div>