<section class="single-team-hero">
    <div class="container">
        <div class="single-team-hero__main">
            <?php // todo: common ?>
            <ul class="single-team-hero__breadcrumbs">
                <li><a href="<?php echo home_url(); ?>">Home</a></li>
                <li><a href="<?php echo get_permalink(1945); ?>">Team</a></li>
                <li><span><?php the_title(); ?></span></li> 
            </ul>

            <div class="single-team-hero__info">
                <div class="single-team-hero__title-wrapper">
                    <span class="single-team-hero__position"><?php the_field('position'); ?></span>
                    <h1 class="single-team-hero__title"><?php the_title(); ?></h1>
                </div>
    
                <?php if(get_field("quote")): ?>
                    <div class="single-team-hero__quote"><?php echo inline_svg('icons/quote.svg'); ?> <?php the_field('quote'); ?></div>    
                <?php endif; ?>

                <?php if(get_field("linkedin")): ?>
                    <a href="<?php the_field("linkedin"); ?>" rel="nofollow noopener" target="_blank" aria-label="Open LinkedIn" class="single-team-hero__social">
                        <?php echo inline_svg('icons/linkedin.svg'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="single-team-hero__img">
            <?php echo wp_get_attachment_image(get_field('black_photo'), 'full', false, [
                'fetchpriority' => 'high'
            ] ); ?>
            <?php echo lazy_attachment(get_field('color_photo'), 'full'); ?>
        </div>
    </div>
</section>