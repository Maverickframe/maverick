        <?php if (!defined('IS_VITE_DEVELOPMENT') || IS_VITE_DEVELOPMENT == false): ?>
        <!-- Google Tag Manager -->
        <script>
        window.addEventListener('load', function() {
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-T4JS5BJV');
        });
        </script>
        <!-- End Google Tag Manager -->
         <?php endif; ?>

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
