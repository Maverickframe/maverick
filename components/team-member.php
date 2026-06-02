<div class="team-members-section__item">
    <a href="<?php the_permalink(); ?>" class="service-page-team__item">
        <?php lazy_attachment(get_post_thumbnail_id(get_the_ID()), 'full'); ?>
    
        <div class="service-page-team__item-overlay">
            <p class="service-page-team__item-name"><?php the_title(); ?></p>
    
            <p class="service-page-team__item-position"><?php the_field('position'); ?></p>
    
            <span class="team-members-section__item-more">learn more</span>
        </div>
    </a>
</div>