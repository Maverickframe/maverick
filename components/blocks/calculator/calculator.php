<?php
/**
 * Price Calculator — interior-rendering instant estimate (service page).
 * Compact single-column card, brand blue accent on a light section.
 * Engine works in HOURS: first view ~8h, each extra ~4h; percent add-ons add
 * hours; hours × rate ($39) = price. Animation is a flat deliverable price.
 * Subscription takes -30% off. Email + selections POST to forms/amo.php.
 * Prices/hours come from data-* (ACF-ready — swap later without JS edits).
 */

$cfg = array(
    'firsth' => 8,   'extrah' => 4,   'maxv' => 20,
    'rate'   => 39,  'sub'    => 0.70,
    'rush'   => 0.25,'mkt'    => 0.20,'pano' => 0.10,
    'anim'   => 2500,
);
$amo = esc_url( home_url('/wp-content/themes/maverickframe/forms/amo.php') );
?>

<section class="mfcalc">
    <div class="container container_small">
        <div class="mfcalc__inner">
            <div class="mfcalc__intro">
                <p class="section-subtitle">Instant estimate</p>
                <h2>Estimate your interior rendering</h2>
                <p class="mfcalc__sub">Set the scope and see a live estimate. Want it exact? We&rsquo;ll email a fixed quote based on your selections.</p>
            </div>

            <div class="mfcalc__card js-mfcalc"
                 data-amo="<?php echo $amo; ?>"
                 data-firsth="<?php echo (int) $cfg['firsth']; ?>"
                 data-extrah="<?php echo (int) $cfg['extrah']; ?>"
                 data-rate="<?php echo (int) $cfg['rate']; ?>"
                 data-sub="<?php echo (float) $cfg['sub']; ?>">

                <div class="mfcalc__field">
                    <div class="mfcalc__field-head">
                        <label for="mfcalc-views">Number of views</label>
                        <span class="mfcalc__views" data-views-out>3</span>
                    </div>
                    <input id="mfcalc-views" class="mfcalc__range" type="range"
                           min="1" max="<?php echo (int) $cfg['maxv']; ?>" value="3" step="1"
                           data-views aria-label="Number of interior views">
                    <p class="mfcalc__hint">First view ~<?php echo (int) $cfg['firsth']; ?>&nbsp;h, each additional view ~<?php echo (int) $cfg['extrah']; ?>&nbsp;h.</p>
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

                <div class="mfcalc__result">
                    <div class="mfcalc__result-main">
                        <p class="mfcalc__result-label">Estimated project</p>
                        <p class="mfcalc__range-out" data-range>$600</p>
                        <p class="mfcalc__result-hours" data-hours>≈ 16 h of work · $39/h</p>
                    </div>
                    <button type="button" class="mfcalc__toggle" data-toggle aria-expanded="false">How it&rsquo;s calculated</button>
                    <div class="mfcalc__breakdown" data-breakdown hidden></div>
                </div>

                <div class="mfcalc__capture">
                    <input type="email" class="mfcalc__email" data-email placeholder="you@company.com" aria-label="Your work email">
                    <button type="button" class="mfcalc__submit" data-submit>Email me an exact quote</button>
                    <p class="mfcalc__note" data-note>We&rsquo;ll reply with a fixed quote based on your selections.</p>
                </div>

            </div><!-- /.mfcalc__card -->
        </div>
    </div>
</section>
