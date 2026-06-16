<?php
/**
 * Lead Quiz — interactive, branching client quiz (homepage section).
 * Routes the visitor into one of three service lines (CGI / Web / Creative),
 * then runs a short tailored path. Self-contained: copy + style-step images
 * baked in. Lead + all answers POST to forms/amo.php via quiz.js.
 *
 * Per-page config via ACF (block acf/quiz): quiz_mode = 'router' (homepage,
 * branching) | 'service' (single linear path for a service/landing page).
 * Router mode keeps the hardcoded tree below; service mode renders its steps
 * from the quiz_steps repeater and its result screen from the quiz_result_*
 * fields, so content can be edited without touching code.
 */

$mfsq_mode    = get_field('quiz_mode') ?: 'router';
$mfsq_eyebrow = get_field('quiz_eyebrow');
$mfsq_eyebrow = ( $mfsq_eyebrow !== '' && $mfsq_eyebrow !== null ) ? $mfsq_eyebrow : '30-second quiz';
$mfsq_heading = get_field('quiz_heading');
$mfsq_heading = ( $mfsq_heading !== '' && $mfsq_heading !== null ) ? $mfsq_heading : 'Not sure where to start? Find your fit';
$mfsq_intro   = get_field('quiz_intro');
$mfsq_intro   = ( $mfsq_intro !== '' && $mfsq_intro !== null ) ? $mfsq_intro : 'Answer a few quick questions and we’ll match you with the right service — plus a free, tailored next step for your project.';
$mfsq_lead_title = get_field('quiz_lead_title');
if ( ! $mfsq_lead_title ) {
    $mfsq_lead_title = ( $mfsq_mode === 'service' ) ? ( get_the_title() . ' / Quiz' ) : 'Homepage / Quiz';
}

// CGI "look" step — 4 subject-matched renders per subject. Absolute prod URLs
// so they render in any environment (staging has a separate media library).
// Swap a URL here to recurate the look step. Keys must match Q1 data-v values.
$U = 'https://maverickframe.com/wp-content/uploads/';
$looks = array(
    'Exterior' => array(
        array('Bright & photoreal', $U . '2026/05/hero-image-modern-house-lawn.webp'),
        array('Warm & inviting',    $U . '2026/05/hero-image-snow-cabin-mountains.webp'),
        array('Moody & cinematic',  $U . '2026/05/hero-image-modern-house-cliff.webp'),
        array('Clean & minimal',    $U . '2026/05/hero-image-apartment-building.webp'),
    ),
    'Interior' => array(
        array('Bright & photoreal', $U . '2026/03/residence-interior-design-open-living-room-garden-views.webp'),
        array('Warm & inviting',    $U . '2026/06/best-3d-interior-rendering-companies-hotel-lounge-rendering.webp'),
        array('Moody & cinematic',  $U . '2026/02/island-hotel_render-16.webp'),
        array('Clean & minimal',    $U . '2026/05/hero-image-minimal-kitchen-dining.webp'),
    ),
    'Product' => array(
        array('Bright & photoreal', $U . '2026/05/hero-image-speaker-amplifier.webp'),
        array('Warm & inviting',    $U . '2026/05/hero-image-seranova-packaging.webp'),
        array('Moody & cinematic',  $U . '2026/05/hero-image-espresso-machine-black.webp'),
        array('Clean & minimal',    $U . '2026/05/hero-image-supplement-jar-green.webp'),
    ),
    'Vehicle' => array(
        array('Bright & photoreal', $U . '2026/05/hero-image-yacht-aerial.webp'),
        array('Warm & inviting',    $U . '2026/05/hero-image-sailboat-sea.webp'),
        array('Moody & cinematic',  $U . '2026/05/hero-image-sports-car-motion.webp'),
        array('Clean & minimal',    $U . '2026/05/hero-image-boat-lake-mountains.webp'),
    ),
    'Development' => array(
        array('Bright & photoreal', $U . '2026/05/hero-image-overwater-villas-aerial.webp'),
        array('Warm & inviting',    $U . '2026/05/hero-image-forest-cabins.webp'),
        array('Moody & cinematic',  $U . '2026/04/site-plan-and-aerial-rendering-services-forest-community.webp'),
        array('Clean & minimal',    $U . '2026/04/landscape-rendering-services-lakeside-cabins-aerial.webp'),
    ),
);
?>

