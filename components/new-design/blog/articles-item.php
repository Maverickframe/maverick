<?php
$articlePermalink = $args['link'] ?? get_permalink($args['id']);
$articleTags = $args['tags'] ?? get_the_tags($args['id']);
$articleDate = $args['date'] ?? get_the_date('F j, Y', $args['id']);
$articleTitle = $args['title'] ?? get_the_title($args['id']);
$articleExcerpt = $args['excerpt'] ?? get_the_excerpt($args['id']);
$articleAuthor = $args['author'] ?? get_field('author', $args['id']);
?>

<article class="case-item<?= $args['class'] ?? null; ?>">
    <a class="case-item__link" href="<?= $articlePermalink; ?>">
        <span class="case-item__img">
            <?php lazy_attachment(get_post_thumbnail_id($args['id']), 'large'); ?>
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

            <h3 class="case-item__title<?= $args['class_title'] ?? null; ?>">
                <span><?= $articleTitle; ?></span>
            </h3>

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
                    <?= $args['read_more_text'] ?? 'Read more'; ?>

                    <?= inline_svg($args['read_more_icon'] ?? 'icons/arrow-right-accent.svg'); ?>
                </span>
            </div>
        </div>
    </a>
</article>