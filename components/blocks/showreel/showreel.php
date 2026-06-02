<?php 
$subtitle = get_field('subtitle');
$title = get_field('title');
$description = get_field('description');
$video = get_field('video');
?>
<section class="showreel">
    <div class="container container_small">
        <div class="showreel__info">
            <div class="showreel__subtitle"><?php echo $subtitle; ?></div>
            <h2 class="js-highlight text-highlight"><?php echo $title; ?></h2>

            <div class="showreel__desc"><?php echo $description; ?></div>

            <button class="btn-main fill js-modal-open" data-modal="book" type="button">Book a call</button>
        </div>

        <?php if($video): ?>
            <div class="showreel__video">
                <?php echo $video; ?>
            </div>
        <?php endif; ?>
    </div>
</section>