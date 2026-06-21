<?php
/**
 * Lead Quiz — interactive, branching client quiz (homepage section).
 * Routes the visitor into one of three service lines (CGI / Web / Creative),
 * then runs a short tailored path. Self-contained: copy + style-step images
 * baked in. Lead + all answers POST to forms/amo.php via quiz.js.
 *
 * Per-page config via ACF (block acf/quiz): quiz_mode = 'router' (homepage,
 * branching) | 'service' (single linear path for a service/landing page).
 * Router mode keeps the hardcoded tree below; service mode renders its steps
 * from the quiz_steps repeater and its result screen from the quiz_result_*
 * fields, so content can be edited without touching code.
 */

$mfsq_mode    = get_field('quiz_mode') ?: 'router';
$mfsq_eyebrow = get_field('quiz_eyebrow');
$mfsq_eyebrow = ( $mfsq_eyebrow !== '' && $mfsq_eyebrow !== null ) ? $mfsq_eyebrow : mfs_t('30-second quiz', 'Quiz de 30 segundos', 'Quiz in 30 Sekunden');
$mfsq_heading = get_field('quiz_heading');
$mfsq_heading = ( $mfsq_heading !== '' && $mfsq_heading !== null ) ? $mfsq_heading : mfs_t('Not sure where to start? Find your fit', '¿No sabes por dónde empezar? Encuentra tu opción', 'Nicht sicher, wo du anfangen sollst? Finde deine Lösung');
$mfsq_intro   = get_field('quiz_intro');
$mfsq_intro   = ( $mfsq_intro !== '' && $mfsq_intro !== null ) ? $mfsq_intro : mfs_t('Answer a few quick questions and we’ll match you with the right service — plus a free, tailored next step for your project.', 'Responde unas preguntas rápidas y te emparejamos con el servicio adecuado — además de un siguiente paso gratuito y personalizado para tu proyecto.', 'Beantworte ein paar kurze Fragen und wir finden die passende Leistung für dich — plus einen kostenlosen, individuellen nächsten Schritt für dein Projekt.');
$mfsq_lead_title = get_field('quiz_lead_title');
if ( ! $mfsq_lead_title ) {
    $mfsq_lead_title = ( $mfsq_mode === 'service' ) ? ( get_the_title() . ' / Quiz' ) : 'Homepage / Quiz';
}

// CGI "look" step — 4 subject-matched renders per subject. Absolute prod URLs
// so they render in any environment (staging has a separate media library).
// Swap a URL here to recurate the look step. Keys must match Q1 data-v values.
$U = 'https://maverickframe.com/wp-content/uploads/';
$looks = array(
    'Exterior' => array(
        array('Bright & photoreal', $U . '2026/05/hero-image-modern-house-lawn.webp'),
        array('Warm & inviting',    $U . '2026/05/hero-image-snow-cabin-mountains.webp'),
        array('Moody & cinematic',  $U . '2026/05/hero-image-modern-house-cliff.webp'),
        array('Clean & minimal',    $U . '2026/05/hero-image-apartment-building.webp'),
    ),
    'Interior' => array(
        array('Bright & photoreal', $U . '2026/03/residence-interior-design-open-living-room-garden-views.webp'),
        array('Warm & inviting',    $U . '2026/06/best-3d-interior-rendering-companies-hotel-lounge-rendering.webp'),
        array('Moody & cinematic',  $U . '2026/02/island-hotel_render-16.webp'),
        array('Clean & minimal',    $U . '2026/05/hero-image-minimal-kitchen-dining.webp'),
    ),
    'Product' => array(
        array('Bright & photoreal', $U . '2026/05/hero-image-speaker-amplifier.webp'),
        array('Warm & inviting',    $U . '2026/05/hero-image-seranova-packaging.webp'),
        array('Moody & cinematic',  $U . '2026/05/hero-image-espresso-machine-black.webp'),
        array('Clean & minimal',    $U . '2026/05/hero-image-supplement-jar-green.webp'),
    ),
    'Vehicle' => array(
        array('Bright & photoreal', $U . '2026/05/hero-image-yacht-aerial.webp'),
        array('Warm & inviting',    $U . '2026/05/hero-image-sailboat-sea.webp'),
        array('Moody & cinematic',  $U . '2026/05/hero-image-sports-car-motion.webp'),
        array('Clean & minimal',    $U . '2026/05/hero-image-boat-lake-mountains.webp'),
    ),
    'Development' => array(
        array('Bright & photoreal', $U . '2026/05/hero-image-overwater-villas-aerial.webp'),
        array('Warm & inviting',    $U . '2026/05/hero-image-forest-cabins.webp'),
        array('Moody & cinematic',  $U . '2026/04/site-plan-and-aerial-rendering-services-forest-community.webp'),
        array('Clean & minimal',    $U . '2026/04/landscape-rendering-services-lakeside-cabins-aerial.webp'),
    ),
);
?>

