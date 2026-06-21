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
            <h2 class="modal__title"><?php echo mfs_t( get_field('download_title', 'options'), 'Descarga nuestro portafolio', 'Lade unser Portfolio herunter' ); ?></h2>

            <p class="modal__desc"><?php echo mfs_t( get_field('download_desc', 'options'), 'Hemos reunido nuestros mejores trabajos en un único PDF. Déjanos tus datos y te lo enviamos al instante.', 'Wir haben unsere besten Arbeiten in einem einzigen PDF zusammengestellt. Hinterlasse deine Daten und wir senden es dir sofort.' ); ?></p>

            <form action="" method="POST" class="js-contacts-form modal-form" data-link="<?php echo get_field('download_link', 'options'); ?>">
                <input type="hidden" name="tag" value="SEO, <?php the_title(); ?>, Services">
                <input type="hidden" name="title" value="<?php the_title(); ?> / Catalog">

                <label class="modal-form__input">
                    <span class="modal-form__label">
                        <?php echo mfs_t('Full Name', 'Nombre completo', 'Vollständiger Name'); ?>
                    </span>

                    <input type="text" name="Name" placeholder="Alex Smith">
                </label>

                <label class="modal-form__input">
                    <span class="modal-form__label">
                        Email
                    </span>

                    <input type="email" name="Email" placeholder="alexsmith@example.com">


                    <span class="modal-form__error">
                        <?php echo mfs_t('It is not email', 'El correo no es válido', 'Keine gültige E-Mail-Adresse'); ?>
                    </span>
                </label>

                <label class="modal-form__input">
                    <span class="modal-form__label">
                        <?php echo mfs_t('Phone Number', 'Teléfono', 'Telefonnummer'); ?>
                    </span>

                    <input type="tel" name="Phone" placeholder="+1 (467) 287-1111">

                    <span class="modal-form__error">
                        <?php echo mfs_t('It is required', 'Campo obligatorio', 'Pflichtfeld'); ?>
                    </span>
                </label>

                <button class="btn-main fill" type="submit">
                    <?php echo mfs_t('Download', 'Descargar', 'Herunterladen'); ?>
                </button>

                <p class="modal-form__reassure"><?php echo mfs_t('Instant download · No spam — we only email about your request.', 'Descarga inmediata · Sin spam — solo te escribimos sobre tu solicitud.', 'Sofortiger Download · Kein Spam — wir schreiben dir nur zu deiner Anfrage.'); ?></p>
            </form>

            <div class="modal__success">
                <b><?php echo mfs_t('Thank you!', '¡Gracias!', 'Danke!'); ?></b>
                <?php echo mfs_t('The catalog will download automatically. If the catalog didn\'t load or if you need to get in touch with us, feel free to reach us at', 'El catálogo se descargará automáticamente. Si no se descarga o necesitas contactarnos, llámanos al', 'Der Katalog wird automatisch heruntergeladen. Falls der Download nicht startet oder du uns kontaktieren möchtest, erreichst du uns unter'); ?> <?php echo get_field('footer_phone', 'options') ?> <?php echo mfs_t('or email us at', 'o escríbenos a', 'oder schreib uns an'); ?> <a href="mailto:<?php echo get_field("footer_email", 'options'); ?>" target='_blank'><?php echo get_field('footer_email', 'options'); ?></a>
            </div>
        </div>
    </div>
</div>