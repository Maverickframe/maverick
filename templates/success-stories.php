<?php
/*
 * Template Name: Success Stories
 */
?>

<?php get_header(); ?>

<?= get_template_part('components/common/header', null, ['class' => 'header_white']); ?>

<main class="main inner-page success-stories-page">
    <div class="container">
        <?= get_template_part('components/new-design/breadcrumbs', null, [
            'breadcrumbs' => [
                1 => [
                    'name' => 'Home',
                    'link' => home_url()
                ]
            ]
        ]); ?>

        <?= get_template_part('components/new-design/success-stories/hero'); ?>
    </div>

    <?= get_template_part('components/blocks/trusted/trusted'); ?>
    <?= get_template_part('components/new-design/articles', null, ['post_type' => 'success-stories']); ?>
    <?= get_template_part('components/new-design/faq'); ?>
</main>

<?= get_template_part('components/common/footer'); ?>

<?php get_footer(); ?>