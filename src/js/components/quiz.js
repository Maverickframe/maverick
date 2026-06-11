// Lead Quiz — stepper, contact gate, personalised result. The lead + all
// answers POST to forms/amo.php (same handler as the site forms).

function escapeHtml(s) {
  return String(s).replace(/[&<>"']/g, function (c) {
    return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
  });
}

function initQuiz() {
  var card = document.querySelector('.js-mfsq');
  if (!card) return;

  var qs = ['what', 'look', 'goal', 'stage', 'volume'];
  var ans = {};
  var cur = 0;

  var steps = card.querySelectorAll('.mfsq__step');
  var dots = card.querySelectorAll('.mfsq__bar span');
  var count = card.querySelector('[data-count]');
  var back = card.querySelector('[data-back]');
  var amoUrl = card.getAttribute('data-amo');

  var svc = { Exterior: '3D exterior rendering', Interior: '3D interior rendering', Product: 'product CGI', 'Development / masterplan': 'masterplan & aerial CGI' };
  var goalT = { 'Sell faster': 'built to sell', 'Win approvals': 'tuned for approvals', 'Market & advertise': 'campaign-ready', 'Impress investors': 'made to win investors' };
  var stageL = { 'Just an idea': 'We start from your references and sketches.', 'In design': 'Send your CAD or design files and we begin.', 'Files ready': 'Your files are ready — we can start fast.' };
  var volL = { 'One project': 'One-off project pricing.', 'Ongoing stream': 'Subscription saves up to 30% on ongoing volume.' };

  function show(i) {
    cur = i;
    steps.forEach(function (s, n) { s.classList.toggle('is-on', n === i); });
    dots.forEach(function (d, n) { d.classList.toggle('is-on', n <= Math.min(i, 4)); });
    count.textContent = i < 5 ? ('Step ' + (i + 1) + ' of 5') : (i === 5 ? 'Last step' : 'Your plan');
    back.classList.toggle('is-on', i > 0 && i < 6);
  }

  function submitLead(name, email) {
    try {
      var fd = new FormData();
      fd.append('Name', name);
      fd.append('Email', email);
      fd.append('title', 'Homepage / Quiz');
      fd.append('tag', 'SEO, Quiz');
      fd.append('What', ans.what || '');
      fd.append('Look', ans.look || '');
      fd.append('Goal', ans.goal || '');
      fd.append('Stage', ans.stage || '');
      fd.append('Volume', ans.volume || '');
      fetch(amoUrl, { method: 'POST', body: fd }).catch(function () {});
      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push({ event: 'generate_lead', form_name: 'quiz', form_type: 'quiz' });
    } catch (e) { /* ignore */ }
  }

  function result(name) {
    var r = card.querySelector('.mfsq__step[data-q="result"]');
    var lookAdj = (ans.look || 'photoreal').toLowerCase().replace(/ *& */, ', ');
    var head = lookAdj + ' ' + (svc[ans.what] || '3D rendering') + ' — ' + (goalT[ans.goal] || 'built to perform');
    r.innerHTML =
      '<div class="mfsq__result">'
      + '<p class="mfsq__result-eyebrow">' + (name ? escapeHtml(name) + ', your' : 'Your') + ' recommended approach</p>'
      + '<p class="mfsq__result-head">' + escapeHtml(head) + '</p>'
      + '<div class="mfsq__result-row">Recommended service:&nbsp;<b>' + escapeHtml(svc[ans.what] || '3D rendering') + '</b></div>'
      + '<div class="mfsq__result-row">' + escapeHtml(stageL[ans.stage] || '') + '</div>'
      + '<div class="mfsq__result-row">' + escapeHtml(volL[ans.volume] || '') + '</div>'
      + '<p style="font-size:13px;color:#5f5e5a;margin:16px 0 14px;line-height:1.55;">We’ll email your moodboard and a free test render shortly.</p>'
      + '<button class="mfsq__submit js-modal-open" data-modal="book" type="button">Book a quick call</button>'
      + '</div>';
  }

  card.querySelector('[data-steps]').addEventListener('click', function (e) {
    var b = e.target.closest('.mfsq__opt, .mfsq__look');
    if (!b) return;
    ans[qs[cur]] = b.getAttribute('data-v');
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
    show(6);
  });

  back.addEventListener('click', function () { if (cur > 0) show(cur - 1); });

  show(0);
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initQuiz);
} else {
  initQuiz();
}
