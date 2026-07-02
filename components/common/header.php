<header class="js-reveal header <?php if (isset($args['class']))
    echo $args['class']; ?>" data-anim="down">
    <div class="container container_small header__container">
        <?php // No fetchpriority on the logo: it's a tiny SVG that loads instantly
              // anyway, and fetchpriority="high" here competes with the hero LCP
              // image for early bandwidth (PSI wants exactly one high-priority hint). ?>
        <?php if (is_front_page()): ?>
            <span class="header__logo logo">
                <img src="<?php echo get_template_directory_uri_vite(); ?>/img//logo.svg"
                    alt="Maverick Frame Studio logo" width="44" height="44">
            </span>
        <?php else: ?>
            <a href="<?php echo home_url(); ?>" class="header__logo logo">
                <img src="<?php echo get_template_directory_uri_vite(); ?>/img//logo.svg"
                    alt="Maverick Frame Studio logo" width="44" height="44">
            </a>
        <?php endif; ?>

        <button class="header__menu-btn js-menu-btn">
            <span class="sr-only"><?php echo esc_html( mfs_t('Toggle menu', 'Alternar menú', 'Menü umschalten') ); ?></span>
            <span class="burger"><?php echo inline_svg('icons/menu.svg'); ?></span>
            <span class="close"><?php echo inline_svg('icons/close-white.svg'); ?></span>
        </button>

        <?php echo get_template_part('components/common/menu/menu'); ?>
    </div>
</header>