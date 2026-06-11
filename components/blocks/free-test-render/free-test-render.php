<?php
/**
 * Free Test Render — qualified lead-magnet section (homepage).
 * Clones the cta-form layout/styles; adds a qualifying form + offer conditions.
 * Single-use section: copy + assets are intentionally baked in here.
 */
$bg_id     = 14727; // exterior render (same asset family as service cta-form)
$bg_mob_id = 14726;
$clients   = [14728, 14731, 14729, 14730];
?>

<div class="container">
    <section class="cta-form-section free-test-render">
        <picture class="cta-form-section__bg">
            <source media="(max-width: 768px)" srcset="<?php echo wp_get_attachment_image_url($bg_mob_id, 'full'); ?>">
            <?php lazy_attachment($bg_id, 'full'); ?>
        </picture>

        <div class="cta-form-section__info">
            <p class="section-subtitle">Free Test Render</p>
            <h2>Start With a Free Test Render</h2>

            <div class="cta-form-section__desc">
                <p>We prove our quality on your own project before you commit. Once we&rsquo;ve scoped the work and agreed on terms, we deliver a free test render &mdash; so you see exactly how it looks in our hands, with zero risk.</p>

                <p class="free-test-render__list-title"><strong>How it works:</strong> book a short call &rarr; we scope the work and agree on the price &rarr; we deliver a free test render within 5 business days.</p>

                <p class="free-test-render__list-title"><strong>Conditions</strong></p>
                <ul>
                    <li>Available for projects from $3,000.</li>
                    <li>One test render &mdash; our team selects the most representative angle.</li>
                    <li>Delivered within 5 business days of the agreed scope; revisions aren&rsquo;t included.</li>
                    <li>For clients with an active project who are ready to discuss it on a call.</li>
                    <li>A quality guarantee for your own project &mdash; not a free sample to resell or pass on to a third party.</li>
                </ul>
            </div>
        </div>

        <div class="js-contacts-form-container cta-form-section__form">
            <h3 class="cta-form-section__form-title">Request your free test render</h3>

            <form action="" method="POST" class="js-contacts-form cta-form free-test-render__form">
                <input type="hidden" name="tag" value="SEO, Free Test Render">
                <input type="hidden" name="title" value="Homepage / Free Test Render">

                <label class="cta-form__input">
                    <span class="cta-form__label sr-only">Full Name</span>
                    <input type="text" name="Name" placeholder="Full name*" required>
                </label>

                <label class="cta-form__input">
                    <span class="cta-form__label sr-only">Work Email</span>
                    <input type="email" name="Email" placeholder="Work email*" required>
                    <span class="cta-form__error">It is not email</span>
                </label>

                <label class="cta-form__input">
                    <span class="cta-form__label sr-only">Company or website</span>
                    <input type="text" name="Company" placeholder="Company or website*" required>
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
                        <option value="" disabled selected>Estimated budget*</option>
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
                    <textarea name="Message" placeholder="Tell us about your project*" rows="2" required></textarea>
                </label>

                <label class="free-test-render__agree">
                    <input type="checkbox" name="Conditions accepted" value="Yes" required>
                    <span>I have read and accept the conditions above.</span>
                </label>

                <button class="btn-main fill" type="submit">Request my free test render</button>
            </form>

            <div class="cta-form__privacy free-test-render__privacy">
                By submitting, you agree to our <a href="<?php echo esc_url( function_exists('get_privacy_policy_url') && get_privacy_policy_url() ? get_privacy_policy_url() : home_url('/privacy-policy/') ); ?>">Privacy Policy</a>.
            </div>

            <div class="cta-form__success">
                <p><b>Thank you &ndash; your request has been received.</b></p>
                <p>Our team will review your project and get back to you shortly to arrange your free test render.</p>
            </div>
        </div>
    </section>
</div>
