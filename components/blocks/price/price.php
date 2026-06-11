<section class="price">
    <div class="container container_small">
        <div class="price__info">
            <p class="section-subtitle"><?php the_field('subtitle'); ?></p>
            <h2><?php the_field('title'); ?></h2>  
            <?php if(get_field('description')): ?>
                <p class="p1"><?php the_field('description'); ?></p>
            <?php endif; ?>
        </div>

        <div class="price__items">
            <?php
                while( have_rows('pricing')) : the_row();
                    $title = get_sub_field('title');
                    $price = get_sub_field('price');
                    $desc = get_sub_field('description');
                    $best_for = get_sub_field('best_for');
                    $index = get_row_index();
            ?>
                <div class="price-item js-reveal">
                    <div class="price-item__header">
                        <?php echo inline_svg("icons/price-$index.svg"); ?>

                        <?php if($index == 2): ?><span class="price-item__badge"><?php echo mfs_t('Most Popular', 'Más popular'); ?></span><?php endif; ?>
                    </div>

                    <h3 class="price-item__title"><?php echo $title; ?></h3>

                    <p class="price-item__price">
                        <?php echo $price; ?>
                    </p>

                    <p class="price-item__desc">
                        <?php echo $desc; ?>
                    </p>

                    <p class="price-item__best-for-title">
                        <?php echo mfs_t('Best for', 'Ideal para'); ?>
                    </p>

                    <p class="price-item__best-for">
                        <?php echo $best_for; ?>
                    </p>

                    <button class="btn-main fill js-modal-open" data-modal="book" type="button">
                        <?php echo mfs_t('Get Started', 'Empezar'); ?>
                    </button>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="price__cta">
            <?php echo inline_svg('icons/messages.svg'); ?>

            <?php
                $cta = get_field('cta');
                if ($cta) :
                    $cta_title = $cta['title'] ?? null;
                    $cta_desc = $cta['description'] ?? null;
            ?>
                <div class="price__cta-info">
                    <?php if ($cta_title) : ?>
                        <h3><?php echo $cta_title; ?></h3>
                    <?php endif; ?>

                    <?php if ($cta_desc) : ?>
                        <p><?php echo $cta_desc; ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <button class="btn-main js-modal-open" data-modal="book" type="button">
                <?php echo mfs_t('Book a call', 'Reservar una llamada'); ?>
                <?php echo inline_svg('icons/arrow-open.svg'); ?>
            </button>
        </div>
    </div>
</section>