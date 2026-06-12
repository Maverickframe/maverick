<?php
/**
 * Price Calculator — interior-rendering instant estimate (service page).
 * Light section, two-column card: config (left) + dark live-price panel (right).
 * Visitor picks views, add-ons and model; the range updates live in JS.
 * Email + selections POST to forms/amo.php via calculator.js. Prices are baked
 * here (single source) and passed to JS as data-* — swap to ACF later w/o JS edits.
 */

// Pricing config (calibrated 12 Jun 2026). First interior view carries scene
// setup (~8h @ $39); each extra view ~4h. Percent add-ons apply to the view
// subtotal; animation is flat. Subscription takes -30% off everything.
$cfg = array(
    'first' => 300,  'extra' => 150, 'maxv' => 20,
    'rush'  => 0.25, 'mkt'   => 0.20, 'pano' => 0.10,
    'anim'  => 2500, 'sub'   => 0.70,
);
$amo = esc_url( home_url('/wp-content/themes/maverickframe/forms/amo.php') );
?>

<section class="mfcalc">
    <div class="container container_small">
        <div class="mfcalc__intro">
            <p class="section-subtitle">Instant estimate</p>
            <h2>Estimate your interior rendering in seconds</h2>
            <p class="mfcalc__sub">Set the scope and see a live price range. Want it exact? We&rsquo;ll email a fixed quote based on your selections &mdash; no obligation.</p>
        </div>

        <div class="mfcalc__card js-mfcalc"
             data-amo="<?php echo $amo; ?>"
             data-first="<?php echo (int) $cfg['first']; ?>"
             data-extra="<?php echo (int) $cfg['extra']; ?>"
             data-anim="<?php echo (int) $cfg['anim']; ?>"
             data-sub="<?php echo (float) $cfg['sub']; ?>">

            <div class="mfcalc__config">

                <div class="mfcalc__field">
                    <div class="mfcalc__field-head">
                        <label for="mfcalc-views">Number of views</label>
                        <span class="mfcalc__views" data-views-out>3</span>
                    </div>
                    <input id="mfcalc-views" class="mfcalc__range" type="range"
                           min="1" max="<?php echo (int) $cfg['maxv']; ?>" value="3" step="1"
                           data-views aria-label="Number of interior views">
                    <p class="mfcalc__hint">First view <?php echo '$' . (int) $cfg['first']; ?>, each additional view <?php echo '$' . (int) $cfg['extra']; ?>.</p>
                </div>

                <div class="mfcalc__field">
                    <p class="mfcalc__field-label">Add-ons</p>
                    <div class="mfcalc__addons">
                        <label class="mfcalc__addon">
                            <input type="checkbox" data-kind="pct" data-val="<?php echo $cfg['rush']; ?>">
                            <span class="mfcalc__addon-name">Rush &mdash; 1-day delivery</span>
                            <span class="mfcalc__addon-price">+25%</span>
                        </label>
                        <label class="mfcalc__addon">
                            <input type="checkbox" data-kind="pct" data-val="<?php echo $cfg['mkt']; ?>">
                            <span class="mfcalc__addon-name">Marketing / social pack</span>
                            <span class="mfcalc__addon-price">+20%</span>
                        </label>
                        <label class="mfcalc__addon">
                            <input type="checkbox" data-kind="pct" data-val="<?php echo $cfg['pano']; ?>">
                            <span class="mfcalc__addon-name">360&deg; panorama</span>
                            <span class="mfcalc__addon-price">+10%</span>
                        </label>
                        <label class="mfcalc__addon">
                            <input type="checkbox" data-kind="flat" data-val="<?php echo (int) $cfg['anim']; ?>">
                            <span class="mfcalc__addon-name">Animation clip (~30s)</span>
                            <span class="mfcalc__addon-price">+$2,500</span>
                        </label>
                    </div>
                </div>

                <div class="mfcalc__field">
                    <p class="mfcalc__field-label">Engagement model</p>
                    <div class="mfcalc__model" data-model>
                        <button type="button" class="mfcalc__model-btn is-on" data-mult="1">One-off project</button>
                        <button type="button" class="mfcalc__model-btn" data-mult="<?php echo (float) $cfg['sub']; ?>">Subscription <span>&minus;30%</span></button>
                    </div>
                </div>

            </div><!-- /.mfcalc__config -->

            <div class="mfcalc__panel">
                <p class="mfcalc__panel-label">Estimated project range</p>
                <p class="mfcalc__range-out" data-range>$600</p>
                <div class="mfcalc__breakdown" data-breakdown></div>

                <div class="mfcalc__capture">
                    <input type="email" class="mfcalc__email" data-email placeholder="you@company.com" aria-label="Your work email">
                    <button type="button" class="mfcalc__submit" data-submit>Email me an exact quote</button>
                    <p class="mfcalc__note" data-note>We&rsquo;ll reply with a fixed quote based on your selections.</p>
                </div>
            </div><!-- /.mfcalc__panel -->

        </div><!-- /.mfcalc__card -->
    </div>
</section>
