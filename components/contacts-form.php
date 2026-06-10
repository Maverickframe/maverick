<form action="" method="POST" class="js-contacts-form contacts-form">
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

    <button class="btn contacts-form__btn" type="submit">
        <?php echo mfs_t('Send', 'Enviar'); ?>
    </button>

    <div class="contacts-form__success">
        Thank you! We will contact you soon.
    </div>
</form>