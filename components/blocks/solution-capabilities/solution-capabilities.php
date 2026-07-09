<?php
/**
 * Block: Solution Capabilities (carousel)
 * Full-width track. Resting state = image only; chips/title/description reveal on hover.
 * (Video-on-hover playback comes later — poster image for now.)
 */

$eyebrow  = get_field('eyebrow') ?: mfs_t('Creative capabilities', 'Capacidades creativas', 'Kreative Leistungen');
$title    = get_field('title') ?: mfs_t('Production-level support from a creative production studio', 'Soporte a nivel producción de un estudio de producción creativa', 'Produktionsstarke Unterstützung von einem Kreativproduktionsstudio');
$subtext  = get_field('subtext') ?: mfs_t('Our creative production services are built for agencies delivering high-volume campaigns, launches, and digital marketing projects.', 'Nuestros servicios de producción creativa están pensados para agencias que ejecutan campañas de gran volumen, lanzamientos y proyectos de marketing digital.', 'Unsere Kreativproduktions-Leistungen sind für Agenturen gemacht, die Kampagnen mit hohem Volumen, Launches und digitale Marketing-Projekte umsetzen.');
$btn_text = get_field('btn_text') ?: mfs_t('All services', 'Todos los servicios', 'Alle Leistungen');
// The "All services" CTA points at the services hub. On /de/ that hub is /de/leistungen/
// (a saved English '/services/' value would otherwise leak through), so force the DE URL there.
if ( function_exists('mfs_is') && mfs_is('de') ) {
    $btn_link = home_url('/de/leistungen/');
} else {
    $btn_link = get_field('btn_link') ?: '/services/';
}

