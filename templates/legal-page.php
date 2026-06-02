<?php
/*
* Template Name: Legal Page
*/
?>

<?php get_header(); ?>
    <?php echo get_template_part( 'components/common/header', null, array( 
                                'class' => 'header_white'
                            )
    ); ?>
    <?php echo get_template_part( 'components/legal-page'); ?>
<?php echo get_template_part('components/common/footer'); ?>
<?php get_footer(); ?>