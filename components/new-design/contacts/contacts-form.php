<?php
/**
 * Contacts page lead form (new design) — matches the Figma form card.
 * Submits through the shared `js-contacts-form` handler (forms/amo.php).
 */
$privacy_url = get_privacy_policy_url();
if (!$privacy_url) {
    $privacy_url = home_url('/privacy-policy/');
}

// [dial code, flag emoji, name] — full country list. Value submitted = dial code.
$countries = [
    ['+93','🇦🇫','Afganistán'],['+355','🇦🇱','Albania'],['+213','🇩🇿','Argelia'],['+376','🇦🇩','Andorra'],
    ['+244','🇦🇴','Angola'],['+54','🇦🇷','Argentina'],['+374','🇦🇲','Armenia'],['+61','🇦🇺','Australia'],
    ['+43','🇦🇹','Austria'],['+994','🇦🇿','Azerbaiyán'],['+973','🇧🇭','Baréin'],['+880','🇧🇩','Bangladés'],
    ['+375','🇧🇾','Bielorrusia'],['+32','🇧🇪','Bélgica'],['+501','🇧🇿','Belice'],['+229','🇧🇯','Benín'],
    ['+591','🇧🇴','Bolivia'],['+387','🇧🇦','Bosnia y Herzegovina'],['+267','🇧🇼','Botsuana'],['+55','🇧🇷','Brasil'],
    ['+359','🇧🇬','Bulgaria'],['+855','🇰🇭','Camboya'],['+237','🇨🇲','Camerún'],['+1','🇨🇦','Canadá'],
    ['+56','🇨🇱','Chile'],['+86','🇨🇳','China'],['+57','🇨🇴','Colombia'],['+506','🇨🇷','Costa Rica'],
    ['+385','🇭🇷','Croacia'],['+53','🇨🇺','Cuba'],['+357','🇨🇾','Chipre'],['+420','🇨🇿','Chequia'],
    ['+45','🇩🇰','Dinamarca'],['+1','🇩🇴','Rep. Dominicana'],['+593','🇪🇨','Ecuador'],['+20','🇪🇬','Egipto'],
    ['+503','🇸🇻','El Salvador'],['+372','🇪🇪','Estonia'],['+251','🇪🇹','Etiopía'],['+358','🇫🇮','Finlandia'],
    ['+33','🇫🇷','Francia'],['+995','🇬🇪','Georgia'],['+49','🇩🇪','Alemania'],['+233','🇬🇭','Ghana'],
    ['+30','🇬🇷','Grecia'],['+502','🇬🇹','Guatemala'],['+504','🇭🇳','Honduras'],['+852','🇭🇰','Hong Kong'],
    ['+36','🇭🇺','Hungría'],['+354','🇮🇸','Islandia'],['+91','🇮🇳','India'],['+62','🇮🇩','Indonesia'],
    ['+98','🇮🇷','Irán'],['+964','🇮🇶','Irak'],['+353','🇮🇪','Irlanda'],['+972','🇮🇱','Israel'],
    ['+39','🇮🇹','Italia'],['+81','🇯🇵','Japón'],['+962','🇯🇴','Jordania'],['+7','🇰🇿','Kazajistán'],
    ['+254','🇰🇪','Kenia'],['+965','🇰🇼','Kuwait'],['+371','🇱🇻','Letonia'],['+961','🇱🇧','Líbano'],
    ['+218','🇱🇾','Libia'],['+370','🇱🇹','Lituania'],['+352','🇱🇺','Luxemburgo'],['+60','🇲🇾','Malasia'],
    ['+356','🇲🇹','Malta'],['+52','🇲🇽','México'],['+373','🇲🇩','Moldavia'],['+377','🇲🇨','Mónaco'],
    ['+212','🇲🇦','Marruecos'],['+95','🇲🇲','Myanmar'],['+264','🇳🇦','Namibia'],['+977','🇳🇵','Nepal'],
    ['+31','🇳🇱','Países Bajos'],['+64','🇳🇿','Nueva Zelanda'],['+505','🇳🇮','Nicaragua'],['+234','🇳🇬','Nigeria'],
    ['+47','🇳🇴','Noruega'],['+968','🇴🇲','Omán'],['+92','🇵🇰','Pakistán'],['+507','🇵🇦','Panamá'],
    ['+595','🇵🇾','Paraguay'],['+51','🇵🇪','Perú'],['+63','🇵🇭','Filipinas'],['+48','🇵🇱','Polonia'],
    ['+351','🇵🇹','Portugal'],['+974','🇶🇦','Catar'],['+40','🇷🇴','Rumanía'],['+7','🇷🇺','Rusia'],
    ['+966','🇸🇦','Arabia Saudita'],['+221','🇸🇳','Senegal'],['+381','🇷🇸','Serbia'],['+65','🇸🇬','Singapur'],
    ['+421','🇸🇰','Eslovaquia'],['+386','🇸🇮','Eslovenia'],['+27','🇿🇦','Sudáfrica'],['+82','🇰🇷','Corea del Sur'],
    ['+34','🇪🇸','España'],['+94','🇱🇰','Sri Lanka'],['+46','🇸🇪','Suecia'],['+41','🇨🇭','Suiza'],
    ['+886','🇹🇼','Taiwán'],['+255','🇹🇿','Tanzania'],['+66','🇹🇭','Tailandia'],['+216','🇹🇳','Túnez'],
    ['+90','🇹🇷','Turquía'],['+380','🇺🇦','Ucrania'],['+971','🇦🇪','Emiratos Árabes Unidos'],['+44','🇬🇧','Reino Unido'],
    ['+1','🇺🇸','Estados Unidos'],['+598','🇺🇾','Uruguay'],['+998','🇺🇿','Uzbekistán'],['+58','🇻🇪','Venezuela'],
    ['+84','🇻🇳','Vietnam'],['+967','🇾🇪','Yemen'],['+260','🇿🇲','Zambia'],['+263','🇿🇼','Zimbabue'],
];
?>
<div class="js-contacts-form-container contacts-form-card">
            <form action="" method="POST" class="js-contacts-form cform" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="tag" value="SEO, <?php the_title(); ?>, Contact form">
                <input type="hidden" name="title" value="<?php the_title(); ?> / Contact form">

                <div class="cform__row">
                    <label class="cform__input">
                        <span class="sr-only"><?php echo mfs_t('Full name', 'Nombre completo', 'Vollständiger Name'); ?></span>
                        <input type="text" name="Name" maxlength="80" placeholder="<?php echo esc_attr(mfs_t('Full name', 'Nombre completo', 'Vollständiger Name')); ?>*" required>
                        <span class="cform__error"><?php echo mfs_t('Enter your name', 'Introduce tu nombre', 'Gib deinen Namen ein'); ?></span>
                    </label>

                    <label class="cform__input">
                        <span class="sr-only">Email</span>
                        <input type="email" name="Email" maxlength="120" placeholder="Email*" required>
                        <span class="cform__error"><?php echo mfs_t('It is not email', 'El correo no es válido', 'Keine gültige E-Mail-Adresse'); ?></span>
                    </label>
                </div>

                <div class="cform__input cform__phone">
                    <div class="cform__country js-country" data-dial="+34">
                        <button type="button" class="cform__country-toggle js-country-toggle" aria-haspopup="listbox" aria-expanded="false" aria-label="<?php echo esc_attr(mfs_t('Country code', 'Código de país', 'Ländervorwahl')); ?>">
                            <span class="cform__country-flag">🇪🇸</span>
                            <span class="cform__country-code">+34</span>
                            <svg class="cform__country-caret" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <div class="cform__country-pop">
                            <input type="text" class="cform__country-search js-country-search" placeholder="<?php echo esc_attr(mfs_t('Search country…', 'Buscar país…', 'Land suchen…')); ?>" aria-label="<?php echo esc_attr(mfs_t('Search country', 'Buscar país', 'Land suchen')); ?>">
                            <ul class="cform__country-list" role="listbox">
                                <?php foreach ($countries as $c): ?>
                                    <li class="cform__country-opt" role="option" data-dial="<?php echo esc_attr($c[0]); ?>" data-flag="<?php echo esc_attr($c[1]); ?>" data-name="<?php echo esc_attr(mb_strtolower($c[2])); ?>">
                                        <span class="cform__country-opt-flag"><?php echo $c[1]; ?></span>
                                        <span class="cform__country-opt-name"><?php echo esc_html($c[2]); ?></span>
                                        <span class="cform__country-opt-code"><?php echo esc_html($c[0]); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <label class="cform__phone-field">
                        <span class="sr-only"><?php echo mfs_t('Phone number', 'Número de teléfono', 'Telefonnummer'); ?></span>
                        <input type="tel" class="cform__phone-num" inputmode="tel" autocomplete="tel-national" maxlength="20" placeholder="<?php echo esc_attr(mfs_t('Phone number', 'Número de teléfono', 'Telefonnummer')); ?>">
                        <span class="cform__error cform__phone-error"><?php echo mfs_t('Enter a valid phone number', 'Introduce un número de teléfono válido', 'Gib eine gültige Telefonnummer ein'); ?></span>
                    </label>
                    <input type="hidden" name="Phone">
                </div>

                <label class="cform__input">
                    <span class="sr-only"><?php echo mfs_t('How can we help you?', '¿Cómo podemos ayudarte?', 'Wie können wir dir helfen?'); ?></span>
                    <textarea name="Message" rows="4" maxlength="1000" class="js-msg" placeholder="<?php echo esc_attr(mfs_t('How can we help you?', '¿Cómo podemos ayudarte?', 'Wie können wir dir helfen?')); ?>"></textarea>
                    <span class="cform__count js-msg-count" aria-hidden="true">0 / 1000</span>
                </label>

                <div class="cform__upload-row">
                    <label class="cform__upload">
                        <input type="file" name="File" class="sr-only" accept=".pdf,.jpg,.jpeg,.png,.zip">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M21 11.5l-8.5 8.5a5 5 0 01-7-7l8.5-8.5a3.3 3.3 0 014.7 4.7L9 16.4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span><?php echo mfs_t('Upload file', 'Adjuntar archivo', 'Datei hochladen'); ?></span>
                    </label>
                    <span class="cform__upload-info" tabindex="0" role="button" aria-label="<?php echo esc_attr(mfs_t('File requirements', 'Requisitos del archivo', 'Dateianforderungen')); ?>">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.6"/><path d="M12 11v5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/><circle cx="12" cy="8" r="1" fill="currentColor"/></svg>
                        <span class="cform__upload-tip" role="tooltip">
                            <?php echo mfs_t(
                                'Max file size: 15 MB. Formats: PDF, JPG, PNG, ZIP.',
                                'Tamaño máximo: 15 MB. Formatos: PDF, JPG, PNG, ZIP.', 'Max. Dateigröße: 15 MB. Formate: PDF, JPG, PNG, ZIP.'
                            ); ?>
                        </span>
                    </span>
                    <span class="cform__file-name js-file-name" aria-live="polite"></span>
                    <span class="cform__error js-file-error"></span>
                </div>

                <label class="cform__check">
                    <input type="checkbox" name="PrivacyConsent" value="1" required>
                    <span><?php echo mfs_t('I agree to the', 'Acepto la', 'Ich akzeptiere die'); ?>
                        <a href="<?php echo esc_url($privacy_url); ?>" target="_blank" rel="noopener"><?php echo mfs_t('Privacy Policy', 'Política de privacidad', 'Datenschutzerklärung'); ?></a>
                        <?php echo mfs_t('and give my permission to process my personal data for the purposes specified in the Privacy Policy.', 'y doy mi consentimiento para tratar mis datos personales con los fines indicados en la Política de privacidad.', 'und willige in die Verarbeitung meiner personenbezogenen Daten zu den in der Datenschutzerklärung genannten Zwecken ein.'); ?>*
                    </span>
                    <span class="cform__error"><?php echo mfs_t('Please accept the Privacy Policy', 'Debes aceptar la Política de privacidad', 'Bitte akzeptiere die Datenschutzerklärung'); ?></span>
                </label>

                <label class="cform__check">
                    <input type="checkbox" name="NewsletterConsent" value="1">
                    <span><?php echo mfs_t('I give my permission to process my email address specified above for the purpose of sending newsletters.', 'Doy mi consentimiento para tratar el correo indicado con el fin de enviarme novedades.', 'Ich willige in die Verarbeitung meiner oben angegebenen E-Mail-Adresse zum Zweck des Newsletter-Versands ein.'); ?></span>
                </label>

                <button class="btn-main fill cform__btn" type="submit"><?php echo mfs_t('Submit', 'Enviar', 'Absenden'); ?></button>
            </form>

            <div class="cform__success contacts-form__success">
                <div class="cform__success-card">
                    <span class="cform__success-deco" aria-hidden="true"></span>
                    <button type="button" class="cform__success-close" aria-label="<?php echo esc_attr(mfs_t('Close', 'Cerrar', 'Schließen')); ?>" onclick="this.closest('.js-contacts-form-container').classList.remove('is-success')">
                        <?php echo inline_svg('icons/close-white.svg') ?: '&times;'; ?>
                    </button>
                    <p class="cform__success-text">
                        <span class="cform__success-hl"><?php echo mfs_t('Thank you – your message has been sent.', 'Gracias, tu mensaje se ha enviado.', 'Danke – deine Nachricht wurde gesendet.'); ?></span>
                        <?php echo mfs_t('Our team will review your request and get back to you shortly.', 'Nuestro equipo revisará tu solicitud y te responderá en breve.', 'Unser Team prüft deine Anfrage und meldet sich in Kürze bei dir.'); ?>
                    </p>
                </div>
            </div>
</div>
