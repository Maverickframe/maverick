<?php 
    $color = get_field('color') ?? 'accent';
    $btnColor = $color === 'accent' ? 'btn-cta' : 'btn-main';
?>

<div class="container">
    <section class="js-reveal small-cta <?php echo $color; ?>">
        <div class="small-cta__info">
            <h2><?php the_field('title'); ?></h2>  
            <p class="p1"><?php the_field('description'); ?></p>
        </div>
        <button class="<?php echo $btnColor; ?> js-modal-open" data-modal="book" type="button">
            <?php the_field('btn_title'); ?>
        </button>
    </section>
</div>