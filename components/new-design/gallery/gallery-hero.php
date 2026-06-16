<section class="gallery-hero">
    <div class="container">
        <div class="gallery-hero__main js-reveal">
            <?php // todo: common
                $bc_is_es = function_exists('pll_current_language') && pll_current_language() === 'es';
                $bc_home  = $bc_is_es ? home_url('/es/') : home_url('/');
            ?>
            <ul class="hero-block__breadcrumbs">
                <li><a href="<?php echo esc_url($bc_home); ?>"><?php echo mfs_t('Home', 'Inicio'); ?></a></li>
                <li><span><?php echo mfs_t('Gallery', 'Galería'); ?></span></li>
            </ul>

            <h1 class="gallery-hero__title js-highlight text-highlight"><?php echo get_field('hero_title'); ?></h1>
        </div>
    </div>
</section>