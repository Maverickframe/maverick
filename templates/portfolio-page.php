<?php
/*
* Template Name: Portfolio
*/
?>

<?php get_header(); ?>
    <?php echo get_template_part( 'components/common/header', null, array( 
                                'class' => 'header_white'
                            )
    ); ?>
    <?php echo get_template_part( 'components/portfolio-page'); ?>
<?php echo get_template_part('components/common/footer'); ?>
<?php get_footer(); ?>