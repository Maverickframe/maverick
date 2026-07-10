<?php // The site header must NEVER be hidden-by-default: it used to be a `.js-reveal`
      // (opacity:0 until reveal.js adds `.is-in`). WP Rocket Remove-Unused-CSS strips
      // runtime classes like `.is-in` from the inline Used CSS, so the reveal-visible
      // rule could vanish and leave the whole nav invisible ("menu disappeared"). The
      // primary navigation is now always visible; entrance animation intentionally dropped. ?>
<header class="header <?php if (isset($args['class']))
    echo $args['class']; ?>">
    <div class="container container_small header__container">
        <?php // Logo carries skip-lazy + data-no-lazy so WP Rocket LazyLoad never rewrites it
              // to data-lazy-src (that late swap once made PSI mobile pick the logo as a
              // late LCP — report r0rjsv2f68). On the FRONT PAGE the hero marquee sits below
              // the fold on mobile, so the (tiny SVG) logo IS the mobile LCP → give it
              // fetchpriority=high. It's ~a few KB, so sharing high priority with the desktop
              // hero LCP slide is negligible. Inner pages keep no hint (their LCP is a hero image). ?>
        <?php if (is_front_page()): ?>
            <span class="header__logo logo">
                <img src="<?php echo get_template_directory_uri_vite(); ?>/img//logo.svg"
                    alt="Maverick Frame Studio logo" width="44" height="44"
                    class="skip-lazy" data-no-lazy="1" fetchpriority="high">
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