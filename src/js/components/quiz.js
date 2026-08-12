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
// Keys are EN answer values (data-v) — never translate them; only the values
// are localized via qt(), since they surface on the result screen.
var SVC = {
  cgi: {
    Exterior: qt('3D exterior rendering', 'renderizado 3D de exteriores', '3D-Außenvisualisierung'),
    Interior: qt('3D interior rendering', 'renderizado 3D de interiores', '3D-Innenvisualisierung'),
    Product: qt('product & furniture CGI', 'CGI de producto y mobiliario', 'Produkt- & Möbel-CGI'),
    Vehicle: qt('yacht, car & aircraft visualization', 'visualización de yates, coches y aviones', 'Yacht-, Auto- & Flugzeug-Visualisierung'),
    Development: qt('masterplan & aerial CGI', 'CGI de masterplan y aéreo', 'Masterplan- & Luftbild-CGI')
  },
  web: {
    Website: qt('web design', 'diseño web', 'Webdesign'),
    'Landing page': qt('landing page design', 'diseño de landing page', 'Landingpage-Design'),
    'Mobile app': qt('mobile app design', 'diseño de app móvil', 'Mobile-App-Design'),
    'UI/UX redesign': qt('UI/UX design', 'diseño UI/UX', 'UI/UX-Design')
  },
  creative: {
    'Brand identity': qt('branding & identity', 'branding e identidad', 'Branding & Identität'),
    'Social media content': qt('social media design', 'diseño para redes sociales', 'Social-Media-Design'),
    'Presentation / pitch deck': qt('presentation design', 'diseño de presentaciones', 'Präsentationsdesign'),
    'FOOH / CGI ad': qt('FOOH CGI advertising', 'publicidad FOOH CGI', 'FOOH-CGI-Werbung')
  }
};
var GOALT = {
  cgi: {
    'Sell faster': qt('built to sell', 'creado para vender', 'gemacht zum Verkaufen'),
    'Win approvals': qt('tuned for approvals', 'optimizado para aprobaciones', 'auf Genehmigungen ausgelegt'),
    'Market & advertise': qt('campaign-ready', 'listo para campañas', 'kampagnenbereit'),
    'Impress investors': qt('made to win investors', 'hecho para conquistar inversores', 'gemacht, um Investoren zu überzeugen')
  },
  web: {
    'Launch something new': qt('built to launch', 'creado para lanzar', 'gemacht für den Launch'),
    'Redesign & modernize': qt('modern & refreshed', 'moderno y renovado', 'modern & frisch'),
    'Increase conversions': qt('conversion-focused', 'enfocado en conversiones', 'conversion-orientiert'),
    'Impress investors': qt('investor-ready', 'listo para inversores', 'investorenbereit')
  },
  creative: {
    'Launch a brand': qt('built to launch', 'creado para lanzar', 'gemacht für den Launch'),
    'Refresh our look': qt('a fresh, modern look', 'un look fresco y moderno', 'ein frischer, moderner Look'),
    'Drive engagement': qt('made to engage', 'hecho para enganchar', 'gemacht, um zu begeistern'),
    'Win investors': qt('investor-ready', 'listo para inversores', 'investorenbereit')
  }
};
var STAGEL = {
  cgi: {
    'Just an idea': qt('We start from your references and sketches.', 'Empezamos con tus referencias y bocetos.', 'Wir starten mit deinen Referenzen und Skizzen.'),
    'In design': qt('Send your CAD or design files and we begin.', 'Envíanos tus archivos CAD o de diseño y empezamos.', 'Schick uns deine CAD- oder Designdateien und wir legen los.'),
    'Files ready': qt('Your files are ready — we can start fast.', 'Tus archivos están listos — podemos empezar rápido.', 'Deine Dateien sind bereit — wir können schnell starten.')
  },
  web: {
    'Just an idea': qt('We start from your goals and references.', 'Empezamos con tus objetivos y referencias.', 'Wir starten mit deinen Zielen und Referenzen.'),
    'Brand & content ready': qt('Brand & content ready — we move straight to design.', 'Marca y contenido listos — pasamos directo al diseño.', 'Marke & Inhalte bereit — wir gehen direkt ins Design.'),
    'Have a live site': qt('We audit your current site and improve from there.', 'Auditamos tu sitio actual y mejoramos desde ahí.', 'Wir auditieren deine aktuelle Website und verbessern von dort.')
  },
  creative: {
    'Starting from scratch': qt('We start from a blank canvas and your vision.', 'Empezamos desde cero con tu visión.', 'Wir starten mit einem leeren Blatt und deiner Vision.'),
    'Have some assets': qt('We build on your existing assets.', 'Construimos sobre tus recursos existentes.', 'Wir bauen auf deinen vorhandenen Assets auf.'),
    'Rebranding existing': qt('We evolve your current brand.', 'Evolucionamos tu marca actual.', 'Wir entwickeln deine aktuelle Marke weiter.')
  }
};
var VOLL = {
  'One project': qt('One-off project pricing.', 'Precio por proyecto único.', 'Preis für ein Einzelprojekt.'),
  'Ongoing stream': qt('Subscription saves up to 30% on ongoing volume.', 'La suscripción ahorra hasta un 30% en volumen continuo.', 'Abo spart bis zu 30% bei laufendem Volumen.')
};

