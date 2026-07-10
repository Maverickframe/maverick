<?php

// Current Polylang language slug, normalized to a string ('en'|'es'|'de'|…).
// Falls back to 'en' when Polylang is inactive or reports no language.
if ( ! function_exists('mfs_lang') ) {
    function mfs_lang() {
        $l = function_exists('pll_current_language') ? pll_current_language() : '';
        return $l ? $l : 'en';
    }
}

// Convenience predicate: mfs_is('de') === true on German pages.
if ( ! function_exists('mfs_is') ) {
    function mfs_is( $lang ) {
        return mfs_lang() === $lang;
    }
}

// Multilingual: return the localized string for the current Polylang language,
// else the English string. Used for hardcoded UI labels (buttons, etc.) across
// templates. Backward-compatible: existing two-arg mfs_t($en,$es) calls keep
// working; $de is optional and falls back to English until a 3rd arg is supplied.
// NOTE: positional — keep $de optional with EN fallback, do not reorder params.
if ( ! function_exists('mfs_t') ) {
    function mfs_t( $en, $es = null, $de = null ) {
        switch ( mfs_lang() ) {
            case 'es': return $es !== null ? $es : $en;
            case 'de': return $de !== null ? $de : $en;
            default:   return $en;
        }
    }
}

// Multilingual section eyebrows: resolve the ACF value (or a hardcoded English default),
// then on /es/ map known English eyebrow labels to Spanish. Handles both empty-field
// (template default) and untranslated-English-content cases without per-page edits.
if ( ! function_exists('mfs_eyebrow') ) {
    function mfs_eyebrow( $value, $default_en = '' ) {
        $v = ( $value !== '' && $value !== null ) ? $value : $default_en;
        if ( mfs_is('es') ) {
            $map = array(
                'Production Process'  => 'Proceso de producción',
                'Visual Results'      => 'Resultados visuales',
                'Why Choose Us'       => 'Por qué elegirnos',
                'What we do'          => 'Qué hacemos',
                'Performance at scale' => 'Rendimiento a escala',
                'Key Visuals'         => 'Visuales clave',
                'Services Provided'   => 'Servicios prestados',
                'Our Process'         => 'Nuestro proceso',
            );
            return isset( $map[$v] ) ? $map[$v] : $v;
        }
        if ( mfs_is('de') ) {
            $map = array(
                'Production Process'   => 'Produktionsprozess',
                'Visual Results'       => 'Visuelle Ergebnisse',
                'Why Choose Us'        => 'Warum wir',
                'What we do'           => 'Was wir tun',
                'Performance at scale' => 'Leistung im großen Maßstab',
                'Key Visuals'          => 'Zentrale Visuals',
                'Services Provided'    => 'Unsere Leistungen',
                'Our Process'          => 'Unser Prozess',
            );
            return isset( $map[$v] ) ? $map[$v] : $v;
        }
        return $v;
    }
}

// Multilingual: translate the consent microcopy at render on /es/ pages.
// The text comes from ACF field values/defaults (cta-form form_privacy,
// modal book_a_call_privacy) that can't be reliably overridden per block,
// so we swap the known English prefix to Spanish on output (EN untouched).
if ( ! function_exists('mfs_consent') ) {
    function mfs_consent( $html ) {
        $en = 'By clicking, you agree to receive communications from Maverick Frame Studio in accordance with our';
        $target = null;
        if ( mfs_is('es') ) {
            $target = 'Al hacer clic, aceptas recibir comunicaciones de Maverick Frame Studio de acuerdo con nuestra';
        } elseif ( mfs_is('de') ) {
            $target = 'Mit dem Klick erklären Sie sich einverstanden, Mitteilungen von Maverick Frame Studio gemäß unserer';
        }
        if ( $target !== null ) {
            // Match flexibly: the stored text uses non-breaking spaces (U+00A0) in places.
            $pattern = '/' . str_replace( ' ', '[\s\x{00A0}]+', preg_quote( $en, '/' ) ) . '/u';
            $html = preg_replace( $pattern, $target, $html );
            // The consent string also carries an English "Privacy Policy" anchor pointing at
            // /privacy-policy/. Localize both the URL (Polylang privacy page) and the link text
            // on ES/DE so the modal/cta consent doesn't leak the English legal page. (EN untouched.)
            if ( function_exists( 'mfs_privacy_url' ) ) {
                $pp_url = mfs_privacy_url();
                $html = preg_replace( '/(href=")[^"]*privacy[^"]*(")/i', '${1}' . esc_url( $pp_url ) . '${2}', $html );
            }
            $html = str_ireplace( 'Privacy Policy', mfs_t( 'Privacy Policy', 'Política de privacidad', 'Datenschutzerklärung' ), $html );
            return $html;
        }
        return $html;
    }
}

// Multilingual privacy-policy URL. WordPress' wp_page_for_privacy_policy option points at the
// unused default draft, so resolve the real published page instead: DE has its own
// Datenschutzerklärung (20457); ES has no privacy page yet → falls back to the English one.
if ( ! function_exists( 'mfs_privacy_url' ) ) {
    function mfs_privacy_url() {
        if ( function_exists( 'mfs_is' ) && mfs_is( 'de' ) ) {
            $u = get_permalink( 20457 );
            return $u ? $u : home_url( '/de/datenschutzerklaerung/' );
        }
        $p = get_page_by_path( 'privacy-policy' );
        if ( $p ) {
            $u = get_permalink( $p->ID );
            if ( $u ) {
                return $u;
            }
        }
        return home_url( '/privacy-policy/' );
    }
}

