        <?php // GTM (+ HubSpot) now loaded by the delayed loader in inc.delay.php. ?>

        <?php echo get_template_part('components/common/modals/modal-book'); ?>
        <?php echo get_template_part('components/common/modals/modal-book-calendar'); ?>
        <?php echo get_template_part('components/common/modals/modal-download'); ?>

        <?php echo get_template_part('components/common/sticky-cta'); ?>

        <?php // GEO readability (T13): flush the buffered body, relocating the site <header>
              // (mega-menu) to here — after the main content — so source order is content-first.
              // Inert unless enabled (non-prod only). See mfs_defer_menu_* in functions.php. ?>
        <?php mfs_defer_menu_flush(); ?>

        <?php wp_footer(); ?>
    </body>
</html>
