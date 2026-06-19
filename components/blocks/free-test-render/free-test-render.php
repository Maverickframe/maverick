<?php
/**
 * Free Test Render — qualified lead-magnet section.
 * Dark section (subtle glow) + white form card. Reuses cta-form layout/styles;
 * adds a structured info column + qualifying form.
 *
 * Copy is editable per-block via ACF (group_freetestrender01). Every field
 * falls back to the original homepage copy when left empty, so existing
 * placements render unchanged. The form structure / field name attributes /
 * select options stay in code (tied to the CRM/form handler).
 */

$ftr_eyebrow      = get_field('ftr_eyebrow')      ?: 'Free Test Render';
$ftr_heading      = get_field('ftr_heading')      ?: 'Start With a Free Test Render';
$ftr_intro        = get_field('ftr_intro')        ?: 'We prove our quality on your own project before you commit. Once we’ve scoped the work and agreed on terms, we deliver a free test render — so you see exactly how it looks in our hands, with zero risk.';
$ftr_hiw_label    = get_field('ftr_hiw_label')    ?: 'How it works';
$ftr_cond_label   = get_field('ftr_cond_label')   ?: 'Conditions';
$ftr_form_title   = get_field('ftr_form_title')   ?: 'Request your free test render';
$ftr_submit_label = get_field('ftr_submit_label') ?: 'Request my free test render';
$ftr_success_ttl  = get_field('ftr_success_title')?: 'Thank you – your request has been received.';
$ftr_success_txt  = get_field('ftr_success_text') ?: 'Our team will review your project and get back to you shortly to arrange your free test render.';

// Offer variant — styling hook + lead-attribution preset (does NOT swap copy; copy lives in the fields above).
$ftr_variant = get_field('offer_variant') ?: 'render';

// Lead tag → hidden "title" field = the lead source recorded in amoCRM (forms/amo.php maps title => form_page).
// Must be unique per page so the 28 placements don't collapse into one source.
// Empty + homepage  => keep the original "Homepage / Free Test Render" (homepage stays untouched).
// Empty elsewhere   => auto-build a unique tag from the variant + page slug.
$ftr_lead_tag = get_field('lead_tag');
if ( ! $ftr_lead_tag ) {
    if ( is_front_page() ) {
        $ftr_lead_tag = 'Homepage / Free Test Render';
    } else {
        $ftr_slug = get_post_field( 'post_name', get_the_ID() );
        $ftr_lead_tag = ucfirst( $ftr_variant ) . ' / Free Test Render – ' . $ftr_slug;
    }
}

$ftr_hiw_default = [
    'Tell us about your project and book a short call.',
    'We scope the work and agree on the price.',
    'We deliver a free test render within 5 business days.',
];
$ftr_cond_default = [
    'Available for projects from $3,000.',
    'One test render — our team selects the most representative angle.',
    'Delivered within 5 business days; revisions aren’t included.',
    'For clients with an active project, ready to discuss it on a call.',
    'A quality guarantee for your own project — not a free sample to resell.',
];
?>