// DE mega-menu, defined in code (git) instead of the per-language ACF options repeater.
// The `menu_items_de` repeater had drifted to 2 items with the wrong structural keynames
// (`leistungen`/`referenzen` instead of the canonical `services`/`solutions`), which broke the
// dropdowns and the catalog CTA. Keeping the DE menu in the theme makes it version-controlled,
// inherits future pages, and leaves the live EN (`menu_items`) / ES (`menu_items_es`) menus
// untouched — they keep rendering from ACF. Rows match the shape menu-item.php expects.
// Service link titles are short DE labels (mirrors the EN menu's custom titles, not the full
// SEO post titles); this menu only ever renders on /de/. Links are post IDs → get_permalink
// returns the correct lowercase DE URL via Polylang.
// Top-level order + grouping mirror the EN menu 1:1 (Services, Portfolio, Resources,
// Company, Solutions) — only labels/links are German. our_works (Portfolio) and resources
// (Ressourcen) reuse the EN special sub-templates; their DE content comes from the code
// helpers below (mfs_menu_ow_de / mfs_menu_res_de), so nothing leaks English and no ACF
// menu_*_de data is needed.
if ( ! function_exists( 'mfs_menu_rows_de' ) ) {
    function mfs_menu_rows_de() {
        return array(
            // 1. Leistungen (services) — mega-dropdown: 14 live DE services in 3 groups + catalog CTA.
            array(
                'keyname'       => 'services',
                'label'         => 'Leistungen',
                'desktop_label' => 'Alle Leistungen',
                'permalink'     => get_permalink( 20757 ), // /de/leistungen/
                'groups_links'  => array(
                    array(
                        'title' => 'Architektur & Immobilien',
                        'link'  => '',
                        'links' => array(
                            array( 'title' => 'Architekturvisualisierung', 'link' => 20450 ),
                            array( 'title' => 'Immobilien-Visualisierung', 'link' => 20454 ),
                            array( 'title' => 'Innenraumvisualisierung',   'link' => 20448 ),
                            array( 'title' => '3D-Grundriss',              'link' => 20632 ),
                            array( 'title' => '3D-Architektur-Animation',  'link' => 20643 ),
                        ),
                    ),
                    array(
                        'title' => 'Produkt',
                        'link'  => '',
                        'links' => array(
                            array( 'title' => '3D-Produktvisualisierung', 'link' => 20621 ),
                            array( 'title' => '3D-Produktanimation',      'link' => 20655 ),
                        ),
                    ),
                    array(
                        'title' => 'Digital & Kreativ',
                        'link'  => '',
                        'links' => array(
                            array( 'title' => 'Webdesign',           'link' => 20663 ),
                            array( 'title' => 'UX/UI-Design',        'link' => 20662 ),
                            array( 'title' => 'Landingpage',         'link' => 20664 ),
                            array( 'title' => 'App-Design',          'link' => 20665 ),
                            array( 'title' => 'Corporate Design',    'link' => 20661 ),
                            array( 'title' => 'Social-Media-Design', 'link' => 20660 ),
                            array( 'title' => 'Präsentationsdesign', 'link' => 20666 ),
                        ),
                    ),
                ),
            ),
            // 2. Portfolio (our_works) — special dropdown: Referenzen + Galerie + recent DE cases
            //    + Katalog-Download. Items come from mfs_menu_ow_de().
            array(
                'keyname'       => 'our_works',
                'label'         => 'Portfolio',
                'desktop_label' => '',
                'permalink'     => null,
                'groups_links'  => array(),
            ),
            // 3. Ressourcen (resources) — special dropdown: icon-links + recent DE blog posts +
            //    "Blog ansehen" footer. Items come from mfs_menu_res_de().
            array(
                'keyname'       => 'resources',
                'label'         => 'Ressourcen',
                'desktop_label' => 'Blog ansehen',
                'permalink'     => get_permalink( 20595 ), // /de/blog/
                'groups_links'  => array(),
            ),
            // 4. Unternehmen (company) — Team + Kontakt (rendered via menu-big-links company branch).
            array(
                'keyname'       => 'company',
                'label'         => 'Unternehmen',
                'desktop_label' => '',
                'permalink'     => null,
                'groups_links'  => array(
                    array( 'title' => 'Team',    'link' => 20755, 'links' => array() ), // /de/team/
                    array( 'title' => 'Kontakt', 'link' => 20750, 'links' => array() ), // /de/kontakt/
                ),
            ),
            // 5. Lösungen (solutions) — dropdown (one audience landing) + catalog CTA.
            array(
                'keyname'       => 'solutions',
                'label'         => 'Lösungen',
                'desktop_label' => '',
                'permalink'     => null,
                'groups_links'  => array(
                    array(
                        'title' => 'Für Marketing-Agenturen',
                        'link'  => 20784, // /de/loesungen/marketing-agenturen/
                        'links' => array(),
                    ),
                ),
            ),
        );
    }
}

// DE content for the Portfolio (our_works) dropdown — mirrors menu_our_works_es (Success
// stories / Gallery curated links + Download-Catalog button). Used by menu-big-links.php
// and menu-our-works.php on /de/ so no English leaks and no ACF menu_our_works_de is needed.
if ( ! function_exists( 'mfs_menu_ow_de' ) ) {
    function mfs_menu_ow_de() {
        return array(
            'items' => array(
                array( 'image' => 20026, 'link' => 20577, 'title' => 'Referenzen' ), // /de/referenzen/
                array( 'image' => 20027, 'link' => 20751, 'title' => 'Galerie' ),    // /de/galerie/
            ),
            'download_image' => 19981,
            'download_title' => 'Katalog herunterladen',
        );
    }
}

// DE content for the Ressourcen (resources) dropdown — mirrors menu_resources_es icon-links.
if ( ! function_exists( 'mfs_menu_res_de' ) ) {
    function mfs_menu_res_de() {
        // Resources = articles/blog only. "Referenzen" lives in the Portfolio dropdown
        // (mfs_menu_ow_de) — it was duplicated here as an EN-quirk carry-over and removed.
        // "Bewertungen" was also removed: it pointed at /de/blog/ (a false target — reviews
        // are not the blog), and there is no DE reviews page; the footer "Bewerten Sie uns"
        // block already carries the Google/Trustpilot review CTA.
        return array(
            array( 'icon_title' => 'articles-menu', 'title' => 'Artikel', 'description' => 'Lernen Sie aus unseren Experten-Artikeln', 'link' => home_url( '/de/blog/' ) ),
        );
    }
}

