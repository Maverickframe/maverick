<?php
/*
* Template Name: Press (new design)
* Template Post Type: page
*/

/*
 * Press / "As Featured In" — social-proof page.
 * Cards come from the ACF repeater `press_items` (group_88f2a9c41e7d3).
 * Outbound links: NOFOLLOW (Arseny's call 2026-07-20 — safest default until
 * the exchange mechanic actually launches; flip to dofollow deliberately,
 * per item, when a partner deal requires a live link).
 * Layout (quote-led featured block + preview-image cards) modeled on
 * Studio McGee's press page structure.
 */

$mfs_press_items = [];
if ( have_rows('press_items') ) {
    while ( have_rows('press_items') ) {
        the_row();
        $mfs_press_items[] = [
            'name'     => get_sub_field('publication_name'),
            'logo'     => get_sub_field('publication_logo'),
            'title'    => get_sub_field('article_title'),
            'url'      => get_sub_field('article_url'),
            'date'     => get_sub_field('date'), // Y-m-d or ''
            'excerpt'  => get_sub_field('excerpt'),
            'category' => get_sub_field('category'),
            'featured' => (bool) get_sub_field('featured'),
            'preview'  => get_sub_field('preview_image'),
        ];
    }
}

usort($mfs_press_items, function ($a, $b) {
    if ( $a['featured'] !== $b['featured'] ) {
        return $a['featured'] ? -1 : 1;
    }
    return strcmp( (string) $b['date'], (string) $a['date'] );
});

$mfs_featured_items = array_values( array_filter( $mfs_press_items, fn($i) => $i['featured'] ) );
$mfs_regular_items  = array_values( array_filter( $mfs_press_items, fn($i) => ! $i['featured'] ) );

$mfs_press_intro = get_field('press_intro');
if ( ! $mfs_press_intro ) {
    $mfs_press_intro = mfs_t(
        'Publications and industry platforms that have featured Maverick Frame Studio — our architectural visualization, CGI and 3D rendering work.',
        'Publicaciones y plataformas del sector que han destacado el trabajo de visualización arquitectónica y CGI de Maverick Frame Studio.'
    );
}

// Categories actually used by non-featured items → filter bar (needs ≥2 to be useful)
$mfs_press_cats = array_values( array_unique( array_filter( array_map(
    fn($i) => $i['category'],
    $mfs_regular_items
) ) ) );
$mfs_show_filters = count( $mfs_press_cats ) >= 2;

$mfs_press_date = function ( $item ) {
    if ( empty( $item['date'] ) ) {
        return '';
    }
    return '<time class="press__date" datetime="' . esc_attr( $item['date'] ) . '">'
        . esc_html( date_i18n( 'F j, Y', strtotime( $item['date'] ) ) )
        . '</time>';
};

$mfs_press_logo = function ( $item, $height_class = '' ) {
    $alt = ! empty( $item['logo']['alt'] )
        ? $item['logo']['alt']
        : $item['name'] . ' — Maverick Frame Studio feature';
    if ( ! empty( $item['logo']['url'] ) ) {
        echo '<span class="press__logo' . esc_attr( $height_class ) . '">';
        echo '<img src="' . esc_url( $item['logo']['url'] ) . '" alt="' . esc_attr( $alt ) . '"';
        if ( ! empty( $item['logo']['width'] ) ) {
            echo ' width="' . (int) $item['logo']['width'] . '" height="' . (int) $item['logo']['height'] . '"';
        }
        echo ' loading="lazy" decoding="async"></span>';
    } else {
        echo '<span class="press__logo press__logo--text' . esc_attr( $height_class ) . '">' . esc_html( $item['name'] ) . '</span>';
    }
};
?>

<?php get_header(); ?>
<?php echo get_template_part('components/common/header', null, [
    'class' => 'header_white'
]); ?>

