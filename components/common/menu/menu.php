<nav class="js-menu menu">
    <ul class="menu__list">
        <?php
            // EN/ES menus are ACF-options-driven (menu_items / menu_items_es). The DE menu is
            // defined in code — see mfs_menu_rows_de() in functions.php — because its ACF
            // repeater had drifted to 2 broken items. Both paths render the same menu-item.php.
            $mfs_menu_lang = mfs_lang();

            if ( $mfs_menu_lang === 'de' && function_exists( 'mfs_menu_rows_de' ) ) {
                foreach ( mfs_menu_rows_de() as $mfs_row ) {
                    get_template_part( 'components/common/menu/menu-item', null, $mfs_row );
                }
            } else {
                // Multilingual: on non-English pages use the language menu (menu_items_es /
                // menu_items_de) when it has been filled in; otherwise fall back to the
                // default English menu (menu_items).
                $menu_field = 'menu_items';
                if ( $mfs_menu_lang !== 'en' && get_field( 'menu_items_' . $mfs_menu_lang, 'options' ) ) {
                    $menu_field = 'menu_items_' . $mfs_menu_lang;
                }
                while ( have_rows( $menu_field, 'options' ) ) : the_row();
                    $link = get_sub_field( 'link' );
                    get_template_part( 'components/common/menu/menu-item', null, [
                        'keyname'       => get_sub_field( 'keyname' ),
                        'label'         => get_sub_field( 'label' ),
                        'desktop_label' => get_sub_field( 'desktop_label' ),
                        'permalink'     => $link ? get_permalink( is_object( $link ) ? $link->ID : $link ) : null,
                        'groups_links'  => get_sub_field( 'groups_links' ) ?: [],
                    ] );
                endwhile;
            }
        ?>
    </ul>

    <button class="menu__cta btn-main js-modal-open" data-modal="bookcall" type="button">
        <?php echo mfs_t('Book a call', 'Reservar una llamada', 'Beratung buchen'); ?>
    </button>
</nav>
