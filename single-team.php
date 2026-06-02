<?php get_header(); ?>
<?php echo get_template_part( 'components/common/header', null, array( 
                            'class' => 'header_white'
                        )
); ?>

<main class="main">
    <article class="single-team-page">
        <?php echo get_template_part('components/new-design/team/single-team-hero'); ?>

        <?php the_content(); ?>
    </article>
</main>

<?php echo get_template_part('components/common/footer'); ?>
<?php get_footer(); ?>