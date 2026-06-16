<?php get_header();

$content_no_filter = get_the_content();
$content = apply_filters('the_content', $content_no_filter);
$index = generateToC($content, get_the_title());
$content = $index['content'];
$toc = $index['toc'];

$is_es = function_exists('pll_current_language') && pll_current_language() === 'es';
?>

<?= get_template_part('components/common/header'); ?>

<main class="main">
    <?= get_template_part('components/new-design/blog/hero-post'); ?>

    <div class="article-page-inner">
        <div class="container">
            <div class="article-page__columns article-page__columns--3">
                <!-- Sidebar Left -->
                <aside class="article-page__aside article-page__aside--left">
                    <button class="article-page__toggle js-sidebar-toggle"><?= mfs_t('Contents', 'Contenido'); ?></button>

                    <div class="article-page__sticky js-sidebar">
                        <?= get_template_part('components/new-design/blog/author-mini'); ?>
                        <?= get_template_part('components/new-design/blog/reading-status'); ?>
                        <?= get_template_part('components/new-design/toc', null, ['title' => mfs_t('Contents', 'Contenido'), 'toc' => $toc]); ?>
                        <?= get_template_part('components/new-design/blog/feedback'); ?>
                    </div>
                </aside>

                <!-- Main Content -->
                <article class="article-page__main">
                    <section class="article-page__content"><?= $content; ?></section>

                    <?= get_template_part('components/new-design/blog/cta'); ?>

                    <?= get_template_part('components/new-design/faq', null, ['class' => ' article-page__faq']); ?>

                    <?= get_template_part('components/new-design/blog/author'); ?>
                </article>

                <!-- Sidebar Right -->
                <aside class="article-page__aside article-page__aside--right">
                    <div class="article-page__sticky">
                        <?= get_template_part('components/new-design/blog/sidebar-cta'); ?>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    <div class="article-page__breadcrumbs-bottom">
        <div class="container">
            <span class="article-page__breadcrumbs-label"><?= mfs_t('You are here', 'Estás aquí'); ?></span>
            <div class="article-page__breadcrumbs-wrap">
                <?= get_template_part('components/new-design/breadcrumbs', null, [
                    'breadcrumbs' => [
                        1 => [ 'name' => mfs_t('Home', 'Inicio'), 'link' => $is_es ? home_url('/es/') : home_url() ],
                        2 => [ 'name' => 'Blog', 'link' => $is_es ? home_url('/es/blog/') : home_url('/blog/') ]
                    ]
                ]); ?>
            </div>
        </div>
    </div>

    <?= get_template_part('components/new-design/blog/articles-related', null, ['post_type' => 'blog']); ?>
</main>

<?= get_template_part('components/common/footer'); ?>
<?php get_footer(); ?>
