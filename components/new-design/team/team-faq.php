<?php
    // Team/studio FAQ for the Team page.
    // Primary source: the ACF `faq_items` repeater on the page (editable in admin).
    // Fallback: built-in bilingual defaults via mfs_t() so the section is never empty.
    $team_faq_defaults = array(
        array(
            mfs_t('How experienced is the Maverick Frame team?', '¿Cuánta experiencia tiene el equipo de Maverick Frame?', 'Wie erfahren ist das Maverick-Frame-Team?'),
            mfs_t(
                'Every member of our team has 7+ years of experience in CGI and 3D visualization, delivering projects for brands, developers and agencies worldwide — from off-plan towers to luxury product launches.',
                'Cada miembro de nuestro equipo cuenta con más de 7 años de experiencia en CGI y visualización 3D, entregando proyectos para marcas, promotoras y agencias de todo el mundo, desde torres sobre plano hasta lanzamientos de productos de lujo.', 'Jedes Mitglied unseres Teams hat über 7 Jahre Erfahrung in CGI und 3D-Visualisierung und liefert Projekte für Marken, Entwickler und Agenturen weltweit — von Off-Plan-Türmen bis zu Luxus-Produktlaunches.'
            ),
        ),
        array(
            mfs_t('Do you work with an in-house team or freelancers?', '¿Trabajáis con equipo propio o con freelancers?', 'Arbeitet ihr mit einem internen Team oder mit Freelancern?'),
            mfs_t(
                'Maverick Frame is a dedicated in-house team of senior 3D artists, designers, architects and project managers. You work with the same people from brief to final delivery — not a rotating pool of freelancers.',
                'Maverick Frame es un equipo propio de artistas 3D sénior, diseñadores, arquitectos y gestores de proyecto. Trabajas con las mismas personas desde el brief hasta la entrega final, no con freelancers rotativos.', 'Maverick Frame ist ein festes internes Team aus erfahrenen 3D-Artists, Designern, Architekten und Projektmanagern. Sie arbeiten vom Brief bis zur finalen Lieferung mit denselben Personen — nicht mit wechselnden Freelancern.'
            ),
        ),
        array(
            mfs_t('Which clients and markets does the team work with?', '¿Con qué clientes y mercados trabaja el equipo?', 'Mit welchen Kunden und Märkten arbeitet das Team?'),
            mfs_t(
                'We serve 150+ brands, developers and agencies worldwide and align our schedule with your time zone to keep communication fast during your working hours.',
                'Atendemos a más de 150 marcas, promotoras y agencias en todo el mundo y adaptamos nuestro horario a tu zona horaria para mantener una comunicación ágil durante tu jornada.', 'Wir betreuen über 150 Marken, Entwickler und Agenturen weltweit und richten unseren Zeitplan nach Ihrer Zeitzone aus, damit die Kommunikation während Ihrer Arbeitszeit schnell bleibt.'
            ),
        ),
        array(
            mfs_t('Can your team handle large or complex projects?', '¿Puede vuestro equipo asumir proyectos grandes o complejos?', 'Kann euer Team große oder komplexe Projekte umsetzen?'),
            mfs_t(
                'Yes. The team scales across specialisations — modelling, lighting, animation, FOOH and AI — so we can take on large estates, full product ranges and multi-scene campaigns without losing consistency.',
                'Sí. El equipo se adapta entre especialidades — modelado, iluminación, animación, FOOH e IA — para asumir grandes complejos, gamas completas de producto y campañas multiescena sin perder consistencia.', 'Ja. Das Team skaliert über alle Spezialgebiete — Modellierung, Beleuchtung, Animation, FOOH und KI — sodass wir große Anlagen, komplette Produktreihen und Multi-Szenen-Kampagnen ohne Konsistenzverlust übernehmen können.'
            ),
        ),
        array(
            mfs_t('How do you keep quality consistent across the team?', '¿Cómo mantenéis una calidad consistente en todo el equipo?', 'Wie haltet ihr die Qualität im gesamten Team konstant?'),
            mfs_t(
                'Every project runs through shared standards, art direction and a project manager who reviews each delivery. That is how we hold a 97% on-time rate and a 90% repeat-client rate.',
                'Cada proyecto pasa por estándares compartidos, dirección de arte y un gestor de proyecto que revisa cada entrega. Así mantenemos un 97% de entregas a tiempo y un 90% de clientes recurrentes.', 'Jedes Projekt durchläuft gemeinsame Standards, Art Direction und einen Projektmanager, der jede Lieferung prüft. So erreichen wir eine Termintreue von 97 % und eine Wiederbestellrate von 90 %.'
            ),
        ),
    );
?>
<section class="faq">
    <div class="container container_small">
        <div class="faq__info">
            <h2><?php echo mfs_t('Frequently asked questions', 'Preguntas frecuentes', 'Häufig gestellte Fragen'); ?></h2>
        </div>

        <div class="faq__items js-faq">
            <?php if ( have_rows('faq_items') ): ?>
                <?php while ( have_rows('faq_items') ): the_row();
                    $q = get_sub_field('title');
                    $a = get_sub_field('description');
                ?>
                    <div class="faq-item js-faq-item js-reveal">
                        <button type="button" class="btn faq-item__btn js-faq-btn">
                            <span><?php echo $q; ?></span>
                        </button>

                        <div class="faq-item__answer">
                            <div class="faq-item__answer-inner">
                                <?php echo $a; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <?php foreach ($team_faq_defaults as $item): ?>
                    <div class="faq-item js-faq-item js-reveal">
                        <button type="button" class="btn faq-item__btn js-faq-btn">
                            <span><?php echo $item[0]; ?></span>
                        </button>

                        <div class="faq-item__answer">
                            <div class="faq-item__answer-inner">
                                <?php echo $item[1]; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
