<section class="team-hero">
    <div class="container">
        <div class="team-hero__main js-reveal">
            <?php
                $bc_is_es = function_exists('pll_current_language') && pll_current_language() === 'es';
                $bc_home  = $bc_is_es ? home_url('/es/') : home_url('/');
            ?>
            <ul class="single-team-hero__breadcrumbs">
                <li><a href="<?php echo esc_url($bc_home); ?>"><?php echo mfs_t('Home', 'Inicio'); ?></a></li>
                <li><span><?php echo mfs_t('Team', 'Equipo'); ?></span></li>
            </ul>
            <h1 class="team-hero__title js-highlight text-highlight"><?php echo get_field('main_title'); ?></h1>

            <div class="team-hero__desc">
                <?php the_field('main_description'); ?>
            </div>

            <div class="team-hero__btns">
                <button class="btn-main fill js-modal-open" data-modal="book" type="button"><?php echo mfs_t('Book a call', 'Reservar una llamada'); ?></button>
                <button class="btn-secondary-black fill js-modal-open" data-modal="download" type="button"><?php echo mfs_t('Download Catalog', 'Descargar catálogo'); ?></button>
            </div>
        </div>

        <ul class="team-hero__numbers js-reveal">
            <?php
                while( have_rows('main_numbers')) : the_row();
                    $number = get_sub_field('number');
                    $description = get_sub_field('description');
            ?>
                <li>
                    <p class="team-hero__numbers-num">
                        <?php echo $number; ?>
                    </p>
                    <p class="team-hero__numbers-desc">
                        <?php echo $description; ?>
                    </p>
                </li>
            <?php
                endwhile; 
            ?>
        </ul>
    </div>
</section>