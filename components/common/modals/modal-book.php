<?php
    $title = get_field('book_a_call_title', 'options');
    $desc = get_field('book_a_call_desc', 'options');
    $privacy = get_field('book_a_call_privacy', 'options');
?> 

<div class="js-contacts-form-container js-modal modal modal-book" data-modal="book">
    <div class="blur-overlay js-modal-close"></div>
    
    <div class="modal__inner">
        <button class="modal__close js-modal-close" aria-label="Close Modal window" type="button">
            <?php echo inline_svg('icons/close.svg'); ?>
        </button>

        <div class="modal-book__main">
            <h2 class="modal__title"><?php echo $title; ?></h2>

            <div class="modal__desc"><?php echo $desc; ?></div>
        </div>

        <div class="modal-book__form">
            <h3 class="modal-book__form-title"><?php echo mfs_t('Book a call with us', 'Reserva una llamada con nosotros'); ?></h3>
            <form action="" method="POST" class="js-contacts-form modal-form">
                <input type="hidden" name="tag" value="SEO, <?php the_title(); ?>, Book a Call">
                <input type="hidden" name="title" value="<?php the_title(); ?> / Book a Call">

                <label class="modal-form__input">
                    <span class="modal-form__label sr-only">
                        <?php echo mfs_t('Full Name', 'Nombre completo'); ?>
                    </span>

                    <input type="text" name="Name" placeholder="<?php echo esc_attr(mfs_t('Full Name', 'Nombre completo')); ?>">
                </label>

                <label class="modal-form__input">
                    <span class="modal-form__label sr-only">
                        Email
                    </span>

                    <input type="email" name="Email" placeholder="Email*">


                    <span class="modal-form__error">
                        <?php echo mfs_t('It is not email', 'El correo no es válido'); ?>
                    </span>
                </label>

                <label class="modal-form__input">
                    <span class="modal-form__label sr-only">
                        WhatsApp
                    </span>

                    <input type="text" name="WhatsApp" placeholder="WhatsApp">
                </label>

                <button class="btn-cta fill" type="submit">
                    <?php echo mfs_t('Book a call', 'Reservar una llamada'); ?>
                </button>
            </form>

            <div class="modal-book__form-privacy">
                <?php echo $privacy; ?>
            </div>

            <div class="modal__success">
                <p><b><?php echo mfs_t('Thank you – your message has been sent.', 'Gracias, tu mensaje se ha enviado.'); ?></b></p>
                <p><?php echo mfs_t('Our team will review your request and get back to you shortly.', 'Nuestro equipo revisará tu solicitud y te responderá en breve.'); ?></p>
            </div>
        </div>
    </div>
</div>