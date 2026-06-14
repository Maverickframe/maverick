<?php
/**
 * Maverickframe 3D Tour Builder — server side.
 * - CPT `pano_tour` stores each tour (config JSON in meta `_pano_tour_config`).
 * - REST `mfs/v1/tour[/<id>]` (GET/POST) loads/saves a tour (editors only).
 * - Page template `templates/template-tour-builder.php` = the gated builder UI.
 * - Shortcode `[pano_tour id="N"]` renders a tour read-only on any page.
 * CODE cycle (theme/git). Assets are flat files in /tour, no Vite rebuild.
 */
if ( ! defined('ABSPATH') ) exit;

define('MFS_TOUR_TEMPLATE', 'templates/template-tour-builder.php');
define('MFS_TOUR_PSV', '5.14.1');
define('MFS_TOUR_THREE', '0.169.0');

/* ---------------------------------------------------------------- CPT */
add_action('init', function () {
    register_post_type('pano_tour', array(
        'labels' => array(
            'name'          => '3D Tours',
            'singular_name' => '3D Tour',
            'add_new_item'  => 'Add New Tour',
            'edit_item'     => 'Edit Tour',
            'menu_name'     => '3D Tours',
        ),
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'menu_icon'          => 'dashicons-panorama',
        'menu_position'      => 26,
        'supports'           => array('title'),
        'capability_type'    => 'post',
        'exclude_from_search'=> true,
        'has_archive'        => false,
        'rewrite'            => false,
    ));

    register_post_meta('pano_tour', '_pano_tour_config', array(
        'type'          => 'string',
        'single'        => true,
        'show_in_rest'  => false, // written via our own REST route below
        'auth_callback' => function ($allowed, $meta, $post_id) {
            return current_user_can('edit_post', $post_id);
        },
    ));
});

/* ------------------------------------------------------------- REST API */
add_action('rest_api_init', function () {
    register_rest_route('mfs/v1', '/tour', array(
        'methods'             => 'POST',
        'callback'            => 'mfs_tour_rest_save',
        'permission_callback' => function () { return current_user_can('edit_posts'); },
    ));
    register_rest_route('mfs/v1', '/tour/(?P<id>\d+)', array(
        array(
            'methods'             => 'GET',
            'callback'            => 'mfs_tour_rest_get',
            'permission_callback' => function ($r) { return current_user_can('edit_post', (int) $r['id']); },
        ),
        array(
            'methods'             => 'POST',
            'callback'            => 'mfs_tour_rest_save',
            'permission_callback' => function ($r) { return current_user_can('edit_post', (int) $r['id']); },
        ),
    ));
});

function mfs_tour_rest_get($req) {
    $id  = (int) $req['id'];
    if (get_post_type($id) !== 'pano_tour') return new WP_Error('not_found', 'Tour not found', array('status' => 404));
    $raw = get_post_meta($id, '_pano_tour_config', true);
    return array(
        'id'     => $id,
        'title'  => get_the_title($id),
        'config' => $raw ? json_decode($raw, true) : array('startId' => null, 'nodes' => array()),
    );
}

function mfs_tour_rest_save($req) {
    $id     = isset($req['id']) ? (int) $req['id'] : 0;
    $params = $req->get_json_params();
    $title  = isset($params['title']) ? sanitize_text_field($params['title']) : 'Untitled tour';
    $config = mfs_tour_sanitize_config(isset($params['config']) ? $params['config'] : array());

    $postarr = array('post_type' => 'pano_tour', 'post_title' => $title, 'post_status' => 'publish');
    if ($id && get_post_type($id) === 'pano_tour') {
        $postarr['ID'] = $id;
        wp_update_post($postarr);
    } else {
        $id = wp_insert_post($postarr);
    }
    if (!$id || is_wp_error($id)) return new WP_Error('save_failed', 'Could not save tour', array('status' => 500));

    update_post_meta($id, '_pano_tour_config', wp_slash(wp_json_encode($config)));
    return array('id' => $id, 'title' => $title);
}

