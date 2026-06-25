<?php
/**
 * Book-a-call CALENDAR modal (header CTA only — data-modal="bookcall").
 * Front-end stage: visual + interaction. Slot availability / submit = stage 2.
 * Markup shell only; the day grid, time slots, timezone select and step
 * transitions are built by book-calendar.js.
 */
?>

<div class="js-modal modal modal-book-calendar" data-modal="bookcall">
    <div class="blur-overlay js-modal-close"></div>

    <div class="modal__inner">
        <button class="modal__close js-modal-close" aria-label="Close Modal window" type="button">
            <?php echo inline_svg('icons/close.svg'); ?>
        </button>

        <div class="bookcal__intro" data-intro-sell>
            <h2 class="modal__title"><?php echo mfs_t('Book your free intro call', 'Reserva tu llamada introductoria gratuita', 'Buchen Sie Ihr kostenloses Erstgespräch'); ?></h2>
            <p class="bookcal__intro-lead"><?php echo mfs_t("Pick a time that suits you. It's a quick, no-pressure chat about your project — we'll confirm by email.", 'Elige la hora que te venga bien. Es una charla rápida y sin compromiso sobre tu proyecto; lo confirmamos por email.', 'Wählen Sie eine Zeit, die Ihnen passt. Es ist ein kurzes, unverbindliches Gespräch über Ihr Projekt — wir bestätigen per E-Mail.'); ?></p>
            <ul class="bookcal__intro-list">
                <li><?php echo mfs_t('A clear plan and next steps', 'Un plan claro y los siguientes pasos', 'Ein klarer Plan und die nächsten Schritte'); ?></li>
                <li><?php echo mfs_t('Realistic timeline and budget range', 'Plazos realistas y rango de presupuesto', 'Realistischer Zeitplan und Budgetrahmen'); ?></li>
                <li><?php echo mfs_t("Honest advice, even if we're not the fit", 'Consejo honesto, aunque no seamos la mejor opción', 'Ehrliche Beratung, auch wenn wir nicht passen'); ?></li>
            </ul>
            <p class="bookcal__intro-note"><?php echo mfs_t('Free &middot; 30 minutes &middot; No commitment', 'Gratis &middot; 30 minutos &middot; Sin compromiso', 'Kostenlos &middot; 30 Minuten &middot; Unverbindlich'); ?></p>
        </div>

        <div class="bookcal__intro bookcal__intro--thanks" data-intro-thanks hidden>
            <h2 class="modal__title"><?php echo mfs_t("Thank you — you're all set", 'Gracias, todo listo', 'Danke — alles erledigt'); ?></h2>
            <p class="bookcal__intro-lead"><?php echo mfs_t("Your call is saved and a calendar invite is on its way to your inbox. We'll send a meeting link before the call.", 'Tu llamada está reservada y te enviamos la invitación de calendario al correo. Te enviaremos el enlace de la reunión antes de la llamada.', 'Ihr Gespräch ist gebucht und eine Kalendereinladung ist auf dem Weg in Ihr Postfach. Den Meeting-Link senden wir vor dem Gespräch.'); ?></p>
            <p class="bookcal__intro-note"><?php echo mfs_t('Talk soon — Maverick Frame Studio', 'Hasta pronto — Maverick Frame Studio', 'Bis bald — Maverick Frame Studio'); ?></p>
        </div>

        <div class="bookcal__panel" data-bookcal>

            <!-- Step 1: date + time -->
            <div class="bookcal__step" data-step="1">
                <h3 class="bookcal__heading"><?php echo mfs_t('Pick a time that works', 'Elige una hora que te funcione', 'Wählen Sie eine passende Zeit'); ?></h3>

                <div class="bookcal__cal-head">
                    <button type="button" class="bookcal__nav" data-cal-prev aria-label="Previous month">&lsaquo;</button>
                    <span class="bookcal__month" data-cal-month></span>
                    <button type="button" class="bookcal__nav" data-cal-next aria-label="Next month">&rsaquo;</button>
                </div>

                <div class="bookcal__weekdays">
                    <?php
                        $bookcal_wd = mfs_is('es')
                            ? array('Lu','Ma','Mi','Ju','Vi','Sá','Do')
                            : ( mfs_is('de')
                                ? array('Mo','Di','Mi','Do','Fr','Sa','So')
                                : array('Mo','Tu','We','Th','Fr','Sa','Su') );
                        foreach ( $bookcal_wd as $bookcal_d ) { echo '<span>' . $bookcal_d . '</span>'; }
                    ?>
                </div>
                <div class="bookcal__grid" data-cal-grid></div>

                <div class="bookcal__slots-wrap" data-slots-wrap hidden>
                    <p class="bookcal__slots-label" data-slots-label></p>
                    <div class="bookcal__slots" data-slots></div>
                </div>

                <div class="bookcal__tzrow">
                    <i class="bookcal__tz-ico" aria-hidden="true">&#128336;</i>
                    <label class="sr-only" for="bookcal-tz"><?php echo mfs_t('Timezone', 'Zona horaria', 'Zeitzone'); ?></label>
                    <select id="bookcal-tz" class="bookcal__tz" data-tz></select>
                </div>

                <div class="bookcal__actions">
                    <button type="button" class="btn-cta bookcal__continue" data-to-step2 disabled><?php echo mfs_t('Continue', 'Continuar', 'Weiter'); ?></button>
                </div>
            </div>

            <!-- Step 2: details -->
            <div class="bookcal__step" data-step="2" hidden>
                <h3 class="bookcal__heading"><?php echo mfs_t('Your details', 'Tus datos', 'Ihre Daten'); ?></h3>

                <div class="bookcal__summary">
                    <span data-summary></span>
                    <a href="#" class="bookcal__change" data-back-step1><?php echo mfs_t('change', 'cambiar', 'ändern'); ?></a>
                </div>

                <form class="bookcal__form" data-bookcal-form>
                    <input type="hidden" name="tag" value="SEO, Book a Call (Calendar)">
                    <input type="hidden" name="title" value="Book a Call (Calendar)">
                    <input type="hidden" name="slot" value="" data-slot-field>
                    <label class="bookcal__field">
                        <span class="sr-only"><?php echo mfs_t('Full Name', 'Nombre completo', 'Vollständiger Name'); ?></span>
                        <input type="text" name="Name" placeholder="<?php echo esc_attr(mfs_t('Full Name', 'Nombre completo', 'Vollständiger Name')); ?>">
                    </label>
                    <label class="bookcal__field">
                        <span class="sr-only">Email</span>
                        <input type="email" name="Email" placeholder="Email*" required>
                    </label>
                    <label class="bookcal__field">
                        <span class="sr-only">WhatsApp</span>
                        <input type="text" name="WhatsApp" placeholder="WhatsApp">
                    </label>
                    <button type="submit" class="btn-cta bookcal__confirm"><?php echo mfs_t('Confirm booking', 'Confirmar reserva', 'Buchung bestätigen'); ?></button>
                    <p class="bookcal__error" data-bookcal-error role="alert"></p>
                    <p class="bookcal__privacy"><?php echo mfs_t('By clicking, you agree to receive communications from Maverick Frame Studio in accordance with our', 'Al hacer clic, aceptas recibir comunicaciones de Maverick Frame Studio de acuerdo con nuestra', 'Mit dem Klick erklären Sie sich einverstanden, Mitteilungen von Maverick Frame Studio gemäß unserer'); ?> <a href="<?php echo get_permalink(6397); ?>"><?php echo mfs_t('Privacy Policy', 'Política de privacidad', 'Datenschutzerklärung'); ?></a>.</p>
                </form>
            </div>

            <!-- Step 3: confirmation -->
            <div class="bookcal__step bookcal__step--done" data-step="3" hidden>
                <div class="bookcal__check" aria-hidden="true">&#10003;</div>
                <h3 class="bookcal__heading"><?php echo mfs_t("You're booked!", '¡Reservado!', 'Sie sind gebucht!'); ?></h3>
                <p class="bookcal__done-text" data-done-text></p>
                <p class="bookcal__done-sub"><?php echo mfs_t('A calendar invite is on its way to your inbox.', 'Te enviamos la invitación de calendario al correo.', 'Eine Kalendereinladung ist auf dem Weg in Ihr Postfach.'); ?></p>
            </div>

        </div>
    </div>
</div>
