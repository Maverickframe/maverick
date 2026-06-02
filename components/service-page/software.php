<section class="service-page-software <?php echo $args['class'] ?? null; ?>">
    <h2 class="service-page-software__title"><?php the_field('software_title'); ?></h2>

    <div class="service-page-software__desc">
        <div>
            <?php the_field('software_desc'); ?>
        </div>
    </div>
</section>