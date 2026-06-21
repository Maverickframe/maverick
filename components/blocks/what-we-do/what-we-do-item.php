<?php 
$item = $args['item'];
$service_index = $args['service_index'] ?? null;
$image = $item['image'];
$title = $item['title'];
$description = $item['description'] ?? null;
$modal_title = $item['modal_title'];

$case = $item['case'] ?? null;
$service_url = '';
if ($case instanceof WP_Post) {
    $service_url = get_permalink($case->ID);
} elseif (is_array($case) && !empty($case['ID'])) {
    $service_url = get_permalink((int) $case['ID']);
} elseif (is_numeric($case)) {
    $service_url = get_permalink((int) $case);
}
?>
<div class="what-we-do-item">
    <?php if($image): ?>
        <?php lazy_attachment($image, 'full'); ?>
    <?php endif; ?>

    <?php if ($modal_title && $service_index !== null): ?>
        <button class="what-we-do-item__btn js-modal-open" data-modal="what-we-do" data-services-source="what-we-do-json" data-service-index="<?php echo $service_index; ?>" type="button" aria-label="Open">
            <?php echo inline_svg('icons/expand.svg'); ?>
            <span><?php echo mfs_t('Open', 'Abrir', 'Öffnen'); ?></span>
        </button>
    <?php endif; ?>

    <div class="what-we-do-item__info">
        <div>
            <h3>
                <?php if ($service_url): ?>
                    <a class="what-we-do-item__title-link" href="<?php echo esc_url($service_url); ?>"><?php echo $title; ?></a>
                <?php else: ?>
                    <?php echo $title; ?>
                <?php endif; ?>
            </h3>

            <?php if ($description): ?>
                <div class="what-we-do-item__desc"><?php echo $description; ?></div>
            <?php endif; ?>

            <?php if ($service_url): ?>
                <a class="what-we-do-item__service" href="<?php echo esc_url($service_url); ?>"><?php echo mfs_t('View service', 'Ver servicio', 'Leistung ansehen'); ?></a>
            <?php endif; ?>
        </div>
    </div>
</div>
