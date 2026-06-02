<?php
    $galleryId = $args['gallery_id'];
    $class = $args['class'];

    $media = $args['media'];
    $videoIframe = $args['video_iframe'] ?? '';

    $title = $args['title'];
    $location = $args['location'];
    $client = $args['client'];
    $year = $args['year'];
    $link = $args['link'];

    $isVideoIframe = !empty($videoIframe);

    if ($isVideoIframe) {
        $class .= ' iframe';
    }

    if ($media) {
        $mime = $media['mime_type'];

        $isImage = strpos($mime, 'image/') === 0;
        $isVideo = strpos($mime, 'video/') === 0;

        if ($isVideo) {
            $class .= ' is-video';
        }
    }
?>

<?php if ($media || $isVideoIframe): ?>

    <?php if ($isVideoIframe): ?>
<div class="js-reveal gallery-item <?php echo esc_attr($class); ?>">
    <?php else: ?>
<a
    href="<?php echo esc_url($link ?: $media['url']); ?>"
    <?php if (!$link && $media): ?>
        data-fancybox="fancy-<?php echo esc_attr($galleryId); ?>"
    <?php endif; ?>
    class="js-reveal gallery-item <?php echo esc_attr($class); ?>"
>
<?php endif; ?>

    <?php if ($isVideoIframe): ?>

        <?php echo $videoIframe; ?>

    <?php elseif ($isImage): ?>

        <?php echo lazy_attachment($media['id'], 'full'); ?>

    <?php elseif ($isVideo): ?>

        <?php $poster = get_the_post_thumbnail_url($media['id'], 'large'); ?>

        <video
            class="js-video-item-hover lazyload"
            muted
            loop
            playsinline
            preload="none"
            poster="<?php echo esc_url($poster); ?>"
            data-src="<?php echo esc_url($media['url']); ?>"
        ></video>

    <?php endif; ?>

    <?php if ($title || $location || $client): ?>
        <div class="gallery-item__overlay">

            <div class="gallery-item__overlay-row gallery-item__overlay-row_header">

                <?php if ($title): ?>
                    <h3 class="gallery-item__overlay-title">
                        <?php echo esc_html($title); ?>
                    </h3>
                <?php endif; ?>

                <?php echo inline_svg('icons/arrow-right-accent.svg'); ?>

            </div>

            <?php if ($location): ?>
                <div class="gallery-item__overlay-row">
                    <div>
                        <span>Location:</span>
                        <?php echo esc_html($location); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($client || $year): ?>
                <div class="gallery-item__overlay-row">

                    <?php if ($client): ?>
                        <div>
                            <span>Client</span>:
                            <?php echo esc_html($client); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($year): ?>
                        <?php echo esc_html($year); ?>
                    <?php endif; ?>

                </div>
            <?php endif; ?>

        </div>
    <?php endif; ?>

    <?php if ($isVideoIframe): ?>
</div>
    <?php else: ?>
</a>
    <?php endif; ?>

<?php endif; ?>