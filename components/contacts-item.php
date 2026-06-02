<div class="contacts-item <?php echo $args['class'] ?? null; ?>">
    <svg width="24" height="24">
        <use href="#<?php echo $args['ico'] ?? null; ?>">
    </svg>

    <?php if(isset($args['title'])): ?>
    <p class="contacts-item__title"><?php echo $args['title']; ?></p>
    <?php endif; ?>

    <?php if(isset($args['link'])): ?>
        <a href="<?php echo $args['link']; ?>" rel="nofollow noopener" target="_blank" class="contacts-item__link"><?php echo $args['link_title'] ?? null; ?></a>
    <?php else: ?>
        <span class="contacts-item__link"><?php echo $args['link_title'] ?? null; ?></span>
    <?php endif; ?>
</div>