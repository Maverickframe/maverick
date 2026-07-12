// Contacts form: custom country-code dropdown, international phone combine,
// message counter, and full client-side validation for every field.
// The shared submit handler lives in contacts.js (window 'submit', bubbling).
// We validate in the CAPTURE phase so we can block that handler when invalid.

const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
const PHONE_MIN = 6; // national-part digit sanity range (not per-country exact)
const PHONE_MAX = 15; // E.164 caps the whole number at 15 digits
const MSG_MAX = 1000;
const FILE_MAX_BYTES = 15 * 1024 * 1024; // 15 MB
const FILE_EXT = ['pdf', 'jpg', 'jpeg', 'png', 'zip'];

const digitsOf = (s) => (s || '').replace(/\D/g, '');

function fieldOf(input, selector) {
  return input ? input.closest(selector || '.cform__input') : null;
}
function setError(field, on) {
  if (field) field.classList.toggle('error', !!on);
}

// ---- country dropdown + phone -------------------------------------------
function initPhone(wrap) {
  const country = wrap.querySelector('.js-country');
  const toggle = wrap.querySelector('.js-country-toggle');
  const search = wrap.querySelector('.js-country-search');
  const opts = Array.prototype.slice.call(wrap.querySelectorAll('.cform__country-opt'));
  const flagEl = wrap.querySelector('.cform__country-flag');
  const codeEl = wrap.querySelector('.cform__country-code');
  const num = wrap.querySelector('.cform__phone-num');
  const hidden = wrap.querySelector('input[name="Phone"]');
  const field = num && num.closest('.cform__phone-field');

  if (!country || !toggle || !num || !hidden) return;

  const clean = () => {
    let v = num.value.replace(/[^\d\s()+-]/g, '');
    if (digitsOf(v).length > PHONE_MAX) {
      let count = 0;
      v = v.replace(/\d/g, (d) => (++count <= PHONE_MAX ? d : ''));
    }
    if (v !== num.value) num.value = v;
  };
  const sync = () => {
    const n = num.value.trim();
    hidden.value = n ? `${country.dataset.dial} ${n}` : '';
  };

  const open = () => {
    country.classList.add('is-open');
    toggle.setAttribute('aria-expanded', 'true');
    if (search) {
      search.value = '';
      filter('');
      setTimeout(() => search.focus(), 0);
    }
  };
  const close = () => {
    country.classList.remove('is-open');
    toggle.setAttribute('aria-expanded', 'false');
  };
  const norm = (s) => (s || '').normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
  const filter = (q) => {
    q = norm(q.trim());
    opts.forEach((o) => {
      const match = !q || norm(o.dataset.name).indexOf(q) > -1 || o.dataset.dial.indexOf(q) > -1;
      o.hidden = !match;
    });
  };

  toggle.addEventListener('click', (e) => {
    e.stopPropagation();
    country.classList.contains('is-open') ? close() : open();
  });
  if (search) {
    search.addEventListener('input', () => filter(search.value));
    search.addEventListener('click', (e) => e.stopPropagation());
  }
  opts.forEach((o) => {
    o.addEventListener('click', () => {
      country.dataset.dial = o.dataset.dial;
      flagEl.textContent = o.dataset.flag;
      codeEl.textContent = o.dataset.dial;
      sync();
      close();
      num.focus();
    });
  });
  document.addEventListener('click', (e) => {
    if (!country.contains(e.target)) close();
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') close();
  });

  num.addEventListener('input', () => {
    clean();
    if (phoneValid(num)) setError(field, false);
    sync();
  });
  num.addEventListener('blur', () => {
    clean();
    sync();
    setError(field, !phoneValid(num));
  });
}

function phoneValid(num) {
  const len = digitsOf(num.value).length;
  return len === 0 || (len >= PHONE_MIN && len <= PHONE_MAX); // optional field
}

