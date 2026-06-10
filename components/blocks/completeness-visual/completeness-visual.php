<section class="completeness-visual">
    <div class="container container_small">
        <div class="completeness-visual__info">
            <h2><?php the_field('title'); ?></h2>  
            <p class="p1"><?php the_field('description'); ?></p>
        </div>

        <div class="completeness-visual__items">
            <div class="js-completeness-visual-slider splide" role="group" aria-label="<?php the_field('title'); ?> Slider">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php
                            while( have_rows('items')) : the_row();
                                $title = get_sub_field('title');
                                $color = get_sub_field('color');
                                $desc = get_sub_field('desc');
                                $image = get_sub_field('image');
                        ?>
                            <li class="splide__slide">
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
                </div>
            </div>
        </div>

        <div class="completeness-visual__cta">
            <?php echo inline_svg('icons/messages.svg'); ?>
            <?php echo get_field('cta_text') ?? "Need help figuring out your render type? Let's chat!"; ?>

            <button class="btn-main js-modal-open" data-modal="book" type="button">
                <?php echo mfs_t('Book a call', 'Reservar una llamada'); ?>
                <?php echo inline_svg('icons/arrow-open.svg'); ?>
            </button>
        </div>
    </div>
</section>