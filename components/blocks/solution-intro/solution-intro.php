<?php
/**
 * Solution Intro block — text column + video/media column (Solutions page).
 * Layout-first build (content hardcoded; ACF fields + video play to come later).
 */
$si_eyebrow = get_field('eyebrow') ?: mfs_t('Most agencies hit capacity before they hit opportunity', 'La mayoría de las agencias llega al límite de capacidad antes que al de oportunidades', 'Die meisten Agenturen stoßen an Kapazitätsgrenzen, bevor sie Chancen nutzen können');
$si_title   = get_field('title') ?: mfs_t('<mark>Scale creative production</mark> services without expanding internal teams', '<mark>Escala tu producción creativa</mark> sin ampliar el equipo interno', '<mark>Kreativproduktion skalieren</mark> – ohne interne Teams zu vergrößern');
$si_desc    = get_field('description') ?: mfs_t('<p>Maverick Frame operates as a production partner for marketing agencies that need reliable execution across multiple campaigns.</p><p>Our team provides content production services, high-end CGI, and scalable visual production while your agency focuses on strategy, client relationships, and campaign management. Agencies work with us when they need to outsource design and production without compromising quality or delivery speed.</p>', '<p>Maverick Frame funciona como partner de producción para agencias de marketing que necesitan una ejecución fiable en múltiples campañas.</p><p>Nuestro equipo aporta servicios de producción de contenido, CGI de alta gama y producción visual escalable mientras tu agencia se centra en la estrategia, la relación con los clientes y la gestión de campañas. Las agencias trabajan con nosotros cuando necesitan externalizar diseño y producción sin sacrificar calidad ni plazos de entrega.</p>', '<p>Maverick Frame agiert als Produktionspartner für Marketing-Agenturen, die eine verlässliche Umsetzung über mehrere Kampagnen hinweg brauchen.</p><p>Unser Team liefert Content-Produktion, High-End-CGI und skalierbare visuelle Produktion, während sich Ihre Agentur auf Strategie, Kundenbeziehungen und Kampagnenmanagement konzentriert. Agenturen arbeiten mit uns, wenn sie Design und Produktion auslagern möchten, ohne Qualität oder Liefergeschwindigkeit zu gefährden.</p>');
$si_btn     = get_field('button_text') ?: mfs_t('Book a call', 'Reservar una llamada', 'Beratung buchen');
$si_poster  = get_field('poster') ?: 16195;
?>
<section class="solution-intro">
    <div class="container container_small">
        <div class="solution-intro__grid">
            <div class="solution-intro__text">
                <span class="solution-intro__eyebrow">
                    <span class="solution-intro__dot" aria-hidden="true"></span>
                    <?php echo esc_html($si_eyebrow); ?>
                </span>

                <h2 class="solution-intro__title"><?php echo wp_kses_post($si_title); ?></h2>

                <div class="solution-intro__desc"><?php echo wp_kses_post($si_desc); ?></div>

                <a href="#" class="solution-intro__btn js-modal-open" data-modal="book"><?php echo esc_html($si_btn); ?></a>
            </div>

            <div class="solution-intro__media">
                <?php
                // Same Bunny Stream showreel as the homepage block. Editable later via an ACF `video` field.
                $si_video = get_field('video');
                if ($si_video) {
                    // Same guard as showreel.php: pasted embeds come without a title
                    // attribute, which PSI flags (iframe-title a11y audit).
                    if (stripos($si_video, '<iframe') !== false && stripos($si_video, 'title=') === false) {
                        $si_video = preg_replace('/<iframe\b/i', '<iframe title="Maverick Frame showreel"', $si_video, 1);
                    }
                    echo $si_video;
                } else {
                ?>
                    <iframe
                        class="solution-intro__video"
                        src="https://player.mediadelivery.net/embed/655216/e3ddaf1f-eb40-4f5f-8b7d-5c529bf12265?autoplay=false&loop=true&muted=false&preload=true&responsive=true"
                        loading="lazy"
                        allow="accelerometer;gyroscope;autoplay;encrypted-media;picture-in-picture;"
                        allowfullscreen="true"
                        title="Maverick Frame showreel"></iframe>
                <?php } ?>
            </div>
        </div>
    </div>
</section>
