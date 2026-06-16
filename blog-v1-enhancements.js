/*
 * blog-v1-enhancements.js
 * ---------------------------------------------------------
 * Adds to single-blog pages:
 *   1) Top reading progress bar (4px, blue) tied to article scroll
 *   2) TOC scroll-spy — highlights the currently-visible section
 *   3) Image lightbox — click any article image → full-screen overlay
 *
 * Each feature is self-contained; if its target isn't found,
 * the feature silently no-ops.
 */
(function () {
    'use strict';

    // Language flag: true on /es/ pages (html lang="es" / "es-ES").
    var MFS_ES = document.documentElement.lang.toLowerCase().indexOf('es') === 0;

    function init() {
        initProgressBar();
        initTocSpy();
        initLightbox();
        initReadingStatus();
        initFeedback();

    /* ---------------------------------------------------------
       Auto-scroll active TOC item into view inside the sidebar
       --------------------------------------------------------- */
    function initTocAutoScroll() {
        var tocRoot = document.querySelector(
            '.article-page__aside--left .toc, .article-page__aside--left .article-page__sticky, .article-page__aside--left'
        );
        if (!tocRoot) return;

        // Find the actual scroll container (the element with overflow-y)
        var scroller = tocRoot;
        var cur = tocRoot;
        for (var i = 0; i < 6 && cur; i++) {
            var cs = window.getComputedStyle(cur);
            if (cs.overflowY === 'auto' || cs.overflowY === 'scroll') {
                scroller = cur;
                break;
            }
            cur = cur.parentElement;
        }

        function scrollActiveIntoView() {
            var active = tocRoot.querySelector(
                'a.is-active, a.active, li.is-active > a, li.active > a, .is-active'
            );
            if (!active) return;
            var sRect = scroller.getBoundingClientRect();
            var aRect = active.getBoundingClientRect();
            var pad = 24;
            if (aRect.top < sRect.top + pad) {
                scroller.scrollBy({ top: aRect.top - sRect.top - pad, behavior: 'smooth' });
            } else if (aRect.bottom > sRect.bottom - pad) {
                scroller.scrollBy({ top: aRect.bottom - sRect.bottom + pad, behavior: 'smooth' });
            }
        }

        // Watch for class changes on any descendant
        var mo = new MutationObserver(function (mutations) {
            for (var i = 0; i < mutations.length; i++) {
                if (mutations[i].type === 'attributes' && mutations[i].attributeName === 'class') {
                    scrollActiveIntoView();
                    return;
                }
            }
        });
        mo.observe(tocRoot, { attributes: true, subtree: true, attributeFilter: ['class'] });

        // Initial sync + on scroll throttled
        scrollActiveIntoView();
        var t;
        window.addEventListener('scroll', function () {
            clearTimeout(t);
            t = setTimeout(scrollActiveIntoView, 120);
        }, { passive: true });
    }
    initTocAutoScroll();

    /* ---------------------------------------------------------
       Sidebar CTA rotator: swap stage based on scroll progress.
       Reads .article-page-inner geometry and divides into 4 quarters.
       --------------------------------------------------------- */
    function initSidebarRotator() {
        var rotator = document.querySelector('[data-cta-rotator]');
        if (!rotator) return;
        var stages = rotator.querySelectorAll('.sidebar-cta__stage');
        if (stages.length < 2) return;

        var article = document.querySelector('.article-page-inner')
            || document.querySelector('.article-page__content');
        if (!article) return;

        function update() {
            var rect = article.getBoundingClientRect();
            var vh = window.innerHeight || 1;
            // 0 when article top hits viewport top; 1 when article bottom hits viewport bottom
            var scrolled = -rect.top;
            var max = rect.height - vh;
            if (max < 1) max = 1;
            var p = scrolled / max;
            if (p < 0) p = 0;
            if (p > 1) p = 1;

            var idx;
            if (p < 0.25)      idx = 1;
            else if (p < 0.5)  idx = 2;
            else if (p < 0.75) idx = 3;
            else               idx = 4;

            stages.forEach(function (s) {
                if (parseInt(s.getAttribute('data-stage'), 10) === idx) {
                    s.classList.add('is-active');
                } else {
                    s.classList.remove('is-active');
                }
            });
        }

        var t;
        window.addEventListener('scroll', function () {
            clearTimeout(t);
            t = setTimeout(update, 60);
        }, { passive: true });
        window.addEventListener('resize', update);
        update();
    }
    initSidebarRotator();

    /* ---------------------------------------------------------
       5 in-article CTAs: visually different, funnel-staged copy.
       Inserted at evenly-spaced H2 section breaks.
       --------------------------------------------------------- */
    function initInArticleCtas() {
        var content = document.querySelector('.article-page__content');
        if (!content) return;
        var h2s = Array.prototype.slice.call(content.querySelectorAll('h2'));
        if (h2s.length < 3) return;

        // Copy source: ACF-driven data (window.mfsBlogCtas, set via
        // wp_localize_script from per-post override → global Site Options)
        // falls back to the on-brand baked defaults below when empty.
        var defaults = MFS_ES ? [
            {
                eyebrow: '¿NUEVO AQUÍ?',
                head: 'Mira nuestros últimos trabajos de renderizado 3D fotorrealista.',
                label: 'Ver nuestro portfolio',
                url: '/es/galeria/'
            },
            {
                eyebrow: 'RECURSO',
                head: 'La plantilla de brief de una página que usamos para planificar proyectos CGI.',
                label: 'Descargar el PDF',
                url: '/contacts/'
            },
            {
                eyebrow: 'CASO DE ÉXITO',
                head: 'Cómo nuestros visuales ayudaron a una marca a vender más rápido: mira las cifras.',
                label: 'Leer el caso',
                url: '/es/casos-de-exito/'
            },
            {
                eyebrow: 'COMPARAR',
                head: 'CGI vs fotografía: coste, velocidad y flexibilidad lado a lado.',
                label: 'Ver la comparación',
                url: '/es/blog/'
            },
            {
                eyebrow: 'HABLEMOS',
                head: '¿Trabajas en un proyecto como este? Veámoslo juntos.',
                label: 'Reserva una llamada de 15 min',
                url: '#book',
                modal: true
            }
        ] : [
            {
                eyebrow: 'NEW HERE?',
                head: 'See our latest photoreal 3D rendering work.',
                label: 'Browse our portfolio',
                url: '/gallery/'
            },
            {
                eyebrow: 'RESOURCE',
                head: 'The one-page brief template we use to scope CGI projects.',
                label: 'Download the PDF',
                url: '/contacts/'
            },
            {
                eyebrow: 'CASE STUDY',
                head: 'How our visuals helped a brand sell faster — see the numbers.',
                label: 'Read the case',
                url: '/success-stories/'
            },
            {
                eyebrow: 'COMPARE',
                head: 'CGI vs photography — cost, speed, and flexibility side by side.',
                label: 'See the comparison',
                url: '/blog/'
            },
            {
                eyebrow: 'TALK TO US',
                head: 'Working on a project like this? Let\'s look at it together.',
                label: 'Book a 15-min call',
                url: '#book',
                modal: true
            }
        ];

        var source = (window.mfsBlogCtas && window.mfsBlogCtas.length) ? window.mfsBlogCtas : defaults;
        var ctas = source.map(function (c, i) {
            return {
                variant: 'v' + (i + 1),
                eyebrow: c.eyebrow || '',
                head: c.head || '',
                label: c.label || '',
                url: c.url || '#book',
                modal: !!c.modal || c.url === '#book'
            };
        });

        var n = Math.min(ctas.length, Math.max(1, h2s.length - 1));
        var step = Math.max(1, Math.floor(h2s.length / (n + 1)));

        for (var i = 0; i < n; i++) {
            var pos = Math.min((i + 1) * step, h2s.length - 1);
            // Skip the very first H2 — never put a CTA before the intro section
            if (pos === 0) pos = 1;
            var h2 = h2s[pos];
            if (!h2) continue;
            var cta = ctas[i];
            var aside = document.createElement('aside');
            aside.className = 'in-cta in-cta--' + cta.variant;
            aside.setAttribute('data-cta-stage', String(i + 1));
            aside.innerHTML =
                '<span class="in-cta__eyebrow">' + cta.eyebrow + '</span>' +
                '<p class="in-cta__head">' + cta.head + '</p>' +
                '<a class="in-cta__link' + (cta.modal ? ' js-modal-open' : '') + '" href="' + cta.url + '"' +
                (cta.modal ? ' data-modal="book"' : '') + '>' +
                '<span class="in-cta__label">' + cta.label + '</span>' +
                '<span aria-hidden="true" class="in-cta__arrow">\u2192</span>' +
                '</a>';
            h2.parentNode.insertBefore(aside, h2);
        }
    }
    initInArticleCtas();

    /* ---------------------------------------------------------
       Micro-animations: fade-up CTAs into view; per-variant
       accents kick in via CSS on .is-in-view.
       --------------------------------------------------------- */
    function initInArticleCtaAnimations() {
        var ctas = document.querySelectorAll('.in-cta');
        if (!ctas.length) return;
        if (!('IntersectionObserver' in window)) {
            Array.prototype.forEach.call(ctas, function (c) { c.classList.add('is-in-view'); });
            return;
        }
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) {
                    e.target.classList.add('is-in-view');
                    io.unobserve(e.target);
                }
            });
        }, { rootMargin: '0px 0px -12% 0px', threshold: 0.12 });
        Array.prototype.forEach.call(ctas, function (c) { io.observe(c); });
    }
    initInArticleCtaAnimations();
        initTableCleaner();
    }

    /* ---------- 1. Reading progress bar ---------- */
    function initProgressBar() {
        var bar = document.createElement('div');
        bar.className = 'blog-progress-bar';
        bar.innerHTML = '<div class="blog-progress-bar__fill"></div>';
        document.body.insertBefore(bar, document.body.firstChild);
        var fill = bar.querySelector('.blog-progress-bar__fill');

        // Track scroll progress within the article body.
        var article = document.querySelector('.article-page__content')
                   || document.querySelector('.article-page__main')
                   || document.body;

        function update() {
            var rect = article.getBoundingClientRect();
            var scrolled = Math.max(0, -rect.top);
            var travel = rect.height - window.innerHeight;
            var ratio = travel > 0 ? Math.min(1, scrolled / travel) : 0;
            fill.style.transform = 'scaleX(' + ratio + ')';
        }
        update();
        window.addEventListener('scroll', update, { passive: true });
        window.addEventListener('resize', update);
    }

    /* ---------- 2. TOC scroll-spy ---------- */
    function initTocSpy() {
        var aside = document.querySelector('.article-page__aside--left');
        if (!aside) return;
        var tocLinks = aside.querySelectorAll('a[href^="#"]');
        if (!tocLinks.length) return;

        // Build pairs of { link, target } where the link's #anchor points to a heading.
        var pairs = [];
        Array.prototype.forEach.call(tocLinks, function (link) {
            var id = (link.getAttribute('href') || '').replace(/^#/, '');
            if (!id) return;
            var target = document.getElementById(id);
            if (target) pairs.push({ link: link, target: target });
        });
        if (!pairs.length) return;

        function update() {
            // The "active" section is the LAST heading whose top has crossed
            // a trigger line at ~25% from the viewport top.
            var trigger = window.innerHeight * 0.25;
            var activeIdx = -1;
            for (var i = 0; i < pairs.length; i++) {
                if (pairs[i].target.getBoundingClientRect().top <= trigger) {
                    activeIdx = i;
                } else {
                    break;
                }
            }
            if (activeIdx < 0) activeIdx = 0;
            pairs.forEach(function (p, i) {
                if (i === activeIdx) p.link.classList.add('is-active');
                else p.link.classList.remove('is-active');
            });
        }
        update();
        window.addEventListener('scroll', update, { passive: true });
        window.addEventListener('resize', update);
    }

    /* ---------- 3. Image lightbox ---------- */
    function initLightbox() {
        var content = document.querySelector('.article-page__content');
        if (!content) return;
        var imgs = content.querySelectorAll('img');

        Array.prototype.forEach.call(imgs, function (img) {
            img.addEventListener('click', function (e) {
                e.preventDefault();
                var src = img.dataset.fullSrc || img.src;
                // Caption: <figcaption> if image is in a <figure>, otherwise alt
                var fig = img.closest('figure');
                var capText = '';
                if (fig) {
                    var cap = fig.querySelector('figcaption');
                    if (cap) capText = cap.textContent.trim();
                }
                if (!capText) capText = (img.getAttribute('alt') || '').trim();
                openLightbox(src, capText);
            });
        });

        function openLightbox(src, caption) {
            var overlay = document.createElement('div');
            overlay.className = 'blog-lightbox';
            overlay.innerHTML =
                '<button type="button" class="blog-lightbox__close" aria-label="' + (MFS_ES ? 'Cerrar' : 'Close') + '">×</button>' +
                '<img class="blog-lightbox__img" src="' + escapeHtml(src) + '" alt="">' +
                (caption ? '<div class="blog-lightbox__caption">' + escapeHtml(caption) + '</div>' : '');
            document.body.appendChild(overlay);
            document.body.style.overflow = 'hidden';

            function close() {
                overlay.remove();
                document.body.style.overflow = '';
                document.removeEventListener('keydown', onKey);
            }
            function onKey(e) { if (e.key === 'Escape') close(); }
            overlay.addEventListener('click', function (e) {
                // Click on backdrop or close button — not on the image itself.
                if (e.target === overlay || e.target.classList.contains('blog-lightbox__close')) close();
            });
            document.addEventListener('keydown', onKey);
        }

        function escapeHtml(s) {
            return String(s)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }
    }

    /* ---------- 4. Reading status (remaining time + section) ---------- */
    function initReadingStatus() {
        var holder = document.querySelector('.reading-status');
        var timeEl = document.querySelector('[data-reading-remaining]');
        var sectionEl = document.querySelector('[data-reading-section]');
        if (!holder || !timeEl) return;

        var totalMin = parseInt(holder.getAttribute('data-read-time'), 10) || 5;
        var article = document.querySelector('.article-page__content')
                   || document.querySelector('.article-page__main');
        if (!article) return;
        var headings = article.querySelectorAll('h2, h3');
        var totalSections = headings.length;

        function update() {
            var rect = article.getBoundingClientRect();
            var scrolled = Math.max(0, -rect.top);
            var travel = rect.height - window.innerHeight;
            var ratio = travel > 0 ? Math.min(1, scrolled / travel) : 0;

            // Time remaining (round up so we never show "0 min")
            var remaining = Math.max(0, Math.ceil(totalMin * (1 - ratio)));
            if (remaining === 0) {
                timeEl.textContent = MFS_ES ? 'Terminado' : 'Finished';
            } else {
                timeEl.textContent = remaining + (MFS_ES ? ' min restantes' : ' min remaining');
            }

            // Current section (which heading just crossed the trigger line)
            if (sectionEl && totalSections > 0) {
                var trigger = window.innerHeight * 0.25;
                var idx = 0;
                for (var i = 0; i < headings.length; i++) {
                    if (headings[i].getBoundingClientRect().top <= trigger) {
                        idx = i + 1;
                    } else {
                        break;
                    }
                }
                if (idx === 0) {
                    sectionEl.textContent = MFS_ES ? 'Inicio' : 'Start';
                } else {
                    sectionEl.textContent = MFS_ES
                        ? ('Sección ' + idx + ' de ' + totalSections)
                        : ('Section ' + idx + ' of ' + totalSections);
                }
            }
        }
        update();
        window.addEventListener('scroll', update, { passive: true });
        window.addEventListener('resize', update);
    }

    /* ---------- 5. Feedback widget ---------- */
    function initFeedback() {
        var widget = document.querySelector('[data-feedback]');
        if (!widget) return;
        var prompt = widget.querySelector('[data-feedback-prompt]');
        var thanks = widget.querySelector('[data-feedback-thanks]');
        var postId = widget.getAttribute('data-post-id') || 'x';
        var storageKey = 'mfs-blog-feedback-' + postId;

        // If the reader already voted, show the thanks state immediately.
        try {
            if (window.localStorage && localStorage.getItem(storageKey)) {
                if (prompt) prompt.hidden = true;
                if (thanks) thanks.hidden = false;
                return;
            }
        } catch (e) { /* ignore */ }

        var buttons = widget.querySelectorAll('[data-feedback-vote]');
        Array.prototype.forEach.call(buttons, function (btn) {
            btn.addEventListener('click', function () {
                var vote = btn.getAttribute('data-feedback-vote');
                try {
                    if (window.localStorage) localStorage.setItem(storageKey, vote);
                } catch (e) { /* ignore */ }
                if (prompt) prompt.hidden = true;
                if (thanks) thanks.hidden = false;
            });
        });
    }

        /* ---------- 6. Table cleaner: strip theme inline-style borders ---------- */
    function initTableCleaner() {
        var tables = document.querySelectorAll('.article-page__content table');
        Array.prototype.forEach.call(tables, function (table) {
            var nodes = table.querySelectorAll('td, th, tr, thead, tbody, tfoot, col, colgroup');
            Array.prototype.forEach.call(nodes, function (n) {
                n.removeAttribute('style');
                n.removeAttribute('bgcolor');
                n.removeAttribute('border');
                n.removeAttribute('cellpadding');
                n.removeAttribute('cellspacing');
            });
            table.removeAttribute('style');
            table.removeAttribute('bgcolor');
            table.removeAttribute('border');
            table.removeAttribute('cellpadding');
            table.removeAttribute('cellspacing');
            table.classList.add('mfs-clean-table');
            var fig = table.closest('figure');
            if (fig) fig.removeAttribute('style');
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
