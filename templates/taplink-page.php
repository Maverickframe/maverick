<?php
/*
* Template Name: Taplink
*/
?>

<?php get_header(); ?>
    <main class="taplink-page__main">
        <a href="<?php echo home_url(); ?>" class="taplink-page__logo">MAVERICK FRAME STUDIO</a>
    
        <div class="taplink-page__info">
            <div class="taplink-page__img">
                <?php lazy_attachment(get_post_thumbnail_id(get_the_ID()), 'full'); ?>
            </div>

            <?php if (get_field('name')): ?>
                <h1 class="taplink-page__name"><?php the_field('name'); ?></h1>
            <?php endif; ?>
        
            <?php if (get_field('position')): ?>
                <p class="taplink-page__position"><?php the_field('position'); ?></p>
            <?php endif; ?>
        </div>

        <ul class="taplink-page__links">
            <?php if (get_field('whatsapp')): ?>
                <li><a href="<?php the_field('whatsapp'); ?>" class="taplink-page__link">WhatsApp</a></li>
            <?php endif; ?>
        
            <?php if (get_field('linkedin')): ?>
                <li><a href="<?php the_field('linkedin'); ?>" class="taplink-page__link">LinkedIn</a></li>
            <?php endif; ?>
        
            <?php if (get_field('email')): ?>
                <li><a href="mailto:<?php the_field('email'); ?>" class="taplink-page__link"><?php the_field('email'); ?></a></li>
            <?php endif; ?>
        
            <?php if (get_field('portfolio')): ?>
                <li><a href="<?php the_field('portfolio'); ?>" class="taplink-page__link">Portfolio</a></li>
            <?php endif; ?>
        
            <?php if (get_field('calendly')): ?>
                <li><a href="<?php the_field('calendly'); ?>" class="taplink-page__link fill">Book a meeting</a></li>
            <?php endif; ?>
        </ul>
    
    </main>
<?php get_footer(); ?>