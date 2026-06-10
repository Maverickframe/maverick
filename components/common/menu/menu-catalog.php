<?php
// On /es/ pages prefer the `_es` variant of each catalog field, falling back to the base field.
$cat = function ($name) {
    if ( function_exists('pll_current_language') && pll_current_language() === 'es' ) {
        $es = get_field($name . '_es', 'options');
        if ( $es !== '' && $es !== null && $es !== false ) {
            return $es;
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