// DE footer navigation — mirrors the German header (mfs_menu_rows_de) so the footer sitemap
// matches the top menu 1:1: Leistungen / Portfolio / Ressourcen / Unternehmen / Lösungen, in
// the same order, German labels only. Links are DE post IDs → get_permalink returns the correct
// lowercase /de/ URL via Polylang. Shaped for footer.php (columns → groups → links). Only ever
// rendered on /de/ (EN/ES footers stay ACF-driven and untouched). Grows with the header.
if ( ! function_exists( 'mfs_footer_menu_de' ) ) {
    function mfs_footer_menu_de() {
        return array(
            // 1. Leistungen — 14 live DE services in 3 groups (same grouping as the header).
            array(
                'title'  => 'Leistungen',
                'groups' => array(
                    array(
                        'title' => 'Architektur & Immobilien',
                        'link'  => 0,
                        'links' => array(
                            array( 'title' => 'Architekturvisualisierung', 'link' => 20450 ),
                            array( 'title' => 'Immobilien-Visualisierung', 'link' => 20454 ),
                            array( 'title' => 'Innenraumvisualisierung',   'link' => 20448 ),
                            array( 'title' => '3D-Grundriss',              'link' => 20632 ),
                            array( 'title' => '3D-Architektur-Animation',  'link' => 20643 ),
                        ),
                    ),
                    array(
                        'title' => 'Produkt',
                        'link'  => 0,
                        'links' => array(
                            array( 'title' => '3D-Produktvisualisierung', 'link' => 20621 ),
                            array( 'title' => '3D-Produktanimation',      'link' => 20655 ),
                        ),
                    ),
                    array(
                        'title' => 'Digital & Kreativ',
                        'link'  => 0,
                        'links' => array(
                            array( 'title' => 'Webdesign',           'link' => 20663 ),
                            array( 'title' => 'UX/UI-Design',        'link' => 20662 ),
                            array( 'title' => 'Landingpage',         'link' => 20664 ),
                            array( 'title' => 'App-Design',          'link' => 20665 ),
                            array( 'title' => 'Corporate Design',    'link' => 20661 ),
                            array( 'title' => 'Social-Media-Design', 'link' => 20660 ),
                            array( 'title' => 'Präsentationsdesign', 'link' => 20666 ),
                        ),
                    ),
                ),
            ),
            // 2. Portfolio — Referenzen + Galerie (header our_works dropdown).
            array(
                'title'  => 'Portfolio',
                'groups' => array(
                    array(
                        'title' => '',
                        'link'  => 0,
                        'links' => array(
                            array( 'title' => 'Referenzen', 'link' => 20577 ),
                            array( 'title' => 'Galerie',    'link' => 20751 ),
                        ),
                    ),
                ),
            ),
            // 3. Ressourcen — Artikel only (mirrors the header resources dropdown after cleanup).
            //    "Referenzen" lives in the Portfolio column above (removed here as a duplicate);
            //    "Bewertungen" removed (it pointed at /de/blog/ — a false target; review CTA is
            //    in the footer "Bewerten Sie uns" block).
            array(
                'title'  => 'Ressourcen',
                'groups' => array(
                    array(
                        'title' => '',
                        'link'  => 0,
                        'links' => array(
                            array( 'title' => 'Artikel', 'link' => 20595 ),
                        ),
                    ),
                ),
            ),
            // 4. Unternehmen — Team + Kontakt (header company branch).
            array(
                'title'  => 'Unternehmen',
                'groups' => array(
                    array(
                        'title' => '',
                        'link'  => 0,
                        'links' => array(
                            array( 'title' => 'Team',    'link' => 20755 ),
                            array( 'title' => 'Kontakt', 'link' => 20750 ),
                        ),
                    ),
                ),
            ),
            // 5. Lösungen — audience landing (header solutions dropdown).
            array(
                'title'  => 'Lösungen',
                'groups' => array(
                    array(
                        'title' => '',
                        'link'  => 0,
                        'links' => array(
                            array( 'title' => 'Für Marketing-Agenturen', 'link' => 20784 ),
                        ),
                    ),
                ),
            ),
        );
    }
}

// Polylang Pro: translate the `solutions` CPT URL segment to German → /de/loesungen/<slug>/.
// EN and ES keep the default `solutions` segment (ES pages are already live on
// /es/solutions/ and must NOT move — changing the ES segment would break those URLs).
// This mirrors how services→leistungen and success-stories→referenzen are translated,
// but is defined in code so the segment ships with the theme and survives any Polylang
// string-translation / transient reset. Hooks PLL_Translate_Slugs_Model::get_translatable_slugs();
// after a change here flush with: wp transient delete pll_translated_slugs && wp rewrite flush.
add_filter( 'pll_translated_slugs', function ( $slugs, $language ) {
    if ( isset( $slugs['solutions'] ) && isset( $language->slug ) && 'de' === $language->slug ) {
        $slugs['solutions']['translations']['de'] = 'loesungen';
    }
    return $slugs;
}, 10, 2 );

// Multilingual: expose UI strings rendered by JS (modals, sliders) so the bundle
// can localize them. Read in src/js via window.MFS_I18N (falls back to English).
add_action( 'wp_head', function () {
    $i18n = array(
        'exploreService' => mfs_t( 'Explore service', 'Ver servicio', 'Leistung ansehen' ),
        'bookACall'      => mfs_t( 'Book a call', 'Reservar una llamada', 'Beratung buchen' ),
        'nextReview'     => mfs_t( 'Next review', 'Siguiente reseña', 'Nächste Bewertung' ),
    );
    // Calendar month/weekday labels for book-calendar.js (rendered client-side).
    $mfs_cal_months = array(
        'en' => array('January','February','March','April','May','June','July','August','September','October','November','December'),
        'es' => array('enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'),
        'de' => array('Januar','Februar','März','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'),
    );
    $mfs_cal_wd = array(
        'en' => array('Mon','Tue','Wed','Thu','Fri','Sat','Sun'),
        'es' => array('Lun','Mar','Mié','Jue','Vie','Sáb','Dom'),
        'de' => array('Mo','Di','Mi','Do','Fr','Sa','So'),
    );
    $mfs_cal_lang = isset( $mfs_cal_months[ mfs_lang() ] ) ? mfs_lang() : 'en';
    $i18n['calMonths']   = $mfs_cal_months[ $mfs_cal_lang ];
    $i18n['calWeekdays'] = $mfs_cal_wd[ $mfs_cal_lang ];
    echo '<script>window.MFS_I18N=' . wp_json_encode( $i18n ) . ';</script>' . "\n";
}, 1 );

// Theme supports

add_theme_support('title-tag');
add_theme_support('post-thumbnails');

// Responsive image sizes — owned by the theme (source of truth, in git).
// Same names/widths as Perfect Images so existing generated files keep matching (no regenerate).
add_image_size('card_small', 400, 9999, false);
add_image_size('case_content', 1024, 9999, false);
add_image_size('big', 1440, 9999, false);
add_image_size('hero_full', 1920, 9999, false);
// Intermediate widths so DPR1 (PSI-desktop) can size to container instead of rounding up.
// card_xs ~ hero portrait slot (288px CSS); card_med ~ performance-scale case card (560px CSS).
// Only used images are regenerated (wp media regenerate <ids> --only-missing); retina/DPR2 picks unchanged.
add_image_size('card_xs', 300, 9999, false);
add_image_size('card_med', 600, 9999, false);

// Don't generate unused oversized core sizes (replaces Perfect Images' disabling). Keep 'large' — used by templates.
add_filter('intermediate_image_sizes_advanced', function ($sizes) {
    unset($sizes['1536x1536'], $sizes['2048x2048'], $sizes['medium']);
    return $sizes;
});

// WP 6.7 core prepends "auto," to `sizes` on every loading=lazy img (wp_img_tag_add_auto_sizes).
// Our templates declare exact sizes (hero marquee 150/300px, cards 560px). In the CSS marquee the
// lazy DUPE slides mis-resolved `sizes=auto` and fetched the full 818w instead of 300w/600w.
// Disable core auto-sizes so the theme's explicit sizes stay authoritative site-wide.
add_filter('wp_img_tag_add_auto_sizes', '__return_false');

// End Theme supports

// New Design

function isNewDesign()
{
    return is_page_template('templates/presentation-design-page.php')
        || is_page_template('templates/success-stories.php')
        || is_singular('success-stories')
        || is_page_template('templates/team-page.php')
        || is_singular('team')
        || is_page_template('templates/template-blog.php')
        || is_singular('blog')
        || is_page_template('templates/template-gallery.php')
        || is_page_template('templates/template-services.php')
        || is_page_template('templates/template-services-hub.php')
        || is_page_template('templates/template-contacts.php')
        || is_page_template('templates/template-legal.php')
        || is_singular('solutions')
        || is_404()
        || is_front_page();
}