function mfs_tour_sanitize_config($c) {
    $out = array('startId' => null, 'nodes' => array());
    if (!is_array($c)) return $out;
    $out['startId'] = isset($c['startId']) ? sanitize_text_field($c['startId']) : null;
    if (empty($c['nodes']) || !is_array($c['nodes'])) return $out;

    foreach ($c['nodes'] as $n) {
        if (!is_array($n)) continue;
        $node = array(
            'id'      => sanitize_text_field($n['id'] ?? ''),
            'name'    => sanitize_text_field($n['name'] ?? 'Scene'),
            'day'     => esc_url_raw($n['day'] ?? ''),
            'dayId'   => isset($n['dayId']) ? (int) $n['dayId'] : null,
            'night'   => !empty($n['night']) ? esc_url_raw($n['night']) : null,
            'nightId' => isset($n['nightId']) ? (int) $n['nightId'] : null,
            'hotspots'=> array(),
        );
        if (!empty($n['hotspots']) && is_array($n['hotspots'])) {
            foreach ($n['hotspots'] as $h) {
                if (!is_array($h)) continue;
                $pos = (isset($h['position']) && is_array($h['position']))
                    ? array('yaw' => (float) ($h['position']['yaw'] ?? 0), 'pitch' => (float) ($h['position']['pitch'] ?? 0))
                    : array('yaw' => 0, 'pitch' => 0);
                $hs = array(
                    'id'       => sanitize_text_field($h['id'] ?? ''),
                    'type'     => (isset($h['type']) && $h['type'] === 'info') ? 'info' : 'nav',
                    'position' => $pos,
                );
                if ($hs['type'] === 'nav') {
                    $hs['target'] = sanitize_text_field($h['target'] ?? '');
                } else {
                    $hs['title']   = sanitize_text_field($h['title'] ?? '');
                    $hs['text']    = sanitize_textarea_field($h['text'] ?? '');
                    $hs['image']   = !empty($h['image']) ? esc_url_raw($h['image']) : null;
                    $hs['imageId'] = isset($h['imageId']) ? (int) $h['imageId'] : null;
                }
                $node['hotspots'][] = $hs;
            }
        }
        $out['nodes'][] = $node;
    }
    return $out;
}

/* ------------------------------------------------- shared asset helpers */
function mfs_tour_uri() { return get_template_directory_uri() . '/tour'; }
function mfs_tour_path() { return get_template_directory() . '/tour'; }
function mfs_tour_ver($file) {
    $abs = mfs_tour_path() . '/' . $file;
    return file_exists($abs) ? filemtime($abs) : MFS_TOUR_PSV;
}

/* PSV stylesheets (core/markers/compass) — printed as plain <link>. */
function mfs_tour_print_psv_css() {
    $v = MFS_TOUR_PSV;
    foreach (array('core', 'markers-plugin', 'compass-plugin') as $pkg) {
        echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/' . $pkg . '@' . $v . '/index.css">' . "\n";
    }
}

/* ES-module importmap for PSV + three (printed once per request). */
function mfs_tour_print_importmap() {
    static $done = false;
    if ($done) return;
    $done = true;
    $v = MFS_TOUR_PSV; $t = MFS_TOUR_THREE;
    $map = array('imports' => array(
        'mfs-tour-engine' => mfs_tour_uri() . '/engine.js?ver=' . mfs_tour_ver('engine.js'),
        'three' => "https://cdn.jsdelivr.net/npm/three@{$t}/build/three.module.js",
        '@photo-sphere-viewer/core'             => "https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/core@{$v}/index.module.js",
        '@photo-sphere-viewer/markers-plugin'   => "https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/markers-plugin@{$v}/index.module.js",
        '@photo-sphere-viewer/autorotate-plugin'=> "https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/autorotate-plugin@{$v}/index.module.js",
        '@photo-sphere-viewer/gyroscope-plugin' => "https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/gyroscope-plugin@{$v}/index.module.js",
        '@photo-sphere-viewer/stereo-plugin'    => "https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/stereo-plugin@{$v}/index.module.js",
        '@photo-sphere-viewer/compass-plugin'   => "https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/compass-plugin@{$v}/index.module.js",
    ));
    echo '<script type="importmap" data-no-optimize="1" data-cfasync="false">' . wp_json_encode($map) . '</script>' . "\n";
}

/* Keep WP Rocket from minifying/delaying the importmap and PSV modules. */
add_filter('rocket_excluded_inline_js_content', function ($e) { $e[] = 'importmap'; $e[] = 'MFS_TOUR'; return $e; });
add_filter('rocket_delay_js_exclusions', function ($e) { $e[] = 'photo-sphere'; $e[] = 'jsdelivr'; $e[] = '/tour/'; return $e; });
add_filter('rocket_minify_excluded_external_js', function ($e) { $e[] = 'jsdelivr.net'; return $e; });

