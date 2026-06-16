<?php
/*
 * Template Name: Blog
 */
?>

<?php get_header(); ?>

<?= get_template_part('components/common/header', null, ['class' => 'header_white']); ?>

<main class="main inner-page">
    <div class="container">
        <?php $bc_is_es = function_exists('pll_current_language') && pll_current_language() === 'es'; ?>
        <?= get_template_part('components/new-design/breadcrumbs', null, [
            'breadcrumbs' => [
                1 => [
                    'name' => mfs_t('Home', 'Inicio'),
                    'link' => $bc_is_es ? home_url('/es/') : home_url()
                ]
            ]
        ]); ?>
    </div>

    <?= get_template_part('components/new-design/blog/hero'); ?>
    <?= get_template_part('components/new-design/blog/trending'); ?>
    <?= get_template_part('components/new-design/articles', null, ['post_type' => 'blog']); ?>
    <?= get_template_part('components/new-design/faq'); ?>
</main>

<?= get_template_part('components/common/footer'); ?>
<?php get_footer(); ?>