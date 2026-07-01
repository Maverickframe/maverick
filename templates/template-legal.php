<?php
/*
* Template Name: Legal (new design)
* Template Post Type: page
*/
?>

<?php get_header(); ?>
<?php echo get_template_part('components/common/header', null, [
    'class' => 'header_white'
]); ?>

<main class="main legal-page">
    <section class="legal">
        <div class="container container_small">
            <nav class="legal__breadcrumbs" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url('/') ); ?>"><?php echo esc_html( mfs_t('Home', 'Inicio', 'Startseite') ); ?></a>
                <span class="legal__breadcrumbs-sep" aria-hidden="true">/</span>
                <span class="legal__breadcrumbs-current"><?php the_title(); ?></span>
            </nav>

            <header class="legal__head">
                <h1 class="legal__title"><?php the_title(); ?></h1>
                <?php
                $mfs_updated = get_the_modified_date( get_option('date_format') );
                if ( $mfs_updated ) : ?>
                    <p class="legal__updated">
                        <?php echo esc_html( mfs_t('Last updated', 'Última actualización', 'Zuletzt aktualisiert') ); ?>:
                        <time datetime="<?php echo esc_attr( get_the_modified_date('c') ); ?>"><?php echo esc_html( $mfs_updated ); ?></time>
                    </p>
                <?php endif; ?>
            </header>

            <div class="legal__content rich-text">
                <?php the_content(); ?>
            </div>
        </div>
    </section>
</main>

<?php echo get_template_part('components/common/footer'); ?>
<?php get_footer(); ?>
