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
    return 'src/scss/bundles/front.scss';
}

add_action( 'wp_enqueue_scripts', function() {
    if (defined('IS_VITE_DEVELOPMENT') && IS_VITE_DEVELOPMENT == true) {
        define('VITE_ENTRY_POINT', $_ENV["VITE_ENTRY_POINT"]);
        define('VITE_STYLES', $_ENV["VITE_STYLES"]);
        define('VITE_STYLES_NEW', $_ENV["VITE_STYLES_NEW"]);
        define('VITE_STYLES_BLOCKS', $_ENV["VITE_STYLES_BLOCKS"]);

        function vite_head_module_hook() {
            echo '<script type="module" crossorigin src="' . VITE_SERVER . '/@vite/client"></script>';

            if (!isNewDesign()) {
                echo '<link rel="stylesheet" href="' . VITE_SERVER . VITE_STYLES . '" rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
            } else {
                echo '<link rel="stylesheet" href="' . VITE_SERVER . VITE_STYLES_NEW . '" rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
                echo '<link rel="stylesheet" href="' . VITE_SERVER . VITE_STYLES_BLOCKS . '" rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">';
            }
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
                        wp_enqueue_script( 'main', DIST_URI . '/' . $js_file, JS_DEPENDENCY, '', JS_LOAD_IN_FOOTER );
                        add_filter('script_loader_tag', function($tag, $handle){
                            if ($handle === 'main') {
                                return str_replace(' src', ' defer src', $tag);
                            }
                            return $tag;
                        }, 10, 2);
                    }
                }
                if (!isNewDesign()) {
                    if($key == "src/scss/main.scss")
                    {
                        $css_file = $value['file'];
                        if ( ! empty($css_file)) {
                            wp_register_style('main', DIST_URI . '/' . $css_file, [], null);
                            wp_enqueue_style('main');
                            add_filter('style_loader_tag', function($tag, $handle){
                                if($handle === 'main') {
                                    return str_replace("rel='stylesheet'", "rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"", $tag);
                                }
                                return $tag;
                            }, 10, 2);
                        }
                    }
                } else if($key == mfs_page_bundle_key())
                {
                    $css_file = $value['file'];
                    if ( ! empty($css_file)) {
                        wp_register_style('style', DIST_URI . '/' . $css_file, [], null);
                        wp_enqueue_style('style');
                    }
                }
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

// Services Hub (build phase): the hub reuses blocks from several pages, whose
// styles live in different page bundles. Load the extra compiled bundles so all
// blocks render correctly while we lay the page out. Trim to a dedicated hub
// bundle before go-live for performance.
add_action('wp_enqueue_scripts', function () {
    if (!is_page_template('templates/template-services-hub.php')) return;
    if (defined('IS_VITE_DEVELOPMENT') && IS_VITE_DEVELOPMENT == true) return;

    $manifest_path = get_template_directory() . '/' . DIST_DEF . '/.vite/manifest.json';
    if (!file_exists($manifest_path)) return;

    $manifest = json_decode(file_get_contents($manifest_path), true);
    if (!is_array($manifest)) return;

    $extra_bundles = array(
        'src/scss/bundles/services.scss',
        'src/scss/bundles/cases.scss',
    );

    foreach ($extra_bundles as $i => $bundle_key) {
        if (isset($manifest[$bundle_key]['file'])) {
            wp_enqueue_style(
                'hub-extra-' . $i,
                get_template_directory_uri() . '/' . DIST_DEF . '/' . $manifest[$bundle_key]['file'],
                array('style'),
                null
            );
        }
    }
}, 20);
