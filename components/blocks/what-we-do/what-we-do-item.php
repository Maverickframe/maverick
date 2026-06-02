<?php 
$item = $args['item'];
$service_index = $args['service_index'] ?? null;
$image = $item['image'];
$title = $item['title'];
$description = $item['description'] ?? null;
$modal_title = $item['modal_title'];
?>
<div class="what-we-do-item">
    <?php if($image): ?>
        <?php lazy_attachment($image, 'full'); ?>
    <?php endif; ?>
    <div class="what-we-do-item__info">
        <div>
            <h3><?php echo $title; ?></h3>
    
            <?php if ($description): ?>
                <div class="what-we-do-item__desc"><?php echo $description; ?></div>
            <?php endif; ?>
        </div>

        <?php if ($modal_title && $service_index !== null): ?>
            <button class="what-we-do-item__btn js-modal-open" data-modal="what-we-do" data-services-source="what-we-do-json" data-service-index="<?php echo $service_index; ?>" type="button">
                <span class="what-we-do-item__btn-icon">
                    <?php echo inline_svg('icons/expand.svg'); ?>
                </span>
                <span class="what-we-do-item__btn-title">Expand</span>
            </button>
        <?php endif; ?>
    </div>
</div>