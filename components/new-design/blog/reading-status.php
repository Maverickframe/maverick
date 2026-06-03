<?php
/**
 * Reading-status sidebar widget — populated by blog-v1-enhancements.js
 * with the time remaining and the current section index.
 */
$readTime = get_field('read_time');
$readTimeMin = is_string($readTime) ? (int) preg_replace('/[^0-9]/', '', $readTime) : (int) $readTime;
if ($readTimeMin < 1) $readTimeMin = 5;
?>
<div class="reading-status" data-read-time="<?= (int) $readTimeMin; ?>">
    <span class="reading-status__time" data-reading-remaining>
        <?= (int) $readTimeMin; ?> min read
    </span>
    <span class="reading-status__sep" aria-hidden="true">·</span>
    <span class="reading-status__section" data-reading-section>
        Start
    </span>
</div>
