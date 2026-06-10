<?php
/*
* Template Name: Guide Page
*/
?>

<?php get_header(); ?>

<article>
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
                Get personal offer
            </button>
            <button type="button" class="btn hero-section__link js-modal-open" data-modal="download">
                <svg width="13.125rem" height="3rem" viewBox="0 0 210 48" class="border">
                    <polyline points="209,1 209,47 1,47 1,1 209,1" class="bg-line" />
                    <polyline points="209,1 209,47 1,47 1,1 209,1" class="hl-line" />
                </svg>
                <?php echo mfs_t('Download', 'Descargar'); ?>
            </button>',
        )
    ); ?>

    <?php echo get_template_part( 'components/services-3d', null, array(
        'title' => 'freedownload_title',
        'description' => 'freedownload_description',
        'items' => 'freedownload_items',
        'download' => true
    ) ); ?>

    <div class="container">
        <?php echo get_template_part( 'components/service-page/how-create' ); ?> 
    </div>

    <?php echo get_template_part( 'components/service-page/solutions', null, [
        'classes' => 'service-page-solutions_guide',
        'download' => "Get Access"
    ] ); ?> 


    <div class="container">
        <?php echo get_template_part( 'components/workflow' ); ?>
        <?php echo get_template_part( 'components/animation-worldwide' ); ?>
    </div>

    <?php echo get_template_part( 'components/about' ); ?>

    <?php echo get_template_part( 'components/contacts', null, array(
        'contacts_title' => 'cta_title',
        'contacts_desc' => 'cta_description',
        'contacts_book_title' => 'cta_book_title',
        'contacts_book_btn_title' => 'cta_book_btn_title',
        'download' => get_field('download_link'),
    ) ); ?>

<?php echo get_template_part('components/common/footer'); ?>
<?php get_footer(); ?>