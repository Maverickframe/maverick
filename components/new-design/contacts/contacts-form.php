<?php
/**
 * Contacts page lead form (new design) — matches the Figma form card.
 * Submits through the shared `js-contacts-form` handler (forms/amo.php).
 */
$privacy_url = get_privacy_policy_url();
if (!$privacy_url) {
    $privacy_url = home_url('/privacy-policy/');
}

// Language for labels + default country. EN/DE show English country names; ES shows Spanish.
$cf_lang = function_exists('mfs_lang') ? mfs_lang() : 'en';
$cf_is_es = ($cf_lang === 'es');
// Default selected country by language: EN → USA, ES → Spain, DE → Germany.
$cf_defaults = [
    'es' => ['+34','🇪🇸','España'],
    'de' => ['+49','🇩🇪','Deutschland'],
    'en' => ['+1','🇺🇸','United States'],
];
$cf_default = $cf_defaults[$cf_lang] ?? $cf_defaults['en'];

// [dial code, flag emoji, Spanish name, English name] — value submitted = dial code.
$countries = [
    ['+93','🇦🇫','Afganistán','Afghanistan'],['+355','🇦🇱','Albania','Albania'],['+213','🇩🇿','Argelia','Algeria'],['+376','🇦🇩','Andorra','Andorra'],
    ['+244','🇦🇴','Angola','Angola'],['+54','🇦🇷','Argentina','Argentina'],['+374','🇦🇲','Armenia','Armenia'],['+61','🇦🇺','Australia','Australia'],
    ['+43','🇦🇹','Austria','Austria'],['+994','🇦🇿','Azerbaiyán','Azerbaijan'],['+973','🇧🇭','Baréin','Bahrain'],['+880','🇧🇩','Bangladés','Bangladesh'],
    ['+375','🇧🇾','Bielorrusia','Belarus'],['+32','🇧🇪','Bélgica','Belgium'],['+501','🇧🇿','Belice','Belize'],['+229','🇧🇯','Benín','Benin'],
    ['+591','🇧🇴','Bolivia','Bolivia'],['+387','🇧🇦','Bosnia y Herzegovina','Bosnia and Herzegovina'],['+267','🇧🇼','Botsuana','Botswana'],['+55','🇧🇷','Brasil','Brazil'],
    ['+359','🇧🇬','Bulgaria','Bulgaria'],['+855','🇰🇭','Camboya','Cambodia'],['+237','🇨🇲','Camerún','Cameroon'],['+1','🇨🇦','Canadá','Canada'],
    ['+56','🇨🇱','Chile','Chile'],['+86','🇨🇳','China','China'],['+57','🇨🇴','Colombia','Colombia'],['+506','🇨🇷','Costa Rica','Costa Rica'],
    ['+385','🇭🇷','Croacia','Croatia'],['+53','🇨🇺','Cuba','Cuba'],['+357','🇨🇾','Chipre','Cyprus'],['+420','🇨🇿','Chequia','Czechia'],
    ['+45','🇩🇰','Dinamarca','Denmark'],['+1','🇩🇴','Rep. Dominicana','Dominican Republic'],['+593','🇪🇨','Ecuador','Ecuador'],['+20','🇪🇬','Egipto','Egypt'],
    ['+503','🇸🇻','El Salvador','El Salvador'],['+372','🇪🇪','Estonia','Estonia'],['+251','🇪🇹','Etiopía','Ethiopia'],['+358','🇫🇮','Finlandia','Finland'],
    ['+33','🇫🇷','Francia','France'],['+995','🇬🇪','Georgia','Georgia'],['+49','🇩🇪','Alemania','Germany'],['+233','🇬🇭','Ghana','Ghana'],
    ['+30','🇬🇷','Grecia','Greece'],['+502','🇬🇹','Guatemala','Guatemala'],['+504','🇭🇳','Honduras','Honduras'],['+852','🇭🇰','Hong Kong','Hong Kong'],
    ['+36','🇭🇺','Hungría','Hungary'],['+354','🇮🇸','Islandia','Iceland'],['+91','🇮🇳','India','India'],['+62','🇮🇩','Indonesia','Indonesia'],
    ['+98','🇮🇷','Irán','Iran'],['+964','🇮🇶','Irak','Iraq'],['+353','🇮🇪','Irlanda','Ireland'],['+972','🇮🇱','Israel','Israel'],
    ['+39','🇮🇹','Italia','Italy'],['+81','🇯🇵','Japón','Japan'],['+962','🇯🇴','Jordania','Jordan'],['+7','🇰🇿','Kazajistán','Kazakhstan'],
    ['+254','🇰🇪','Kenia','Kenya'],['+965','🇰🇼','Kuwait','Kuwait'],['+371','🇱🇻','Letonia','Latvia'],['+961','🇱🇧','Líbano','Lebanon'],
    ['+218','🇱🇾','Libia','Libya'],['+370','🇱🇹','Lituania','Lithuania'],['+352','🇱🇺','Luxemburgo','Luxembourg'],['+60','🇲🇾','Malasia','Malaysia'],
    ['+356','🇲🇹','Malta','Malta'],['+52','🇲🇽','México','Mexico'],['+373','🇲🇩','Moldavia','Moldova'],['+377','🇲🇨','Mónaco','Monaco'],
    ['+212','🇲🇦','Marruecos','Morocco'],['+95','🇲🇲','Myanmar','Myanmar'],['+264','🇳🇦','Namibia','Namibia'],['+977','🇳🇵','Nepal','Nepal'],
    ['+31','🇳🇱','Países Bajos','Netherlands'],['+64','🇳🇿','Nueva Zelanda','New Zealand'],['+505','🇳🇮','Nicaragua','Nicaragua'],['+234','🇳🇬','Nigeria','Nigeria'],
    ['+47','🇳🇴','Noruega','Norway'],['+968','🇴🇲','Omán','Oman'],['+92','🇵🇰','Pakistán','Pakistan'],['+507','🇵🇦','Panamá','Panama'],
    ['+595','🇵🇾','Paraguay','Paraguay'],['+51','🇵🇪','Perú','Peru'],['+63','🇵🇭','Filipinas','Philippines'],['+48','🇵🇱','Polonia','Poland'],
    ['+351','🇵🇹','Portugal','Portugal'],['+974','🇶🇦','Catar','Qatar'],['+40','🇷🇴','Rumanía','Romania'],['+7','🇷🇺','Rusia','Russia'],
    ['+966','🇸🇦','Arabia Saudita','Saudi Arabia'],['+221','🇸🇳','Senegal','Senegal'],['+381','🇷🇸','Serbia','Serbia'],['+65','🇸🇬','Singapur','Singapore'],
    ['+421','🇸🇰','Eslovaquia','Slovakia'],['+386','🇸🇮','Eslovenia','Slovenia'],['+27','🇿🇦','Sudáfrica','South Africa'],['+82','🇰🇷','Corea del Sur','South Korea'],
    ['+34','🇪🇸','España','Spain'],['+94','🇱🇰','Sri Lanka','Sri Lanka'],['+46','🇸🇪','Suecia','Sweden'],['+41','🇨🇭','Suiza','Switzerland'],
    ['+886','🇹🇼','Taiwán','Taiwan'],['+255','🇹🇿','Tanzania','Tanzania'],['+66','🇹🇭','Tailandia','Thailand'],['+216','🇹🇳','Túnez','Tunisia'],
    ['+90','🇹🇷','Turquía','Turkey'],['+380','🇺🇦','Ucrania','Ukraine'],['+971','🇦🇪','Emiratos Árabes Unidos','United Arab Emirates'],['+44','🇬🇧','Reino Unido','United Kingdom'],
    ['+1','🇺🇸','Estados Unidos','United States'],['+598','🇺🇾','Uruguay','Uruguay'],['+998','🇺🇿','Uzbekistán','Uzbekistan'],['+58','🇻🇪','Venezuela','Venezuela'],
    ['+84','🇻🇳','Vietnam','Vietnam'],['+967','🇾🇪','Yemen','Yemen'],['+260','🇿🇲','Zambia','Zambia'],['+263','🇿🇼','Zimbabue','Zimbabwe'],
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
                        <span class="cform__error"><?php echo mfs_t('Enter your name', 'Introduce tu nombre', 'Geben Sie Ihren Namen ein'); ?></span>
                    </label>

                    <label class="cform__input">
                        <span class="sr-only">Email</span>
                        <input type="email" name="Email" maxlength="120" placeholder="Email*" required>
                        <span class="cform__error"><?php echo mfs_t('It is not email', 'El correo no es válido', 'Keine gültige E-Mail-Adresse'); ?></span>
                    </label>
                </div>

                <div class="cform__input cform__phone">
                    <div class="cform__country js-country" data-dial="<?php echo esc_attr($cf_default[0]); ?>">
                        <button type="button" class="cform__country-toggle js-country-toggle" aria-haspopup="listbox" aria-expanded="false" aria-label="<?php echo esc_attr(mfs_t('Country code', 'Código de país', 'Ländervorwahl')); ?>">
                            <span class="cform__country-flag"><?php echo $cf_default[1]; ?></span>
                            <span class="cform__country-code"><?php echo esc_html($cf_default[0]); ?></span>
                            <svg class="cform__country-caret" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <div class="cform__country-pop">
                            <input type="text" class="cform__country-search js-country-search" placeholder="<?php echo esc_attr(mfs_t('Search country…', 'Buscar país…', 'Land suchen…')); ?>" aria-label="<?php echo esc_attr(mfs_t('Search country', 'Buscar país', 'Land suchen')); ?>">
                            <ul class="cform__country-list" role="listbox">
                                <?php foreach ($countries as $c): $cf_cn = $cf_is_es ? $c[2] : $c[3]; ?>
                                    <li class="cform__country-opt" role="option" data-dial="<?php echo esc_attr($c[0]); ?>" data-flag="<?php echo esc_attr($c[1]); ?>" data-name="<?php echo esc_attr(mb_strtolower($cf_cn)); ?>">
                                        <span class="cform__country-opt-flag"><?php echo $c[1]; ?></span>
                                        <span class="cform__country-opt-name"><?php echo esc_html($cf_cn); ?></span>
                                        <span class="cform__country-opt-code"><?php echo esc_html($c[0]); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    <label class="cform__phone-field">
                        <span class="sr-only"><?php echo mfs_t('Phone number', 'Número de teléfono', 'Telefonnummer'); ?></span>
                        <input type="tel" class="cform__phone-num" inputmode="tel" autocomplete="tel-national" maxlength="20" placeholder="<?php echo esc_attr(mfs_t('Phone number', 'Número de teléfono', 'Telefonnummer')); ?>">
                        <span class="cform__error cform__phone-error"><?php echo mfs_t('Enter a valid phone number', 'Introduce un número de teléfono válido', 'Geben Sie eine gültige Telefonnummer ein'); ?></span>
                    </label>
                    <input type="hidden" name="Phone">
                </div>

                <label class="cform__input">
                    <span class="sr-only"><?php echo mfs_t('How can we help you?', '¿Cómo podemos ayudarte?', 'Wie können wir Ihnen helfen?'); ?></span>
                    <textarea name="Message" rows="4" maxlength="1000" class="js-msg" placeholder="<?php echo esc_attr(mfs_t('How can we help you?', '¿Cómo podemos ayudarte?', 'Wie können wir Ihnen helfen?')); ?>"></textarea>
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
                    <span class="cform__error"><?php echo mfs_t('Please accept the Privacy Policy', 'Debes aceptar la Política de privacidad', 'Bitte akzeptieren Sie die Datenschutzerklärung'); ?></span>
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
                        <span class="cform__success-hl"><?php echo mfs_t('Thank you – your message has been sent.', 'Gracias, tu mensaje se ha enviado.', 'Danke – Ihre Nachricht wurde gesendet.'); ?></span>
                        <?php echo mfs_t('Our team will review your request and get back to you shortly.', 'Nuestro equipo revisará tu solicitud y te responderá en breve.', 'Unser Team prüft Ihre Anfrage und meldet sich in Kürze bei Ihnen.'); ?>
                    </p>
                </div>
            </div>
</div>