// End New Design

// Enqueue Scripts and Styles

include "inc.vite.php";
require_once __DIR__ . '/forms/book-call-handler.php';
require_once __DIR__ . '/inc.tour.php';
require_once __DIR__ . '/inc.video.php';

// HubSpot tracking — self-hosted loader. We dropped the bloated `leadin` plugin,
// which also pulled the cookie-consent banner, ads pixel, forms JS and preconnect
// hints, and force-loaded eagerly (it self-excluded from WP Rocket's delay). This
// prints ONLY the HubSpot tracking code: page views, the hubspotutk cookie, and
// behavioural events for workflow triggers. It's a plain external script (not
// excluded), so WP Rocket's "Delay JS" defers it to the first user interaction
// (scroll/move/touch/key) — off the critical path, still set before any form
// submit. Forms are server-side (forms/hubspot.php) and independent of this.
// The banner + ads pixel are portal-gated: keep them OFF in HubSpot Settings ->
// Privacy & Consent so the loader does not pull them back in.
add_action('wp_footer', function () {
    echo '<script type="text/javascript" id="hs-script-loader" async defer src="//js-eu1.hs-scripts.com/148670517.js"></script>' . "\n";
}, 20);

// End Enqueue Scripts and Styles

//  Add assets

function assets()
{
    // global $wp_query;
    wp_localize_script('main', 'contacts', array(
        // Use the unfiltered site root (get_option('home')) so the form endpoint
        // resolves to /wp-content/.../amo.php even on Polylang /es/ pages
        // (home_url() is rewritten to /es/ there, which 404s the handler).
        'home_url' => get_option('home')
    ));
    $ajax_nonce = wp_create_nonce('pld-ajax-nonce');

    $js_object = array('admin_ajax_url' => admin_url('admin-ajax.php'), 'admin_ajax_nonce' => $ajax_nonce);
    wp_localize_script('main', 'pld_js_object', $js_object);
}

add_action('wp_enqueue_scripts', 'assets');

// End add assets


// Remove admin bar margin

add_action('get_header', 'my_filter_head');

function my_filter_head()
{
    remove_action('wp_head', '_admin_bar_bump_cb');
}

// End Remove admin bar margin


// Lazy load

function lazy_attachment($attachment_id, $size, $nativeLazy = 'lazy', $class = '', $sizes = 'auto')
{
    // Native lazy-loading. wp_get_attachment_image() emits the real src + srcset,
    // so the browser fetches the correct variant directly — no lazysizes
    // measurement (that per-image getBoundingClientRect/getComputedStyle pass was
    // the site's biggest "forced reflow" source), and no lqip -> real double-load.
    // 'sizes=auto' lets Chrome pick the exact rendered-width variant with zero JS
    // (Safari/FF fall back to 100vw). Do NOT emit the 'lazyload'/'blur-up'
    // classes: '.lazyloaded' was added by lazysizes (gone now), so '.blur-up'
    // would stay permanently blurred.
    $attrs = [
        'loading'  => $nativeLazy,
        'decoding' => 'async',
    ];
    if ($class) $attrs['class'] = $class;
    if ($sizes) $attrs['sizes'] = $sizes;

    echo wp_get_attachment_image($attachment_id, $size, false, $attrs);
}

function eager_attachment($attachment_id, $size, $sizes = null, $fetchpriority = false)
{
    // data-no-lazy + skip-lazy: WP Rocket's LazyLoad ignores loading="eager" and
    // rewrote these images to JS lazyload for logged-out visitors anyway — the
    // mobile LCP hero image got a 3.3s resource-load delay (PSI "LCP request
    // discovery": lazy + no fetchpriority). Both markers are honored by WP Rocket
    // (and other lazyload plugins), keeping eager images truly eager on prod.
    $attrs = [
        'loading'      => 'eager',
        'data-no-lazy' => '1',
        'class'        => 'skip-lazy',
    ];
    if ($sizes) $attrs['sizes'] = $sizes;
    if ($fetchpriority) $attrs['fetchpriority'] = 'high';
    echo wp_get_attachment_image($attachment_id, $size, false, $attrs);
}

// End Lazy load


// WebP quality for generated intermediate sizes. WP default is 82, which left hero
// slide variants at 150-185 KB (PSI "Improve image delivery" flagged ~540 KB on the
// front page). 68 is visually indistinguishable on photographic CGI renders but
// roughly halves variant size. Applies on (re)generation only — after deploying,
// run Force Regenerate Thumbnails (plugin already active) on the hero/media images
// so existing variants get re-encoded in place with the same filenames/URLs.
add_filter('wp_editor_set_quality', function ($quality, $mime_type) {
    return $mime_type === 'image/webp' ? 68 : $quality;
}, 10, 2);


// Remove unused

remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'start_post_rel_link');
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'adjacent_posts_rel_link');
remove_action('wp_head', 'rest_output_link_wp_head');
remove_action('wp_head', 'wp_oembed_add_discovery_links');
remove_action('template_redirect', 'rest_output_link_header', 11);
remove_action('wp_head', 'wp_shortlink_wp_head');

function wpassist_remove_block_library_css()
{
    wp_dequeue_style('global-styles');
    wp_dequeue_style('classic-theme-styles');
    wp_dequeue_style('pld-frontend');
    wp_dequeue_script('pld-frontend');
}
add_action('wp_enqueue_scripts', 'wpassist_remove_block_library_css');

function disable_emojis()
{
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'disable_emojis');

function my_deregister_scripts()
{
    wp_deregister_script('wp-embed');
}
add_action('wp_footer', 'my_deregister_scripts');

function disable_all_feeds()
{
    wp_die(__('This WordPress does not have Feeds.'), '', 404);
}

add_action('do_feed', 'disable_all_feeds', 1);

add_action('do_feed_rdf', 'disable_all_feeds', 1);
add_action('do_feed_rss', 'disable_all_feeds', 1);
add_action('do_feed_rss2', 'disable_all_feeds', 1);
add_action('do_feed_atom', 'disable_all_feeds', 1);
add_action('do_feed_rss2_comments', 'disable_all_feeds', 1);
add_action('do_feed_atom_comments', 'disable_all_feeds', 1);

add_action('feed_links_show_posts_feed', '__return_false', 1);
add_action('feed_links_show_comments_feed', '__return_false', 1);

// Disable comments on all post types
function df_disable_comments_post_types_support()
{
    $post_types = get_post_types();
    foreach ($post_types as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
}
add_action('admin_init', 'df_disable_comments_post_types_support');

// Close comments on the front-end
function df_disable_comments_status()
{
    return false;
}
add_filter('comments_open', 'df_disable_comments_status', 20, 2);
add_filter('pings_open', 'df_disable_comments_status', 20, 2);

// Hide existing comments
function df_disable_comments_hide_existing_comments($comments)
{
    $comments = array();
    return $comments;
}
add_filter('comments_array', 'df_disable_comments_hide_existing_comments', 10, 2);

// Remove comments page in menu
function df_disable_comments_admin_menu()
{
    remove_menu_page('edit-comments.php');
}
add_action('admin_menu', 'df_disable_comments_admin_menu');

// Redirect any user trying to access comments page
function df_disable_comments_admin_menu_redirect()
{
    global $pagenow;
    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url());
        exit;
    }
}
add_action('admin_init', 'df_disable_comments_admin_menu_redirect');

