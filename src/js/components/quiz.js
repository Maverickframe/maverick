// Lead Quiz — branching stepper, contact gate, personalised result. The visitor
// is routed into one of three service lines (CGI / Web / Creative); each runs a
// short tailored path. Lead + all answers POST to forms/amo.php (same handler
// as the site forms).

function escapeHtml(s) {
  return String(s).replace(/[&<>"']/g, function (c) {
    return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
  });
}

// Current page language from <html lang> (Polylang sets de/de-DE, es/es-ES).
var MFSQ_LANG = (function () {
  var l = (document.documentElement.getAttribute('lang') || 'en').toLowerCase();
  if (l.indexOf('de') === 0) return 'de';
  if (l.indexOf('es') === 0) return 'es';
  return 'en';
})();
// JS twin of PHP mfs_t(): pick the string for the current language, EN fallback.
function qt(en, es, de) {
  if (MFSQ_LANG === 'de') return de != null ? de : en;
  if (MFSQ_LANG === 'es') return es != null ? es : en;
  return en;
}
// Result-screen eyebrow ("<name>, your recommended approach"). Whole phrase is
// translated (word order differs by language), name kept where natural.
function resultEyebrow(name) {
  return name
    ? qt(escapeHtml(name) + ', your recommended approach', escapeHtml(name) + ', tu enfoque recomendado', escapeHtml(name) + ', dein empfohlener Ansatz')
    : qt('Your recommended approach', 'Tu enfoque recomendado', 'Dein empfohlener Ansatz');
}

// Step order per branch (route + branch steps + gate + result).
var SEQ = {
  cgi: ['route', 'subject', 'look', 'goal', 'stage', 'volume', 'gate', 'result'],
  web: ['route', 'webtype', 'webgoal', 'webstage', 'gate', 'result'],
  creative: ['route', 'crtype', 'crgoal', 'crstage', 'gate', 'result']
};

// Human labels for each answer key (for the amoCRM note).
var LABELS = {
  route: 'Need', subject: 'Subject', look: 'Look', goal: 'Goal', stage: 'Stage', volume: 'Volume',
  webtype: 'Need', webgoal: 'Goal', webstage: 'Stage',
  crtype: 'Need', crgoal: 'Goal', crstage: 'Stage'
};

// Recommended-service maps + voice fragments per branch.
var SVC = {
  cgi: { Exterior: '3D exterior rendering', Interior: '3D interior rendering', Product: 'product & furniture CGI', Vehicle: 'yacht, car & aircraft visualization', Development: 'masterplan & aerial CGI' },
  web: { Website: 'web design', 'Landing page': 'landing page design', 'Mobile app': 'mobile app design', 'UI/UX redesign': 'UI/UX design' },
  creative: { 'Brand identity': 'branding & identity', 'Social media content': 'social media design', 'Presentation / pitch deck': 'presentation design', 'FOOH / CGI ad': 'FOOH CGI advertising' }
};
var GOALT = {
  cgi: { 'Sell faster': 'built to sell', 'Win approvals': 'tuned for approvals', 'Market & advertise': 'campaign-ready', 'Impress investors': 'made to win investors' },
  web: { 'Launch something new': 'built to launch', 'Redesign & modernize': 'modern & refreshed', 'Increase conversions': 'conversion-focused', 'Impress investors': 'investor-ready' },
  creative: { 'Launch a brand': 'built to launch', 'Refresh our look': 'a fresh, modern look', 'Drive engagement': 'made to engage', 'Win investors': 'investor-ready' }
};
var STAGEL = {
  cgi: { 'Just an idea': 'We start from your references and sketches.', 'In design': 'Send your CAD or design files and we begin.', 'Files ready': 'Your files are ready — we can start fast.' },
  web: { 'Just an idea': 'We start from your goals and references.', 'Brand & content ready': 'Brand & content ready — we move straight to design.', 'Have a live site': 'We audit your current site and improve from there.' },
  creative: { 'Starting from scratch': 'We start from a blank canvas and your vision.', 'Have some assets': 'We build on your existing assets.', 'Rebranding existing': 'We evolve your current brand.' }
};
var VOLL = { 'One project': 'One-off project pricing.', 'Ongoing stream': 'Subscription saves up to 30% on ongoing volume.' };

// Per-branch gate copy + result closing line.
var GATE_SUB = {
  cgi: 'Your tailored visual plan, a moodboard in your style, and one free test render of your project.',
  web: 'A tailored plan for your project and a free design concept to get started.',
  creative: 'A tailored creative plan and a free concept to get started.'
};
var CLOSING = {
  cgi: 'We’ll email your moodboard and a free test render shortly.',
  web: 'We’ll email your tailored plan and a free design concept shortly.',
  creative: 'We’ll email your tailored plan and a free concept shortly.'
};

