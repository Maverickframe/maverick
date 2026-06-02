<?php
/*
* Template Name: BIM Page
* Template Post Type: services
*/
?>

<?php get_header(); ?>

<article>
    <?php echo get_template_part( 'components/common/header' ); ?>

    <?php echo get_template_part( 'components/hero', null, array( 
            'images' => get_field('hero_images'),
            'title' => get_field('hero_title'),
            'subtitle' => get_field('hero_subtitle'),
            'links' => '<button type="button" class="btn hero-section__link js-modal-open" data-modal="book">
                <svg width="13.125rem" height="3rem" viewBox="0 0 210 48" class="border">
                    <polyline points="209,1 209,47 1,47 1,1 209,1" class="bg-line" />
                    <polyline points="209,1 209,47 1,47 1,1 209,1" class="hl-line" />
                </svg>
                Get personal offer
            </button>
            <button type="button" class="btn hero-section__link js-modal-open" data-modal="download">
                <svg width="13.125rem" height="3rem" viewBox="0 0 210 48" class="border">
                    <polyline points="209,1 209,47 1,47 1,1 209,1" class="bg-line" />
                    <polyline points="209,1 209,47 1,47 1,1 209,1" class="hl-line" />
                </svg>
                Download Models
            </button>',
        )
    ); ?>

    <div class="container">
        <?php echo get_template_part( 'components/service-page/how-create' ); ?> 
        <?php echo get_template_part( 'components/cta', null, array(
            'title' => 'cta_1_title',
            'subtitle' => 'cta_1_subtitle',
        ) ); ?> 
    </div>

    <?php echo get_template_part( 'components/service-page/solutions' ); ?> 

    <?php echo get_template_part( 'components/contacts', null, array(
        'contacts_title' => 'cta_2_title',
        'contacts_desc' => 'cta_2_description',
        'contacts_book_title' => 'cta_2_book_title',
        'contacts_book_btn_title' => 'cta_2_book_btn_title',
    ) ); ?>

    <div class="container">
        <?php echo get_template_part( 'components/workflow' ); ?>
        <?php echo get_template_part( 'components/animation-worldwide' ); ?>
    </div>

    <div class="container">
        <?php echo get_template_part( 'components/service-page/software', null, array(
            'class' => 'bim',
        ) ); ?>
    </div>

        <?php echo get_template_part( 'components/service-page/why-choose' ); ?> 

        <?php echo get_template_part( 'components/contacts', null, array(
            'contacts_title' => 'cta_3_title',
            'contacts_desc' => 'cta_3_description',
            'contacts_book_title' => 'cta_3_book_title',
            'contacts_book_btn_title' => 'cta_3_book_btn_title',
        ) ); ?>

    <div class="container">
        <?php echo get_template_part( 'components/service-page/team' ); ?> 

        <?php echo get_template_part( 'components/service-page/faq' ); ?> 
    </div>
</article>

<?php echo get_template_part('components/common/footer'); ?>
<?php get_footer(); ?>