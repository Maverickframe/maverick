<section class="gallery-hero">
    <div class="container">
        <div class="gallery-hero__main js-reveal">
            <?php // todo: common
                $bc_lang = mfs_lang();
                $bc_home = $bc_lang === 'es' ? home_url('/es/') : ( $bc_lang === 'de' ? home_url('/de/') : home_url('/') );
            ?>
            <ul class="hero-block__breadcrumbs">
                <li><a href="<?php echo esc_url($bc_home); ?>"><?php echo mfs_t('Home', 'Inicio'); ?></a></li>
                <li><span><?php echo mfs_t('Gallery', 'Galería'); ?></span></li>
            </ul>

            <h1 class="gallery-hero__title js-highlight text-highlight"><?php echo get_field('hero_title'); ?></h1>
        </div>
    </div>
</section>