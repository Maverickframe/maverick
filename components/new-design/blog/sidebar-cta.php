<?php
/**
 * Right sidebar CTA — funnel rotator (4 stages).
 * JS in blog-v1-enhancements.js toggles .is-active by scroll progress.
 *
 *   Stage 1 (0-25%)   — Browse portfolio       (soft entry)
 *   Stage 2 (25-50%)  — Download brief         (resource)
 *   Stage 3 (50-75%)  — Case study             (social proof)
 *   Stage 4 (75-100%) — Book a 15-min call     (conversion)
 *
 * Each stage is overridable via ACF; sensible defaults baked in.
 * Static trust footer stays visible across all stages.
 */

$ctaBrand = get_field('sidebar_cta_brand') ?: 'Maverick Frame Studio';
$ctaProof = get_field('sidebar_cta_proof') ?: 'Trusted by 300+ teams worldwide';
$ctaRating = get_field('sidebar_cta_rating') ?: '4.9';

$stages = [
    1 => [
        'eyebrow' => get_field('sidebar_stage1_eyebrow') ?: 'NEW HERE?',
        'head'    => get_field('sidebar_stage1_head')    ?: 'See our green-architecture renders',
        'sub'     => get_field('sidebar_stage1_sub')     ?: 'Look-book of recent projects — passive design, daylight, materials in context.',
        'label'   => get_field('sidebar_stage1_label')   ?: 'Browse portfolio',
        'url'     => get_field('sidebar_stage1_url')     ?: home_url('/gallery/'),
        'modal'   => false,
    ],
    2 => [
        'eyebrow' => get_field('sidebar_stage2_eyebrow') ?: 'RESOURCE',
        'head'    => get_field('sidebar_stage2_head')    ?: 'Get the project brief template we use',
        'sub'     => get_field('sidebar_stage2_sub')     ?: 'One-pager for green-CGI projects — scope, deliverables, milestones.',
        'label'   => get_field('sidebar_stage2_label')   ?: 'Download PDF',
        'url'     => get_field('sidebar_stage2_url')     ?: home_url('/contacts/'),
        'modal'   => false,
    ],
    3 => [
        'eyebrow' => get_field('sidebar_stage3_eyebrow') ?: 'SOCIAL PROOF',
        'head'    => get_field('sidebar_stage3_head')    ?: '300+ teams trust Maverick Frame',
        'sub'     => get_field('sidebar_stage3_sub')     ?: 'Case study: how we cut a developer\'s approval cycle by 40% with green-CGI.',
        'label'   => get_field('sidebar_stage3_label')   ?: 'Read case study',
        'url'     => get_field('sidebar_stage3_url')     ?: home_url('/gallery/'),
        'modal'   => false,
    ],
    4 => [
        'eyebrow' => get_field('sidebar_stage4_eyebrow') ?: 'TALK TO US',
        'head'    => get_field('sidebar_stage4_head')    ?: 'Working on a project like this?',
        'sub'     => get_field('sidebar_stage4_sub')     ?: 'Free 15-min consultation. No commitment — just a quick chat about your project.',
        'label'   => get_field('sidebar_stage4_label')   ?: 'Book a 15-min call',
        'url'     => get_field('sidebar_stage4_url')     ?: '#book',
        'modal'   => true,
    ],
];
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
        <?php foreach ($stages as $i => $s): ?>
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
