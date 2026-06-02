<div class="case-item">
    <a href="<?= esc_url($args['link']); ?>" class="case-item__link">
        <span class="case-item__img">
            <?php lazy_attachment(get_post_thumbnail_id($args['id']), 'large'); ?>

            <span class="case-item__hover">
                <?php the_field('hover_text', $args['id']); ?>
            </span>
        </span>

        <span class="case-item__info">
            <time class="case-item__date"><?= get_the_date('F d, Y', $args['id']); ?></time>

            <h3 class="case-item__title"><?= $args['title']; ?></h3>

            <p class="case-item__excerpt">
                <?= get_the_excerpt($args['id']); ?>
            </p>

            <span class="case-item__arrow">
                <?= inline_svg('icons/arrow-right-accent.svg'); ?>
            </span>
        </span>
    </a>
</div>