<?php
/*
* Template Name: Contacts
*/
?>

<?php get_header(); ?>
    <?php echo get_template_part( 'components/common/header', null, array( 
                                'class' => 'header_white'
                            )
    ); ?>
    <?php echo get_template_part( 'components/contacts-page'); ?>
<?php echo get_template_part('components/common/footer'); ?>
<?php get_footer(); ?>