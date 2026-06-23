<?php
/**
 * Block: Solution Stats (metrics row) — matches Figma node 13759:19085.
 * White section, 3 columns: big number (last symbol accent) + label,
 * separated by thin vertical dividers.
 */

$stats = array(
    array('num' => '10',  'sym' => '+', 'label' => mfs_t('Years in creative production and visual production', 'Años en producción creativa y visual')),
    array('num' => '95',  'sym' => '%', 'label' => mfs_t('On-time project delivery', 'Entrega de proyectos a tiempo')),
    array('num' => '350', 'sym' => '+', 'label' => mfs_t('Brands and agencies supported', 'Marcas y agencias atendidas')),
);
?>
<section class="sol-stats">
    <div class="container container_small">
        <div class="sol-stats__grid">
            <?php foreach ($stats as $s) : ?>
                <div class="sol-stats__item">
                    <p class="sol-stats__num js-counter"><?php echo esc_html($s['num']); ?><span class="sol-stats__sym"><?php echo esc_html($s['sym']); ?></span></p>
                    <p class="sol-stats__label"><?php echo esc_html($s['label']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
