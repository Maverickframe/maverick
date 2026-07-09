<?php get_header(); ?>
<?php
// Solutions posts are authored with Gutenberg/ACF blocks and render via the_content()
// (same pattern as single-success-stories). The legacy no-blocks service-page layout was
// removed 2026-07-09: all published solutions use blocks, and the old service-page/* parts
// (and single-services.php + 9 unused page templates) were deleted as dead legacy.
?>
<?php echo get_template_part( 'components/common/header' ); ?>

<main class="main">
    <?php the_content(); ?>
</main>

<?php echo get_template_part( 'components/common/footer' ); ?>
<?php get_footer(); ?>
