// Price Calculator — interior-rendering instant estimate. Live price range from
// views + add-ons + engagement model. Prices come from data-* on the card
// (set in calculator.php, ACF-ready). Email + selections POST to forms/amo.php.

function mfcalcMoney(n) {
  return '$' + Math.round(n).toLocaleString('en-US');
}

function mfcalcRound50(x) {
  return Math.round(x / 50) * 50;
}

function initCalculator() {
  var card = document.querySelector('.js-mfcalc');
  if (!card) return;

  var FIRST = parseFloat(card.getAttribute('data-first')) || 300;
  var EXTRA = parseFloat(card.getAttribute('data-extra')) || 150;
  var SUB = parseFloat(card.getAttribute('data-sub')) || 0.7;
  var amoUrl = card.getAttribute('data-amo');

  var viewsEl = card.querySelector('[data-views]');
  var viewsOut = card.querySelector('[data-views-out]');
  var rangeOut = card.querySelector('[data-range]');
  var breakdown = card.querySelector('[data-breakdown]');
  var addonEls = Array.prototype.slice.call(card.querySelectorAll('.mfcalc__addon'));
  var modelEls = Array.prototype.slice.call(card.querySelectorAll('.mfcalc__model-btn'));
  var emailEl = card.querySelector('[data-email]');
  var submitEl = card.querySelector('[data-submit]');
  var noteEl = card.querySelector('[data-note]');
  var modelMult = 1;
  var sent = false;

  function row(label, val, neg) {
    return '<div class="mfcalc__row"><span>' + label + '</span><span>'
      + (neg ? '−' : '') + mfcalcMoney(Math.abs(val)) + '</span></div>';
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
    var subtotal = FIRST + (views - 1) * EXTRA;
    var pct = 0, flat = 0, html = '';
    var vlabel = views === 1
      ? '1 interior view × ' + mfcalcMoney(FIRST)
      : 'Interior views: ' + mfcalcMoney(FIRST) + ' + ' + (views - 1) + ' × ' + mfcalcMoney(EXTRA);
    html += row(vlabel, subtotal);
    addonEls.forEach(function (el) {
      var input = el.querySelector('input');
      if (!input.checked) return;
      var val = parseFloat(input.getAttribute('data-val'));
      var name = el.querySelector('.mfcalc__addon-name').textContent;
      if (input.getAttribute('data-kind') === 'pct') {
        pct += val;
        html += row(name + ' (+' + Math.round(val * 100) + '%)', subtotal * val);
      } else {
        flat += val;
        html += row(name, val);
      }
    });
    var pre = subtotal * (1 + pct) + flat;
    var total = pre;
    if (modelMult !== 1) {
      html += row('Subscription (−30%)', pre * (1 - modelMult), true);
      total = pre * modelMult;
    }
    var low = mfcalcRound50(total * 0.9), high = mfcalcRound50(total * 1.2);
    return { views: views, html: html, low: low, high: high };
  }

  function recalc() {
    var r = compute();
    viewsOut.textContent = r.views;
    var rangeStr = mfcalcMoney(r.low) + ' – ' + mfcalcMoney(r.high);
    rangeOut.textContent = rangeStr;
    breakdown.innerHTML = r.html
      + '<div class="mfcalc__row mfcalc__row--total"><span>Estimated range</span><span>' + rangeStr + '</span></div>';
  }

  function submitLead() {
    var email = (emailEl.value || '').trim();
    if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) {
      emailEl.focus();
      emailEl.classList.add('is-error');
      noteEl.textContent = 'Please enter a valid email so we can send your quote.';
      noteEl.classList.add('is-error');
      return;
    }
    emailEl.classList.remove('is-error');
    noteEl.classList.remove('is-error');
    var r = compute();
    try {
      var fd = new FormData();
      fd.append('Email', email);
      fd.append('title', 'Interior page / Calculator');
      fd.append('tag', 'SEO, Calculator');
      fd.append('Service', 'Interior rendering');
      fd.append('Views', String(r.views));
      fd.append('Add-ons', selections().join(', ') || 'None');
      fd.append('Model', modelMult === 1 ? 'One-off' : 'Subscription');
      fd.append('Estimate', mfcalcMoney(r.low) + ' - ' + mfcalcMoney(r.high));
      fetch(amoUrl, { method: 'POST', body: fd }).catch(function () {});
      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push({ event: 'generate_lead', form_name: 'calculator', form_type: 'calculator' });
    } catch (e) { /* ignore */ }
    sent = true;
    noteEl.textContent = 'Thanks — we’ll email your fixed quote shortly.';
    noteEl.classList.add('is-ok');
    submitEl.textContent = 'Quote requested ✓';
    submitEl.disabled = true;
  }

  viewsEl.addEventListener('input', recalc);
  addonEls.forEach(function (el) {
    el.addEventListener('change', function () {
      if (sent) { sent = false; submitEl.disabled = false; submitEl.textContent = 'Email me an exact quote'; }
      recalc();
    });
  });
  modelEls.forEach(function (b) {
    b.addEventListener('click', function () {
      modelEls.forEach(function (x) { x.classList.remove('is-on'); });
      b.classList.add('is-on');
      modelMult = parseFloat(b.getAttribute('data-mult'));
      if (sent) { sent = false; submitEl.disabled = false; submitEl.textContent = 'Email me an exact quote'; }
      recalc();
    });
  });
  submitEl.addEventListener('click', submitLead);

  recalc();
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initCalculator);
} else {
  initCalculator();
}
