<?php // The site header must NEVER be hidden-by-default: it used to be a `.js-reveal`
      // (opacity:0 until reveal.js adds `.is-in`). WP Rocket Remove-Unused-CSS strips
      // runtime classes like `.is-in` from the inline Used CSS, so the reveal-visible
      // rule could vanish and leave the whole nav invisible ("menu disappeared"). The
      // primary navigation is now always visible; entrance animation intentionally dropped. ?>
<header class="header <?php if (isset($args['class']))
    echo $args['class']; ?>">
    <div class="container container_small header__container">
        <?php // No fetchpriority on the logo (it competed with the hero LCP image for
              // early bandwidth), BUT it must carry data-no-lazy + skip-lazy: it turned
              // out fetchpriority="high" was what kept WP Rocket LazyLoad away — once
              // removed, Rocket rewrote the logo to data-lazy-src and PSI mobile picked
              // the late-swapping logo as the LCP element (report r0rjsv2f68, 07-02). ?>
        <?php if (is_front_page()): ?>
            <span class="header__logo logo">
                <img src="<?php echo get_template_directory_uri_vite(); ?>/img//logo.svg"
                    alt="Maverick Frame Studio logo" width="44" height="44"
                    class="skip-lazy" data-no-lazy="1">
            </span>
        <?php else: ?>
            <a href="<?php echo home_url(); ?>" class="header__logo logo">
                <img src="<?php echo get_template_directory_uri_vite(); ?>/img//logo.svg"
                    alt="Maverick Frame Studio logo" width="44" height="44"
                    class="skip-lazy" data-no-lazy="1">
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