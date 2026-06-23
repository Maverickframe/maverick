<?php get_header(); ?>
<?php
// New-design Solutions posts are authored with Gutenberg/ACF blocks and render
// via the_content() (same pattern as single-success-stories). Legacy Solutions
// posts (no blocks) keep the original service-page layout below.
$sol_obj = get_queried_object();
if ( $sol_obj && has_blocks( $sol_obj->post_content ) ) : ?>
    <?php echo get_template_part( 'components/common/header' ); ?>

    <main class="main">
        <?php the_content(); ?>
    </main>

    <?php echo get_template_part( 'components/common/footer' ); ?>
<?php else : ?>
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
<?php endif; ?>
<?php get_footer(); ?>
