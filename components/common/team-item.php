<a href="<?php echo $args['link']; ?>" class="team-item js-reveal">
    <div class="team-item__img">
        <?php lazy_attachment($args['img'], 'large'); ?>
        <?php lazy_attachment($args['color_photo'], 'large'); ?>
    </div>

    <div class="team-item__info">
        <p class="team-item__name"><?php echo $args['name']; ?></p>

        <div class="team-item__bottom">
            <p class="team-item__position"><?php echo $args['position']; ?></p>
            <?php echo inline_svg('icons/arrow-right-accent.svg'); ?>
        </div>
    </div>
</a>