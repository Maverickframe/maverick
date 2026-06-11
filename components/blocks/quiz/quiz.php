<?php
/**
 * Lead Quiz — interactive, image-answer client quiz (homepage section).
 * Self-contained: copy + style-step images baked in. Lead + answers POST to
 * forms/amo.php via quiz.js (same handler as the site forms).
 */
// Absolute prod URLs so the style images render in any environment (staging
// has a separate media library). Swap these to recolour the "look" step.
$look = array(
    'daylight' => array('url' => 'https://maverickframe.com/wp-content/uploads/2026/06/architectural-exterior-rendering-island-resort-768x487.webp', 'label' => 'Bright &amp; photoreal'),
    'warm'     => array('url' => 'https://maverickframe.com/wp-content/uploads/2026/06/best-3d-interior-rendering-companies-hotel-lounge-rendering-768x871.webp', 'label' => 'Warm &amp; inviting'),
    'moody'    => array('url' => 'https://maverickframe.com/wp-content/uploads/2026/06/luxury-villa-exterior-architectural-rendering-in-dubai-768x432.webp', 'label' => 'Moody &amp; cinematic'),
    'minimal'  => array('url' => 'https://maverickframe.com/wp-content/uploads/2026/06/contemporary-house-exterior-rendering-in-dubai-768x922.webp', 'label' => 'Clean &amp; minimal'),
);
?>

<section class="mfsq">
    <div class="container container_small">
        <div class="mfsq__intro">
            <p class="section-subtitle">30-second quiz</p>
            <h2>Not sure where to start? Find your render</h2>
            <p class="mfsq__sub">Answer five quick questions and we&rsquo;ll match you with the right approach &mdash; plus a free test render of your project.</p>
        </div>

        <div class="mfsq__card js-mfsq" data-amo="<?php echo esc_url( home_url('/wp-content/themes/maverickframe/forms/amo.php') ); ?>">
            <div class="mfsq__head">
                <span class="mfsq__count" data-count>Step 1 of 5</span>
                <div class="mfsq__bar"><span data-dot></span><span data-dot></span><span data-dot></span><span data-dot></span><span data-dot></span></div>
            </div>

            <div data-steps>
                <div class="mfsq__step is-on" data-q="what">
                    <h3>What are you visualizing?</h3>
                    <div class="mfsq__opts">
                        <button class="mfsq__opt" data-v="Exterior">Building exterior</button>
                        <button class="mfsq__opt" data-v="Interior">Interior space</button>
                        <button class="mfsq__opt" data-v="Product">A product</button>
                        <button class="mfsq__opt" data-v="Development / masterplan">Development / masterplan</button>
                    </div>
                </div>

                <div class="mfsq__step" data-q="look">
                    <h3>Pick the look you love</h3>
                    <div class="mfsq__looks">
                        <?php foreach ($look as $val => $l) : ?>
                            <button class="mfsq__look" data-v="<?php echo esc_attr($l['label']); ?>">
                                <span class="mfsq__look-img"><img src="<?php echo esc_url($l['url']); ?>" loading="lazy" alt="<?php echo esc_attr($l['label']); ?>"></span>
                                <span class="mfsq__look-cap"><?php echo $l['label']; ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mfsq__step" data-q="goal">
                    <h3>What&rsquo;s the main goal?</h3>
                    <div class="mfsq__opts">
                        <button class="mfsq__opt" data-v="Sell faster">Sell faster</button>
                        <button class="mfsq__opt" data-v="Win approvals">Win approvals</button>
                        <button class="mfsq__opt" data-v="Market &amp; advertise">Market &amp; advertise</button>
                        <button class="mfsq__opt" data-v="Impress investors">Impress investors</button>
                    </div>
                </div>

                <div class="mfsq__step" data-q="stage">
                    <h3>Where&rsquo;s your project now?</h3>
                    <div class="mfsq__opts">
                        <button class="mfsq__opt" data-v="Just an idea">Just an idea</button>
                        <button class="mfsq__opt" data-v="In design">In design</button>
                        <button class="mfsq__opt" data-v="Files ready">Files are ready</button>
                    </div>
                </div>

                <div class="mfsq__step" data-q="volume">
                    <h3>How much do you need?</h3>
                    <div class="mfsq__opts">
                        <button class="mfsq__opt" data-v="One project">One project</button>
                        <button class="mfsq__opt" data-v="Ongoing stream">Ongoing stream</button>
                    </div>
                </div>

                <div class="mfsq__step" data-q="gate">
                    <h3>Almost there &mdash; where do we send it?</h3>
                    <p class="mfsq__gate-sub">Your tailored visual plan, a moodboard in your style, and one free test render of your project.</p>
                    <input type="text" data-name placeholder="Your name" class="mfsq__input">
                    <input type="email" data-email placeholder="Work email*" class="mfsq__input">
                    <button class="mfsq__submit" data-reveal type="button">Reveal my visual plan</button>
                    <p class="mfsq__fine">No spam &mdash; we only email about your project.</p>
                </div>

                <div class="mfsq__step" data-q="result"></div>
            </div>

            <button class="mfsq__back" data-back type="button">&larr; Back</button>
        </div>
    </div>
</section>
