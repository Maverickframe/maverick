<a href="<?php echo $args['link'] ?? null; ?>" class="services-item <?php echo $args['class'] ?? null; ?>">
    <span class="services-item__num">0<?php echo $args['num'] ?? null; ?></span>

    <?php lazy_attachment($args['icon'] ?? null, 'full'); ?>

    <span class="services-item__title">
        <?php echo $args['title'] ?? null; ?>
    </span>
</a>