// Per-branch gate copy + result closing line.
var GATE_SUB = {
  cgi: qt('Your tailored visual plan, a moodboard in your style, and one free test render of your project.', 'Tu plan visual personalizado, un moodboard en tu estilo y un test render gratuito de tu proyecto.', 'Dein individueller Visual-Plan, ein Moodboard in deinem Stil und ein kostenloser Testrender deines Projekts.'),
  web: qt('A tailored plan for your project and a free design concept to get started.', 'Un plan personalizado para tu proyecto y un concepto de diseño gratuito para empezar.', 'Ein individueller Plan für dein Projekt und ein kostenloses Designkonzept zum Start.'),
  creative: qt('A tailored creative plan and a free concept to get started.', 'Un plan creativo personalizado y un concepto gratuito para empezar.', 'Ein individueller Kreativplan und ein kostenloses Konzept zum Start.')
};
var CLOSING = {
  cgi: qt('We’ll email your moodboard and a free test render shortly.', 'Te enviaremos pronto tu moodboard y un test render gratuito por correo.', 'Wir senden dir in Kürze dein Moodboard und einen kostenlosen Testrender per E-Mail.'),
  web: qt('We’ll email your tailored plan and a free design concept shortly.', 'Te enviaremos pronto tu plan personalizado y un concepto de diseño gratuito por correo.', 'Wir senden dir in Kürze deinen individuellen Plan und ein kostenloses Designkonzept per E-Mail.'),
  creative: qt('We’ll email your tailored plan and a free concept shortly.', 'Te enviaremos pronto tu plan personalizado y un concepto gratuito por correo.', 'Wir senden dir in Kürze deinen individuellen Plan und ein kostenloses Konzept per E-Mail.')
};
// Look-tile adjective for the CGI result head. Keyed by EN data-v (kept EN);
// value localized so the head reads naturally in each language.
var LOOKADJ = {
  'Bright & photoreal': qt('bright, photoreal', 'brillante, fotorrealista', 'hell, fotorealistisch'),
  'Warm & inviting': qt('warm, inviting', 'cálido, acogedor', 'warm, einladend'),
  'Moody & cinematic': qt('moody, cinematic', 'atmosférico, cinematográfico', 'stimmungsvoll, filmisch'),
  'Clean & minimal': qt('clean, minimal', 'limpio, minimalista', 'klar, minimalistisch')
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
    // Router mode before a branch is picked: seq is just ['route'] (no 'result'),
    // so nums.length is 0 — show "Step 1" without the bogus "of 0".
    else if (nums.length === 0) count.textContent = qt('Step', 'Paso', 'Schritt') + ' ' + (i + 1);
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
      // l[0] = EN value for data-v (CRM), l[1] = image, l[2] = localized caption.
      var cap = l[2] || l[0];
      return '<button class="mfsq__look" data-v="' + escapeHtml(l[0]) + '">'
        + '<span class="mfsq__look-img"><img src="' + escapeHtml(l[1]) + '" loading="lazy" alt="' + escapeHtml(cap) + '"></span>'
        + '<span class="mfsq__look-cap">' + escapeHtml(cap) + '</span></button>';
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
      fd.append('lead_event', 'lead_form');
      fd.append('form_name', 'quiz');
      fd.append('form_type', 'quiz');
      (function (f) {
        try {
          var ck = function (n) { return (document.cookie.match('(^|; )' + n + '=([^;]+)') || [])[2] || ''; };
          var m = (ck('_ga') || '').match(/GA\d\.\d\.(\d+\.\d+)/);
          if (m) f.set('ga_client_id', m[1]);
          var utk = ck('hubspotutk'); if (utk) f.set('hubspotutk', utk);
          var gc = new URLSearchParams(location.search).get('gclid') || ((ck('_gcl_aw').match(/GCL\.\d+\.(.+)$/) || [])[1] || '');
          if (gc) f.set('gclid', gc);
        } catch (e) {}
      })(fd);
      fetch(amoUrl, { method: 'POST', body: fd }).catch(function () {});
      window.dataLayer = window.dataLayer || [];
      // user_data feeds Google Ads Enhanced Conversions; GTM hashes it in-browser.
      window.dataLayer.push({
        event: 'lead_form', form_name: 'quiz', form_type: 'quiz', quiz_branch: branch,
        user_data: email ? { email: String(email).trim().toLowerCase() } : {}
      });
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
    var svc = (SVC[branch] && SVC[branch][type]) || qt('3D rendering', 'renderizado 3D', '3D-Rendering');
    var goalKey = ans.goal || ans.webgoal || ans.crgoal;
    var goalFrag = (GOALT[branch] && GOALT[branch][goalKey]) || qt('built to perform', 'creado para rendir', 'gemacht für Performance');
    var stageKey = ans.stage || ans.webstage || ans.crstage;
    var stageLine = (STAGEL[branch] && STAGEL[branch][stageKey]) || '';
    var head;
    if (branch === 'cgi') {
      // Adjective comes from the (EN) data-v answer; LOOKADJ localizes it, with a
      // lowercased fallback for any unmapped value.
      var lookAdj = LOOKADJ[ans.look] || (ans.look || qt('photoreal', 'fotorrealista', 'fotorealistisch')).toLowerCase().replace(/ *& */, ', ');
      head = lookAdj + ' ' + svc + ' — ' + goalFrag;
    } else {
      head = qt('Custom ', 'Personalizado: ', 'Individuell: ') + svc + ' — ' + goalFrag;
    }
    var rows = '<div class="mfsq__result-row">' + qt('Recommended service:', 'Servicio recomendado:', 'Empfohlene Leistung:') + '&nbsp;<b>' + escapeHtml(svc) + '</b></div>';
    if (stageLine) rows += '<div class="mfsq__result-row">' + escapeHtml(stageLine) + '</div>';
    if (branch === 'cgi' && ans.volume) rows += '<div class="mfsq__result-row">' + escapeHtml(VOLL[ans.volume] || '') + '</div>';
    var extra = branch === 'cgi'
      ? '<p style="font-size:13px;color:#5f5e5a;margin:14px 0 4px;line-height:1.55;">' + qt('Need it as animation, a virtual tour or 360°? We do that too.', '¿Lo necesitas como animación, tour virtual o 360°? También lo hacemos.', 'Brauchst du es als Animation, virtuelle Tour oder 360°? Machen wir auch.') + '</p>'
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
