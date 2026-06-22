<?php
// On non-English pages prefer the language variant of each catalog field
// (`_es` / `_de`), falling back to the base (English) field.
$cat = function ($name) {
    $lang = mfs_lang();
    if ( $lang !== 'en' ) {
        $v = get_field($name . '_' . $lang, 'options');
        if ( $v !== '' && $v !== null && $v !== false ) {
            return $v;
        }
    }
    return get_field($name, 'options');
};
?>
<div class="menu-catalog">
    <?php lazy_attachment(get_field('menu_catalog_img', 'options'), 'medium'); ?>

    <div class="menu-catalog__info">
        <p class="menu-catalog__title"><?php echo $cat('menu_catalog_title'); ?></p>
        <p><?php echo $cat('menu_catalog_desc'); ?></p>
        <button class="menu-catalog__download js-modal-open" data-modal="download" type="button"><?php echo $cat('menu_catalog_download'); ?></button>
    </div>
</div>
