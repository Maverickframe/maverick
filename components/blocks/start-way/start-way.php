<section class="start-way">
    <div class="container container_small">
        <div class="start-way__info">
            <p class="section-subtitle"><?php the_field('subtitle'); ?></p>
            <h2><?php the_field('title'); ?></h2>  
            <?php if(get_field('description')): ?>
                <div class="start-way__desc"><?php the_field('description'); ?></div>
            <?php endif; ?>
            <?php if(get_field('check_title')): ?>
                <p class="start-way__check-title">
                    <?php echo inline_svg('icons/check.svg'); ?>
                    <?php the_field('check_title'); ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="start-way__items-container">
            <?php if(get_field('marked_title')): ?>
                <em class="start-way__marked-title mark"><?php the_field('marked_title'); ?></em>
            <?php endif; ?>
            <div class="start-way__items">
                <?php
                    while( have_rows('items')) : the_row();
                        $title = get_sub_field('title');
                        $desc = get_sub_field('description');
                ?>
                    <div class="start-way-item js-reveal">
                        <h3 class="start-way-item__title"><?php echo $title; ?></h3>

                        <p class="start-way-item__desc">
                            <?php echo $desc; ?>
                        </p>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="start-way__cta">
            <?php
                $cta = get_field('cta');
                if ($cta) :
                    $cta_title = $cta['title'] ?? null;
                    $cta_desc = $cta['description'] ?? null;
                    $cta_img = $cta['image'] ?? null;
            ?>
                <?php if ($cta_img) : ?>
                    <?php echo lazy_attachment($cta_img, 'full'); ?>
                <?php endif; ?>

                <div class="start-way__cta-info">
                    <?php if ($cta_title) : ?>
                        <h3><?php echo $cta_title; ?></h3>
                    <?php endif; ?>

                    <?php if ($cta_desc) : ?>
                        <div><?php echo $cta_desc; ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="start-way__cta-btns">
                <button class="btn-main js-modal-open" data-modal="book" type="button">
                    <?php echo mfs_t('Book a call', 'Reservar una llamada', 'Beratung buchen'); ?>
                    <?php echo inline_svg('icons/arrow-open.svg'); ?>
                </button>
                <button class="btn-secondary js-modal-open" data-modal="book" type="button">
                    Send project materials
                </button>
            </div>
        </div>
    </div>
</section>