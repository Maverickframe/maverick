<?php
/**
 * Free Test Render — qualified lead-magnet section.
 * Dark section (subtle glow) + white form card. Reuses cta-form layout/styles;
 * adds a structured info column + qualifying form.
 *
 * Copy is editable per-block via ACF (group_freetestrender01). Every field
 * falls back to the original homepage copy when left empty, so existing
 * placements render unchanged. The form structure / field name attributes /
 * select options stay in code (tied to the CRM/form handler).
 */

$ftr_eyebrow      = get_field('ftr_eyebrow')      ?: mfs_t('Free Test Render', 'Render de prueba gratis', 'Kostenloser Testrender');
$ftr_heading      = get_field('ftr_heading')      ?: mfs_t('Start With a Free Test Render', 'Empieza con un render de prueba gratis', 'Starten Sie mit einem kostenlosen Testrender');
$ftr_intro        = get_field('ftr_intro')        ?: mfs_t('We prove our quality on your own project before you commit. Once we’ve scoped the work and agreed on terms, we deliver a free test render — so you see exactly how it looks in our hands, with zero risk.', 'Demostramos nuestra calidad en tu propio proyecto antes de que te comprometas. Una vez definido el alcance y acordadas las condiciones, entregamos un render de prueba gratuito — para que veas exactamente cómo queda en nuestras manos, sin ningún riesgo.', 'Wir beweisen unsere Qualität an Ihrem eigenen Projekt, bevor Sie sich festlegen. Sobald der Umfang geklärt und die Konditionen vereinbart sind, liefern wir einen kostenlosen Testrender — so sehen Sie genau, wie es in unseren Händen aussieht, ganz ohne Risiko.');
$ftr_hiw_label    = get_field('ftr_hiw_label')    ?: mfs_t('How it works', 'Cómo funciona', 'So funktioniert’s');
$ftr_cond_label   = get_field('ftr_cond_label')   ?: mfs_t('Conditions', 'Condiciones', 'Bedingungen');
$ftr_form_title   = get_field('ftr_form_title')   ?: mfs_t('Request your free test render', 'Solicita tu render de prueba gratis', 'Fordern Sie Ihren kostenlosen Testrender an');
$ftr_submit_label = get_field('ftr_submit_label') ?: mfs_t('Request my free test render', 'Solicitar mi render de prueba gratis', 'Meinen kostenlosen Testrender anfordern');
$ftr_success_ttl  = get_field('ftr_success_title')?: mfs_t('Thank you – your request has been received.', 'Gracias – hemos recibido tu solicitud.', 'Danke – Ihre Anfrage ist eingegangen.');
$ftr_success_txt  = get_field('ftr_success_text') ?: mfs_t('Our team will review your project and get back to you shortly to arrange your free test render.', 'Nuestro equipo revisará tu proyecto y te responderá en breve para organizar tu render de prueba gratuito.', 'Unser Team prüft Ihr Projekt und meldet sich in Kürze bei Ihnen, um Ihren kostenlosen Testrender abzustimmen.');

// Offer variant — styling hook + lead-attribution preset (does NOT swap copy; copy lives in the fields above).
$ftr_variant = get_field('offer_variant') ?: 'render';

// Lead tag → hidden "title" field = the lead source recorded in the CRM (forms/lead.php maps title => form_page).
// Must be unique per page so the 28 placements don't collapse into one source.
// Empty + homepage  => keep the original "Homepage / Free Test Render" (homepage stays untouched).
// Empty elsewhere   => auto-build a unique tag from the variant + page slug.
$ftr_lead_tag = get_field('lead_tag');
if ( ! $ftr_lead_tag ) {
    if ( is_front_page() ) {
        $ftr_lead_tag = 'Homepage / Free Test Render';
    } else {
        $ftr_slug = get_post_field( 'post_name', get_the_ID() );
        $ftr_lead_tag = ucfirst( $ftr_variant ) . ' / Free Test Render – ' . $ftr_slug;
    }
}

$ftr_hiw_default = [
    mfs_t('Tell us about your project and book a short call.', 'Cuéntanos sobre tu proyecto y reserva una llamada corta.', 'Erzählen Sie uns von Ihrem Projekt und buchen Sie ein kurzes Gespräch.'),
    mfs_t('We scope the work and agree on the price.', 'Definimos el alcance y acordamos el precio.', 'Wir klären den Umfang und vereinbaren den Preis.'),
    mfs_t('We deliver a free test render within 5 business days.', 'Entregamos un render de prueba gratuito en un plazo de 5 días hábiles.', 'Wir liefern einen kostenlosen Testrender innerhalb von 5 Werktagen.'),
];
$ftr_cond_default = [
    mfs_t('Available for projects from $3,000.', 'Disponible para proyectos a partir de 3.000 €.', 'Verfügbar für Projekte ab 3.000 €.'),
    mfs_t('One test render — our team selects the most representative angle.', 'Un render de prueba — nuestro equipo elige el ángulo más representativo.', 'Ein Testrender — unser Team wählt die aussagekräftigste Ansicht.'),
    mfs_t('Delivered within 5 business days; revisions aren’t included.', 'Entregado en 5 días hábiles; las revisiones no están incluidas.', 'Lieferung innerhalb von 5 Werktagen; Korrekturen sind nicht enthalten.'),
    mfs_t('For clients with an active project, ready to discuss it on a call.', 'Para clientes con un proyecto activo, listos para hablarlo en una llamada.', 'Für Kunden mit einem aktiven Projekt, die bereit sind, es in einem Gespräch zu besprechen.'),
    mfs_t('A quality guarantee for your own project — not a free sample to resell.', 'Una garantía de calidad para tu propio proyecto — no una muestra gratuita para revender.', 'Eine Qualitätsgarantie für Ihr eigenes Projekt — kein kostenloses Muster zum Weiterverkauf.'),
];
?>

