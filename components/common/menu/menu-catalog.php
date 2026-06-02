<div class="menu-catalog">
    <?php lazy_attachment(get_field('menu_catalog_img', 'options'), 'medium'); ?>

    <div class="menu-catalog__info">
        <p class="menu-catalog__title"><?php echo get_field('menu_catalog_title', 'options'); ?></p>
        <p><?php echo get_field('menu_catalog_desc', 'options'); ?></p>
        <button class="menu-catalog__download js-modal-open" data-modal="download" type="button"><?php echo get_field('menu_catalog_download', 'options'); ?></button>
    </div>
</div>