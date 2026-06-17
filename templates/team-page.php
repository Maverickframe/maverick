<?php
/*
* Template Name: Team
*/
?>

<?php get_header(); ?>
<?php echo get_template_part( 'components/common/header', null, [ 
        'class' => 'header_white'
    ]
); ?>

<main class="main team-page">
    <?php echo get_template_part('components/new-design/team/team-hero'); ?>
    <?php echo get_template_part('components/new-design/team/team-members'); ?>
    <?php echo get_template_part('components/new-design/team/team-awards'); ?>
    <?php echo get_template_part('components/common/reviews'); ?>
    <?php echo get_template_part('components/new-design/team/team-performance'); ?>
    <?php echo get_template_part('components/new-design/team/team-faq'); ?>
</main>

<?php echo get_template_part('components/common/footer', null, [
    'footer_title' => get_field('footer_title'),
    'footer_description' => get_field('footer_description'),
]); ?>
<?php get_footer(); ?>