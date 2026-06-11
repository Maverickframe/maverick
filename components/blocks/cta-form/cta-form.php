<?php
    $title = get_field('title');
    $description = get_field('description');
    $background_id = get_field('background');
    $background_mob_id = get_field('background_mob');
    $form_title = get_field('form_title');
    $form_privacy = get_field('form_privacy');
?>

<div class="container">
    <section class="cta-form-section">
        <picture class="cta-form-section__bg">
            <?php if ($background_mob_id): ?>
                <source media="(max-width: 768px)" srcset="<?php echo wp_get_attachment_image_url($background_mob_id, 'full'); ?>">
            <?php endif; ?>

            <?php lazy_attachment($background_id, 'full'); ?>
        </picture>

        <div class="cta-form-section__info">
            <h2 class="js-highlight text-highlight"><?php echo $title; ?></h2>  
            <div class="cta-form-section__desc"><?php echo $description; ?></div>

            <div class="cta-form-section__clients">
                <?php
                    while( have_rows('clients')) : the_row();
                        $image_id = get_sub_field('image');
                ?>
                    <div class="cta-form-section__client">
                        <?php lazy_attachment($image_id, 'full'); ?>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="js-contacts-form-container cta-form-section__form">
            <h3 class="cta-form-section__form-title"><?php echo $form_title; ?></h3>
            <form action="" method="POST" class="js-contacts-form cta-form">
                <input type="hidden" name="tag" value="SEO, <?php the_title(); ?>, Book a Call">
                <input type="hidden" name="title" value="<?php the_title(); ?> / Book a Call">

                <label class="cta-form__input">
                    <span class="cta-form__label sr-only">
                        <?php echo mfs_t('Full Name', 'Nombre completo'); ?>
                    </span>

                    <input type="text" name="Name" placeholder="<?php echo esc_attr(mfs_t('Full Name', 'Nombre completo')); ?>">
                </label>

                <label class="cta-form__input">
                    <span class="cta-form__label sr-only">
                        Email
                    </span>

                    <input type="email" name="Email" placeholder="Email*">

                    <span class="cta-form__error">
                        <?php echo mfs_t('It is not email', 'El correo no es válido'); ?>
                    </span>
                </label>

                <label class="cta-form__input">
                    <span class="cta-form__label sr-only">
                        WhatsApp
                    </span>

                    <input type="text" name="WhatsApp" placeholder="WhatsApp">
                </label>

                <label class="cta-form__input">
                    <span class="cta-form__label sr-only">
                        <?php echo mfs_t('Message', 'Mensaje'); ?>
                    </span>

                    <textarea name="Message" placeholder="<?php echo esc_attr(mfs_t('Message', 'Mensaje')); ?>" rows="1"></textarea>
                </label>

                <?php // todo: Upload file; ?>

                <button class="btn-main fill" type="submit">
                    <?php echo mfs_t('Book a meeting', 'Reservar una reunión'); ?>
                </button>
            </form>

            <div class="cta-form__privacy">
                <?php echo mfs_consent($form_privacy); ?>
            </div>

            <div class="cta-form__success">
                <p><b><?php echo mfs_t('Thank you – your message has been sent.', 'Gracias, tu mensaje se ha enviado.'); ?></b></p>
                <p><?php echo mfs_t('Our team will review your request and get back to you shortly.', 'Nuestro equipo revisará tu solicitud y te responderá en breve.'); ?></p>
            </div>
        </div>
    </section>
</div>
