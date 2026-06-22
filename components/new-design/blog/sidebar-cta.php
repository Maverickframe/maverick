<?php
/**
 * Right sidebar CTA — funnel rotator (up to 4 stages).
 * JS in blog-v1-enhancements.js toggles .is-active by scroll quartile.
 *
 * Content source (first non-empty wins):
 *   1. Per-post override  — ACF repeater `sidebar_stages` on the blog post
 *   2. Global default     — ACF repeater `sidebar_stages` on Site Options
 *   3. Baked fallback     — the $fallback array below
 *
 * Brand / proof / rating: Site Options (Blog CTA) → baked default.
 */

$mfs_lang   = mfs_lang();
$mfs_not_en = ( $mfs_lang !== 'en' );

$ctaBrand  = get_field('sidebar_cta_brand', 'options')  ?: 'Maverick Frame Studio';
$ctaProof  = ($mfs_not_en ? '' : get_field('sidebar_cta_proof', 'options'))  ?: mfs_t('Trusted by 300+ teams worldwide', 'Con la confianza de más de 300 equipos en todo el mundo', 'Über 300 Teams weltweit vertrauen uns');
$ctaRating = get_field('sidebar_cta_rating', 'options') ?: '4.9';

// Repeater: post override → global options → baked fallback.
$stages = get_field('sidebar_stages');
// On non-English pages skip the English global-options stages and use the localized
// $fallback below (until a localized `sidebar_stages` is provided per-post).
if (empty($stages) && !$mfs_not_en) {
    $stages = get_field('sidebar_stages', 'options');
}

$fallback = [
    ['eyebrow' => mfs_t('NEW HERE?', '¿NUEVO AQUÍ?', 'NEU HIER?'),    'head' => mfs_t('See our latest 3D rendering work', 'Mira nuestros últimos trabajos de renderizado 3D', 'Unsere neuesten 3D-Renderings ansehen'),          'sub' => mfs_t('A look-book of recent architectural and product visualization projects.', 'Un look-book de proyectos recientes de visualización arquitectónica y de producto.', 'Ein Look-book aktueller Architektur- und Produktvisualisierungen.'), 'label' => mfs_t('Browse portfolio', 'Ver portfolio', 'Portfolio ansehen'),    'url' => $mfs_lang === 'es' ? home_url('/es/galeria/') : ( $mfs_lang === 'de' ? home_url('/de/galerie/') : home_url('/gallery/') ),   'modal' => false],
    ['eyebrow' => mfs_t('RESOURCE', 'RECURSO', 'RESSOURCE'),     'head' => mfs_t('Get our project brief template', 'Consigue nuestra plantilla de brief de proyecto', 'Hol dir unsere Projekt-Brief-Vorlage'),             'sub' => mfs_t('A one-pager we use to scope CGI projects — scope, deliverables, milestones.', 'Una página que usamos para planificar proyectos CGI: alcance, entregables, hitos.', 'Ein Onepager, mit dem wir CGI-Projekte planen — Umfang, Ergebnisse, Meilensteine.'), 'label' => mfs_t('Download PDF', 'Descargar PDF', 'PDF herunterladen'),        'url' => home_url('/contacts/'),  'modal' => false],
    ['eyebrow' => mfs_t('SOCIAL PROOF', 'PRUEBA SOCIAL', 'SOCIAL PROOF'), 'head' => mfs_t('300+ teams trust Maverick Frame', 'Más de 300 equipos confían en Maverick Frame', 'Über 300 Teams vertrauen Maverick Frame'),            'sub' => mfs_t('See how our visuals helped brands and developers sell faster.', 'Mira cómo nuestros visuales ayudaron a marcas y promotores a vender más rápido.', 'Sieh, wie unsere Visuals Marken und Entwicklern geholfen haben, schneller zu verkaufen.'),               'label' => mfs_t('Read case studies', 'Leer casos de éxito', 'Fallstudien lesen'),   'url' => $mfs_lang === 'es' ? home_url('/es/casos-de-exito/') : ( $mfs_lang === 'de' ? home_url('/de/referenzen/') : home_url('/success-stories/') ), 'modal' => false],
    ['eyebrow' => mfs_t('TALK TO US', 'HABLEMOS', 'SPRICH MIT UNS'),   'head' => mfs_t('Working on a project like this?', '¿Trabajas en un proyecto como este?', 'Arbeitest du an einem ähnlichen Projekt?'),            'sub' => mfs_t('Free 15-min consultation. No commitment — just a quick chat about your project.', 'Consulta gratuita de 15 min. Sin compromiso: solo una charla rápida sobre tu proyecto.', 'Kostenlose 15-minütige Beratung. Unverbindlich — einfach ein kurzes Gespräch über dein Projekt.'), 'label' => mfs_t('Book a 15-min call', 'Reserva una llamada de 15 min', '15-Min-Gespräch buchen'), 'url' => '#book', 'modal' => true],
];

if (empty($stages)) {
    $stages = $fallback;
}

// Normalise to a clean 1-based list (max 4) with consistent keys.
$rows = [];
$i = 0;
foreach ($stages as $s) {
    if ($i >= 4) break;
    $i++;
    $rows[$i] = [
        'eyebrow' => $s['eyebrow'] ?? '',
        'head'    => $s['head']    ?? '',
        'sub'     => $s['sub']     ?? '',
        'label'   => $s['label']   ?? '',
        'url'     => $s['url']     ?: '#book',
        'modal'   => !empty($s['modal']),
    ];
}
if (empty($rows)) return;
?>
<aside class="sidebar-cta" data-cta-rotator>

    <div class="sidebar-cta__brand">
        <span class="sidebar-cta__monogram" aria-hidden="true">
            <img src="<?php echo get_template_directory_uri_vite(); ?>/img/logo.svg" alt="Maverick Frame Studio logo" width="40" height="40">
        </span>
        <p class="sidebar-cta__brand-text">
            <span class="sidebar-cta__brand-eyebrow"><?= mfs_t('From', 'De', 'Ab'); ?> <?= esc_html($ctaBrand); ?></span>
        </p>
    </div>

    <div class="sidebar-cta__stages">
        <?php foreach ($rows as $i => $s): ?>
            <div class="sidebar-cta__stage<?= $i === 1 ? ' is-active' : ''; ?>" data-stage="<?= $i; ?>">
                <span class="sidebar-cta__eyebrow"><?= esc_html($s['eyebrow']); ?></span>
                <h3 class="sidebar-cta__head"><?= esc_html($s['head']); ?></h3>
                <p class="sidebar-cta__sub"><?= esc_html($s['sub']); ?></p>
                <a class="sidebar-cta__btn<?= $s['modal'] ? ' js-modal-open' : ''; ?>" href="<?= esc_url($s['url']); ?>"<?= $s['modal'] ? ' data-modal="book"' : ''; ?>>
                    <span><?= esc_html($s['label']); ?></span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 12h14"/>
                        <path d="M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="sidebar-cta__trust">
        <div class="sidebar-cta__stars" aria-label="<?= esc_attr($ctaRating); ?> stars">
            <?php for ($i = 0; $i < 5; $i++): ?>
                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2l3 7h7l-5.5 4.5L18 21l-6-4-6 4 1.5-7.5L2 9h7z"/></svg>
            <?php endfor; ?>
            <span class="sidebar-cta__rating-num"><?= esc_html($ctaRating); ?></span>
        </div>
        <p class="sidebar-cta__proof"><?= esc_html($ctaProof); ?></p>
    </div>

</aside>