<main class="main press-page">
    <section class="press">
        <div class="container">
            <nav class="press__breadcrumbs" aria-label="Breadcrumb">
                <a href="<?php echo esc_url( home_url('/') ); ?>"><?php echo esc_html( mfs_t('Home', 'Inicio', 'Startseite') ); ?></a>
                <span class="press__breadcrumbs-sep" aria-hidden="true">/</span>
                <span class="press__breadcrumbs-current"><?php the_title(); ?></span>
            </nav>

            <header class="press__head">
                <h1 class="press__title"><?php the_title(); ?></h1>
                <p class="press__intro"><?php echo esc_html( $mfs_press_intro ); ?></p>
            </header>

            <?php foreach ( $mfs_featured_items as $item ) : ?>
                <article class="press-feature">
                    <p class="press-feature__eyebrow">
                        <span class="press-feature__eyebrow-label"><?php echo esc_html( mfs_t('Featured', 'Destacado') ); ?></span>
                        <?php if ( $item['category'] ) : ?>
                            <span class="press-feature__eyebrow-sep" aria-hidden="true">·</span>
                            <?php echo esc_html( $item['category'] ); ?>
                        <?php endif; ?>
                        <?php if ( $item['date'] ) : ?>
                            <span class="press-feature__eyebrow-sep" aria-hidden="true">·</span>
                            <?php echo $mfs_press_date( $item ); ?>
                        <?php endif; ?>
                    </p>

                    <?php if ( $item['excerpt'] ) : ?>
                        <blockquote class="press-feature__quote">
                            <p>&ldquo;<?php echo esc_html( $item['excerpt'] ); ?>&rdquo;</p>
                        </blockquote>
                    <?php endif; ?>

                    <div class="press-feature__source">
                        <?php $mfs_press_logo( $item, ' press__logo--feature' ); ?>
                        <span class="press-feature__name"><?php echo esc_html( $item['name'] ); ?></span>
                    </div>

                    <p class="press-feature__link-row">
                        <a class="press-feature__link" href="<?php echo esc_url( $item['url'] ); ?>" target="_blank" rel="noopener nofollow">
                            <?php echo esc_html( $item['title'] ); ?>
                            <?php echo inline_svg('icons/arrow-right-accent.svg'); ?>
                        </a>
                    </p>
                </article>
            <?php endforeach; ?>

            <?php if ( $mfs_show_filters ) : ?>
                <div class="press-filters" role="group" aria-label="<?php echo esc_attr( mfs_t('Filter mentions by category', 'Filtrar menciones por categoría') ); ?>">
                    <button class="press-filters__btn is-active" type="button" data-filter="all" aria-pressed="true">
                        <?php echo esc_html( mfs_t('All', 'Todos', 'Alle') ); ?>
                    </button>
                    <?php foreach ( $mfs_press_cats as $cat ) : ?>
                        <button class="press-filters__btn" type="button" data-filter="<?php echo esc_attr( $cat ); ?>" aria-pressed="false">
                            <?php echo esc_html( $cat ); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ( $mfs_regular_items ) : ?>
                <ul class="press__grid<?php echo $mfs_show_filters ? ' press__grid--filtered' : ''; ?>">
                    <?php foreach ( $mfs_regular_items as $item ) : ?>
                        <li class="press-card" data-category="<?php echo esc_attr( (string) $item['category'] ); ?>">
                            <article class="press-card__inner">
                                <?php if ( ! empty( $item['preview']['url'] ) ) :
                                    $preview_alt = ! empty( $item['preview']['alt'] )
                                        ? $item['preview']['alt']
                                        : $item['title'] . ' — ' . $item['name'];
                                    $preview_src = ! empty( $item['preview']['sizes']['large'] ) ? $item['preview']['sizes']['large'] : $item['preview']['url'];
                                ?>
                                    <span class="press-card__preview">
                                        <img
                                            src="<?php echo esc_url( $preview_src ); ?>"
                                            alt="<?php echo esc_attr( $preview_alt ); ?>"
                                            loading="lazy"
                                            decoding="async"
                                        >
                                    </span>
                                <?php endif; ?>

                                <div class="press-card__body">
                                    <div class="press-card__top">
                                        <?php $mfs_press_logo( $item ); ?>

                                        <?php if ( $item['category'] ) : ?>
                                            <span class="press-card__tag"><?php echo esc_html( $item['category'] ); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="press-card__meta">
                                        <span class="press-card__name"><?php echo esc_html( $item['name'] ); ?></span>
                                        <?php echo $mfs_press_date( $item ); ?>
                                    </div>

                                    <h2 class="press-card__title">
                                        <a class="press-card__link" href="<?php echo esc_url( $item['url'] ); ?>" target="_blank" rel="noopener nofollow">
                                            <?php echo esc_html( $item['title'] ); ?>
                                        </a>
                                    </h2>

                                    <?php if ( $item['excerpt'] ) : ?>
                                        <p class="press-card__excerpt"><?php echo esc_html( $item['excerpt'] ); ?></p>
                                    <?php endif; ?>

                                    <span class="press-card__arrow" aria-hidden="true">
                                        <?php echo esc_html( mfs_t('Read the feature', 'Leer el artículo', 'Beitrag lesen') ); ?>
                                        <?php echo inline_svg('icons/arrow-right-accent.svg'); ?>
                                    </span>
                                </div>
                            </article>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="press-more" hidden>
                    <button class="press-more__btn js-press-more" type="button">
                        <?php echo esc_html( mfs_t('Show more', 'Mostrar más', 'Mehr anzeigen') ); ?>
                    </button>
                </div>

                <script>
                /* Press grid: client-side category filter + batched reveal.
                   All cards (and their dofollow links) stay in the initial HTML for
                   crawlers; hidden cards don't fetch their lazy images. */
                (function () {
                    var grid = document.querySelector('.press__grid');
                    if (!grid) return;
                    var items = [].slice.call(grid.children);
                    var btns = [].slice.call(document.querySelectorAll('.press-filters__btn'));
                    var moreWrap = document.querySelector('.press-more');
                    var moreBtn = document.querySelector('.js-press-more');
                    var BATCH = 9, cat = 'all', limit = BATCH;

                    function apply() {
                        var shown = 0, left = 0;
                        items.forEach(function (li) {
                            var match = cat === 'all' || li.getAttribute('data-category') === cat;
                            if (match && shown < limit) {
                                li.hidden = false;
                                shown++;
                            } else {
                                li.hidden = true;
                                if (match) left++;
                            }
                        });
                        if (moreWrap) moreWrap.hidden = left === 0;
                    }

                    btns.forEach(function (b) {
                        b.addEventListener('click', function () {
                            cat = b.getAttribute('data-filter');
                            limit = BATCH;
                            btns.forEach(function (x) {
                                x.classList.toggle('is-active', x === b);
                                x.setAttribute('aria-pressed', x === b ? 'true' : 'false');
                            });
                            apply();
                        });
                    });

                    if (moreBtn) {
                        moreBtn.addEventListener('click', function () {
                            limit += BATCH;
                            apply();
                        });
                    }

                    apply();
                })();
                </script>
            <?php endif; ?>
        </div>
    </section>

    <section class="press-cta">
        <div class="container">
            <div class="press-cta__inner">
                <h2 class="press-cta__title"><?php echo esc_html( mfs_t(
                    'Writing about architecture, design, or real estate?',
                    '¿Escribes sobre arquitectura, diseño o inmobiliaria?'
                ) ); ?></h2>
                <p class="press-cta__text"><?php echo esc_html( mfs_t(
                    'If you run a media outlet or a blog and would like to feature our work or get expert commentary on architectural visualization, we\'d love to hear from you.',
                    'Si tienes un medio o un blog y quieres destacar nuestro trabajo u obtener comentarios expertos sobre visualización arquitectónica, escríbenos.'
                ) ); ?></p>
                <a class="btn-main press-cta__btn" href="mailto:deals@maverickframe.com">deals@maverickframe.com</a>
            </div>
        </div>
    </section>
</main>

<?php echo get_template_part('components/common/footer'); ?>
<?php get_footer(); ?>
