<?php
/**
 * Block: Solution Benefits (4 cards) — "You won't miss"
 * Layout-first (matches Figma node 23518:5487).
 */

$eyebrow = get_field('eyebrow') ?: mfs_t('You won’t miss', 'Lo que sí tendrás');
$title   = get_field('title') ?: mfs_t('Built to support agency delivery at scale', 'Diseñado para sostener la entrega de las agencias a escala');
$desc    = get_field('description') ?: mfs_t('Marketing agencies rely on Maverick Frame when campaigns require scalable digital content production and reliable marketing content production. Our workflow ensures agencies can deliver complex visual campaigns without internal production bottlenecks.', 'Las agencias de marketing confían en Maverick Frame cuando las campañas exigen una producción de contenido digital escalable y una producción de contenido de marketing fiable. Nuestro flujo de trabajo permite a las agencias entregar campañas visuales complejas sin cuellos de botella de producción internos.');

$cards = array(
    array('variant' => 'grey',    'text' => mfs_t('Faster turnaround', 'Entregas más rápidas')),
    array('variant' => 'outline', 'text' => mfs_t('Scale creative production capacity up or down depending on campaign volume', 'Escala la capacidad de producción creativa según el volumen de cada campaña')),
    array('variant' => 'dark',    'text' => mfs_t('Campaign-ready assets', 'Assets listos para campaña')),
    array('variant' => 'accent',  'text' => mfs_t('Structured collaboration', 'Colaboración estructurada')),
);
?>
<section class="sol-ben">
    <div class="container container_small">
        <div class="sol-ben__head">
            <div class="sol-ben__head-main">
                <span class="sol-ben__eyebrow"><span class="sol-ben__dot"></span><?php echo esc_html($eyebrow); ?></span>
                <h2 class="sol-ben__title"><?php echo esc_html($title); ?></h2>
            </div>
            <p class="sol-ben__desc"><?php echo esc_html($desc); ?></p>
        </div>

        <div class="sol-ben__grid">
            <?php foreach ($cards as $c) : ?>
                <div class="sol-ben__card sol-ben__card--<?php echo esc_attr($c['variant']); ?> js-animate">
                    <?php if ($c['variant'] === 'outline') : ?>
                        <span class="sol-ben__plus sol-ben__plus--tl" aria-hidden="true"></span>
                        <span class="sol-ben__plus sol-ben__plus--tr" aria-hidden="true"></span>
                        <span class="sol-ben__plus sol-ben__plus--bl" aria-hidden="true"></span>
                        <span class="sol-ben__plus sol-ben__plus--br" aria-hidden="true"></span>
                    <?php endif; ?>
                    <div class="sol-ben__card-inner">
                        <span class="sol-ben__card-face sol-ben__card-front" aria-hidden="true"></span>
                        <div class="sol-ben__card-face sol-ben__card-back">
                            <p class="sol-ben__card-text"><?php echo esc_html($c['text']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