// End Remove unused

// Load More
function loadmore_ajax_handler()
{
    $cat = isset($_POST['cat']) && $_POST['cat'] != 'all' ? urlencode($_POST['cat']) : null;

    $args = array(
        'post_type' => 'portfolio',
        'posts_per_page' => 12,
        'paged' => $_POST['page'] + 1,
        'post_status' => 'publish',
        'cat' => $cat,
        'meta_query' => array(
            array(
                'key' => 'portfolio_type',
                'value' => '"' . get_field('portfolio_type', $_POST['post_id']) . '"',
                'compare' => 'LIKE'
            )
        )
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            echo get_template_part('components/portfolio-item', null, array(
                'class' => 'portfolio-item_page'
            ));
        }
    }
    wp_reset_query();
    die; // here we exit the script and even no wp_reset_query() required!
}

add_action('wp_ajax_loadmore', 'loadmore_ajax_handler');
add_action('wp_ajax_nopriv_loadmore', 'loadmore_ajax_handler');

function loadmore_front_ajax_handler()
{
    $args = array(
        'post_type' => 'portfolio',
        'posts_per_page' => 6,
        'paged' => $_POST['page'] + 1,
        'post_status' => 'publish',
        'cat' => get_field('portfolio_cat', $_POST['post_id']) ?? null,
        'meta_query' => array(
            array(
                'key' => 'portfolio_type',
                'value' => '"common"',
                'compare' => 'LIKE'
            )
        )
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            echo get_template_part('components/portfolio-front-item');
        }
    }
    wp_reset_query();
    die; // here we exit the script and even no wp_reset_query() required!
}

add_action('wp_ajax_loadmore_front', 'loadmore_front_ajax_handler');
add_action('wp_ajax_nopriv_loadmore_front', 'loadmore_front_ajax_handler');

// End Load More

// Load More Articles

function _wp_adjust_show_request($request)
{
    if ($request->post_name === 'blog') {
        add_filter('redirect_canonical', '__return_false');
    }
    return $request;
}
add_action('parse_query', '_wp_adjust_show_request');

function pagination($current, $max, $cat, $search)
{
    add_filter('number_format_i18n', 'give_numbers_leading_zero');

    $paginate_links = paginate_links(array(
        'base' => '?cat=' . $cat . '&search=' . $search . '&current_page=%#%',
        'total' => $max ?? 1,
        'current' => max($current ?? 1, get_query_var('current_page')),
        'type' => 'array',
        'show_all' => false,
        'end_size' => 2,
        'mid_size' => 1,
        'prev_next' => true,
        'prev_text' => '<svg width="26" height="4" viewBox="0 0 26 4" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.823223 1.82323C0.725592 1.92086 0.725592 2.07915 0.823223 2.17678L2.41421 3.76777C2.51184 3.8654 2.67014 3.8654 2.76777 3.76777C2.8654 3.67014 2.8654 3.51185 2.76777 3.41422L1.35355 2L2.76777 0.585788C2.8654 0.488157 2.8654 0.329866 2.76777 0.232235C2.67014 0.134604 2.51184 0.134604 2.41421 0.232235L0.823223 1.82323ZM26 2L26 1.75L1 1.75L1 2L1 2.25L26 2.25L26 2Z" fill="black"/>
                            </svg>
                            ',
        'next_text' => '<svg width="26" height="4" viewBox="0 0 26 4" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M25.1768 2.17678C25.2744 2.07915 25.2744 1.92085 25.1768 1.82322L23.5858 0.232233C23.4882 0.134602 23.3299 0.134602 23.2322 0.232233C23.1346 0.329864 23.1346 0.488155 23.2322 0.585786L24.6464 2L23.2322 3.41421C23.1346 3.51184 23.1346 3.67014 23.2322 3.76777C23.3299 3.8654 23.4882 3.8654 23.5858 3.76777L25.1768 2.17678ZM0 2.25H25V1.75H0V2.25Z" fill="black"/>
                            </svg>
                            ',
        'add_args' => false,
        'add_fragment' => '',
    ));

    remove_filter('number_format_i18n', 'give_numbers_leading_zero');

    if ($paginate_links) {
        ob_start();
        foreach ($paginate_links as $link) {
            echo $link;
        }
        $var = ob_get_contents();
        ob_end_clean();
        return $var;
    }
}

function load_template_part($template_name, $part_name = null, $args = [])
{
    ob_start();
    get_template_part($template_name, $part_name, $args);
    $var = ob_get_contents();
    ob_end_clean();
    return $var;
}

function loadmore_articles_ajax_handler()
{
    $post_type = $_POST['post_type'] ?? '';
    $cat = isset($_POST['cat']) && $_POST['cat'] != 'all' ? urlencode($_POST['cat']) : null;
    $subcat = isset($_POST['subcat']) && $_POST['subcat'] != 'all' ? urlencode($_POST['subcat']) : null;
    $tag = isset($_POST['tag']) && $_POST['tag'] != 'all' ? urlencode($_POST['tag']) : null;
    $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    $orderbyRaw = $_POST['orderby'] ?? 'latest';
    $paged = $_POST['current_page'] ?? 1;
    // admin-ajax runs in is_admin() context where Polylang does NOT auto-filter
    // the query by language, so it returns posts from ALL languages and diverges
    // from the language-filtered front-end list (duplicates + cross-language leak).
    // Pass the current language explicitly so WP_Query is filtered like the front end.
    $lang = isset($_POST['lang']) ? sanitize_key($_POST['lang']) : '';

    $args = [
        'post_type' => $post_type,
        'posts_per_page' => 6,
        'paged' => (int) $paged,
        'post_status' => 'publish',
        'cat' => $cat,
    ];

    if ($lang !== '') {
        $args['lang'] = $lang;
    }

    if (!empty($search)) {
        $args['s'] = $search;
        $args['orderby'] = 'relevance';
        $args['order'] = 'DESC';
    } else {
        switch ($orderbyRaw) {
            case 'latest':
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
                break;

            case 'popular':
                $args['meta_key'] = 'post_views';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                break;
        }
    }

    if ($tag !== null) {
        if (is_numeric($tag)) {
            $args['tag_id'] = (int) $tag;
        } else {
            $args['tag'] = sanitize_title($tag);
        }
    }

    if ($subcat !== null) {
        $args['category__not_in'] = [(int) $cat];
        $args['category__in'] = [(int) $subcat];
    }

    $query = new WP_Query($args);

    $data = '';

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();

            if ($post_type === 'blog') {
                $data .= load_template_part('components/new-design/blog/articles-item', null, [
                    'id' => get_the_ID(),
                    'class' => ' --blog'
                ]);
            } else if ($post_type === 'success-stories') {
                $data .= load_template_part('components/new-design/success-stories/articles-item', null, [
                    'id' => get_the_ID(),
                    'class' => ' --success-stories'
                ]);
            }
        }
    }

    wp_send_json([
        'max_page' => $query->max_num_pages,
        'data' => $data,
    ]);

    wp_reset_query();
    die; // here we exit the script and even no wp_reset_query() required!
}

