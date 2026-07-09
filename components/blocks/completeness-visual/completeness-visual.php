<section class="completeness-visual">
    <div class="container container_small">
        <div class="completeness-visual__info">
            <h2><?php the_field('title'); ?></h2>  
            <p class="p1"><?php the_field('description'); ?></p>
        </div>

        <div class="completeness-visual__items">
            <div class="mfs-snap" role="group" aria-label="<?php the_field('title'); ?> Slider">
                <ul class="mfs-snap__track">
                    <?php
                        while( have_rows('items')) : the_row();
                            $title = get_sub_field('title');
                            $color = get_sub_field('color');
                            $desc = get_sub_field('desc');
                            $image = get_sub_field('image');
                    ?>
                        <li class="mfs-snap__item">
                            <div class="completeness-visual-item js-reveal">
                                <?php lazy_attachment($image, 'full'); ?>

                                <div class="completeness-visual-item__info <?php echo $color === 'black' ? 'black-color' : ''; ?>">
                                    <h3>
                                        <?php echo $title; ?>
                                    </h3>

                                    <div class="p1 completeness-visual-item__text">
                                        <?php echo $desc; ?>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php
                        endwhile; 
                    ?>
                </ul>

                <div class="mfs-snap__dots"></div>
            </div>
        </div>

        <div class="completeness-visual__cta">
            <?php echo inline_svg('icons/messages.svg'); ?>
            <?php echo get_field('cta_text') ?: mfs_t("Need help figuring out your render type? Let's chat!", "¿No sabes qué tipo de render necesitas? ¡Hablemos!", "Unsicher, welcher Render-Typ der richtige ist? Lassen Sie uns sprechen!"); ?>

            <button class="btn-main js-modal-open" data-modal="book" type="button">
                <?php echo mfs_t('Book a call', 'Reservar una llamada', 'Beratung buchen'); ?>
                <?php echo inline_svg('icons/arrow-open.svg'); ?>
            </button>
        </div>
    </div>
</section>