<div class="container container_small">
    <section class="cta-form-section free-test-render" data-variant="<?php echo esc_attr($ftr_variant); ?>">
        <div class="cta-form-section__info free-test-render__info">
            <p class="section-subtitle"><?php echo esc_html($ftr_eyebrow); ?></p>
            <h2><?php echo esc_html($ftr_heading); ?></h2>

            <p class="free-test-render__intro"><?php echo esc_html($ftr_intro); ?></p>

            <div class="free-test-render__group">
                <p class="free-test-render__label"><?php echo esc_html($ftr_hiw_label); ?></p>
                <ol class="free-test-render__steps">
                    <?php if ( have_rows('ftr_hiw_steps') ) : ?>
                        <?php while ( have_rows('ftr_hiw_steps') ) : the_row(); ?>
                            <li><?php echo esc_html( get_sub_field('text') ); ?></li>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <?php foreach ( $ftr_hiw_default as $step ) : ?>
                            <li><?php echo esc_html($step); ?></li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ol>
            </div>

            <div class="free-test-render__group">
                <p class="free-test-render__label"><?php echo esc_html($ftr_cond_label); ?></p>
                <ul class="free-test-render__checklist">
                    <?php if ( have_rows('ftr_cond_items') ) : ?>
                        <?php while ( have_rows('ftr_cond_items') ) : the_row(); ?>
                            <li><?php echo esc_html( get_sub_field('text') ); ?></li>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <?php foreach ( $ftr_cond_default as $cond ) : ?>
                            <li><?php echo esc_html($cond); ?></li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="js-contacts-form-container cta-form-section__form">
            <h3 class="cta-form-section__form-title"><?php echo esc_html($ftr_form_title); ?></h3>

            <form action="" method="POST" class="js-contacts-form cta-form free-test-render__form" data-ga-event="lead_form" data-ga-form="freetest" data-ga-type="free_test">
                <input type="hidden" name="tag" value="SEO, Free Test Render">
                <input type="hidden" name="title" value="<?php echo esc_attr($ftr_lead_tag); ?>">

                <label class="cta-form__input">
                    <span class="cta-form__label sr-only">Full Name</span>
                    <input type="text" name="Name" placeholder="Full name *" required>
                </label>

                <label class="cta-form__input">
                    <span class="cta-form__label sr-only">Work Email</span>
                    <input type="email" name="Email" placeholder="Work email *" required>
                    <span class="cta-form__error">It is not email</span>
                </label>

                <label class="cta-form__input">
                    <span class="cta-form__label sr-only">Company or website</span>
                    <input type="text" name="Company" placeholder="Company or website *" required>
                </label>

                <label class="cta-form__input">
                    <span class="cta-form__label sr-only">I am a</span>
                    <select name="Role">
                        <option value="" disabled selected>I am a&hellip;</option>
                        <option>Architect / design studio</option>
                        <option>Real estate developer</option>
                        <option>Product or e-commerce brand</option>
                        <option>Marketing team or agency</option>
                        <option>Other</option>
                    </select>
                </label>

                <label class="cta-form__input">
                    <span class="cta-form__label sr-only">Project type</span>
                    <select name="Project type">
                        <option value="" disabled selected>Project type</option>
                        <option>Exterior</option>
                        <option>Interior</option>
                        <option>Product</option>
                        <option>Aerial</option>
                        <option>Animation</option>
                        <option>Other</option>
                    </select>
                </label>

                <label class="cta-form__input">
                    <span class="cta-form__label sr-only">Estimated budget</span>
                    <select name="Budget" required>
                        <option value="" disabled selected>Estimated budget *</option>
                        <option>Under $3,000</option>
                        <option>$3,000 &ndash; $10,000</option>
                        <option>$10,000+</option>
                    </select>
                </label>

                <label class="cta-form__input free-test-render__full">
                    <span class="cta-form__label sr-only">Link to files or references</span>
                    <input type="url" name="Files link" placeholder="Link to files or references (optional)">
                </label>

                <label class="cta-form__input free-test-render__full">
                    <span class="cta-form__label sr-only">Tell us about your project</span>
                    <textarea name="Message" placeholder="Tell us about your project *" rows="2" required></textarea>
                </label>

                <button class="btn-main fill" type="submit"><?php echo esc_html($ftr_submit_label); ?></button>
            </form>

            <div class="cta-form__privacy free-test-render__privacy">
                By submitting, you agree to our <a href="<?php echo esc_url( function_exists('get_privacy_policy_url') && get_privacy_policy_url() ? get_privacy_policy_url() : home_url('/privacy-policy/') ); ?>">Privacy Policy</a>.
            </div>

            <div class="cta-form__success">
                <p><b><?php echo esc_html($ftr_success_ttl); ?></b></p>
                <p><?php echo esc_html($ftr_success_txt); ?></p>
            </div>
        </div>
    </section>
</div>
