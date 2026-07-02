<?php 
$subtitle = get_field('subtitle');
$title = get_field('title');
$description = get_field('description');
$video = get_field('video');

// The `video` field holds raw embed HTML (Bunny iframe) pasted by editors, and it
// comes without a title attribute — PSI flags "<iframe> elements do not have a
// title" (a11y + agentic-browsing). Inject a fallback title here in code instead
// of chasing the field value across EN/ES/DE pages. No-op if a title is present.
if ($video && stripos($video, '<iframe') !== false && stripos($video, 'title=') === false) {
    $video = preg_replace('/<iframe\b/i', '<iframe title="Maverick Frame showreel"', $video, 1);
}
?>
<section class="showreel">
    <div class="container container_small">
        <div class="showreel__info">
            <div class="showreel__subtitle"><?php echo $subtitle; ?></div>
            <h2 class="js-highlight text-highlight"><?php echo $title; ?></h2>

            <div class="showreel__desc"><?php echo $description; ?></div>

            <button class="btn-main fill js-modal-open" data-modal="book" type="button"><?php echo mfs_t('Book a call', 'Reservar una llamada', 'Beratung buchen'); ?></button>
        </div>

        <?php if($video): ?>
            <div class="showreel__video">
                <?php echo $video; ?>
            </div>
        <?php endif; ?>
    </div>
</section>