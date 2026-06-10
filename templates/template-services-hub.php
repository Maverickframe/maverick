<?php
/*
* Template Name: Services Hub
* Template Post Type: page
*/
?>

<?php get_header(); ?>
    <?php echo get_template_part('components/common/header'); ?>

    <main class="main inner-page success-stories-page">
        <div class="container">
            <?php echo get_template_part('components/new-design/breadcrumbs', null, [
                'breadcrumbs' => [
                    1 => [
                        'name' => 'Home',
                        'link' => home_url()
                    ]
                ]
            ]); ?>

            <section class="hero__main">
                <h1 class="hero__title"><?php echo esc_html(get_post_meta(get_the_ID(), 'hero_title', true)); ?></h1>

                <?php $hub_hero_desc = get_post_meta(get_the_ID(), 'hero_description', true); ?>
                <?php if ($hub_hero_desc): ?>
                    <div class="hero__desc"><?php echo wp_kses_post($hub_hero_desc); ?></div>
                <?php endif; ?>

                <div class="hero__reviews">
                    <?php $hub_rev_count = (int) get_post_meta(get_the_ID(), 'hero_reviews', true); ?>
                    <?php for ($i = 0; $i < $hub_rev_count; $i++):
                        $hub_icon   = get_post_meta(get_the_ID(), "hero_reviews_{$i}_icon", true);
                        $hub_rating = get_post_meta(get_the_ID(), "hero_reviews_{$i}_rating", true); ?>
                        <div class="review-item">
                            <?= inline_svg("icons/{$hub_icon}.svg"); ?>
                            <span><?= esc_html($hub_rating); ?></span>
                            <?= inline_svg('icons/star.svg'); ?>
                        </div>
                    <?php endfor; ?>
                </div>
            </section>
        </div>

        <?php the_content(); ?>
    </main>

    <?php echo get_template_part('components/common/footer'); ?>
<?php get_footer(); ?>
