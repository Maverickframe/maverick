<div class="sp-item" id="<?php echo $args['id']; ?>">
    <h2 class="sp-item__title"><span class="sp-item__title-num"><?php echo $args['num']; ?> /</span> <?php echo $args['title']; ?></h2>

    <div class="sp-item__img">
        <?php 
            if ($args['num'] == 1) {
                echo wp_get_attachment_image($args['img'], 'full');
            } else {
                lazy_attachment($args['img'], 'full');
            }
        ?>

        <?php if($args['link']): ?>
        <a href="<?php echo $args['link']; ?>" class="btn sp-item__link">
            <span>READ MORE</span><span class="sr-only"> about <?php echo $args['title']; ?></span>
        </a>
        <?php endif; ?>
    </div>

    <div class="sp-item__desc">
        <?php echo $args['desc']; ?>
    </div>
</div>