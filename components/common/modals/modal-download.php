<div class="js-contacts-form-container js-modal modal modal-download" data-modal="download">
    <div class="blur-overlay js-modal-close"></div>
    
    <div class="modal__inner">
        <button class="modal__close js-modal-close" aria-label="Close Modal window" type="button">
            <?php echo inline_svg('icons/close.svg'); ?>
        </button>

        <div class="modal-download__img">
            <?php lazy_attachment(get_field('download_img', 'options'), 'full'); ?>
        </div>

        <div class="modal-download__main">
            <h2 class="modal__title"><?php echo get_field('download_title', 'options'); ?></h2>

            <p class="modal__desc"><?php echo get_field('download_desc', 'options'); ?></p>

            <form action="" method="POST" class="js-contacts-form modal-form" data-link="<?php echo get_field('download_link', 'options'); ?>">
                <input type="hidden" name="tag" value="SEO, <?php the_title(); ?>, Services">
                <input type="hidden" name="title" value="<?php the_title(); ?> / Catalog">

                <label class="modal-form__input">
                    <span class="modal-form__label">
                        <?php echo mfs_t('Full Name', 'Nombre completo'); ?>
                    </span>

                    <input type="text" name="Name" placeholder="Alex Smith">
                </label>

                <label class="modal-form__input">
                    <span class="modal-form__label">
                        Email
                    </span>

                    <input type="email" name="Email" placeholder="alexsmith@example.com">


                    <span class="modal-form__error">
                        <?php echo mfs_t('It is not email', 'El correo no es válido'); ?>
                    </span>
                </label>

                <label class="modal-form__input">
                    <span class="modal-form__label">
                        <?php echo mfs_t('Phone Number', 'Teléfono'); ?>
                    </span>

                    <input type="tel" name="Phone" placeholder="+1 (467) 287-1111">

                    <span class="modal-form__error">
                        <?php echo mfs_t('It is required', 'Campo obligatorio'); ?>
                    </span>
                </label>

                <button class="btn-main fill" type="submit">
                    <?php echo mfs_t('Download', 'Descargar'); ?>
                </button>

                <p class="modal-form__reassure"><?php echo mfs_t('Instant download · No spam — we only email about your request.', 'Descarga inmediata · Sin spam — solo te escribimos sobre tu solicitud.'); ?></p>
            </form>

            <div class="modal__success">
                <b><?php echo mfs_t('Thank you!', '¡Gracias!'); ?></b>
                <?php echo mfs_t('The catalog will download automatically. If the catalog didn\'t load or if you need to get in touch with us, feel free to reach us at', 'El catálogo se descargará automáticamente. Si no se descarga o necesitas contactarnos, llámanos al'); ?> <?php echo get_field('footer_phone', 'options') ?> <?php echo mfs_t('or email us at', 'o escríbenos a'); ?> <a href="mailto:<?php echo get_field("footer_email", 'options'); ?>" target='_blank'><?php echo get_field('footer_email', 'options'); ?></a>
            </div>
        </div>
    </div>
</div>