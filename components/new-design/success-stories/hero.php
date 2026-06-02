<section class="hero__main">
    <h1 class="hero__title"><?php the_field('hero_title'); ?></h1>

    <div class="hero__desc"><?php the_content(); ?></div>

    <?php // todo: common ?>
    <div class="hero__reviews">
        <?php $reviews = get_field('hero_reviews'); ?>
        <?php if ($reviews): ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review-item">
                    <?= inline_svg("icons/{$review['icon']}.svg"); ?>
                    <span>
                        <?= $review['rating']; ?>
                    </span>
                    <?= inline_svg('icons/star.svg'); ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>