<?php get_header(); ?>
    <?php echo get_template_part( 'components/common/header', null, array( 
                                'class' => 'header_white'
                            )
    ); ?>

<article class="inner-page service-page">
    <div class="container">
        <?php echo get_template_part( 'components/service-page/hero' ); ?> 

        <?php echo get_template_part( 'components/service-page/download' ); ?> 

        <?php echo get_template_part( 'components/service-page/portfolio' ); ?> 

        <?php echo get_template_part( 'components/service-page/book' ); ?> 
    </div>

    <?php echo get_template_part( 'components/service-page/solutions' ); ?> 

    <div class="container">
        <?php echo get_template_part( 'components/service-page/price' ); ?> 

        <?php echo get_template_part( 'components/service-page/how-works' ); ?> 

        <?php echo get_template_part( 'components/service-page/get-started' ); ?> 

        <?php echo get_template_part( 'components/service-page/how-create' ); ?> 
        
        <?php echo get_template_part( 'components/service-page/software' ); ?> 
        
        <?php echo get_template_part( 'components/service-page/team' ); ?> 

        <?php echo get_template_part( 'components/service-page/ceo' ); ?> 
    </div>

    <?php echo get_template_part( 'components/service-page/why-choose' ); ?> 
        
    <?php echo get_template_part( 'components/service-page/clients' ); ?> 

    <div class="container">
        <?php echo get_template_part( 'components/service-page/faq' ); ?> 
        
        <?php echo get_template_part( 'components/service-page/contacts' ); ?> 
    </div>
</article>

<?php echo get_template_part('components/common/footer'); ?>
<?php get_footer(); ?>