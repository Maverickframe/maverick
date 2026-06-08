<?php
/**
 * Book-a-call CALENDAR modal (header CTA only — data-modal="bookcall").
 * Front-end stage: visual + interaction. Slot availability / submit = stage 2.
 * Markup shell only; the day grid, time slots, timezone select and step
 * transitions are built by book-calendar.js.
 */
$title = get_field('book_a_call_title', 'options');
$desc  = get_field('book_a_call_desc', 'options');
?>

<div class="js-modal modal modal-book-calendar" data-modal="bookcall">
    <div class="blur-overlay js-modal-close"></div>

    <div class="modal__inner">
        <button class="modal__close js-modal-close" aria-label="Close Modal window" type="button">
            <?php echo inline_svg('icons/close.svg'); ?>
        </button>

        <div class="bookcal__intro">
            <h2 class="modal__title"><?php echo $title ?: "Let's schedule a quick online call"; ?></h2>
            <div class="modal__desc"><?php echo $desc; ?></div>
        </div>

        <div class="bookcal__panel" data-bookcal>

            <!-- Step 1: date + time -->
            <div class="bookcal__step" data-step="1">
                <h3 class="bookcal__heading">Pick a time that works</h3>

                <div class="bookcal__cal-head">
                    <button type="button" class="bookcal__nav" data-cal-prev aria-label="Previous month">&lsaquo;</button>
                    <span class="bookcal__month" data-cal-month></span>
                    <button type="button" class="bookcal__nav" data-cal-next aria-label="Next month">&rsaquo;</button>
                </div>

                <div class="bookcal__weekdays">
                    <span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span><span>Su</span>
                </div>
                <div class="bookcal__grid" data-cal-grid></div>

                <div class="bookcal__slots-wrap" data-slots-wrap hidden>
                    <p class="bookcal__slots-label" data-slots-label></p>
                    <div class="bookcal__slots" data-slots></div>
                </div>

                <div class="bookcal__tzrow">
                    <i class="bookcal__tz-ico" aria-hidden="true">&#128336;</i>
                    <label class="sr-only" for="bookcal-tz">Timezone</label>
                    <select id="bookcal-tz" class="bookcal__tz" data-tz></select>
                </div>

                <div class="bookcal__actions">
                    <button type="button" class="btn-cta bookcal__continue" data-to-step2 disabled>Continue</button>
                </div>
            </div>

            <!-- Step 2: details -->
            <div class="bookcal__step" data-step="2" hidden>
                <h3 class="bookcal__heading">Your details</h3>

                <div class="bookcal__summary">
                    <span data-summary></span>
                    <a href="#" class="bookcal__change" data-back-step1>change</a>
                </div>

                <form class="bookcal__form" data-bookcal-form>
                    <input type="hidden" name="tag" value="SEO, Book a Call (Calendar)">
                    <input type="hidden" name="title" value="Book a Call (Calendar)">
                    <input type="hidden" name="slot" value="" data-slot-field>
                    <label class="bookcal__field">
                        <span class="sr-only">Full Name</span>
                        <input type="text" name="Name" placeholder="Full Name">
                    </label>
                    <label class="bookcal__field">
                        <span class="sr-only">Email</span>
                        <input type="email" name="Email" placeholder="Email*" required>
                    </label>
                    <label class="bookcal__field">
                        <span class="sr-only">WhatsApp</span>
                        <input type="text" name="WhatsApp" placeholder="WhatsApp">
                    </label>
                    <button type="submit" class="btn-cta bookcal__confirm">Confirm booking</button>
                    <p class="bookcal__privacy">By clicking, you agree to receive communications from Maverick Frame Studio in accordance with our <a href="<?php echo get_permalink(6397); ?>">Privacy Policy</a>.</p>
                </form>
            </div>

            <!-- Step 3: confirmation -->
            <div class="bookcal__step bookcal__step--done" data-step="3" hidden>
                <div class="bookcal__check" aria-hidden="true">&#10003;</div>
                <h3 class="bookcal__heading">You're booked!</h3>
                <p class="bookcal__done-text" data-done-text></p>
                <p class="bookcal__done-sub">A calendar invite is on its way to your inbox.</p>
            </div>

        </div>
    </div>
</div>