<section class="mfsq">
    <div class="container container_small">
        <div class="mfsq__intro">
            <p class="section-subtitle"><?php echo esc_html( $mfsq_eyebrow ); ?></p>
            <h2><?php echo esc_html( $mfsq_heading ); ?></h2>
            <p class="mfsq__sub"><?php echo esc_html( $mfsq_intro ); ?></p>
        </div>

        <div class="mfsq__card js-mfsq" data-mode="<?php echo esc_attr( $mfsq_mode ); ?>" data-title="<?php echo esc_attr( $mfsq_lead_title ); ?>" data-amo="<?php echo esc_url( home_url('/wp-content/themes/maverickframe/forms/amo.php') ); ?>">
            <?php if ( $mfsq_mode === 'service' ) {
                $mfsq_res = array(
                    'head'    => get_field('quiz_result_head') ?: '',
                    'service' => get_field('quiz_result_service') ?: '',
                    'note'    => get_field('quiz_result_note') ?: '',
                    'gateSub' => get_field('quiz_gate_sub') ?: '',
                );
                echo '<script type="application/json" data-mfsq-result>' . wp_json_encode( $mfsq_res ) . '</script>';
            } else { ?>
            <script type="application/json" data-mfsq-looks><?php echo wp_json_encode( $looks ); ?></script>
            <?php } ?>

            <div class="mfsq__head">
                <span class="mfsq__count" data-count><?php echo esc_html( mfs_t('Step 1', 'Paso 1', 'Schritt 1') ); ?></span>
                <div class="mfsq__bar" data-bar></div>
            </div>

            <div data-steps>

                <?php if ( $mfsq_mode === 'service' ) :
                    $mfsq_steps = get_field('quiz_steps');
                    if ( ! is_array( $mfsq_steps ) ) { $mfsq_steps = array(); }
                    $mfsq_i = 0;
                    foreach ( $mfsq_steps as $mfsq_step ) :
                        $mfsq_q = isset( $mfsq_step['step_question'] ) ? trim( $mfsq_step['step_question'] ) : '';
                        $mfsq_opts = ( isset( $mfsq_step['step_options'] ) && is_array( $mfsq_step['step_options'] ) ) ? $mfsq_step['step_options'] : array();
                        if ( $mfsq_q === '' || empty( $mfsq_opts ) ) { continue; }
                        $mfsq_has_img = false;
                        foreach ( $mfsq_opts as $mfsq_o ) { if ( ! empty( $mfsq_o['opt_image'] ) ) { $mfsq_has_img = true; break; } }
                        ?>
                        <div class="mfsq__step<?php echo $mfsq_i === 0 ? ' is-on' : ''; ?>" data-q="s<?php echo (int) $mfsq_i; ?>" data-label="<?php echo esc_attr( $mfsq_q ); ?>">
                            <h3><?php echo esc_html( $mfsq_q ); ?></h3>
                            <?php if ( $mfsq_has_img ) : ?>
                                <div class="mfsq__looks">
                                    <?php foreach ( $mfsq_opts as $mfsq_o ) :
                                        $mfsq_l = isset( $mfsq_o['opt_label'] ) ? trim( $mfsq_o['opt_label'] ) : '';
                                        if ( $mfsq_l === '' ) { continue; }
                                        $mfsq_img = ! empty( $mfsq_o['opt_image'] ) ? wp_get_attachment_image_url( $mfsq_o['opt_image'], 'medium' ) : '';
                                        ?>
                                        <button class="mfsq__look" data-v="<?php echo esc_attr( $mfsq_l ); ?>">
                                            <?php if ( $mfsq_img ) : ?><span class="mfsq__look-img"><img src="<?php echo esc_url( $mfsq_img ); ?>" loading="lazy" alt="<?php echo esc_attr( $mfsq_l ); ?>"></span><?php endif; ?>
                                            <span class="mfsq__look-cap"><?php echo esc_html( $mfsq_l ); ?></span>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            <?php else : ?>
                                <div class="mfsq__opts">
                                    <?php foreach ( $mfsq_opts as $mfsq_o ) :
                                        $mfsq_l = isset( $mfsq_o['opt_label'] ) ? trim( $mfsq_o['opt_label'] ) : '';
                                        if ( $mfsq_l === '' ) { continue; }
                                        ?>
                                        <button class="mfsq__opt" data-v="<?php echo esc_attr( $mfsq_l ); ?>"><?php echo esc_html( $mfsq_l ); ?></button>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php $mfsq_i++;
                    endforeach;
                else : ?>

                <!-- Q1 router (shared) -->
                <div class="mfsq__step is-on" data-q="route">
                    <h3><?php echo mfs_t('What can we help you create?', '¿Qué podemos ayudarte a crear?', 'Womit können wir dir helfen?'); ?></h3>
                    <div class="mfsq__opts">
                        <button class="mfsq__opt" data-branch="cgi" data-v="3D visuals &amp; CGI"><?php echo mfs_t('3D visuals &amp; CGI', 'Visuales 3D y CGI', '3D-Visualisierung &amp; CGI'); ?></button>
                        <button class="mfsq__opt" data-branch="web" data-v="Website or app"><?php echo mfs_t('Website or app', 'Sitio web o app', 'Website oder App'); ?></button>
                        <button class="mfsq__opt" data-branch="creative" data-v="Branding &amp; creative"><?php echo mfs_t('Branding &amp; creative', 'Branding y creatividad', 'Branding &amp; Kreativ'); ?></button>
                    </div>
                </div>

                <!-- ===== CGI branch ===== -->
                <div class="mfsq__step" data-q="subject" data-branch="cgi">
                    <h3><?php echo mfs_t('What are you visualizing?', '¿Qué estás visualizando?', 'Was möchtest du visualisieren?'); ?></h3>
                    <div class="mfsq__opts">
                        <button class="mfsq__opt" data-v="Exterior"><?php echo mfs_t('Building exterior', 'Exterior de edificio', 'Gebäude-Außenansicht'); ?></button>
                        <button class="mfsq__opt" data-v="Interior"><?php echo mfs_t('Interior space', 'Espacio interior', 'Innenraum'); ?></button>
                        <button class="mfsq__opt" data-v="Product"><?php echo mfs_t('Product or furniture', 'Producto o mobiliario', 'Produkt oder Möbel'); ?></button>
                        <button class="mfsq__opt" data-v="Vehicle"><?php echo mfs_t('Yacht, car or aircraft', 'Yate, coche o avión', 'Yacht, Auto oder Flugzeug'); ?></button>
                        <button class="mfsq__opt" data-v="Development"><?php echo mfs_t('Development, site or aerial', 'Desarrollo, terreno o aéreo', 'Projekt, Gelände oder Luftaufnahme'); ?></button>
                    </div>
                </div>

                <div class="mfsq__step" data-q="look" data-branch="cgi">
                    <h3><?php echo mfs_t('Pick the look you love', 'Elige el estilo que te encanta', 'Wähle den Look, der dir gefällt'); ?></h3>
                    <div class="mfsq__looks" data-looks></div>
                </div>

                <div class="mfsq__step" data-q="goal" data-branch="cgi">
                    <h3><?php echo mfs_t('What&rsquo;s the main goal?', '¿Cuál es el objetivo principal?', 'Was ist das Hauptziel?'); ?></h3>
                    <div class="mfsq__opts">
                        <button class="mfsq__opt" data-v="Sell faster"><?php echo mfs_t('Sell faster', 'Vender más rápido', 'Schneller verkaufen'); ?></button>
                        <button class="mfsq__opt" data-v="Win approvals"><?php echo mfs_t('Win approvals', 'Conseguir aprobaciones', 'Genehmigungen erhalten'); ?></button>
                        <button class="mfsq__opt" data-v="Market &amp; advertise"><?php echo mfs_t('Market &amp; advertise', 'Marketing y publicidad', 'Marketing &amp; Werbung'); ?></button>
                        <button class="mfsq__opt" data-v="Impress investors"><?php echo mfs_t('Impress investors', 'Impresionar a inversores', 'Investoren überzeugen'); ?></button>
                    </div>
                </div>

                <div class="mfsq__step" data-q="stage" data-branch="cgi">
                    <h3><?php echo mfs_t('Where&rsquo;s your project now?', '¿En qué fase está tu proyecto?', 'Wo steht dein Projekt aktuell?'); ?></h3>
                    <div class="mfsq__opts">
                        <button class="mfsq__opt" data-v="Just an idea"><?php echo mfs_t('Just an idea', 'Solo una idea', 'Nur eine Idee'); ?></button>
                        <button class="mfsq__opt" data-v="In design"><?php echo mfs_t('In design', 'En diseño', 'In der Gestaltung'); ?></button>
                        <button class="mfsq__opt" data-v="Files ready"><?php echo mfs_t('Files are ready', 'Archivos listos', 'Dateien sind bereit'); ?></button>
                    </div>
                </div>

                <div class="mfsq__step" data-q="volume" data-branch="cgi">
                    <h3><?php echo mfs_t('How much do you need?', '¿Cuánto necesitas?', 'Wie viel brauchst du?'); ?></h3>
                    <div class="mfsq__opts">
                        <button class="mfsq__opt" data-v="One project"><?php echo mfs_t('One project', 'Un proyecto', 'Ein Projekt'); ?></button>
                        <button class="mfsq__opt" data-v="Ongoing stream"><?php echo mfs_t('Ongoing stream', 'Flujo continuo', 'Laufender Bedarf'); ?></button>
                    </div>
                </div>

                <!-- ===== Web &amp; app branch ===== -->
                <div class="mfsq__step" data-q="webtype" data-branch="web">
                    <h3><?php echo mfs_t('What do you need?', '¿Qué necesitas?', 'Was brauchst du?'); ?></h3>
                    <div class="mfsq__opts">
                        <button class="mfsq__opt" data-v="Website"><?php echo mfs_t('A new website', 'Un nuevo sitio web', 'Eine neue Website'); ?></button>
                        <button class="mfsq__opt" data-v="Landing page"><?php echo mfs_t('A landing page', 'Una landing page', 'Eine Landingpage'); ?></button>
                        <button class="mfsq__opt" data-v="Mobile app"><?php echo mfs_t('A mobile app', 'Una app móvil', 'Eine mobile App'); ?></button>
                        <button class="mfsq__opt" data-v="UI/UX redesign"><?php echo mfs_t('UI/UX redesign', 'Rediseño UI/UX', 'UI/UX-Redesign'); ?></button>
                    </div>
                </div>

                <div class="mfsq__step" data-q="webgoal" data-branch="web">
                    <h3><?php echo mfs_t('What&rsquo;s the main goal?', '¿Cuál es el objetivo principal?', 'Was ist das Hauptziel?'); ?></h3>
                    <div class="mfsq__opts">
                        <button class="mfsq__opt" data-v="Launch something new"><?php echo mfs_t('Launch something new', 'Lanzar algo nuevo', 'Etwas Neues launchen'); ?></button>
                        <button class="mfsq__opt" data-v="Redesign &amp; modernize"><?php echo mfs_t('Redesign &amp; modernize', 'Rediseñar y modernizar', 'Neugestalten &amp; modernisieren'); ?></button>
                        <button class="mfsq__opt" data-v="Increase conversions"><?php echo mfs_t('Increase conversions', 'Aumentar conversiones', 'Conversions steigern'); ?></button>
                        <button class="mfsq__opt" data-v="Impress investors"><?php echo mfs_t('Impress investors', 'Impresionar a inversores', 'Investoren überzeugen'); ?></button>
                    </div>
                </div>

                <div class="mfsq__step" data-q="webstage" data-branch="web">
                    <h3><?php echo mfs_t('Where are you now?', '¿Dónde estás ahora?', 'Wo stehst du gerade?'); ?></h3>
                    <div class="mfsq__opts">
                        <button class="mfsq__opt" data-v="Just an idea"><?php echo mfs_t('Just an idea', 'Solo una idea', 'Nur eine Idee'); ?></button>
                        <button class="mfsq__opt" data-v="Brand &amp; content ready"><?php echo mfs_t('Brand &amp; content ready', 'Marca y contenido listos', 'Marke &amp; Inhalte bereit'); ?></button>
                        <button class="mfsq__opt" data-v="Have a live site"><?php echo mfs_t('Have a live site to improve', 'Tengo un sitio activo que mejorar', 'Habe eine Live-Website zum Verbessern'); ?></button>
                    </div>
                </div>

                <!-- ===== Branding &amp; creative branch ===== -->
                <div class="mfsq__step" data-q="crtype" data-branch="creative">
                    <h3><?php echo mfs_t('What do you need?', '¿Qué necesitas?', 'Was brauchst du?'); ?></h3>
                    <div class="mfsq__opts">
                        <button class="mfsq__opt" data-v="Brand identity"><?php echo mfs_t('Brand identity', 'Identidad de marca', 'Markenidentität'); ?></button>
                        <button class="mfsq__opt" data-v="Social media content"><?php echo mfs_t('Social media content', 'Contenido para redes sociales', 'Social-Media-Content'); ?></button>
                        <button class="mfsq__opt" data-v="Presentation / pitch deck"><?php echo mfs_t('Presentation / pitch deck', 'Presentación / pitch deck', 'Präsentation / Pitch-Deck'); ?></button>
                        <button class="mfsq__opt" data-v="FOOH / CGI ad"><?php echo mfs_t('FOOH / CGI ad', 'Anuncio FOOH / CGI', 'FOOH- / CGI-Werbung'); ?></button>
                    </div>
                </div>

                <div class="mfsq__step" data-q="crgoal" data-branch="creative">
                    <h3><?php echo mfs_t('What&rsquo;s the main goal?', '¿Cuál es el objetivo principal?', 'Was ist das Hauptziel?'); ?></h3>
                    <div class="mfsq__opts">
                        <button class="mfsq__opt" data-v="Launch a brand"><?php echo mfs_t('Launch a brand', 'Lanzar una marca', 'Eine Marke launchen'); ?></button>
                        <button class="mfsq__opt" data-v="Refresh our look"><?php echo mfs_t('Refresh our look', 'Renovar nuestra imagen', 'Unseren Look auffrischen'); ?></button>
                        <button class="mfsq__opt" data-v="Drive engagement"><?php echo mfs_t('Drive engagement', 'Impulsar el engagement', 'Engagement steigern'); ?></button>
                        <button class="mfsq__opt" data-v="Win investors"><?php echo mfs_t('Win investors', 'Ganar inversores', 'Investoren gewinnen'); ?></button>
                    </div>
                </div>

                <div class="mfsq__step" data-q="crstage" data-branch="creative">
                    <h3><?php echo mfs_t('Where are you now?', '¿Dónde estás ahora?', 'Wo stehst du gerade?'); ?></h3>
                    <div class="mfsq__opts">
                        <button class="mfsq__opt" data-v="Starting from scratch"><?php echo mfs_t('Starting from scratch', 'Empezando desde cero', 'Von Grund auf neu'); ?></button>
                        <button class="mfsq__opt" data-v="Have some assets"><?php echo mfs_t('Have some assets', 'Tengo algunos recursos', 'Habe einige Assets'); ?></button>
                        <button class="mfsq__opt" data-v="Rebranding existing"><?php echo mfs_t('Rebranding existing', 'Rebranding de lo existente', 'Bestehendes Rebranding'); ?></button>
                    </div>
                </div>

                <?php endif; ?>

                <!-- ===== Shared gate + result ===== -->
                <div class="mfsq__step" data-q="gate">
                    <h3><?php echo esc_html( mfs_t('Almost there — where do we send it?', 'Casi listo — ¿a dónde te lo enviamos?', 'Fast geschafft — wohin sollen wir es schicken?') ); ?></h3>
                    <p class="mfsq__gate-sub" data-gate-sub><?php echo esc_html( mfs_t('Your tailored plan and a free next step for your project.', 'Tu plan personalizado y un siguiente paso gratuito para tu proyecto.', 'Dein individueller Plan und ein kostenloser nächster Schritt für dein Projekt.') ); ?></p>
                    <input type="text" data-name placeholder="<?php echo esc_attr( mfs_t('Your name', 'Tu nombre', 'Dein Name') ); ?>" class="mfsq__input">
                    <input type="email" data-email placeholder="<?php echo esc_attr( mfs_t('Work email*', 'Correo de trabajo*', 'Geschäftliche E-Mail*') ); ?>" class="mfsq__input">
                    <button class="mfsq__submit" data-reveal type="button"><?php echo esc_html( mfs_t('Reveal my plan', 'Ver mi plan', 'Meinen Plan anzeigen') ); ?></button>
                    <p class="mfsq__fine"><?php echo esc_html( mfs_t('No spam — we only email about your project.', 'Sin spam — solo te escribimos sobre tu proyecto.', 'Kein Spam — wir schreiben dir nur zu deinem Projekt.') ); ?></p>
                </div>

                <div class="mfsq__step" data-q="result"></div>
            </div>

            <button class="mfsq__back" data-back type="button"><?php echo esc_html( mfs_t('← Back', '← Volver', '← Zurück') ); ?></button>
        </div>
    </div>
</section>
