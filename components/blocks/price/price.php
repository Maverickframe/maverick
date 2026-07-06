<section class="price">
    <?php
        $mfs_price_is_front = is_front_page();
        $mfs_tariff_btn = $mfs_price_is_front ? mfs_t('Get a free estimate', 'Solicita un presupuesto gratis', 'Kostenloses Angebot anfordern') : mfs_t('Get Started', 'Empezar', 'Loslegen');
        $mfs_cta_btn = $mfs_price_is_front ? mfs_t('Get a free estimate', 'Solicita un presupuesto gratis', 'Kostenloses Angebot anfordern') : mfs_t('Book a call', 'Reservar una llamada', 'Beratung buchen');
    ?>
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

                        <?php if($index == 2): ?><span class="price-item__badge"><?php echo mfs_t('Most Popular', 'Más popular', 'Am beliebtesten'); ?></span><?php endif; ?>
                    </div>

                    <h3 class="price-item__title"><?php echo $title; ?></h3>

                    <p class="price-item__price">
                        <?php echo $price; ?>
                    </p>

                    <p class="price-item__desc">
                        <?php echo $desc; ?>
                    </p>

                    <p class="price-item__best-for-title">
                        <?php echo mfs_t('Best for', 'Ideal para', 'Ideal für'); ?>
                    </p>

                    <p class="price-item__best-for">
                        <?php echo $best_for; ?>
                    </p>

                    <button class="btn-main fill js-modal-open" data-modal="book" data-offer="<?php echo esc_attr( $title ); ?>" type="button">
                        <?php echo $mfs_tariff_btn; ?>
                    </button>
                </div>
            <?php endwhile; ?>
        </div>

        <?php if ( get_field('cost_q') || get_field('cost_answer') ) : ?>
            <div class="price__cost-answer">
                <?php if ( get_field('cost_q') ) : ?>
                    <h3 class="price__cost-q"><?php the_field('cost_q'); ?></h3>
                <?php endif; ?>

                <?php if ( get_field('cost_answer') ) : ?>
                    <p class="price__cost-answer-text"><?php the_field('cost_answer'); ?></p>
                <?php endif; ?>

                <?php if ( have_rows('cost_rows') ) : ?>
                    <ul class="price__cost-rows">
                        <?php while ( have_rows('cost_rows') ) : the_row();
                            $cost_url = get_sub_field('url');
                            $cost_tag = $cost_url ? 'a' : 'span';
                        ?>
                            <li class="price__cost-row">
                                <<?php echo $cost_tag; ?> class="price__cost-line<?php echo $cost_url ? ' price__cost-line--link' : ''; ?>"<?php echo $cost_url ? ' href="' . esc_url( $cost_url ) . '"' : ''; ?>>
                                    <span class="price__cost-label"><?php the_sub_field('label'); ?></span>
                                    <span class="price__cost-price"><?php the_sub_field('price'); ?></span>
                                </<?php echo $cost_tag; ?>>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php endif; ?>

                <?php if ( get_field('cost_note') ) : ?>
                    <p class="price__cost-note"><?php the_field('cost_note'); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

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

            <button class="btn-main js-modal-open" data-modal="book" data-offer="Undecided" type="button">
                <?php echo $mfs_cta_btn; ?>
                <?php echo inline_svg('icons/arrow-open.svg'); ?>
            </button>
        </div>
    </div>
</section>