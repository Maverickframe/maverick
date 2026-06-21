<section class="client-context js-reveal">
    <div class="container">
        <div class="client-context__info">
            <p class="section-subtitle"><?php echo mfs_t('Client & Market Context', 'Contexto del cliente y del mercado', 'Kunden- & Marktkontext'); ?></p>
            <h2><?php the_field('title'); ?></h2>
            <div class="p1">
                <?php the_field('description'); ?>
            </div>
        </div>

        <div class="client-context__images">
            <?php 
                $img1 = get_field('image_1');
                $img1Mob = get_field('image_1_mob');
                $img2 = get_field('image_2');
                $img2Mob = get_field('image_2_mob');
            ?>
            <picture>
                <?php if($img1Mob): ?>
                    <source 
                        media="(max-width: 768px)" 
                        srcset="<?php echo wp_get_attachment_image_url($img1Mob, 'large'); ?>"
                    >
                <?php endif; ?>

                <?php lazy_attachment($img1, 'large'); ?>
            </picture>
            <picture>
                <?php if($img2Mob): ?>
                    <source 
                        media="(max-width: 768px)" 
                        srcset="<?php echo wp_get_attachment_image_url($img2Mob, 'large'); ?>"
                    >
                <?php endif; ?>

                <?php lazy_attachment($img2, 'large'); ?>
            </picture>
        </div>
    </div>
</section>