// Per-page cards via ACF repeater; fall back to the default capability cards.
$acf_cards = get_field('cards');
$cards = array();
if ($acf_cards) {
    foreach ($acf_cards as $row) {
        $chips = array();
        if (!empty($row['chips'])) {
            foreach ($row['chips'] as $cr) { if (!empty($cr['chip'])) { $chips[] = $cr['chip']; } }
        }
        $cards[] = array(
            'img'   => $row['image'] ?: 0,
            'video' => !empty($row['video']),
            'chips' => $chips,
            'title' => isset($row['title']) ? $row['title'] : '',
            'desc'  => isset($row['desc']) ? $row['desc'] : '',
        );
    }
}
if (empty($cards)) $cards = array(
    array('img' => 16176, 'video' => true,  'chips' => array(mfs_t('Exterior rendering', 'Render exterior', 'Außenrendering'), mfs_t('Interior rendering', 'Render interior', 'Innenrendering')),                                            'title' => mfs_t('Exterior & Architectural Rendering', 'Render exterior y arquitectónico', 'Außen- & Architekturrendering'),         'desc' => mfs_t('High-quality architectural rendering used in property marketing campaigns, real estate presentations, and development launches.', 'Render arquitectónico de alta calidad para campañas de marketing inmobiliario, presentaciones de proyectos y lanzamientos de promociones.', 'Hochwertiges Architekturrendering für Immobilien-Marketingkampagnen, Projektpräsentationen und Bauträger-Launches.')),
    array('img' => 16187, 'video' => false, 'chips' => array(mfs_t('Product showcase', 'Showcase de producto', 'Produkt-Showcase'), mfs_t('Featured products', 'Productos destacados', 'Highlight-Produkte')),                                                'title' => mfs_t('Product Rendering', 'Render de producto', 'Produktrendering'),                          'desc' => mfs_t('High-end product rendering for marketing visuals, product launches, and e-commerce campaigns.', 'Render de producto de alta gama para visuales de marketing, lanzamientos de producto y campañas de ecommerce.', 'High-End-Produktrendering für Marketing-Visuals, Produkt-Launches und E-Commerce-Kampagnen.')),
    array('img' => 16712, 'video' => false, 'chips' => array(mfs_t('Pitch deck', 'Pitch deck', 'Pitch-Deck'), mfs_t('Investor deck', 'Investor deck', 'Investor-Deck'), mfs_t('Sales deck', 'Sales deck', 'Sales-Deck'), mfs_t('Events & keynotes', 'Eventos y keynotes', 'Events & Keynotes'), mfs_t('Business presentation', 'Presentación de empresa', 'Unternehmenspräsentation')), 'title' => mfs_t('Presentation Design', 'Diseño de presentaciones', 'Präsentationsdesign'),                        'desc' => mfs_t('Professional presentation design for client pitches, investor decks, and campaign presentations.', 'Diseño de presentaciones profesional para pitches de clientes, investor decks y presentaciones de campaña.', 'Professionelles Präsentationsdesign für Kundenpitches, Investor-Decks und Kampagnenpräsentationen.')),
    array('img' => 17136, 'video' => true,  'chips' => array(mfs_t('Architectural animation', 'Animación arquitectónica', 'Architekturanimation'), mfs_t('Animated video', 'Vídeo animado', 'Animiertes Video')),                                            'title' => mfs_t('Architectural Animation & Motion Graphics', 'Animación arquitectónica y motion graphics', 'Architekturanimation & Motion Graphics'),  'desc' => mfs_t('Advanced 3D animation and motion graphics used for digital campaigns, landing pages, and promotional videos.', 'Animación 3D y motion graphics avanzados para campañas digitales, landing pages y vídeos promocionales.', 'Fortgeschrittene 3D-Animation und Motion Graphics für digitale Kampagnen, Landingpages und Werbevideos.')),
    array('img' => 16177, 'video' => false, 'chips' => array(mfs_t('Organic', 'Orgánico', 'Organisch'), mfs_t('Concept design', 'Diseño de concepto', 'Konzeptdesign'), mfs_t('Video', 'Vídeo', 'Video')),                                                    'title' => mfs_t('Social Media Creative', 'Creatividades para redes sociales', 'Social-Media-Creatives'),                      'desc' => mfs_t('High-performing social media creative assets designed for modern campaign distribution across digital platforms.', 'Creatividades para redes sociales de alto rendimiento, diseñadas para la distribución de campañas en plataformas digitales.', 'Leistungsstarke Social-Media-Creatives für die moderne Kampagnen-Distribution über digitale Plattformen.')),
    array('img' => 15238, 'video' => false, 'chips' => array(mfs_t('UI/UX design', 'Diseño UI/UX', 'UI/UX-Design'), mfs_t('Web design', 'Diseño web', 'Webdesign'), mfs_t('Landing page', 'Landing page', 'Landingpage'), mfs_t('Design system', 'Design system', 'Designsystem')),                           'title' => mfs_t('Web & Landing Page Design', 'Diseño web y de landing pages', 'Web- & Landingpage-Design'),                  'desc' => mfs_t('Conversion-driven landing page design built around campaign messaging and visual storytelling.', 'Diseño de landing pages orientado a conversión, construido en torno al mensaje de campaña y el storytelling visual.', 'Conversion-orientiertes Landingpage-Design rund um Kampagnenbotschaft und visuelles Storytelling.')),
);
?>
<section class="sol-cap">
    <div class="container">
        <div class="sol-cap__head">
            <span class="sol-cap__eyebrow"><span class="sol-cap__dot"></span><?php echo esc_html($eyebrow); ?></span>
            <h2 class="sol-cap__title"><?php echo esc_html($title); ?></h2>
            <p class="sol-cap__subtext"><?php echo esc_html($subtext); ?></p>
        </div>
    </div>

    <div class="sol-cap__track-wrap">
        <?php
            // Pure-CSS marquee (was Splide + AutoScroll). Track rendered ×2 for a seamless
            // translateX(-50%) loop; when there are few cards, repeat the set enough times
            // ($set_reps) so one half still overflows the widest viewport (~2000px @ ~475px/card).
            // Only the very first pass is exposed to assistive tech; the rest are aria-hidden.
            $card_count = count( $cards );
            $set_reps   = max( 1, (int) ceil( 2000 / ( max( 1, $card_count ) * 475 ) ) );
        ?>
        <div class="mfs-marquee sol-cap__marquee" role="group" aria-label="<?php echo esc_attr( mfs_t('Creative capabilities', 'Capacidades creativas', 'Kreative Leistungen') ); ?>">
            <ul class="mfs-marquee__track">
                <?php for ( $half = 0; $half < 2; $half++ ) : ?>
                    <?php for ( $r = 0; $r < $set_reps; $r++ ) : ?>
                        <?php foreach ($cards as $c) :
                            $url  = $c['img'] ? wp_get_attachment_image_url($c['img'], 'large') : '';
                            $dupe = ( $half || $r ); // only first pass ($half=0,$r=0) exposed to AT
                        ?>
                            <li class="mfs-marquee__item sol-cap__card<?php echo !empty($c['video']) ? ' sol-cap__card--video' : ''; ?>"<?php echo $dupe ? ' aria-hidden="true"' : ''; ?><?php echo $url ? ' style="background-image:url(\'' . esc_url($url) . '\')"' : ''; ?>>
                                <span class="sol-cap__card-overlay" aria-hidden="true"></span>
                                <?php if (!empty($c['chips'])) : ?>
                                    <div class="sol-cap__chips">
                                        <?php foreach ($c['chips'] as $chip) : ?>
                                            <span class="sol-cap__chip"><?php echo esc_html($chip); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <div class="sol-cap__card-body">
                                    <h3 class="sol-cap__card-title"><?php echo esc_html($c['title']); ?></h3>
                                    <p class="sol-cap__card-desc"><?php echo esc_html($c['desc']); ?></p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php endfor; ?>
                <?php endfor; ?>
            </ul>
        </div>
    </div>

    <?php if ($btn_text) : ?>
        <div class="container">
            <div class="sol-cap__footer">
                <a href="<?php echo esc_url($btn_link); ?>" class="sol-cap__all"><?php echo esc_html($btn_text); ?></a>
            </div>
        </div>
    <?php endif; ?>
</section>
