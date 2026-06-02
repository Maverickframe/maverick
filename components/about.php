<section class="about-section" id="about">
    <div class="container about-section__container">
        <h2 class="about-section__title"><?php the_field('about_title'); ?></h2>

        <div class="about-section__img">
            <?php lazy_attachment(get_field('about_img'), 'full'); ?>
        </div>

        <div class="about-section__desc">
            <?php the_field('about_desc'); ?>
        </div>

        <div class="about-section__counters about-counters">
            <div class="about-counters__item">
                <h3>Projects</h3>
                <span><span id="js-counter-projects" data-value="<?php the_field('about_project'); ?>"><?php the_field('about_project'); ?></span>+</span>
            </div>
            <div class="about-counters__item">
                <h3>Countries</h3>
                <span id="js-counter-countries" data-value="<?php the_field('about_countries'); ?>"><?php the_field('about_countries'); ?></span>
            </div>
            <div class="about-counters__item">
                <h3>Clients</h3>
                <span id="js-counter-clients" data-value="<?php the_field('about_clients'); ?>"><?php the_field('about_clients'); ?></span>
            </div>
        </div>
    </div>
</section>