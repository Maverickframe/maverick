<div class="container">
    <section class="cta js-reveal">
        <div class="cta__container">
            <h2><?php the_field('title'); ?></h2>  
            <p class="p1"><?php the_field('description'); ?></p>

            <button class="btn-cta js-modal-open" data-modal="book" type="button">
                <?php echo mfs_t('Book a call', 'Reservar una llamada'); ?>
            </button>
        </div>
    </section>
</div>