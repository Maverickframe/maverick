<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
    exit;  

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

define('IS_VITE_DEVELOPMENT', filter_var($_ENV['IS_VITE_DEVELOPMENT'] ?? false, FILTER_VALIDATE_BOOLEAN));
define('DIST_DEF', 'build');

if (defined('IS_VITE_DEVELOPMENT') && IS_VITE_DEVELOPMENT == true) {
    define('VITE_SERVER', '//localhost:' . $_ENV['VITE_ASSETS_PORT'] . $_ENV['VITE_THEME_PATH']);
}

// enqueue hook

function get_template_directory_uri_vite($path = '') {
    if (defined('IS_VITE_DEVELOPMENT') && IS_VITE_DEVELOPMENT == true) {
        return VITE_SERVER . $path;
    }

    return esc_url( get_template_directory_uri() ) . '/' . DIST_DEF . $path;
}


function mfs_page_bundle_key() {
    // /lp/ ad landing pages: no per-type block bundle. The bare template ships a
    // flat, self-contained assets/lp/lp.css (enqueued in inc.lp.php) instead of
    // page.scss, so it stays lean and isolated. Returning a key absent from the
    // manifest means no 'style' handle is enqueued for this template.
    if (is_page_template('templates/page-lp-bare.php')) return 'src/scss/bundles/__lp_none__';
    if (is_front_page()) return 'src/scss/bundles/front.scss';
    if (is_singular('blog')) return 'src/scss/bundles/blog-single.scss';
    if (is_page_template('templates/template-blog.php')) return 'src/scss/bundles/blog.scss';
    if (is_singular('success-stories') || is_page_template('templates/success-stories.php')) return 'src/scss/bundles/cases.scss';
    if (is_page_template('templates/template-services-hub.php')) return 'src/scss/bundles/cases.scss';
    if (is_page_template('templates/template-services.php')) return 'src/scss/bundles/services.scss';
    if (is_page_template('templates/template-gallery.php')) return 'src/scss/bundles/gallery.scss';
    if (is_page_template('templates/team-page.php') || is_singular('team')) return 'src/scss/bundles/team.scss';
    if (is_page_template('templates/presentation-design-page.php')) return 'src/scss/bundles/presentation.scss';
    if (is_page_template('templates/template-contacts.php')) return 'src/scss/bundles/contacts.scss';
    if (is_page_template('templates/template-legal.php')) return 'src/scss/bundles/legal.scss';
    if (is_page_template('templates/template-press.php')) return 'src/scss/bundles/press.scss';
    if (is_404()) return 'src/scss/bundles/error.scss';
    // Plain content pages on the default template (page.php): /app/, DE legal pages,
    // any future plain page. Replaces the retired old-design main.scss for them.
    if (is_page()) return 'src/scss/bundles/page.scss';
    // Catch-all: anything else without a dedicated bundle (solutions singulars) gets
    // the full block set, not the slim homepage bundle. Only the real front page uses
    // front.scss.
    return 'src/scss/bundles/fallback.scss';
}

// Rewrite the homepage BTF stylesheet <link> into a non-blocking load:
// preload → swap to stylesheet onload, with a <noscript> fallback so it still
// applies without JS. Only touches the 'style-btf' handle; every other sheet
// is left blocking. Registered only on the homepage split path.
function mfs_async_btf_style_tag($tag, $handle, $href, $media) {
    if ($handle !== 'style-btf') {
        return $tag;
    }
    $esc = esc_url($href);
    return '<link rel="preload" as="style" href="' . $esc . '" '
        . 'onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n"
        . '<noscript><link rel="stylesheet" href="' . $esc . '"></noscript>' . "\n";
}