function initQuiz() {
  var card = document.querySelector('.js-mfsq');
  if (!card) return;

  var stepEls = {};
  card.querySelectorAll('.mfsq__step').forEach(function (s) { stepEls[s.getAttribute('data-q')] = s; });

  var bar = card.querySelector('[data-bar]');
  var count = card.querySelector('[data-count]');
  var back = card.querySelector('[data-back]');
  var gateSub = card.querySelector('[data-gate-sub]');
  var amoUrl = card.getAttribute('data-amo');
  var mode = card.getAttribute('data-mode') || 'router';
  var leadTitle = card.getAttribute('data-title') || 'Homepage / Quiz';

  var looks = {};
  var looksEl = card.querySelector('[data-mfsq-looks]');
  if (looksEl) { try { looks = JSON.parse(looksEl.textContent || '{}'); } catch (e) { looks = {}; } }

  // Service mode result config (ACF-driven), if present.
  var resultCfg = {};
  var resEl = card.querySelector('[data-mfsq-result]');
  if (resEl) { try { resultCfg = JSON.parse(resEl.textContent || '{}'); } catch (e) { resultCfg = {}; } }

  var branch = null;
  var seq = ['route'];
  var ans = {};
  var cur = 0;

  // Service mode: no Q1 router. The sequence is the DOM order of the rendered
  // steps (s0…sN, then gate, result); the result screen is driven by resultCfg.
  if (mode === 'service') {
    branch = 'service';
    seq = Array.prototype.map.call(card.querySelectorAll('.mfsq__step'), function (s) {
      return s.getAttribute('data-q');
    });
  }

  function numbered() { return seq.slice(0, seq.indexOf('result')); }

  function renderDots() {
    var n = numbered().length;
    var html = '';
    for (var i = 0; i < n; i++) html += '<span></span>';
    bar.innerHTML = html;
  }

  function show(i) {
    cur = i;
    var key = seq[i];
    Object.keys(stepEls).forEach(function (k) { stepEls[k].classList.toggle('is-on', k === key); });
    var nums = numbered();
    var dots = bar.querySelectorAll('span');
    dots.forEach(function (d, n) { d.classList.toggle('is-on', n <= i && i < nums.length); });
    if (key === 'result') count.textContent = qt('Your plan', 'Tu plan', 'Dein Plan');
    else if (key === 'gate') count.textContent = qt('Last step', 'Último paso', 'Letzter Schritt');
    else count.textContent = qt('Step', 'Paso', 'Schritt') + ' ' + (i + 1) + ' ' + qt('of', 'de', 'von') + ' ' + nums.length;
    back.classList.toggle('is-on', i > 0 && key !== 'result');
    if (key === 'look') renderLooks();
    if (key === 'gate' && gateSub) {
      gateSub.innerHTML = mode === 'service'
        ? (resultCfg.gateSub || qt('Your tailored plan and a free next step for your project.', 'Tu plan personalizado y un siguiente paso gratuito para tu proyecto.', 'Dein individueller Plan und ein kostenloser nächster Schritt für dein Projekt.'))
        : (GATE_SUB[branch] || GATE_SUB.cgi);
    }
  }

  function renderLooks() {
    var box = stepEls.look.querySelector('[data-looks]');
    var set = looks[ans.subject] || [];
    box.innerHTML = set.map(function (l) {
      return '<button class="mfsq__look" data-v="' + escapeHtml(l[0]) + '">'
        + '<span class="mfsq__look-img"><img src="' + escapeHtml(l[1]) + '" loading="lazy" alt="' + escapeHtml(l[0]) + '"></span>'
        + '<span class="mfsq__look-cap">' + l[0] + '</span></button>';
    }).join('');
  }

  function submitLead(name, email) {
    try {
      var fd = new FormData();
      fd.append('Name', name);
      fd.append('Email', email);
      fd.append('title', leadTitle);
      fd.append('tag', 'SEO, Quiz');
      if (mode === 'service') {
        fd.append('Service', resultCfg.service || '');
        seq.forEach(function (k) {
          if (k === 'gate' || k === 'result') return;
          var stepEl = stepEls[k];
          var label = (stepEl && stepEl.getAttribute('data-label')) || k;
          if (ans[k]) fd.append(label, ans[k]);
        });
      } else {
        fd.append('Branch', ans.route || '');
        fd.append('Service', (SVC[branch] && SVC[branch][branchType()]) || '');
        seq.forEach(function (k) {
          if (k === 'route' || k === 'gate' || k === 'result') return;
          if (ans[k]) fd.append(LABELS[k] || k, ans[k]);
        });
      }
      fetch(amoUrl, { method: 'POST', body: fd }).catch(function () {});
      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push({ event: 'lead_form', form_name: 'quiz', form_type: 'quiz', quiz_branch: branch });
    } catch (e) { /* ignore */ }
  }

  // The "type" answer that drives the recommended service, per branch.
  function branchType() {
    return branch === 'cgi' ? ans.subject : branch === 'web' ? ans.webtype : ans.crtype;
  }

  function result(name) {
    var r = stepEls.result;
    if (mode === 'service') {
      var sHead = resultCfg.head || (resultCfg.service
        ? qt('Custom ' + resultCfg.service, resultCfg.service + ' personalizado', 'Individuell: ' + resultCfg.service)
        : qt('Custom project', 'Proyecto personalizado', 'Individuelles Projekt'));
      var sRows = resultCfg.service
        ? '<div class="mfsq__result-row">' + qt('Recommended service:', 'Servicio recomendado:', 'Empfohlene Leistung:') + '&nbsp;<b>' + escapeHtml(resultCfg.service) + '</b></div>'
        : '';
      var sNote = resultCfg.note
        ? '<p style="font-size:13px;color:#5f5e5a;margin:16px 0 14px;line-height:1.55;">' + escapeHtml(resultCfg.note) + '</p>'
        : '';
      r.innerHTML =
        '<div class="mfsq__result">'
        + '<p class="mfsq__result-eyebrow">' + resultEyebrow(name) + '</p>'
        + '<p class="mfsq__result-head">' + escapeHtml(sHead) + '</p>'
        + sRows
        + sNote
        + '<button class="mfsq__submit js-modal-open" data-modal="book" type="button">' + qt('Book a quick call', 'Reserva una llamada rápida', 'Kurzes Gespräch buchen') + '</button>'
        + '</div>';
      return;
    }
    var type = branchType();
    var svc = (SVC[branch] && SVC[branch][type]) || '3D rendering';
    var goalKey = ans.goal || ans.webgoal || ans.crgoal;
    var goalFrag = (GOALT[branch] && GOALT[branch][goalKey]) || 'built to perform';
    var stageKey = ans.stage || ans.webstage || ans.crstage;
    var stageLine = (STAGEL[branch] && STAGEL[branch][stageKey]) || '';
    var head;
    if (branch === 'cgi') {
      var lookAdj = (ans.look || 'photoreal').toLowerCase().replace(/ *& */, ', ');
      head = lookAdj + ' ' + svc + ' — ' + goalFrag;
    } else {
      head = 'Custom ' + svc + ' — ' + goalFrag;
    }
    var rows = '<div class="mfsq__result-row">' + qt('Recommended service:', 'Servicio recomendado:', 'Empfohlene Leistung:') + '&nbsp;<b>' + escapeHtml(svc) + '</b></div>';
    if (stageLine) rows += '<div class="mfsq__result-row">' + escapeHtml(stageLine) + '</div>';
    if (branch === 'cgi' && ans.volume) rows += '<div class="mfsq__result-row">' + escapeHtml(VOLL[ans.volume] || '') + '</div>';
    var extra = branch === 'cgi'
      ? '<p style="font-size:13px;color:#5f5e5a;margin:14px 0 4px;line-height:1.55;">Need it as animation, a virtual tour or 360°? We do that too.</p>'
      : '';
    r.innerHTML =
      '<div class="mfsq__result">'
      + '<p class="mfsq__result-eyebrow">' + resultEyebrow(name) + '</p>'
      + '<p class="mfsq__result-head">' + escapeHtml(head) + '</p>'
      + rows
      + extra
      + '<p style="font-size:13px;color:#5f5e5a;margin:16px 0 14px;line-height:1.55;">' + CLOSING[branch] + '</p>'
      + '<button class="mfsq__submit js-modal-open" data-modal="book" type="button">' + qt('Book a quick call', 'Reserva una llamada rápida', 'Kurzes Gespräch buchen') + '</button>'
      + '</div>';
  }

  // Click on any option or look tile.
  card.querySelector('[data-steps]').addEventListener('click', function (e) {
    var b = e.target.closest('.mfsq__opt, .mfsq__look');
    if (!b) return;
    var key = seq[cur];
    if (key === 'route') {
      branch = b.getAttribute('data-branch');
      seq = SEQ[branch] || SEQ.cgi;
      ans = { route: b.getAttribute('data-v') };
      renderDots();
      show(1);
      return;
    }
    ans[key] = b.getAttribute('data-v');
    show(cur + 1);
  });

  card.querySelector('[data-reveal]').addEventListener('click', function () {
    var nameEl = card.querySelector('[data-name]');
    var emailEl = card.querySelector('[data-email]');
    var email = (emailEl.value || '').trim();
    if (!email || email.indexOf('@') < 1) {
      emailEl.focus();
      emailEl.style.borderColor = '#e24b4a';
      return;
    }
    var name = (nameEl.value || '').trim();
    submitLead(name, email);
    result(name);
    show(seq.indexOf('result'));
  });

  back.addEventListener('click', function () {
    if (cur <= 0) return;
    if (mode === 'router' && cur === 1) { // back to router resets the branch choice
      branch = null;
      seq = ['route'];
      renderDots();
      show(0);
      return;
    }
    show(cur - 1);
  });

  renderDots();
  show(0);
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initQuiz);
} else {
  initQuiz();
}
