<?php
// Theme supports

add_theme_support('title-tag');
add_theme_support('post-thumbnails');

// Intermediate responsive sizes (fill gap large(1024)->full). Originals untouched;
// run `wp media regenerate --only-missing` after deploy.
add_image_size('mfs-1366', 1366, 9999);
add_image_size('mfs-1920', 1920, 9999);

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
        || is_front_page();
}

// End New Design

// Enqueue Scripts and Styles

include "inc.vite.php";

// End Enqueue Scripts and Styles

//  Add assets

function assets()
{
    // global $wp_query;
    wp_localize_script('main', 'contacts', array(
        'home_url' => home_url()
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

function lazy_attachment($attachment_id, $size, $nativeLazy = 'lazy', $class = '')
{
    $src = wp_get_attachment_image_url($attachment_id, 'lqip') != wp_get_attachment_image_url($attachment_id, 'full') ? wp_get_attachment_image_url($attachment_id, 'lqip') : wp_get_attachment_image_url($attachment_id, 'thumbnail');

    echo wp_get_attachment_image($attachment_id, $size, false, [
        'loading' => $nativeLazy,
        'class' => 'lazyload blur-up ' . $class,
        'src' => $src,
        'srcset' => $src,
        'data-src' => wp_get_attachment_image_url($attachment_id, $size),
        'data-srcset' => wp_get_attachment_image_srcset($attachment_id, $size)
    ]);
}

// End Lazy load


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

    $args = [
        'post_type' => $post_type,
        'posts_per_page' => 6,
        'paged' => (int) $paged,
        'post_status' => 'publish',
        'cat' => $cat,
    ];

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

// Seo title

add_filter('document_title', 'document_title_filter');
function document_title_filter($title)
{
    global $post;
    if ((!empty($post->ID) && get_field('title', $post->ID)))
        return get_field('title');
    return $title;
}

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

// End Fix wp rocket optimization for dynamic content

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
    }
}, 200);
/* === /Blog v1 width override === */