add_action('wp_ajax_loadmore_articles', 'loadmore_articles_ajax_handler');
add_action('wp_ajax_nopriv_loadmore_articles', 'loadmore_articles_ajax_handler');

// End Load More Articles

// Post views

function set_post_views($post_id)
{
    if (is_admin())
        return;

    $cookie_key = 'post_viewed_' . $post_id;

    if (isset($_COOKIE[$cookie_key]))
        return;

    setcookie($cookie_key, '1', time() + 3600, '/');

    $key = 'post_views';
    $count = get_post_meta($post_id, $key, true);

    $count = $count ? (int) $count + 1 : 1;

    update_post_meta($post_id, $key, $count);
}

add_action('wp', function () {
    if (is_admin())
        return;

    if (is_singular(['blog', 'success-stories'])) {
        set_post_views(get_queried_object_id());
    }
});

// End Post views

// Remove yoast seo tags
function ikva_remove_robots_meta()
{
    return null;
}

add_filter('wpseo_robots', 'ikva_remove_robots_meta');
add_filter('wpseo_googlebot', 'ikva_remove_robots_meta');
add_filter('wpseo_bingbot', 'ikva_remove_robots_meta');
add_filter('wpseo_canonical', '__return_false');
remove_filter('wp_robots', 'wp_robots_max_image_preview_large');

// End Remove yoast seo tags

// Shortcode

add_shortcode('form2', 'form_shortcode');
add_shortcode('form5', 'form_shortcode');

function form_shortcode($atts)
{
    ob_start();
    get_template_part('components/blog-page/form-5');
    return ob_get_clean();
}

// End Shortcode

// Seo title — RETIRED 2026-07-10. Custom ACF `title` + document_title filter
// was dead code: Rank Math short-circuits pre_get_document_title, so this
// end-of-chain document_title filter never affected the live <title>. Titles
// are now sourced solely from Rank Math. ACF field `title` (field_655a4d86a7e52)
// removed from group_64ea0fc4ddc43.json in the same change.
// End Seo title

// Images Sizes; Menu

add_action('after_setup_theme', 'default_attachment_display_settings');

function default_attachment_display_settings()
{
    update_option('image_default_size', 'full');
}

add_filter('big_image_size_threshold', '__return_false');

// End Images Szies

// Remove category and author pages

function rn_author_page_redirect()
{
    if (is_author() || is_category()) {
        wp_redirect(home_url());
    }
}
add_action('template_redirect', 'rn_author_page_redirect');

// End Remove category and author pages

// Filter private & common portfolio

function get_portfolio_item_join($join)
{
    if (is_singular('portfolio')) {
        global $wpdb;
        $new_join = $join . "INNER JOIN $wpdb->postmeta AS m ON p.ID = m.post_id ";
        return $new_join;
    }
    return $join;
}
add_filter('get_previous_post_join', 'get_portfolio_item_join');
add_filter('get_next_post_join', 'get_portfolio_item_join');

function get_prev_portfolio_item($where)
{
    if (is_singular('portfolio')) {
        global $wpdb;
        $like = '%common%';

        $prev_where = $wpdb->prepare("$where AND (m.meta_key = 'portfolio_type' AND (m.meta_key = 'portfolio_type' AND m.meta_value LIKE '%s'))", $like);

        return $prev_where;
    }
    return $where;
}
add_filter('get_previous_post_where', 'get_prev_portfolio_item');

function get_next_portfolio_item($where)
{
    if (is_singular('portfolio')) {
        global $wpdb;
        $like = '%common%';

        $new_where = $wpdb->prepare("$where AND (m.meta_key = 'portfolio_type' AND (m.meta_key = 'portfolio_type' AND m.meta_value LIKE '%s'))", $like);

        return $new_where;
    }
    return $where;
}
add_filter('get_next_post_where', 'get_next_portfolio_item');

// End Filter private & common portfolio

// Leading zero pagination

function give_numbers_leading_zero($number)
{
    return sprintf("%02s", $number);
}

// End Leading zero pagination

// Insert SVGs

function inline_svg($path)
{
    $file = get_template_directory() . '/public/img/' . $path;
    return file_exists($file) ? file_get_contents($file) : '';
}

// End Insert SVGs

// Acf Blocks

add_action('after_setup_theme', function () {
    add_theme_support('editor-styles');
});

add_action('init', function () {
    foreach (glob(__DIR__ . '/components/blocks/*/block.json') as $block) {
        register_block_type(dirname($block));
    }
});

// End Acf Blocks

// Generate ToC

function generateToC($content, $title)
{
    preg_match_all('/<h([2])*[^>]*>(.*?)<\/h[2]>/', $content, $matches);

    $toc = "<ul class='toc__list scrollbar'>";

    foreach ($matches[0] as $i => $match) {
        $text = strip_tags($match);
        $slug = strtolower(str_replace("--", "-", preg_replace('/[^\da-z]/i', '-', $text)));

        $anchor = "<a name='{$slug}' id='{$slug}' class='sr-only'>{$text}</a>{$match}";
        $content = str_replace($match, $anchor, $content);

        $toc .= "<li class='toc__item js-toc-item'><a href='#{$slug}' class='toc__link'>{$text}</a></li>";
    }

    $toc .= "</ul>";

    return ["content" => $content, "toc" => $toc];
}

// End Generate ToC

// Get excerpt trim

function get_excerpt_trim($id, $num_words = '20', $more = '...')
{
    $excerpt = get_the_excerpt($id);
    $excerpt = wp_trim_words($excerpt, $num_words, $more);
    return $excerpt;
}

// End Get excerpt trim

// Get title trim

function get_title_trim($id, $num_words = '7', $more = '...')
{
    $title = get_the_title($id);
    $title = wp_trim_words($title, $num_words, $more);
    return $title;
}

// End Get title trim

// Fix wp rocket optimization for dynamic content

add_filter('rocket_lrc_optimization', '__return_false', 999);

// Take LCP optimization off WP Rocket's ATF beacon (wpr-beacon.js). That beacon ran
// getBoundingClientRect over hundreds of elements (img, video, p, div, li, section…)
// on load to auto-detect the LCP element → a post-paint forced reflow (PSI "forced
// reflow": ~60ms beacon JS + ~190ms unattributed layout it forces). We now set
// fetchpriority=high on the real LCP image per template ourselves (hero video posters,
// first blog-listing card, first gallery cell; text heroes need none), so the
// auto-detection is redundant. Both filter names are covered across WP Rocket
// versions — the one that isn't used in this version is simply a no-op.
add_filter('rocket_atf_optimization', '__return_false');
add_filter('rocket_lcp_optimization', '__return_false');

