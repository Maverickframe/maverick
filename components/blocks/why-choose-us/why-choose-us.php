<section class="why-choose-us">
    <div class="container container_small">
        <div class="why-choose-us__info">
            <p class="section-subtitle"><?php echo mfs_eyebrow(get_field('subtitle')); ?></p>
            <h2><?php the_field('title'); ?></h2>  
            <?php if(get_field('description')): ?>
                <p class="p1"><?php the_field('description'); ?></p>
            <?php endif; ?>
        </div>

        <div class="why-choose-us__items">
            <?php
                while( have_rows('challenges')) : the_row();
                    $title = get_sub_field('title');
                    $desc = get_sub_field('description');
                    $icon = get_sub_field('icon');
            ?>
                <div class="why-choose-us-item js-reveal">
                    <?php if ($icon) : ?>
                        <?php lazy_attachment($icon, 'full'); ?>
                    <?php endif; ?>

                    <div class="why-choose-us-item__info">
                        <h3><?php echo $title; ?></h3>
    
                        <p>
                            <?php echo $desc; ?>
                        </p>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="why-choose-us__cta">
            <?php echo inline_svg('icons/messages.svg'); ?>

            <?php
                $cta = get_field('cta');
                if ($cta) :
                    $cta_title = $cta['title'] ?? null;
                    $cta_desc = $cta['description'] ?? null;
                    $cta_advantages = $cta['advantages'] ?? null;
            ?>
                <div class="why-choose-us__cta-info">
                    <?php if ($cta_title) : ?>
                        <h3><?php echo $cta_title; ?></h3>
                    <?php endif; ?>

                    <?php if ($cta_desc) : ?>
                        <p><?php echo $cta_desc; ?></p>
                    <?php endif; ?>

                    <?php if ($cta_advantages) : ?>
                        <ul class="why-choose-us__cta-advantages">
                            <?php foreach ($cta_advantages as $advantage) : ?>
                                <li>
                                    <?php echo inline_svg('icons/check.svg'); ?>
                                    <?php echo $advantage['advantage']; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <button class="btn-main js-modal-open" data-modal="book" type="button">
                <?php echo mfs_t('Book a call', 'Reservar una llamada', 'Beratung buchen'); ?>
                <?php echo inline_svg('icons/arrow-open.svg'); ?>
            </button>
        </div>
    </div>
</section>