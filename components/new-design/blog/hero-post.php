<?php
$mfs_lang  = mfs_lang();
$mfs_is_es = ( $mfs_lang === 'es' );

if ($mfs_is_es) {
    $mfs_meses = ['1' => 'enero', '2' => 'febrero', '3' => 'marzo', '4' => 'abril', '5' => 'mayo', '6' => 'junio', '7' => 'julio', '8' => 'agosto', '9' => 'septiembre', '10' => 'octubre', '11' => 'noviembre', '12' => 'diciembre'];
    $mfs_ts_pub = get_post_timestamp();
    $date = (int) wp_date('j', $mfs_ts_pub) . ' de ' . $mfs_meses[(string) (int) wp_date('n', $mfs_ts_pub)] . ' de ' . wp_date('Y', $mfs_ts_pub);
    if (get_the_modified_date('Y-m-d') > get_the_date('Y-m-d')) {
        $mfs_ts_mod = get_post_modified_time('U');
        $updated = (int) wp_date('j', $mfs_ts_mod) . ' de ' . $mfs_meses[(string) (int) wp_date('n', $mfs_ts_mod)] . ' de ' . wp_date('Y', $mfs_ts_mod);
    } else {
        $updated = '';
    }
} elseif ($mfs_lang === 'de') {
    $mfs_monate = ['1' => 'Januar', '2' => 'Februar', '3' => 'März', '4' => 'April', '5' => 'Mai', '6' => 'Juni', '7' => 'Juli', '8' => 'August', '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Dezember'];
    $mfs_ts_pub = get_post_timestamp();
    $date = (int) wp_date('j', $mfs_ts_pub) . '. ' . $mfs_monate[(string) (int) wp_date('n', $mfs_ts_pub)] . ' ' . wp_date('Y', $mfs_ts_pub);
    if (get_the_modified_date('Y-m-d') > get_the_date('Y-m-d')) {
        $mfs_ts_mod = get_post_modified_time('U');
        $updated = (int) wp_date('j', $mfs_ts_mod) . '. ' . $mfs_monate[(string) (int) wp_date('n', $mfs_ts_mod)] . ' ' . wp_date('Y', $mfs_ts_mod);
    } else {
        $updated = '';
    }
} else {
    $date = get_the_date('F j, Y');
    $updated = (get_the_modified_date('Y-m-d') > get_the_date('Y-m-d')) ? get_the_modified_date('F j, Y') : '';
}
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
                    <?= mfs_t('Published:', 'Publicado:'); ?> <?= $date; ?>
                </span>
                <?php if ($updated): ?>
                    <div class="dot"></div>
                    <span class="metabox__item metabox__item--text">
                        <?= mfs_t('Updated:', 'Actualizado:'); ?> <?= $updated; ?>
                    </span>
                <?php endif; ?>
                <?php if ($readTime): ?>
                    <div class="dot"></div>
                    <span class="metabox__item metabox__item--text">
                        <?= $readTime; ?><?= mfs_t(' read', ' de lectura'); ?>
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