/* ------------------------------------------------------ BUILDER (gated) */
add_action('wp_enqueue_scripts', function () {
    if (!is_page_template(MFS_TOUR_TEMPLATE)) return;
    // Bare full-screen app: drop the theme bundle (incl. its own three.js) + chrome JS.
    wp_dequeue_style('style');
    wp_dequeue_style('main');
    wp_dequeue_script('main');
    wp_dequeue_script('book-calendar');
    // Media Library picker (frontend, logged-in).
    wp_enqueue_media();
    wp_enqueue_style('mfs-tour-builder', mfs_tour_uri() . '/builder.css', array(), mfs_tour_ver('builder.css'));
}, 100);

// Builder page must never be indexed.
add_filter('wp_robots', function ($robots) {
    if (is_page_template(MFS_TOUR_TEMPLATE)) { $robots['noindex'] = true; $robots['nofollow'] = true; }
    return $robots;
}, 99);

// importmap into <head> on the builder page.
add_action('wp_head', function () {
    if (!is_page_template(MFS_TOUR_TEMPLATE)) return;
    mfs_tour_print_psv_css();
    mfs_tour_print_importmap();
}, 5);

// config bootstrap + builder module in the footer of the builder page.
add_action('wp_footer', function () {
    if (!is_page_template(MFS_TOUR_TEMPLATE)) return;
    $tour_id = isset($_GET['tour']) ? (int) $_GET['tour'] : 0;
    $config  = array('startId' => null, 'nodes' => array());
    if ($tour_id && get_post_type($tour_id) === 'pano_tour' && current_user_can('edit_post', $tour_id)) {
        $raw = get_post_meta($tour_id, '_pano_tour_config', true);
        if ($raw) { $decoded = json_decode($raw, true); if ($decoded) $config = $decoded; }
    }
    $boot = array(
        'restUrl' => esc_url_raw(rest_url('mfs/v1/')),
        'nonce'   => wp_create_nonce('wp_rest'),
        'tourId'  => $tour_id,
        'config'  => $config,
    );
    echo '<script id="mfs-tour-boot">window.MFS_TOUR=' . wp_json_encode($boot) . ';</script>' . "\n";
    echo '<script type="module" src="' . esc_url(mfs_tour_uri() . '/builder.js') . '?ver=' . mfs_tour_ver('builder.js') . '"></script>' . "\n";
}, 5);

/* --------------------------------------------------- SHORTCODE / VIEWER */
function mfs_tour_need_viewer($set = false) {
    static $need = false;
    if ($set) $need = true;
    return $need;
}

function mfs_tour_shortcode($atts) {
    $atts = shortcode_atts(array('id' => 0, 'height' => ''), $atts, 'pano_tour');
    $id   = (int) $atts['id'];
    if (!$id || get_post_type($id) !== 'pano_tour') {
        return '<div class="mfs-tour"><div class="mfs-tour-empty">Tour not found.</div></div>';
    }
    $raw = get_post_meta($id, '_pano_tour_config', true);
    $cfg = $raw ? json_decode($raw, true) : null;
    if (!$cfg || empty($cfg['nodes'])) {
        return '<div class="mfs-tour"><div class="mfs-tour-empty">This tour has no scenes yet.</div></div>';
    }
    mfs_tour_need_viewer(true);

    $hattr = ''; $style = '';
    if (!empty($atts['height'])) {
        $h = preg_replace('/[^0-9a-z%.]/i', '', $atts['height']);
        if (is_numeric($h)) $h .= 'px';
        $hattr = ' data-height="1"'; $style = ' style="height:' . esc_attr($h) . '"';
    }
    return '<div class="mfs-tour"' . $hattr . $style . ' data-tour="' . esc_attr(wp_json_encode($cfg)) . '"></div>';
}
add_shortcode('pano_tour', 'mfs_tour_shortcode');

// Print viewer assets in the footer only if a tour was rendered on this page.
add_action('wp_footer', function () {
    if (!mfs_tour_need_viewer()) return;
    echo '<link rel="stylesheet" href="' . esc_url(mfs_tour_uri() . '/viewer.css') . '?ver=' . mfs_tour_ver('viewer.css') . '">' . "\n";
    mfs_tour_print_psv_css();
    mfs_tour_print_importmap();
    echo '<script type="module" src="' . esc_url(mfs_tour_uri() . '/viewer.js') . '?ver=' . mfs_tour_ver('viewer.js') . '"></script>' . "\n";
}, 20);
