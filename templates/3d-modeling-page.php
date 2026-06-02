<?php
/*
* Template Name: 3d Modeling Page
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

    <?php echo get_template_part( 'components/portfolio' ); ?>

    <?php echo get_template_part( 'components/contacts', null, array(
        'contacts_title' => 'cta_1_title',
        'contacts_desc' => 'cta_1_description',
        'contacts_book_title' => 'cta_1_book_title',
        'contacts_book_btn_title' => 'cta_1_book_btn_title',
    ) ); ?>

    <div class="container">
        <?php echo get_template_part( 'components/workflow' ); ?>
        <?php echo get_template_part( 'components/animation-worldwide' ); ?>
    </div>

    <?php echo get_template_part( 'components/service-page/solutions' ); ?> 

    <div class="container">
        <?php echo get_template_part( 'components/service-page/software', null, array(
            'class' => 'modeling',
        ) ); ?>

        <?php echo get_template_part( 'components/service-page/how-create' ); ?> 

        <?php echo get_template_part( 'components/cta' ); ?> 
    </div>

        <?php echo get_template_part( 'components/service-page/why-choose' ); ?> 

    <div class="container">
        <?php echo get_template_part( 'components/service-page/team' ); ?> 

        <?php echo get_template_part( 'components/service-page/faq' ); ?> 
    </div>

    <?php echo get_template_part( 'components/contacts', null, array(
        'contacts_title' => 'cta_3_title',
        'contacts_desc' => 'cta_3_description',
        'contacts_book_title' => 'cta_3_book_title',
        'contacts_book_btn_title' => 'cta_3_book_btn_title',
    ) ); ?>
</article>

<?php echo get_template_part('components/common/footer'); ?>
<?php get_footer(); ?>