// End Fix wp rocket optimization for dynamic content

// WP Rocket "Remove Unused CSS" builds the inline Used CSS by scanning the STATIC
// DOM and drops any selector it doesn't find there. Our reveal / lazy / sticky
// visibility is driven by classes JS adds at RUNTIME (.is-in from reveal.js,
// .is-animated from .js-highlight, .lazyloaded from lazy-media, .is-fixed on the
// scroll-locked body/header). Those classes aren't in the static DOM, so RUCSS
// stripped e.g. `.js-reveal.is-in{opacity:1}` from the homepage Used CSS — leaving
// the header (a `.js-reveal`, opacity:0 until revealed) permanently invisible =
// "the menu disappeared". Safelist the runtime-toggled classes so their rules are
// always kept. (Regenerate Used CSS after deploy for this to take effect.)
add_filter('rocket_rucss_safelist', function ($safelist) {
    return array_merge((array) $safelist, [
        '.is-in',
        '.is-animated',
        '.lazyloaded',
        '.is-fixed',
        '.opened',
        '.is-active',
    ]);
});

// Delay ALL theme JS until first user interaction (scroll/click/tap/key), so the
// initial load / critical path carries zero theme JavaScript. WP Rocket's "Delay
// JS" is already on globally, but the theme build folder was explicitly excluded
// (setting `delay_js_exclusions` → `/wp-content/themes/maverickframe/build/`), which
// left `main` + chunks merely deferred. Drop that one exclusion from the final list
// so they get delayed too. Tour/panorama excludes (added in inc.tour.php) are kept —
// they must run without interaction. Above-the-fold is unaffected: hero paints via
// CSS (.reveal-css / .hero-front__reveal), marquees are CSS, LCP image is native.
// Trade-off (accepted): hero showreel autoplays only after first interaction, and
// header-scroll / counters / reveals trigger on first scroll. Kill-switch: define
// MFS_DELAY_THEME_JS as false (e.g. in wp-config) to instantly restore the exclusion.
add_filter('rocket_delay_js_exclusions', function ($exclusions) {
    if (defined('MFS_DELAY_THEME_JS') && ! MFS_DELAY_THEME_JS) {
        return $exclusions;
    }
    return array_values(array_filter((array) $exclusions, function ($entry) {
        return strpos($entry, 'themes/maverickframe/build') === false;
    }));
}, 20);

// Search Posts in ACF Fields

add_filter('posts_search', function ($search, $wp_query) {
    if (!is_admin() || !wp_doing_ajax()) {
        return $search;
    }

    if (
        empty($_REQUEST['action']) ||
        !in_array($_REQUEST['action'], [
            'acf/fields/post_object/query',
            'acf/fields/relationship/query'
        ])
    ) {
        return $search;
    }

    global $wpdb;

    if (empty($search)) {
        return $search;
    }

    $search = preg_replace(
        "/OR\s*\(\s*{$wpdb->posts}\.post_content.*?\)/",
        "",
        $search
    );

    $search = preg_replace(
        "/OR\s*\(\s*{$wpdb->posts}\.post_excerpt.*?\)/",
        "",
        $search
    );

    return $search;

}, 999, 2);

// End Search Posts in ACF Fields

/* === Blog v2: conditional enqueue + body class ===
   Enables the new article layout ONLY for blog posts that have the
   SCF flag `use_blog_v2` checked. Other posts keep the current
   layout untouched. */
function blog_v2_is_active() {
    if (!is_singular('blog')) return false;
    if (!function_exists('get_field')) return false;
    return (bool) get_field('use_blog_v2', get_queried_object_id());
}

/* === /Blog v2 enqueue === */

/* === Blog v1 overrides: widths + reading colors ===
   Loads blog-v1-overrides.css on every single-blog post (no toggle).
   Real enqueued stylesheet (not inline <style>) so WP Rocket's
   Used-CSS / inline-cleanup optimizations don't strip it. */
/* Book-a-call CALENDAR: the builder JS is now a lazy Vite chunk, imported by
   bundle.js on first open of the bookcall modal (it used to be a site-wide
   classic <script> sitting on the critical request chain). Only the AJAX config
   stays global so the calendar's step-2 submit keeps its nonce/ajaxurl — attached
   to 'main' (always enqueued) so mfsBookCfg is defined whenever the chunk loads. */
add_action('wp_enqueue_scripts', function () {
    wp_localize_script('main', 'mfsBookCfg', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('pld-ajax-nonce'),
    ));
}, 200);

add_action('wp_enqueue_scripts', function () {
    if (!is_singular('blog')) return;

    // JS enhancements (progress bar, TOC scroll-spy, image lightbox)
    $jsRel = '/blog-v1-enhancements.js';
    $jsAbs = get_template_directory() . $jsRel;
    if (file_exists($jsAbs)) {
        wp_enqueue_script(
            'blog-v1-enhancements',
            get_template_directory_uri() . $jsRel,
            array(),
            filemtime($jsAbs),
            true  // in footer
        );

        // In-article CTAs: per-post override → global (Site Options) → JS baked default.
        // On non-English pages (/es/, /de/) skip the English global-options CTAs so the
        // JS uses its localized baked defaults (until a per-post override is provided).
        $mfs_is_en = ( mfs_lang() === 'en' );
        $inCtas = get_field('in_article_ctas');
        if (empty($inCtas) && $mfs_is_en) {
            $inCtas = get_field('in_article_ctas', 'options');
        }
        $payload = array();
        if (!empty($inCtas) && is_array($inCtas)) {
            foreach ($inCtas as $c) {
                $payload[] = array(
                    'eyebrow' => isset($c['eyebrow']) ? wp_strip_all_tags($c['eyebrow']) : '',
                    'head'    => isset($c['head'])    ? wp_strip_all_tags($c['head'])    : '',
                    'label'   => isset($c['label'])   ? wp_strip_all_tags($c['label'])   : '',
                    'url'     => isset($c['url']) && $c['url'] ? $c['url'] : '#book',
                    'modal'   => !empty($c['modal']),
                );
            }
        }
        wp_localize_script('blog-v1-enhancements', 'mfsBlogCtas', $payload);
    }
}, 200);
/* === /Blog v1 width override === */