<div class="container container_small">
    <section class="cta-form-section free-test-render" data-variant="<?php echo esc_attr($ftr_variant); ?>">
        <div class="cta-form-section__info free-test-render__info">
            <p class="section-subtitle"><?php echo esc_html($ftr_eyebrow); ?></p>
            <h2><?php echo esc_html($ftr_heading); ?></h2>

            <p class="free-test-render__intro"><?php echo esc_html($ftr_intro); ?></p>

            <div class="free-test-render__group">
                <p class="free-test-render__label"><?php echo esc_html($ftr_hiw_label); ?></p>
                <ol class="free-test-render__steps">
                    <?php if ( have_rows('ftr_hiw_steps') ) : ?>
                        <?php while ( have_rows('ftr_hiw_steps') ) : the_row(); ?>
                            <li><?php echo esc_html( get_sub_field('text') ); ?></li>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <?php foreach ( $ftr_hiw_default as $step ) : ?>
                            <li><?php echo esc_html($step); ?></li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ol>
            </div>

            <div class="free-test-render__group">
                <p class="free-test-render__label"><?php echo esc_html($ftr_cond_label); ?></p>
                <ul class="free-test-render__checklist">
                    <?php if ( have_rows('ftr_cond_items') ) : ?>
                        <?php while ( have_rows('ftr_cond_items') ) : the_row(); ?>
                            <li><?php echo esc_html( get_sub_field('text') ); ?></li>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <?php foreach ( $ftr_cond_default as $cond ) : ?>
                            <li><?php echo esc_html($cond); ?></li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="js-contacts-form-container cta-form-section__form">
            <h3 class="cta-form-section__form-title"><?php echo esc_html($ftr_form_title); ?></h3>

            <form action="" method="POST" class="js-contacts-form cta-form free-test-render__form" data-ga-event="lead_form" data-ga-form="freetest" data-ga-type="free_test">
                <input type="hidden" name="tag" value="SEO, Free Test Render">
                <input type="hidden" name="title" value="<?php echo esc_attr($ftr_lead_tag); ?>">

                <label class="cta-form__input">
                    <span class="cta-form__label sr-only"><?php echo esc_html( mfs_t('Full Name', 'Nombre completo', 'Vollständiger Name') ); ?></span>
                    <input type="text" name="Name" placeholder="<?php echo esc_attr( mfs_t('Full name *', 'Nombre completo *', 'Vollständiger Name *') ); ?>" required>
                </label>

                <label class="cta-form__input">
                    <span class="cta-form__label sr-only"><?php echo esc_html( mfs_t('Work Email', 'Correo de trabajo', 'Geschäftliche E-Mail') ); ?></span>
                    <input type="email" name="Email" placeholder="<?php echo esc_attr( mfs_t('Work email *', 'Correo de trabajo *', 'Geschäftliche E-Mail *') ); ?>" required>
                    <span class="cta-form__error"><?php echo esc_html( mfs_t('It is not email', 'El correo no es válido', 'Keine gültige E-Mail-Adresse') ); ?></span>
                </label>

                <label class="cta-form__input">
                    <span class="cta-form__label sr-only"><?php echo esc_html( mfs_t('Company or website', 'Empresa o sitio web', 'Unternehmen oder Website') ); ?></span>
                    <input type="text" name="Company" placeholder="<?php echo esc_attr( mfs_t('Company or website *', 'Empresa o sitio web *', 'Unternehmen oder Website *') ); ?>" required>
                </label>

                <label class="cta-form__input">
                    <span class="cta-form__label sr-only"><?php echo esc_html( mfs_t('I am a', 'Soy', 'Ich bin') ); ?></span>
                    <select name="Role">
                        <option value="" disabled selected><?php echo esc_html( mfs_t('I am a…', 'Soy…', 'Ich bin…') ); ?></option>
                        <option value="Architect / design studio"><?php echo esc_html( mfs_t('Architect / design studio', 'Arquitecto / estudio de diseño', 'Architekt / Designstudio') ); ?></option>
                        <option value="Real estate developer"><?php echo esc_html( mfs_t('Real estate developer', 'Promotora inmobiliaria', 'Immobilienentwickler') ); ?></option>
                        <option value="Product or e-commerce brand"><?php echo esc_html( mfs_t('Product or e-commerce brand', 'Marca de producto o e-commerce', 'Produkt- oder E-Commerce-Marke') ); ?></option>
                        <option value="Marketing team or agency"><?php echo esc_html( mfs_t('Marketing team or agency', 'Equipo o agencia de marketing', 'Marketing-Team oder Agentur') ); ?></option>
                        <option value="Other"><?php echo esc_html( mfs_t('Other', 'Otro', 'Sonstige') ); ?></option>
                    </select>
                </label>

                <label class="cta-form__input">
                    <span class="cta-form__label sr-only"><?php echo esc_html( mfs_t('Project type', 'Tipo de proyecto', 'Projektart') ); ?></span>
                    <select name="Project type">
                        <option value="" disabled selected><?php echo esc_html( mfs_t('Project type', 'Tipo de proyecto', 'Projektart') ); ?></option>
                        <option value="Exterior"><?php echo esc_html( mfs_t('Exterior', 'Exterior', 'Exterieur') ); ?></option>
                        <option value="Interior"><?php echo esc_html( mfs_t('Interior', 'Interior', 'Interieur') ); ?></option>
                        <option value="Product"><?php echo esc_html( mfs_t('Product', 'Producto', 'Produkt') ); ?></option>
                        <option value="Aerial"><?php echo esc_html( mfs_t('Aerial', 'Aérea', 'Luftaufnahme') ); ?></option>
                        <option value="Animation"><?php echo esc_html( mfs_t('Animation', 'Animación', 'Animation') ); ?></option>
                        <option value="Other"><?php echo esc_html( mfs_t('Other', 'Otro', 'Sonstige') ); ?></option>
                    </select>
                </label>

                <label class="cta-form__input">
                    <span class="cta-form__label sr-only"><?php echo esc_html( mfs_t('Estimated budget', 'Presupuesto estimado', 'Geschätztes Budget') ); ?></span>
                    <select name="Budget" required>
                        <option value="" disabled selected><?php echo esc_html( mfs_t('Estimated budget *', 'Presupuesto estimado *', 'Geschätztes Budget *') ); ?></option>
                        <option value="Under $3,000"><?php echo esc_html( mfs_t('Under $3,000', 'Menos de 3.000 €', 'Unter 3.000 €') ); ?></option>
                        <option value="$3,000 &ndash; $10,000"><?php echo esc_html( mfs_t('$3,000 – $10,000', '3.000 – 10.000 €', '3.000 – 10.000 €') ); ?></option>
                        <option value="$10,000+"><?php echo esc_html( mfs_t('$10,000+', 'Más de 10.000 €', 'Über 10.000 €') ); ?></option>
                    </select>
                </label>

                <label class="cta-form__input free-test-render__full">
                    <span class="cta-form__label sr-only"><?php echo esc_html( mfs_t('Link to files or references', 'Enlace a archivos o referencias', 'Link zu Dateien oder Referenzen') ); ?></span>
                    <input type="url" name="Files link" placeholder="<?php echo esc_attr( mfs_t('Link to files or references (optional)', 'Enlace a archivos o referencias (opcional)', 'Link zu Dateien oder Referenzen (optional)') ); ?>">
                </label>

                <label class="cta-form__input free-test-render__full">
                    <span class="cta-form__label sr-only"><?php echo esc_html( mfs_t('Tell us about your project', 'Cuéntanos sobre tu proyecto', 'Erzählen Sie uns von Ihrem Projekt') ); ?></span>
                    <textarea name="Message" placeholder="<?php echo esc_attr( mfs_t('Tell us about your project *', 'Cuéntanos sobre tu proyecto *', 'Erzählen Sie uns von Ihrem Projekt *') ); ?>" rows="2" required></textarea>
                </label>

                <button class="btn-main fill" type="submit"><?php echo esc_html($ftr_submit_label); ?></button>
            </form>

            <div class="cta-form__privacy free-test-render__privacy">
                <?php
                $ftr_pp_url = function_exists('mfs_privacy_url') ? mfs_privacy_url() : ( ( function_exists('get_privacy_policy_url') && get_privacy_policy_url() ) ? get_privacy_policy_url() : home_url('/privacy-policy/') );
                printf(
                    esc_html( mfs_t('By submitting, you agree to our %s.', 'Al enviar, aceptas nuestra %s.', 'Mit dem Absenden stimmen Sie unserer %s zu.') ),
                    '<a href="' . esc_url( $ftr_pp_url ) . '">' . esc_html( mfs_t('Privacy Policy', 'Política de privacidad', 'Datenschutzerklärung') ) . '</a>'
                );
                ?>
            </div>

            <div class="cta-form__success">
                <p><b><?php echo esc_html($ftr_success_ttl); ?></b></p>
                <p><?php echo esc_html($ftr_success_txt); ?></p>
            </div>
        </div>
    </section>
</div>
