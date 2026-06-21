<?php
/**
 * Contacts FAQ (new design) — contact/working-process oriented Q&A, bilingual.
 * Uses the shared .faq markup so faq.scss styling applies.
 */
$is_es = mfs_is('es');
$mfs_faq_lang = mfs_lang();

$faqs = [
    [
        'q_en' => 'How quickly will you reply to my message?',
        'q_es' => '¿Con qué rapidez responderéis a mi mensaje?',
        'a_en' => 'We usually reply within one business day. If you book a call, we confirm a time that fits your schedule and time zone.',
        'a_es' => 'Normalmente respondemos en un día laborable. Si reservas una llamada, confirmamos un horario que se ajuste a tu agenda y zona horaria.',
    ],
    [
        'q_en' => 'What should I include in my first message?',
        'q_es' => '¿Qué debo incluir en mi primer mensaje?',
        'a_en' => 'A short description of your project, the type of visuals you need, references or files if you have them, and your timeline. With that we can prepare a clear next step.',
        'a_es' => 'Una breve descripción de tu proyecto, el tipo de visuales que necesitas, referencias o archivos si los tienes y tus plazos. Con eso preparamos un siguiente paso claro.',
    ],
    [
        'q_en' => 'Do you work with international and remote clients?',
        'q_es' => '¿Trabajáis con clientes internacionales y en remoto?',
        'a_en' => 'Yes. We work with brands, developers, and agencies worldwide and align our schedule with your time zone to keep communication fast.',
        'a_es' => 'Sí. Trabajamos con marcas, promotoras y agencias de todo el mundo y adaptamos nuestro horario a tu zona horaria para mantener una comunicación ágil.',
    ],
    [
        'q_en' => 'In which languages can I contact you?',
        'q_es' => '¿En qué idiomas puedo contactaros?',
        'a_en' => 'You can write to us in Spanish or English. Project communication continues in the language that works best for your team.',
        'a_es' => 'Puedes escribirnos en español o en inglés. La comunicación del proyecto continúa en el idioma que mejor funcione para tu equipo.',
    ],
    [
        'q_en' => 'Can I book a call to discuss my project?',
        'q_es' => '¿Puedo reservar una llamada para hablar de mi proyecto?',
        'a_en' => 'Of course. Use the “Book a call” button and pick a slot. We will review your brief beforehand so the call is focused on your goals.',
        'a_es' => 'Por supuesto. Usa el botón «Reservar una llamada» y elige un hueco. Revisamos tu brief de antemano para que la llamada se centre en tus objetivos.',
    ],
    [
        'q_en' => 'What happens after I get in touch?',
        'q_es' => '¿Qué pasa después de ponerme en contacto?',
        'a_en' => 'We review your request, ask any clarifying questions, and propose scope, timeline, and a first preview plan so you know exactly how the project will start.',
        'a_es' => 'Revisamos tu solicitud, hacemos las preguntas necesarias y proponemos alcance, plazos y un plan de primera previsualización para que sepas exactamente cómo arrancará el proyecto.',
    ],
];
?>
<section class="faq">
    <div class="container container_small">
        <div class="faq__info">
            <h2><?php echo mfs_t('Frequently asked questions', 'Preguntas frecuentes'); ?></h2>
        </div>

        <div class="faq__items">
            <?php foreach ($faqs as $item):
                $q = $item['q_' . $mfs_faq_lang] ?? $item['q_en'];
                $a = $item['a_' . $mfs_faq_lang] ?? $item['a_en']; ?>
                <div class="faq-item">
                    <button class="faq-item__btn js-faq-btn" type="button">
                        <span><?php echo esc_html($q); ?></span>
                    </button>
                    <div class="faq-item__answer">
                        <div class="faq-item__answer-inner"><?php echo esc_html($a); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