<section class="mfsq">
    <div class="container container_small">
        <div class="mfsq__intro">
            <p class="section-subtitle"><?php echo esc_html( $mfsq_eyebrow ); ?></p>
            <h2><?php echo esc_html( $mfsq_heading ); ?></h2>
            <p class="mfsq__sub"><?php echo esc_html( $mfsq_intro ); ?></p>
        </div>

        <div class="mfsq__card js-mfsq" data-mode="<?php echo esc_attr( $mfsq_mode ); ?>" data-title="<?php echo esc_attr( $mfsq_lead_title ); ?>" data-amo="<?php echo esc_url( home_url('/wp-content/themes/maverickframe/forms/amo.php') ); ?>">
            <?php if ( $mfsq_mode === 'service' ) {
                $mfsq_res = array(
                    'head'    => get_field('quiz_result_head') ?: '',
                    'service' => get_field('quiz_result_service') ?: '',
                    'note'    => get_field('quiz_result_note') ?: '',
                    'gateSub' => get_field('quiz_gate_sub') ?: '',
                );
                echo '<script type="application/json" data-mfsq-result>' . wp_json_encode( $mfsq_res ) . '</script>';
            } else { ?>
            <script type="application/json" data-mfsq-looks><?php echo wp_json_encode( $looks ); ?></script>
            <?php } ?>

            <div class="mfsq__head">
                <span class="mfsq__count" data-count>Step 1</span>
                <div class="mfsq__bar" data-bar></div>
            </div>

            <div data-steps>

                <?php if ( $mfsq_mode === 'service' ) :
                    $mfsq_steps = get_field('quiz_steps');
                    if ( ! is_array( $mfsq_steps ) ) { $mfsq_steps = array(); }
                    $mfsq_i = 0;
                    foreach ( $mfsq_steps as $mfsq_step ) :
                        $mfsq_q = isset( $mfsq_step['step_question'] ) ? trim( $mfsq_step['step_question'] ) : '';
                        $mfsq_opts = ( isset( $mfsq_step['step_options'] ) && is_array( $mfsq_step['step_options'] ) ) ? $mfsq_step['step_options'] : array();
                        if ( $mfsq_q === '' || empty( $mfsq_opts ) ) { continue; }
                        $mfsq_has_img = false;
                        foreach ( $mfsq_opts as $mfsq_o ) { if ( ! empty( $mfsq_o['opt_image'] ) ) { $mfsq_has_img = true; break; } }
                        ?>
                        <div class="mfsq__step<?php echo $mfsq_i === 0 ? ' is-on' : ''; ?>" data-q="s<?php echo (int) $mfsq_i; ?>" data-label="<?php echo esc_attr( $mfsq_q ); ?>">
                            <h3><?php echo esc_html( $mfsq_q ); ?></h3>
                            <?php if ( $mfsq_has_img ) : ?>
                                <div class="mfsq__looks">
                                    <?php foreach ( $mfsq_opts as $mfsq_o ) :
                                        $mfsq_l = isset( $mfsq_o['opt_label'] ) ? trim( $mfsq_o['opt_label'] ) : '';
                                        if ( $mfsq_l === '' ) { continue; }
                                        $mfsq_img = ! empty( $mfsq_o['opt_image'] ) ? wp_get_attachment_image_url( $mfsq_o['opt_image'], 'medium' ) : '';
                                        ?>
                                        <button class="mfsq__look" data-v="<?php echo esc_attr( $mfsq_l ); ?>">
                                            <?php if ( $mfsq_img ) : ?><span class="mfsq__look-img"><img src="<?php echo esc_url( $mfsq_img ); ?>" loading="lazy" alt="<?php echo esc_attr( $mfsq_l ); ?>"></span><?php endif; ?>
                                            <span class="mfsq__look-cap"><?php echo esc_html( $mfsq_l ); ?></span>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            <?php else : ?>
                                <div class="mfsq__opts">
                                    <?php foreach ( $mfsq_opts as $mfsq_o ) :
                                        $mfsq_l = isset( $mfsq_o['opt_label'] ) ? trim( $mfsq_o['opt_label'] ) : '';
                                        if ( $mfsq_l === '' ) { continue; }
                                        ?>
                                        <button class="mfsq__opt" data-v="<?php echo esc_attr( $mfsq_l ); ?>"><?php echo esc_html( $mfsq_l ); ?></button>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php $mfsq_i++;
                    endforeach;
                else : ?>

                <!-- Q1 router (shared) -->
                <div class="mfsq__step is-on" data-q="route">
                    <h3>What can we help you create?</h3>
                    <div class="mfsq__opts">
                        <button class="mfsq__opt" data-branch="cgi" data-v="3D visuals &amp; CGI">3D visuals &amp; CGI</button>
                        <button class="mfsq__opt" data-branch="web" data-v="Website or app">Website or app</button>
                        <button class="mfsq__opt" data-branch="creative" data-v="Branding &amp; creative">Branding &amp; creative</button>
                    </div>
                </div>

                <!-- ===== CGI branch ===== -->
                <div class="mfsq__step" data-q="subject" data-branch="cgi">
                    <h3>What are you visualizing?</h3>
                    <div class="mfsq__opts">
                        <button class="mfsq__opt" data-v="Exterior">Building exterior</button>
                        <button class="mfsq__opt" data-v="Interior">Interior space</button>
                        <button class="mfsq__opt" data-v="Product">Product or furniture</button>
                        <button class="mfsq__opt" data-v="Vehicle">Yacht, car or aircraft</button>
                        <button class="mfsq__opt" data-v="Development">Development, site or aerial</button>
                    </div>
                </div>

                <div class="mfsq__step" data-q="look" data-branch="cgi">
                    <h3>Pick the look you love</h3>
                    <div class="mfsq__looks" data-looks></div>
                </div>

                <div class="mfsq__step" data-q="goal" data-branch="cgi">
                    <h3>What&rsquo;s the main goal?</h3>
                    <div class="mfsq__opts">
                        <button class="mfsq__opt" data-v="Sell faster">Sell faster</button>
                        <button class="mfsq__opt" data-v="Win approvals">Win approvals</button>
                        <button class="mfsq__opt" data-v="Market &amp; advertise">Market &amp; advertise</button>
                        <button class="mfsq__opt" data-v="Impress investors">Impress investors</button>
                    </div>
                </div>

                <div class="mfsq__step" data-q="stage" data-branch="cgi">
                    <h3>Where&rsquo;s your project now?</h3>
                    <div class="mfsq__opts">
                        <button class="mfsq__opt" data-v="Just an idea">Just an idea</button>
                        <button class="mfsq__opt" data-v="In design">In design</button>
                        <button class="mfsq__opt" data-v="Files ready">Files are ready</button>
                    </div>
                </div>

                <div class="mfsq__step" data-q="volume" data-branch="cgi">
                    <h3>How much do you need?</h3>
                    <div class="mfsq__opts">
                        <button class="mfsq__opt" data-v="One project">One project</button>
                        <button class="mfsq__opt" data-v="Ongoing stream">Ongoing stream</button>
                    </div>
                </div>

                <!-- ===== Web &amp; app branch ===== -->
                <div class="mfsq__step" data-q="webtype" data-branch="web">
                    <h3>What do you need?</h3>
                    <div class="mfsq__opts">
                        <button class="mfsq__opt" data-v="Website">A new website</button>
                        <button class="mfsq__opt" data-v="Landing page">A landing page</button>
                        <button class="mfsq__opt" data-v="Mobile app">A mobile app</button>
                        <button class="mfsq__opt" data-v="UI/UX redesign">UI/UX redesign</button>
                    </div>
                </div>

                <div class="mfsq__step" data-q="webgoal" data-branch="web">
                    <h3>What&rsquo;s the main goal?</h3>
                    <div class="mfsq__opts">
                        <button class="mfsq__opt" data-v="Launch something new">Launch something new</button>
                        <button class="mfsq__opt" data-v="Redesign &amp; modernize">Redesign &amp; modernize</button>
                        <button class="mfsq__opt" data-v="Increase conversions">Increase conversions</button>
                        <button class="mfsq__opt" data-v="Impress investors">Impress investors</button>
                    </div>
                </div>

                <div class="mfsq__step" data-q="webstage" data-branch="web">
                    <h3>Where are you now?</h3>
                    <div class="mfsq__opts">
                        <button class="mfsq__opt" data-v="Just an idea">Just an idea</button>
                        <button class="mfsq__opt" data-v="Brand &amp; content ready">Brand &amp; content ready</button>
                        <button class="mfsq__opt" data-v="Have a live site">Have a live site to improve</button>
                    </div>
                </div>

                <!-- ===== Branding &amp; creative branch ===== -->
                <div class="mfsq__step" data-q="crtype" data-branch="creative">
                    <h3>What do you need?</h3>
                    <div class="mfsq__opts">
                        <button class="mfsq__opt" data-v="Brand identity">Brand identity</button>
                        <button class="mfsq__opt" data-v="Social media content">Social media content</button>
                        <button class="mfsq__opt" data-v="Presentation / pitch deck">Presentation / pitch deck</button>
                        <button class="mfsq__opt" data-v="FOOH / CGI ad">FOOH / CGI ad</button>
                    </div>
                </div>

                <div class="mfsq__step" data-q="crgoal" data-branch="creative">
                    <h3>What&rsquo;s the main goal?</h3>
                    <div class="mfsq__opts">
                        <button class="mfsq__opt" data-v="Launch a brand">Launch a brand</button>
                        <button class="mfsq__opt" data-v="Refresh our look">Refresh our look</button>
                        <button class="mfsq__opt" data-v="Drive engagement">Drive engagement</button>
                        <button class="mfsq__opt" data-v="Win investors">Win investors</button>
                    </div>
                </div>

                <div class="mfsq__step" data-q="crstage" data-branch="creative">
                    <h3>Where are you now?</h3>
                    <div class="mfsq__opts">
                        <button class="mfsq__opt" data-v="Starting from scratch">Starting from scratch</button>
                        <button class="mfsq__opt" data-v="Have some assets">Have some assets</button>
                        <button class="mfsq__opt" data-v="Rebranding existing">Rebranding existing</button>
                    </div>
                </div>

                <?php endif; ?>

                <!-- ===== Shared gate + result ===== -->
                <div class="mfsq__step" data-q="gate">
                    <h3>Almost there &mdash; where do we send it?</h3>
                    <p class="mfsq__gate-sub" data-gate-sub>Your tailored plan and a free next step for your project.</p>
                    <input type="text" data-name placeholder="Your name" class="mfsq__input">
                    <input type="email" data-email placeholder="Work email*" class="mfsq__input">
                    <button class="mfsq__submit" data-reveal type="button">Reveal my plan</button>
                    <p class="mfsq__fine">No spam &mdash; we only email about your project.</p>
                </div>

                <div class="mfsq__step" data-q="result"></div>
            </div>

            <button class="mfsq__back" data-back type="button">&larr; Back</button>
        </div>
    </div>
</section>
