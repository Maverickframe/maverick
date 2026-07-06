<section class="worldwide-rendering js-reveal">
    <div class="container container_small">
        <div class="worldwide-rendering__info">
            <div class="worldwide-rendering__title">
                <?php if(get_field('subtitle')): ?><p class="section-subtitle"><?php the_field('subtitle'); ?></p><?php endif; ?>
                <h2><?php the_field('title'); ?></h2>
            </div>

            <div class="worldwide-rendering__desc">
                <?php the_field('description'); ?>
            </div>

            <button class="btn-main js-modal-open" data-modal="book" type="button">
                <?php echo mfs_t('Book a call', 'Reservar una llamada', 'Beratung buchen'); ?>
            </button>
        </div>

        <div class="js-render-reveal worldwide-rendering__particle-wrapper" data-img="<?php echo esc_url( get_field('img') ); ?>">
            <img src="<?php echo esc_url( get_field('img') ); ?>" alt="<?php the_field('title'); ?>" loading="lazy" decoding="async">
            <span class="worldwide-rendering__hint" aria-hidden="true">
                <?php echo esc_html( mfs_t('Move to reveal the render', 'Mueve para revelar el render', 'Bewegen und Render enthüllen') ); ?>
            </span>
        </div>
    </div>
</section>