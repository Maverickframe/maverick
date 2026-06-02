<?php
/*
* Template Name: Developers Page
* Template Post Type: solutions
*/
?>

<?php get_header(); ?>
    <?php echo get_template_part( 'components/common/header' ); ?>

    <?php echo get_template_part( 'components/hero', null, array( 
            'classes' => 'darken',
            'images' => get_field('hero_images'),
            'title' => get_field('hero_title'),
            'subtitle' => get_field('hero_subtitle'),
            'links' => '<button type="button" class="btn hero-section__link js-modal-open" data-modal="book">
                <svg width="13.125rem" height="3rem" viewBox="0 0 210 48" class="border">
                    <polyline points="209,1 209,47 1,47 1,1 209,1" class="bg-line" />
                    <polyline points="209,1 209,47 1,47 1,1 209,1" class="hl-line" />
                </svg>
                BOOK A CALL
            </button>
            <button type="button" class="btn hero-section__link js-modal-open" data-modal="download">
                <svg width="13.125rem" height="3rem" viewBox="0 0 210 48" class="border">
                    <polyline points="209,1 209,47 1,47 1,1 209,1" class="bg-line" />
                    <polyline points="209,1 209,47 1,47 1,1 209,1" class="hl-line" />
                </svg>
                DOWNLOAD PDF WITH PLANS
            </button>',
        )
    ); ?>

    <?php echo get_template_part( 'components/service-page/match' ); ?> 

    <?php echo get_template_part( 'components/service-page/kinds' ); ?> 
    
    <?php echo get_template_part( 'components/service-page/marketing-solutions' ); ?> 

    <?php echo get_template_part( 'components/service-page/trusted' ); ?> 

    <div class="container">
        <?php echo get_template_part( 'components/cta', null, [
            'title' => 'cta_1_title',
            'subtitle' => 'cta_1_subtitle',
        ] ); ?> 
    </div>

    <?php echo get_template_part( 'components/service-page/cgi' ); ?>

    <?php echo get_template_part( 'components/service-page/estate' ); ?>

    <?php echo get_template_part( 'components/service-page/case', null, ['num' => 1] ); ?>

    <?php echo get_template_part( 'components/service-page/audit' ); ?>

    <?php echo get_template_part( 'components/service-page/climate' ); ?> 

    <?php echo get_template_part( 'components/service-page/dev-team' ); ?> 

    <?php echo get_template_part( 'components/service-page/stack' ); ?> 

    <?php echo get_template_part( 'components/contacts', null, array(
        'contacts_title' => 'cta_2_title',
        'contacts_desc' => 'cta_2_description',
        'contacts_book_title' => 'cta_2_book_title',
        'contacts_book_btn_title' => 'cta_2_book_btn_title',
        'current_companies' => true
    ) ); ?>

    <?php echo get_template_part( 'components/service-page/case', null, ['num' => 2] ); ?> 

    <?php echo get_template_part( 'components/service-page/packages' ); ?> 

    <?php echo get_template_part( 'components/service-page/dev-portfolio' ); ?> 

    <?php echo get_template_part( 'components/contacts', null, array(
        'contacts_title' => 'cta_3_title',
        'contacts_desc' => 'cta_3_description',
        'contacts_book_title' => 'cta_3_book_title',
        'contacts_book_btn_title' => 'cta_3_book_btn_title',
        'current_companies' => true
    ) ); ?>

    <div class="container">
        <?php echo get_template_part( 'components/service-page/faq' ); ?> 
    </div>
</article>

<?php echo get_template_part('components/common/footer'); ?>
<?php get_footer(); ?>