/* === GEO readability (T13): relocate site <header> to end of <body> ===
 * The service pages emit ~150KB of mega-menu markup (all submenu panels) BEFORE the
 * main content. Crawlers / LLMs that read source HTML top-down spend budget on that
 * navigation before reaching the actual page copy and pricing. This moves the whole
 * <header> block to just before </body>, so content precedes the menu in source order.
 *
 * Why it is visually / functionally inert:
 *   - .header is position:fixed (viewport-relative) — DOM position does not affect layout.
 *   - z-index is explicit: header 100, blur 999, modal 1000 — modals/overlays stay above
 *     the header even though it now comes later in the DOM.
 *   - All header/menu JS is selector-based (.header / .js-menu / .js-menu-btn / .closest)
 *     with no reliance on DOM order or sibling position.
 * So NO CSS/JS change is needed — only the source order of the <header> node changes.
 *
 * Mechanism: header.php opens an output buffer right after <body>; footer.php flushes it
 * before wp_footer(), cutting the leading <header>…</header> block and re-emitting it last.
 *
 * LIVE site-wide (enabled on prod per Dima's sign-off, 2026-07-04).
 * Kill switch / A-B override on any URL: ?mfs_defer=0 forces OFF, ?mfs_defer=1 forces ON.
 * To disable globally: set MFS_DEFER_MENU to false (or revert this block) and redeploy.
 */
if ( ! defined( 'MFS_DEFER_MENU' ) ) {
    define( 'MFS_DEFER_MENU', true ); // LIVE — verified: header/menu render + reveal OK when relocated (earlier "stall" was a hidden-tab rAF artifact, identical on normal prod)
}

if ( ! function_exists( 'mfs_defer_menu_enabled' ) ) {
    function mfs_defer_menu_enabled() {
        // Per-request override / kill switch: ?mfs_defer=0 forces OFF, ?mfs_defer=1 forces ON.
        // Lets us A/B-compare the same live URL and disable per-request in an emergency.
        if ( isset( $_GET['mfs_defer'] ) ) {
            return $_GET['mfs_defer'] === '1';
        }
        return (bool) MFS_DEFER_MENU;
    }
}

if ( ! function_exists( 'mfs_defer_menu_start' ) ) {
    function mfs_defer_menu_start() {
        if ( mfs_defer_menu_enabled() ) {
            ob_start();
        }
    }
}

if ( ! function_exists( 'mfs_defer_menu_flush' ) ) {
    function mfs_defer_menu_flush() {
        if ( ! mfs_defer_menu_enabled() ) return;
        if ( ! ob_get_level() ) return;

        $buf = ob_get_clean();

        // Locate the site header by its unique class signature. If absent (e.g. the
        // full-screen tour-builder template never renders it), emit the buffer untouched.
        $marker = '<header class="js-reveal header';
        $start  = strpos( $buf, $marker );
        if ( $start === false ) { echo $buf; return; }

        // The site header renders in full (all submenus) before any page content, and
        // contains no nested <header>, so the first </header> after $start closes it.
        $close = strpos( $buf, '</header>', $start );
        if ( $close === false ) { echo $buf; return; }

        $end         = $close + strlen( '</header>' );
        $header_html = substr( $buf, $start, $end - $start );
        $rest        = substr( $buf, 0, $start ) . substr( $buf, $end );

        echo $rest;         // main content first
        echo $header_html;  // navigation / mega-menu last
    }
}
/* === /GEO readability (T13) === */


/* === GEO readability (T14): drop unused WordPress core CSS ===
 * The theme is a classic (SCSS-bundle) theme and does not use the block editor's
 * global styles or the core block-library CSS. WordPress injects them anyway as
 * inline <style> BEFORE the main content: global-styles-inline-css (~9 KB/page)
 * plus wp-block-library (~3.6 KB). Pure dead weight on page types whose body is
 * NOT built from core Gutenberg blocks.
 *
 * Scope = DENYLIST by content: strip on every view EXCEPT one whose queried post
 * body contains CORE Gutenberg blocks (wp:paragraph/heading/image/table/list/...).
 * ACF blocks (wp:acf/*) render as theme HTML and need no WP core CSS, so ACF-only
 * bodies (front, services, solutions, team, landings, most pages) get stripped;
 * blog posts and any Gutenberg-authored page (e.g. /privacy-policy/, 41 core blocks)
 * keep the CSS because their inline block styles may reference the --wp--preset--*
 * custom properties defined in global-styles. Verified via per-type DOM audit 05.07.2026.
 *
 * Timing: wp_dequeue_style('global-styles') only takes effect when it runs AFTER
 * core enqueues it — hence priority 100. The pre-existing wpassist_remove_block_library_css
 * runs at the default priority 10 and was a no-op for global-styles (fired before the
 * enqueue), which is why global-styles still survived in the raw HTML.
 *
 * Kill switch / A-B override on any URL: ?mfs_dequeue=0 forces OFF (WP CSS restored),
 * ?mfs_dequeue=1 forces ON. To disable globally: set MFS_GEO_DEQUEUE_CSS to false and redeploy.
 */
if ( ! defined( 'MFS_GEO_DEQUEUE_CSS' ) ) {
    define( 'MFS_GEO_DEQUEUE_CSS', true );
}

if ( ! function_exists( 'mfs_geo_dequeue_enabled' ) ) {
    function mfs_geo_dequeue_enabled() {
        // Per-request override / kill switch: ?mfs_dequeue=0 forces OFF, ?mfs_dequeue=1 forces ON.
        if ( isset( $_GET['mfs_dequeue'] ) ) {
            return $_GET['mfs_dequeue'] === '1';
        }
        return (bool) MFS_GEO_DEQUEUE_CSS;
    }
}

if ( ! function_exists( 'mfs_page_has_core_blocks' ) ) {
    function mfs_page_has_core_blocks() {
        // Only a singular view can carry a block-authored body (archives/search/404 → none).
        if ( ! is_singular() ) {
            return false;
        }
        $post = get_queried_object();
        if ( ! ( $post instanceof WP_Post ) ) {
            return false;
        }
        // Match core/plugin block markers (<!-- wp:paragraph -->, <!-- wp:heading -->, ...)
        // but NOT ACF blocks (<!-- wp:acf/* -->), which produce plain theme HTML and need
        // no WP core CSS. So ACF-only bodies (front/services/solutions/team/landings) get
        // stripped; blog posts and Gutenberg-authored pages (e.g. /privacy-policy/) keep it.
        return (bool) preg_match( '/<!--\s+wp:(?!acf\/)[a-z0-9-]+/i', (string) $post->post_content );
    }
}

if ( ! function_exists( 'mfs_geo_dequeue_wp_css' ) ) {
    function mfs_geo_dequeue_wp_css() {
        if ( is_admin() || ! mfs_geo_dequeue_enabled() ) {
            return;
        }
        // Keep WP core CSS only where core Gutenberg blocks actually render.
        if ( mfs_page_has_core_blocks() ) {
            return;
        }
        wp_dequeue_style( 'global-styles' );       // theme.json-generated inline CSS (~9 KB)
        wp_dequeue_style( 'wp-block-library' );     // core block library base CSS (~3.6 KB)
        wp_dequeue_style( 'wp-block-library-theme' );
        // Prevent a late re-print of global styles from the footer hook.
        remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
    }
    add_action( 'wp_enqueue_scripts', 'mfs_geo_dequeue_wp_css', 100 );
}
/* === /GEO readability (T14) === */