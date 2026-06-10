<?php
/*
* Template Name: Services Hub
* Template Post Type: page
*/
?>

<?php get_header(); ?>
    <?php echo get_template_part('components/common/header'); ?>

    <main class="main">
        <?php the_content(); ?>
    </main>

    <?php echo get_template_part('components/common/footer'); ?>
<?php get_footer(); ?>
