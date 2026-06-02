<?php
$keyname = $args['keyname'] ?? '';
$label = $args['label'] ?? '';
$permalink = $args['permalink'] ?? '';
$links = $args['links'] ?? [];

if ($keyname == 'our_works') {
    $our_works = get_field('menu_our_works', 'options');
    if (!empty($our_works['items'])) {
        $links = $our_works['items'];
    }
}
?>
<div class="menu__big-links <?php echo $keyname; ?>">
    <?php if(empty($links)): ?>
        <?php if($permalink): ?>
            <a href="<?php echo $permalink; ?>" class="menu__big-link"><?php echo $label; ?></a>
        <?php else: ?>
            <span class="menu__big-link"><?php echo $label; ?></span>
        <?php endif; ?>
    <?php else: ?>
        <?php foreach ($links as $link): ?>
            <?php if (isset($link['link'])): ?>
                <a href="<?php echo get_permalink($link['link']); ?>" class="menu__big-link"><?php echo $link['title']; ?></a>
            <?php else: ?>
                <span class="menu__big-link"><?php echo $link['title']; ?></span>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>