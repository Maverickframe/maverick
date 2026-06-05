<?php
/**
 * Compact author card shown in the left TOC sidebar above "Contents".
 * Builds trust at first glance (E-E-A-T signal) without taking up
 * vertical space the way the bottom-of-article author block does.
 */
$author = get_field('author');
if (!$author) return;
$avatarId = get_field('black_photo', $author->ID);
$avatarUrl = $avatarId ? wp_get_attachment_image_url($avatarId, 'thumbnail') : '';
$name      = get_the_title($author->ID);
$position  = get_field('position', $author->ID);
$linkedin  = get_field('linkedin', $author->ID);
$authorLink = get_permalink($author->ID);
?>
<div class="author-mini">
    <a class="author-mini__avatar" href="<?= esc_url($authorLink); ?>" aria-label="<?= esc_attr($name); ?>">
        <?php if ($avatarUrl): ?>
            <img src="<?= esc_url($avatarUrl); ?>" alt="<?= esc_attr($name); ?>" loading="lazy">
        <?php else: ?>
            <span class="author-mini__avatar-fallback"><?= esc_html(strtoupper(mb_substr($name, 0, 1))); ?></span>
        <?php endif; ?>
    </a>
    <div class="author-mini__body">
        <a class="author-mini__name" href="<?= esc_url($authorLink); ?>"><?= esc_html($name); ?></a>
        <?php if ($position): ?>
            <span class="author-mini__role"><?= esc_html($position); ?></span>
        <?php endif; ?>
    </div>
    <?php if ($linkedin): ?>
        <a class="author-mini__linkedin" href="<?= esc_url($linkedin); ?>" rel="nofollow noopener" target="_blank" aria-label="LinkedIn">
            <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M19 3A2 2 0 0 1 21 5v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14zM8.5 18v-7H6v7h2.5zM7.25 9.75A1.5 1.5 0 1 0 7.25 6.75a1.5 1.5 0 0 0 0 3zM18 18v-3.9c0-2.1-1.1-3.1-2.6-3.1-1.2 0-1.7.7-2 1.1V11H10.9c0 .7 0 7 0 7H13.4v-3.9c0-.2 0-.4.1-.6.2-.4.5-.9 1.2-.9.8 0 1.2.6 1.2 1.5V18H18z"/>
            </svg>
        </a>
    <?php endif; ?>
</div>
