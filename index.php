<?php get_header(); ?>

    <?php echo get_template_part( 'components/common/header', null, array( 
                                    'class' => 'header_white'
                                )
    ); ?>

    <main class="main"></main>

<?php echo get_template_part('components/common/footer'); ?>
<?php get_footer(); ?>