// Price Calculator — interior-rendering instant estimate. Engine works in HOURS
// (first view ~8h, each extra ~4h); percent add-ons add hours; hours × rate
// ($39) = price. Animation is a flat deliverable price. Subscription −30%.
// Config comes from data-* on the card. Email + selections POST to amo.php.

function mfcMoney(n) { return '$' + Math.round(n).toLocaleString('en-US'); }
function mfcRound50(x) { return Math.round(x / 50) * 50; }
function mfcHours(h) { return (Math.round(h * 10) / 10).toString().replace(/\.0$/, ''); }

function initCalculator() {
  var card = document.querySelector('.js-mfcalc');
  if (!card) return;

  var FIRSTH = parseFloat(card.getAttribute('data-firsth')) || 8;
  var EXTRAH = parseFloat(card.getAttribute('data-extrah')) || 4;
  var RATE = parseFloat(card.getAttribute('data-rate')) || 39;
  var SUB = parseFloat(card.getAttribute('data-sub')) || 0.7;
  var amoUrl = card.getAttribute('data-amo');

  var viewsEl = card.querySelector('[data-views]');
  var viewsOut = card.querySelector('[data-views-out]');
  var rangeOut = card.querySelector('[data-range]');
  var hoursOut = card.querySelector('[data-hours]');
  var breakdown = card.querySelector('[data-breakdown]');
  var toggleEl = card.querySelector('[data-toggle]');
  var addonEls = Array.prototype.slice.call(card.querySelectorAll('.mfcalc__addon'));
  var modelEls = Array.prototype.slice.call(card.querySelectorAll('.mfcalc__model-btn'));
  var emailEl = card.querySelector('[data-email]');
  var submitEl = card.querySelector('[data-submit]');
  var noteEl = card.querySelector('[data-note]');
  var modelMult = 1;
  var sent = false;

  function row(label, val) {
    return '<div class="mfcalc__row"><span>' + label + '</span><span>' + val + '</span></div>';
  }

  function selections() {
    var out = [];
    addonEls.forEach(function (el) {
      if (el.querySelector('input').checked) {
        out.push(el.querySelector('.mfcalc__addon-name').textContent.replace(/—/g, '-').trim());
      }
    });
    return out;
  }

  function compute() {
    var views = parseInt(viewsEl.value, 10);
    var baseH = FIRSTH + (views - 1) * EXTRAH;
    var pct = 0, flat = 0, html = '';
    html += row(views + (views > 1 ? ' interior views' : ' interior view'), mfcHours(baseH) + ' h');
    addonEls.forEach(function (el) {
      var input = el.querySelector('input');
      if (!input.checked) return;
      var val = parseFloat(input.getAttribute('data-val'));
      var name = el.querySelector('.mfcalc__addon-name').textContent;
      if (input.getAttribute('data-kind') === 'pct') {
        pct += val;
        html += row(name + ' (+' + Math.round(val * 100) + '%)', '+' + mfcHours(baseH * val) + ' h');
      } else {
        flat += val;
      }
    });
    var hoursTotal = baseH * (1 + pct);
    var workPrice = hoursTotal * RATE;
    html += row('Work · ' + mfcHours(hoursTotal) + ' h × ' + mfcMoney(RATE) + '/h', mfcMoney(workPrice));
    if (flat) html += row('Animation clip', '+' + mfcMoney(flat));
    var price = workPrice + flat;
    if (modelMult !== 1) {
      html += row('Subscription (−30%)', '−' + mfcMoney(price * (1 - modelMult)));
      price = price * modelMult;
    }
    var low = mfcRound50(price * 0.9), high = mfcRound50(price * 1.2);
    return { views: views, hoursTotal: hoursTotal, html: html, low: low, high: high };
  }

  function recalc() {
    var r = compute();
    viewsOut.textContent = r.views;
    rangeOut.textContent = mfcMoney(r.low) + ' – ' + mfcMoney(r.high);
    hoursOut.textContent = '≈ ' + mfcHours(r.hoursTotal) + ' h of work · ' + mfcMoney(RATE) + '/h';
    breakdown.innerHTML = r.html
      + '<div class="mfcalc__row mfcalc__row--total"><span>Estimated range</span><span>'
      + mfcMoney(r.low) + ' – ' + mfcMoney(r.high) + '</span></div>';
  }

  function resetSent() {
    if (!sent) return;
    sent = false; submitEl.disabled = false; submitEl.textContent = 'Email me an exact quote';
  }

  function submitLead() {
    var email = (emailEl.value || '').trim();
    if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) {
      emailEl.focus(); emailEl.classList.add('is-error');
      noteEl.textContent = 'Please enter a valid email so we can send your quote.';
      noteEl.className = 'mfcalc__note is-error';
      return;
    }
    emailEl.classList.remove('is-error');
    var r = compute();
    try {
      var fd = new FormData();
      fd.append('Email', email);
      fd.append('title', 'Interior page / Calculator');
      fd.append('tag', 'SEO, Calculator');
      fd.append('Service', 'Interior rendering');
      fd.append('Views', String(r.views));
      fd.append('Hours', mfcHours(r.hoursTotal));
      fd.append('Add-ons', selections().join(', ') || 'None');
      fd.append('Model', modelMult === 1 ? 'One-off' : 'Subscription');
      fd.append('Estimate', mfcMoney(r.low) + ' - ' + mfcMoney(r.high));
      fetch(amoUrl, { method: 'POST', body: fd }).catch(function () {});
      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push({ event: 'lead_form', form_name: 'calculator', form_type: 'calculator' });
    } catch (e) { /* ignore */ }
    sent = true;
    noteEl.textContent = 'Thanks — we’ll email your fixed quote shortly.';
    noteEl.className = 'mfcalc__note is-ok';
    submitEl.textContent = 'Quote requested ✓';
    submitEl.disabled = true;
  }

  viewsEl.addEventListener('input', function () { resetSent(); recalc(); });
  addonEls.forEach(function (el) {
    el.addEventListener('change', function () { resetSent(); recalc(); });
  });
  modelEls.forEach(function (b) {
    b.addEventListener('click', function () {
      modelEls.forEach(function (x) { x.classList.remove('is-on'); });
      b.classList.add('is-on');
      modelMult = parseFloat(b.getAttribute('data-mult'));
      resetSent(); recalc();
    });
  });
  if (toggleEl) {
    toggleEl.addEventListener('click', function () {
      var open = breakdown.hidden;
      breakdown.hidden = !open;
      toggleEl.setAttribute('aria-expanded', open ? 'true' : 'false');
      toggleEl.classList.toggle('is-open', open);
    });
  }
  submitEl.addEventListener('click', submitLead);

  recalc();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initCalculator);
} else {
  initCalculator();
}
