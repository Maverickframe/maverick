<?php
$articlePermalink = $args['link'] ?? get_permalink($args['id']);
$articleTags = $args['tags'] ?? get_the_tags($args['id']);
if (isset($args['date'])) {
	$articleDate = $args['date'];
} elseif (mfs_is('es')) {
	$mfs_ts = get_post_timestamp($args['id']);
	$mfs_meses = ['1' => 'enero', '2' => 'febrero', '3' => 'marzo', '4' => 'abril', '5' => 'mayo', '6' => 'junio', '7' => 'julio', '8' => 'agosto', '9' => 'septiembre', '10' => 'octubre', '11' => 'noviembre', '12' => 'diciembre'];
	$articleDate = (int) wp_date('j', $mfs_ts) . ' de ' . $mfs_meses[(string) (int) wp_date('n', $mfs_ts)] . ' de ' . wp_date('Y', $mfs_ts);
} elseif (mfs_is('de')) {
	$mfs_ts = get_post_timestamp($args['id']);
	$mfs_monate = ['1' => 'Januar', '2' => 'Februar', '3' => 'März', '4' => 'April', '5' => 'Mai', '6' => 'Juni', '7' => 'Juli', '8' => 'August', '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Dezember'];
	$articleDate = (int) wp_date('j', $mfs_ts) . '. ' . $mfs_monate[(string) (int) wp_date('n', $mfs_ts)] . ' ' . wp_date('Y', $mfs_ts);
} else {
	$articleDate = get_the_date('F j, Y', $args['id']);
}
$articleTitle = $args['title'] ?? get_the_title($args['id']);
$articleExcerpt = $args['excerpt'] ?? get_the_excerpt($args['id']);
$articleAuthor = $args['author'] ?? get_field('author', $args['id']);
$articleTitleTag = $args['title_tag'] ?? 'h3'; // 'p' for cards rendered above the first H2 (heading order)
?>

<article class="case-item<?= $args['class'] ?? null; ?>">
    <a class="case-item__link" href="<?= $articlePermalink; ?>">
        <span class="case-item__img">
            <?php
                // The first hero card on the blog listing is the mobile LCP → eager +
                // fetchpriority (we took LCP off WP Rocket's ATF beacon). Others stay lazy.
                if (!empty($args['eager'])) {
                    eager_attachment(get_post_thumbnail_id($args['id']), 'large', null, true);
                } else {
                    lazy_attachment(get_post_thumbnail_id($args['id']), 'large');
                }
            ?>
        </span>

        <div class="case-item__info">
            <?php if ($articleTags): ?>
                <span class="case-item__tags">
                    <?php foreach ($articleTags as $tag): ?>
                        <span class="case-item__tag">
                            <?= esc_html($tag->name); ?>
                        </span>
                    <?php endforeach; ?>
                </span>
            <?php endif; ?>

            <time datetime="<?= get_the_date('Y-m-d'); ?>" class="case-item__date"><?= $articleDate; ?></time>

            <<?= $articleTitleTag; ?> class="case-item__title<?= $args['class_title'] ?? null; ?>">
                <span><?= $articleTitle; ?></span>
            </<?= $articleTitleTag; ?>>

            <?php if ($articleExcerpt): ?>
                <p class="case-item__excerpt">
                    <?= $articleExcerpt; ?>
                </p>
            <?php endif; ?>

            <div class="case-item__footer">
                <?php if ($articleAuthor): ?>
                    <div class="case-item__author author">
                        <span class="author__avatar" aria-hidden="true">
                            <?php lazy_attachment(get_post_thumbnail_id($articleAuthor->ID), 'thumbnail'); ?>
                        </span>

                        <div class="author__info">
                            <span class="author__name"><?= $articleAuthor->post_title; ?></span>
                            <span class="author__text"><?= get_field('position', $articleAuthor->ID); ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <span class="case-item__arrow">
                    <?= $args['read_more_text'] ?? mfs_t('Read more', 'Leer más', 'Mehr lesen'); ?>

                    <?= inline_svg($args['read_more_icon'] ?? 'icons/arrow-right-accent.svg'); ?>
                </span>
            </div>
        </div>
    </a>
</article>