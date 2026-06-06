<?php
$date = get_the_date('F j, Y');
$updated = (get_the_modified_date('Y-m-d') > get_the_date('Y-m-d')) ? get_the_modified_date('F j, Y') : '';
$readTime = get_field('read_time');
$categories = get_the_category();
if ($categories) {
    $categoryName = $categories[0]->name;
}

$heroTitle = get_field('hero_title') ? get_field('hero_title') : get_the_title();

$poster_id = '';
$poster_mobile_id = get_field('background_mobile');
$video_url = '';

if (get_field('video')) {
    $video_url = get_field('video');
    $video_id = attachment_url_to_postid($video_url);
    $poster_id = $video_id ? get_post_thumbnail_id($video_id) : '';
} elseif (get_field('background')) {
    $poster_id = get_field('background');
} else {
    $poster_id = get_post_thumbnail_id(get_the_ID());
}
?>

<section class="hero-block">
    <div class="hero-block__media">
        <?php if ($poster_id || $poster_mobile_id): ?>
            <picture>
                <?php if ($poster_mobile_id): ?>
                    <source media="(max-width: 768px)" srcset="<?= wp_get_attachment_image_url($poster_mobile_id, 'full'); ?>">
                <?php endif; ?>

                <?= wp_get_attachment_image($poster_id, 'full', false, ['fetchpriority' => 'high']); ?>
            </picture>
        <?php endif; ?>

        <?php if ($video_url): ?>
            <video class="js-video-autoplay" muted loop playsinline preload="none" data-src="<?= $video_url; ?>"></video>
        <?php endif; ?>
    </div>

    <div class="container">
        <div class="hero-block__main">
            <div class="hero-block__metabox metabox js-reveal js-reveal-init" data-anim="up">
                <span class="metabox__item metabox__item--text">
                    Published: <?= $date; ?>
                </span>
                <?php if ($updated): ?>
                    <div class="dot"></div>
                    <span class="metabox__item metabox__item--text">
                        Updated: <?= $updated; ?>
                    </span>
                <?php endif; ?>
                <?php if ($readTime): ?>
                    <div class="dot"></div>
                    <span class="metabox__item metabox__item--text">
                        <?= $readTime; ?> read
                    </span>
                <?php endif; ?>
                <?php if ($categories): ?>
                    <div class="dot"></div>
                    <span class="metabox__item metabox__item--text">
                        <?= $categoryName; ?>
                    </span>
                <?php endif; ?>
            </div>

            <h1 class="hero-block__title js-reveal js-reveal-init" data-anim="up"><?= $heroTitle; ?></h1>
        </div>
    </div>
</section>