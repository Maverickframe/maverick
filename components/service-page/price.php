<section class="service-page-price">
    <h2 class="service-page-price__title"><?php the_field('price_title'); ?></h2>

    <div class="service-page-price__img">
        <?php lazy_attachment(get_field('price_image'), 'full'); ?>
    </div>

    <div class="service-page-price__desc">
        <?php the_field('price_desc'); ?>
    </div>

    <div class="service-page-price__form">
        <?php if(get_field('price_quiz')): ?>
            <a href="<?php the_Field('price_quiz'); ?>" class="btn service-page-btn">Calculate your estimate</a>
        <?php else: ?>
            <form action="" method="POST" class="js-contacts-form contacts-form" data-link="<?php the_field('price_file'); ?>" data-ga-event="<?php echo get_field('price_file') ? 'download_catalog' : 'lead_form'; ?>" data-ga-form="<?php echo get_field('price_file') ? 'pricelist' : 'service_quote'; ?>" data-ga-type="service">
                <input type="hidden" name="title" value="<?php the_title(); ?> / <?php the_field('price_title'); ?>">

                <label class="contacts-form__input">
                    <input type="text" name="Name" placeholder="Name">
        
                    <span class="contacts-form__label">
                        Name
                    </span>
        
                    <hr />
                </label>
        
                <label class="contacts-form__input">
                    <input type="email" name="Email" placeholder="Email">
        
                    <span class="contacts-form__label">
                        Email
                    </span>
        
                    <span class="contacts-form__error">
                        It is not email
                    </span>
        
                    <hr />
                </label>
        
                <label class="contacts-form__input">
                    <input type="tel" name="Phone" placeholder="Phone">
        
                    <span class="contacts-form__label">
                        Phone
                    </span>
        
                    <span class="contacts-form__error">
                        It is required
                    </span>
        
                    <hr />
                </label>
        
                <button class="btn service-page-btn" type="submit">
                    Calculate your estimate
                </button>
        
                <div class="contacts-form__success">
                    Thank you! We will contact you soon.
                </div>
            </form>
        <?php endif; ?>
    </div>
</section>


<?php if(get_field('price_quiz_script')): ?>
    <?php echo get_field('price_quiz_script'); ?>
<?php endif; ?>