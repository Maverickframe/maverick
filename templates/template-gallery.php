<?php
/*
 * Template Name: Gallery
 */
?>

<?php get_header(); ?>

<?php echo get_template_part('components/common/header', null, ['class' => 'header_white']); ?>

<main class="main gallery-page">
    <?php echo get_template_part('components/new-design/gallery/gallery-hero'); ?>
    <?php echo get_template_part('components/new-design/gallery/gallery-items'); ?>
</main>

<?php echo get_template_part('components/common/footer'); ?>
<?php get_footer(); ?>