<?php
    // Team/studio FAQ for the Team page.
    // Primary source: the ACF `faq_items` repeater on the page (editable in admin).
    // Fallback: built-in bilingual defaults via mfs_t() so the section is never empty.
    $team_faq_defaults = array(
        array(
            mfs_t('How experienced is the Maverick Frame team?', '¿Cuánta experiencia tiene el equipo de Maverick Frame?'),
            mfs_t(
                'Every member of our team has 7+ years of experience in CGI and 3D visualization, delivering projects for brands, developers and agencies worldwide — from off-plan towers to luxury product launches.',
                'Cada miembro de nuestro equipo cuenta con más de 7 años de experiencia en CGI y visualización 3D, entregando proyectos para marcas, promotoras y agencias de todo el mundo, desde torres sobre plano hasta lanzamientos de productos de lujo.'
            ),
        ),
        array(
            mfs_t('Do you work with an in-house team or freelancers?', '¿Trabajáis con equipo propio o con freelancers?'),
            mfs_t(
                'Maverick Frame is a dedicated in-house team of senior 3D artists, designers, architects and project managers. You work with the same people from brief to final delivery — not a rotating pool of freelancers.',
                'Maverick Frame es un equipo propio de artistas 3D sénior, diseñadores, arquitectos y gestores de proyecto. Trabajas con las mismas personas desde el brief hasta la entrega final, no con freelancers rotativos.'
            ),
        ),
        array(
            mfs_t('Which clients and markets does the team work with?', '¿Con qué clientes y mercados trabaja el equipo?'),
            mfs_t(
                'We serve 150+ brands, developers and agencies worldwide and align our schedule with your time zone to keep communication fast during your working hours.',
                'Atendemos a más de 150 marcas, promotoras y agencias en todo el mundo y adaptamos nuestro horario a tu zona horaria para mantener una comunicación ágil durante tu jornada.'
            ),
        ),
        array(
            mfs_t('Can your team handle large or complex projects?', '¿Puede vuestro equipo asumir proyectos grandes o complejos?'),
            mfs_t(
                'Yes. The team scales across specialisations — modelling, lighting, animation, FOOH and AI — so we can take on large estates, full product ranges and multi-scene campaigns without losing consistency.',
                'Sí. El equipo se adapta entre especialidades — modelado, iluminación, animación, FOOH e IA — para asumir grandes complejos, gamas completas de producto y campañas multiescena sin perder consistencia.'
            ),
        ),
        array(
            mfs_t('How do you keep quality consistent across the team?', '¿Cómo mantenéis una calidad consistente en todo el equipo?'),
            mfs_t(
                'Every project runs through shared standards, art direction and a project manager who reviews each delivery. That is how we hold a 97% on-time rate and a 90% repeat-client rate.',
                'Cada proyecto pasa por estándares compartidos, dirección de arte y un gestor de proyecto que revisa cada entrega. Así mantenemos un 97% de entregas a tiempo y un 90% de clientes recurrentes.'
            ),
        ),
    );
?>
<section class="faq">
    <div class="container container_small">
        <div class="faq__info">
            <h2><?php echo mfs_t('Frequently asked questions', 'Preguntas frecuentes'); ?></h2>
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
