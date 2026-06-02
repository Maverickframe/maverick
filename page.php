<?php get_header(); ?>

    <?php echo get_template_part( 'components/common/header', null, array( 
                                    'class' => 'header_white'
                                )
    ); ?>

<article class="inner-page text-page">
    <div class="container">
        <h1 class="text-page__title"><?php the_title(); ?></h1>

        <main class="text-page__content"><?php the_content(); ?></main>
    </div>
</article>

<?php echo get_template_part('components/common/footer'); ?>
<?php get_footer(); ?>