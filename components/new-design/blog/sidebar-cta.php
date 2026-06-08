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

$ctaBrand  = get_field('sidebar_cta_brand', 'options')  ?: 'Maverick Frame Studio';
$ctaProof  = get_field('sidebar_cta_proof', 'options')  ?: 'Trusted by 300+ teams worldwide';
$ctaRating = get_field('sidebar_cta_rating', 'options') ?: '4.9';

// Repeater: post override → global options → baked fallback.
$stages = get_field('sidebar_stages');
if (empty($stages)) {
    $stages = get_field('sidebar_stages', 'options');
}

$fallback = [
    ['eyebrow' => 'NEW HERE?',    'head' => 'See our latest 3D rendering work',          'sub' => 'A look-book of recent architectural and product visualization projects.', 'label' => 'Browse portfolio',    'url' => home_url('/gallery/'),   'modal' => false],
    ['eyebrow' => 'RESOURCE',     'head' => 'Get our project brief template',             'sub' => 'A one-pager we use to scope CGI projects — scope, deliverables, milestones.', 'label' => 'Download PDF',        'url' => home_url('/contacts/'),  'modal' => false],
    ['eyebrow' => 'SOCIAL PROOF', 'head' => '300+ teams trust Maverick Frame',            'sub' => 'See how our visuals helped brands and developers sell faster.',               'label' => 'Read case studies',   'url' => home_url('/success-stories/'), 'modal' => false],
    ['eyebrow' => 'TALK TO US',   'head' => 'Working on a project like this?',            'sub' => 'Free 15-min consultation. No commitment — just a quick chat about your project.', 'label' => 'Book a 15-min call', 'url' => '#book', 'modal' => true],
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
            <span class="sidebar-cta__brand-eyebrow">From <?= esc_html($ctaBrand); ?></span>
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
