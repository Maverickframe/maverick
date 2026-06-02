
<div class="services-3d-item">
    <?php lazy_attachment($args['icon'], 'full'); ?>
    <h3 class="services-3d-item__title"><?php echo $args['title']; ?></h3>
    <p class="services-3d-item__description"><?php echo $args['description']; ?></p>
    <?php if(isset($args['download']) && $args['download']): ?>
        <button class="services-3d-item__link js-modal-open" data-modal="download" type="button">
            <svg width="14.5625rem" height="3rem" viewBox="0 0 233 48" class="border">
                <polyline points="232,1 232,47 1,47 1,1 232,1" class="bg-line" />
                <polyline points="232,1 232,47 1,47 1,1 232,1" class="hl-line" />
            </svg>
            <?php echo $args['btn_title']; ?>
        </button>
    <?php elseif(!empty($args['calendly']) && $args['calendly']): ?>
        <button class="services-3d-item__link js-modal-open" data-modal="book" type="button">
            <svg width="14.5625rem" height="3rem" viewBox="0 0 233 48" class="border">
                <polyline points="232,1 232,47 1,47 1,1 232,1" class="bg-line" />
                <polyline points="232,1 232,47 1,47 1,1 232,1" class="hl-line" />
            </svg>
            <?php echo $args['btn_title']; ?>
        </button>
    <?php elseif(!empty($args['btn_link'])): ?>
        <a class="services-3d-item__link" href="<?php echo $args['btn_link']; ?>">
            <svg width="14.5625rem" height="3rem" viewBox="0 0 233 48" class="border">
                <polyline points="232,1 232,47 1,47 1,1 232,1" class="bg-line" />
                <polyline points="232,1 232,47 1,47 1,1 232,1" class="hl-line" />
            </svg>
            <?php echo $args['btn_title']; ?>
        </a>
    <?php else: ?>
        <span class="services-3d-item__link">
            <svg width="14.5625rem" height="3rem" viewBox="0 0 233 48" class="border">
                <polyline points="232,1 232,47 1,47 1,1 232,1" class="bg-line" />
                <polyline points="232,1 232,47 1,47 1,1 232,1" class="hl-line" />
            </svg>
            <?php echo $args['btn_title']; ?>
        </span>
    <?php endif; ?>
</div>