// ---- form-level validation ----------------------------------------------
function initValidation(form) {
  const name = form.querySelector('input[name="Name"]');
  const email = form.querySelector('input[name="Email"]');
  const phone = form.querySelector('.cform__phone-num');
  const msg = form.querySelector('.js-msg');
  const file = form.querySelector('input[name="File"]');
  const consent = form.querySelector('input[name="PrivacyConsent"]');
  const fileError = form.querySelector('.js-file-error');
  const fileNameEl = form.querySelector('.js-file-name');

  const lang = document.documentElement.lang || '';
  const es = lang.indexOf('es') === 0;
  const t = (en, sp) => (es ? sp : en);

  const fileCheck = () => {
    if (!file || !file.files || !file.files.length) {
      if (fileNameEl) fileNameEl.textContent = '';
      return true;
    }
    const f = file.files[0];
    const ext = (f.name.split('.').pop() || '').toLowerCase();
    if (fileNameEl) fileNameEl.textContent = f.name;
    if (FILE_EXT.indexOf(ext) === -1) {
      if (fileError) fileError.textContent = t('Unsupported format. Use PDF, JPG, PNG or ZIP.', 'Formato no admitido. Usa PDF, JPG, PNG o ZIP.');
      return false;
    }
    if (f.size > FILE_MAX_BYTES) {
      if (fileError) fileError.textContent = t('File is too large. Max 15 MB.', 'El archivo es demasiado grande. Máximo 15 MB.');
      return false;
    }
    return true;
  };

  // live clearing
  if (name) name.addEventListener('input', () => setError(fieldOf(name), name.value.trim().length < 2));
  if (email) email.addEventListener('input', () => setError(fieldOf(email), !EMAIL_RE.test(email.value.trim())));
  if (consent) consent.addEventListener('change', () => setError(fieldOf(consent, '.cform__check'), !consent.checked));
  if (file) file.addEventListener('change', () => setError(fieldOf(file, '.cform__upload-row'), !fileCheck()));

  const validate = () => {
    let ok = true;
    let first = null;
    const fail = (field, el) => {
      setError(field, true);
      ok = false;
      if (!first) first = el;
    };

    // Name is validated only when the form actually has a Name field. Minimal
    // landing-page forms (e.g. email-only /lp/ free-render) omit it on purpose;
    // treating an absent field as a failure silently blocked their submit here.
    if (name && name.value.trim().length < 2) fail(fieldOf(name), name);
    else setError(fieldOf(name), false);

    if (!email || !EMAIL_RE.test(email.value.trim())) fail(fieldOf(email), email);
    else setError(fieldOf(email), false);

    if (phone && !phoneValid(phone)) fail(phone.closest('.cform__phone-field'), phone);
    else if (phone) setError(phone.closest('.cform__phone-field'), false);

    if (msg && msg.value.length > MSG_MAX) {
      msg.value = msg.value.slice(0, MSG_MAX);
    }

    if (!fileCheck()) fail(fieldOf(file, '.cform__upload-row'), file);
    else setError(fieldOf(file, '.cform__upload-row'), false);

    if (consent && !consent.checked) fail(fieldOf(consent, '.cform__check'), consent);
    else if (consent) setError(fieldOf(consent, '.cform__check'), false);

    if (first && first.focus) first.focus();
    return ok;
  };

  // capture phase → runs before the bubbling submit handler in contacts.js
  form.addEventListener(
    'submit',
    (e) => {
      if (!validate()) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
      }
    },
    true
  );
}

document.querySelectorAll('.cform__phone').forEach(initPhone);
document.querySelectorAll('.js-contacts-form').forEach(initValidation);

// Message field: live character counter against the native maxlength.
document.querySelectorAll('.js-msg').forEach((ta) => {
  const max = +(ta.getAttribute('maxlength') || MSG_MAX);
  const counter = ta.parentNode.querySelector('.js-msg-count');
  if (!counter) return;
  const update = () => {
    if (ta.value.length > max) ta.value = ta.value.slice(0, max);
    counter.textContent = `${ta.value.length} / ${max}`;
    counter.classList.toggle('is-max', ta.value.length >= max);
  };
  ta.addEventListener('input', update);
  update();
});