add_action( 'wp_enqueue_scripts', function() {
    if (defined('IS_VITE_DEVELOPMENT') && IS_VITE_DEVELOPMENT == true) {
        define('VITE_ENTRY_POINT', $_ENV["VITE_ENTRY_POINT"]);
        define('VITE_STYLES', $_ENV["VITE_STYLES"]);
        define('VITE_STYLES_NEW', $_ENV["VITE_STYLES_NEW"]);
        define('VITE_STYLES_BLOCKS', $_ENV["VITE_STYLES_BLOCKS"]);

        function vite_head_module_hook() {
            echo '<script type="module" crossorigin src="' . VITE_SERVER . '/@vite/client"></script>';

            // Old-design main.scss retired — dev always serves the new-design styles.
            echo '<link rel="stylesheet" href="' . VITE_SERVER . VITE_STYLES_NEW . '" rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
            echo '<link rel="stylesheet" href="' . VITE_SERVER . VITE_STYLES_BLOCKS . '" rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
            echo '<script type="module" crossorigin src="' . VITE_SERVER . VITE_ENTRY_POINT . '"></script>';
        }
        add_action('wp_head', 'vite_head_module_hook');
        wp_enqueue_script( 'main', get_template_directory_uri_vite() . '/' .'src/js/bundle.js', array(), '', true );
    } else {
        define('DIST_URI', get_template_directory_uri_vite());
        define('DIST_PATH', get_template_directory() . '/' . DIST_DEF);
        define('JS_DEPENDENCY', array());
        define('JS_LOAD_IN_FOOTER', true);

        $manifest = json_decode( file_get_contents( DIST_PATH . '/.vite/manifest.json'), true );

        if (is_array($manifest)) {
            foreach ($manifest as $key => $value) {
                if($key == "src/js/bundle.js")
                {
                    $js_file = $value['file'];
                    if ( ! empty($js_file)) {
                        // Version MUST be null (no ?ver query). The hashed filename
                        // already busts cache. With a ?ver, WordPress loads the ESM
                        // entry as main-*.js?ver=X while Vite's code-split runtime
                        // references the same chunk by its canonical main-*.js (no
                        // query). Different URLs = two separate module instances, so
                        // every top-level binding (e.g. the archive "Load more" click
                        // handler) runs TWICE — one click fired two AJAX requests and
                        // appended each page of posts twice (duplicate cards).
                        wp_enqueue_script( 'main', DIST_URI . '/' . $js_file, JS_DEPENDENCY, null, JS_LOAD_IN_FOOTER );
                        add_filter('script_loader_tag', function($tag, $handle){
                            if ($handle === 'main') {
                                // The code-split bundle is ESM: the entry contains
                                // import.meta + dynamic import() (Vite preload helper),
                                // which are SyntaxErrors in a classic script. Load as a
                                // module — modules are deferred by default, so the old
                                // `defer` timing is preserved. Strip any type WP added.
                                $tag = preg_replace('/\stype=("|\')[^"\']*\1/', '', $tag);
                                return str_replace('<script ', '<script type="module" ', $tag);
                            }
                            return $tag;
                        }, 10, 2);
                    }
                }
            }

            // CSS. Every page loads its per-type bundle (page.scss / cases.scss / …)
            // as one render-blocking sheet. The homepage is the exception: it's split
            // into front-atf.scss (render-blocking, first screen only) + front-btf.scss
            // (async, everything below the fold — modals/footer/lower blocks). This cuts
            // the blocking CSS on the homepage from ~22 KB to ~8 KB gz and clears the
            // "render-blocking / unused CSS / critical-path" PageSpeed flags without
            // inline critical CSS (which we deliberately avoid) or WP Rocket RUCSS
            // (which stripped runtime reveal classes before). FOUC-safe: modals are
            // display:none; below-fold blocks start reveal-hidden via common.scss (ATF).
            // Kill-switch: ?mfs_split=0 or define('MFS_CSS_SPLIT', false) → combined
            // front.scss, the old single-file behavior.
            $mfs_split = is_front_page()
                && (! defined('MFS_CSS_SPLIT') || MFS_CSS_SPLIT)
                && (! isset($_GET['mfs_split']) || $_GET['mfs_split'] !== '0');

            $mfs_atf = $mfs_split && ! empty($manifest['src/scss/bundles/front-atf.scss']['file'])
                ? $manifest['src/scss/bundles/front-atf.scss']['file'] : null;
            $mfs_btf = $mfs_split && ! empty($manifest['src/scss/bundles/front-btf.scss']['file'])
                ? $manifest['src/scss/bundles/front-btf.scss']['file'] : null;

            if ($mfs_atf && $mfs_btf) {
                // ATF: normal blocking stylesheet (handle stays 'style' so any
                // wp_add_inline_style('style', …) still targets it).
                wp_register_style('style', DIST_URI . '/' . $mfs_atf, [], null);
                wp_enqueue_style('style');
                // BTF: enqueued here, rewritten to preload+onload by the filter below.
                wp_register_style('style-btf', DIST_URI . '/' . $mfs_btf, [], null);
                wp_enqueue_style('style-btf');
                add_filter('style_loader_tag', 'mfs_async_btf_style_tag', 10, 4);
            } elseif ( ! empty($manifest[mfs_page_bundle_key()]['file'])) {
                // Non-homepage, or split disabled / build missing a split file: single
                // combined sheet (front.scss on the homepage kill-switch path).
                wp_register_style('style', DIST_URI . '/' . $manifest[mfs_page_bundle_key()]['file'], [], null);
                wp_enqueue_style('style');
            }
        }
    }
});

add_action('enqueue_block_editor_assets', function () {
    if (defined('IS_VITE_DEVELOPMENT') && IS_VITE_DEVELOPMENT) {
        wp_enqueue_style(
            'blocks-editor',
            VITE_SERVER . $_ENV['VITE_STYLES_BLOCKS'],
            [],
            null
        );
        return;
    }

    $manifest = json_decode(
        file_get_contents(get_template_directory() . '/' . DIST_DEF . '/.vite/manifest.json'),
        true
    );

    if (isset($manifest['src/scss/blocks.scss']['file'])) {
        wp_enqueue_style(
            'blocks-editor',
            get_template_directory_uri() . '/' . DIST_DEF . '/' . $manifest['src/scss/blocks.scss']['file'],
            [],
            null
        );
    }
});

// Services Hub: previously loaded extra service+case bundles because cases.scss
// didn't carry the hub's blocks. cases.scss now includes the hub blocks
// (hero-front, what-we-do, performance-scale, reviews, worldwide-rendering,
// breadcrumbs), so the temporary extra-bundle load is no longer needed.
