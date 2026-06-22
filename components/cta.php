
<div class="cta-block">
    <div class="cta-block__info">
        <h2 class="cta-block__title"><?php the_field($args['title'] ?? 'cta_2_title', $args['contacts_from'] ?? null); ?></h2>
        <div class="cta-block__subtitle"><?php the_field($args['subtitle'] ?? 'cta_2_subtitle', $args['contacts_from'] ?? null); ?></div>
    </div>
    
    <form action="" method="POST" class="js-contacts-form contacts-form cta-block__form">
        <input type="hidden" name="title" value="<?php the_title(); ?> / Get in touch">
    
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
    
        <label class="contacts-form__input message">
            <input type="text" name="Message" placeholder="Message">
    
            <span class="contacts-form__label">
                Message
            </span>
    
            <hr />
        </label>
    
        <button class="btn department-section__link" type="submit">
            <svg width="14.5625rem" height="3rem" viewBox="0 0 233 48" class="border">
                <polyline points="232,1 232,47 1,47 1,1 232,1" class="bg-line" />
                <polyline points="232,1 232,47 1,47 1,1 232,1" class="hl-line" />
            </svg>
            <?php echo mfs_t('Send', 'Enviar', 'Senden'); ?>
        </button>
    
        <div class="contacts-form__success">
            Thank you! We will contact you soon.
        </div>
    </form>
</div>
