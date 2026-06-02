<?php if (!function_exists("rank_math_the_breadcrumbs")): ?>
    <?php rank_math_the_breadcrumbs(); ?>
<?php else: ?>
    <ul class="hero-block__breadcrumbs js-reveal js-reveal-init" id="breadcrumb" data-anim="down" itemscope
        itemtype="https://schema.org/BreadcrumbList">
        <?php foreach ($args['breadcrumbs'] as $key => $breadcrumb): ?>
            <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                <a href="<?= $breadcrumb['link']; ?>" itemprop="item">
                    <meta itemprop="position" content="<?= $key; ?>" />
                    <span itemprop="name"><?= $breadcrumb['name']; ?></span>
                </a>
            </li>
        <?php endforeach; ?>
        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
            <meta itemprop="position" content="<?= $key + 1; ?>" />

            <span itemprop="item" content="<?= get_the_permalink(); ?>">
                <span itemprop="name"><?= get_the_title(); ?></span>
            </span>
        </li>
    </ul>
<?php endif; ?>