<?php
/* 404 — "scene failed to render" (new design).
   Styles: src/scss/new/pages/error-404.scss (bundle: src/scss/bundles/error.scss).
   Interactive: wireframe cube tilts toward the cursor, render counter climbs to 99%
   and stalls — inline script below, no dependency on the main JS bundle. */
?>
<?php get_header(); ?>

<?php echo get_template_part('components/common/header', null, [
    'class' => 'header_white'
]); ?>

<main class="main error-page">
    <section class="error404">
        <div class="container">

            <div class="error404__viewport" id="error404-viewport">
                <span class="error404__corner" aria-hidden="true"></span>
                <span class="error404__corner" aria-hidden="true"></span>
                <span class="error404__corner" aria-hidden="true"></span>
                <span class="error404__corner" aria-hidden="true"></span>

                <div class="error404__hud error404__hud--tl" aria-hidden="true">
                    <span class="error404__hud-dot">&#9679;</span>
                    <span>Perspective&nbsp;/&nbsp;Cam_404</span>
                </div>
                <div class="error404__hud error404__hud--tr" aria-hidden="true">
                    <span>Samples&nbsp;<span id="error404-samples">0</span>&nbsp;/&nbsp;&infin;</span>
                </div>
                <div class="error404__hud error404__hud--bl" aria-hidden="true">
                    <span>Verts&nbsp;0&nbsp;&middot;&nbsp;Faces&nbsp;0&nbsp;&middot;&nbsp;Tris&nbsp;0</span>
                </div>
                <div class="error404__hud error404__hud--br" aria-hidden="true">
                    <span>page_not_found.blend</span>
                </div>

                <div class="error404__scene" aria-hidden="true">
                    <div class="error404__digit">4</div>
                    <div class="error404__stage">
                        <div class="error404__tilt" id="error404-tilt">
                            <div class="error404__cube">
                                <span class="error404__face"></span>
                                <span class="error404__face"></span>
                                <span class="error404__face"></span>
                                <span class="error404__face"></span>
                                <span class="error404__face"></span>
                                <span class="error404__face"></span>
                            </div>
                        </div>
                        <span class="error404__bbox"></span>
                        <span class="error404__shadow"></span>
                    </div>
                    <div class="error404__digit">4</div>
                </div>

                <div class="error404__progress" aria-hidden="true">
                    <div class="error404__progress-row">
                        <span><?php echo esc_html( mfs_t('Rendering page', 'Renderizando la página', 'Seite wird gerendert') ); ?>&hellip;</span>
                        <span><span id="error404-percent">0</span>%</span>
                    </div>
                    <div class="error404__progress-track">
                        <div class="error404__progress-fill" id="error404-fill"></div>
                    </div>
                    <p class="error404__progress-note">
                        <span id="error404-path"></span> &mdash; <?php echo esc_html( mfs_t('0 samples found', '0 muestras encontradas', '0 Samples gefunden') ); ?>
                    </p>
                </div>
            </div>

            <div class="error404__body">
                <h1 class="error404__title"><?php echo esc_html( mfs_t(
                    "This page didn't survive the render",
                    'Esta página no sobrevivió al render',
                    'Diese Seite hat das Rendering nicht überlebt'
                ) ); ?></h1>

                <p class="error404__text"><?php echo esc_html( mfs_t(
                    'It may have been moved, deleted, or never modeled in the first place. Everything else made the final cut — pick a scene below.',
                    'Puede que la hayan movido, eliminado o que nunca llegara a modelarse. Todo lo demás pasó el corte final: elige una escena.',
                    'Sie wurde verschoben, gelöscht oder nie modelliert. Alles andere hat den Final Cut geschafft – wählen Sie eine Szene.'
                ) ); ?></p>

                <div class="error404__actions">
                    <a class="btn-main" href="<?php echo esc_url( home_url('/') ); ?>">
                        <?php echo esc_html( mfs_t('Back to homepage', 'Volver al inicio', 'Zur Startseite') ); ?>
                    </a>
                    <a class="error404__btn-outline" href="<?php echo esc_url( home_url('/success-stories/') ); ?>">
                        <?php echo esc_html( mfs_t('See our work', 'Ver nuestros proyectos', 'Unsere Arbeiten ansehen') ); ?>
                    </a>
                </div>

                <nav class="error404__links" aria-label="<?php echo esc_attr( mfs_t('Popular pages', 'Páginas populares', 'Beliebte Seiten') ); ?>">
                    <a class="error404__link" href="<?php echo esc_url( home_url('/gallery/') ); ?>"><?php echo esc_html( mfs_t('Gallery', 'Galería', 'Galerie') ); ?></a>
                    <span class="error404__link-sep" aria-hidden="true">/</span>
                    <a class="error404__link" href="<?php echo esc_url( home_url('/blog/') ); ?>"><?php echo esc_html( mfs_t('Blog', 'Blog', 'Blog') ); ?></a>
                    <span class="error404__link-sep" aria-hidden="true">/</span>
                    <a class="error404__link" href="<?php echo esc_url( home_url('/team/') ); ?>"><?php echo esc_html( mfs_t('Team', 'Equipo', 'Team') ); ?></a>
                    <span class="error404__link-sep" aria-hidden="true">/</span>
                    <a class="error404__link" href="<?php echo esc_url( home_url('/contacts/') ); ?>"><?php echo esc_html( mfs_t('Contacts', 'Contacto', 'Kontakt') ); ?></a>
                </nav>
            </div>

        </div>
    </section>
</main>

<script>
(function () {
    var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // requested path in the progress note
    var pathEl = document.getElementById('error404-path');
    if (pathEl) pathEl.textContent = window.location.pathname;

    // render counter climbs fast, stalls at 99%
    var percentEl = document.getElementById('error404-percent');
    var fillEl = document.getElementById('error404-fill');
    var samplesEl = document.getElementById('error404-samples');

    function setProgress(p) {
        if (percentEl) percentEl.textContent = p;
        if (fillEl) fillEl.style.width = p + '%';
        if (samplesEl) samplesEl.textContent = (p * 4.04).toFixed(0);
    }

    if (reduced) {
        setProgress(99);
    } else {
        var start = null;
        function tick(ts) {
            if (start === null) start = ts;
            var t = Math.min((ts - start) / 2200, 1); // ~2.2s
            var eased = 1 - Math.pow(1 - t, 3);
            setProgress(Math.min(99, Math.round(eased * 99)));
            if (t < 1) requestAnimationFrame(tick);
        }
        requestAnimationFrame(tick);
    }

    // cube tilts toward the cursor (pointer devices only)
    var tilt = document.getElementById('error404-tilt');
    var viewport = document.getElementById('error404-viewport');
    if (tilt && viewport && !reduced && window.matchMedia('(hover: hover)').matches) {
        viewport.addEventListener('mousemove', function (e) {
            var r = viewport.getBoundingClientRect();
            var x = (e.clientX - r.left) / r.width - 0.5;  // -0.5 .. 0.5
            var y = (e.clientY - r.top) / r.height - 0.5;
            tilt.style.transform = 'rotateY(' + (x * 36) + 'deg) rotateX(' + (-y * 28) + 'deg)';
        });
        viewport.addEventListener('mouseleave', function () {
            tilt.style.transform = '';
        });
    }
})();
</script>

<?php echo get_template_part('components/common/footer'); ?>
<?php get_footer(); ?>
