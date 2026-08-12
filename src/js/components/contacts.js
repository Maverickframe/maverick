const contactsForms = document.querySelectorAll('.js-contacts-form');

// --- UTM capture --------------------------------------------------------------
// contacts.js is a STATIC import in bundle.js, so this module runs on every page
// load site-wide. We stash the acquisition UTM in a 90-day first-touch cookie the
// first time a pageview carries them, then a submit on any later page reads the
// URL first (last touch on that page), else this cookie. The 5 values are posted
// as utm_source/…/content and land in the matching HubSpot contact properties
// (forms/hubspot.php → mfs_hs_props reads them straight from $_POST).
const MFS_UTM_KEYS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];
const MFS_UTM_COOKIE = 'mfs_utm';

function mfsReadCookie(n) {
  return (document.cookie.match('(^|; )' + n + '=([^;]+)') || [])[2] || '';
}

function mfsUtmFromUrl() {
  const q = new URLSearchParams(location.search);
  const out = {};
  MFS_UTM_KEYS.forEach((k) => {
    const v = (q.get(k) || '').trim();
    if (v) out[k] = v.slice(0, 500);
  });
  return out;
}

function mfsUtmFromCookie() {
  try {
    const raw = mfsReadCookie(MFS_UTM_COOKIE);
    return raw ? JSON.parse(decodeURIComponent(raw)) : {};
  } catch (e) {
    return {};
  }
}

// First-touch: write only when this pageview carries utm AND no cookie exists yet,
// so the acquisition campaign wins over any later utm-tagged internal link.
(function mfsCaptureFirstTouchUtm() {
  try {
    if (mfsReadCookie(MFS_UTM_COOKIE)) return;
    const utm = mfsUtmFromUrl();
    if (!Object.keys(utm).length) return;
    const val = encodeURIComponent(JSON.stringify(utm));
    const exp = new Date(Date.now() + 90 * 864e5).toUTCString();
    document.cookie = `${MFS_UTM_COOKIE}=${val}; expires=${exp}; path=/; SameSite=Lax`;
  } catch (e) {}
})();

// Read HubSpot/GA attribution from cookies + URL and attach to the POST so the
// PHP handler can forward it to HubSpot (hutk, GA client_id, gclid, utm_*).
function mfsHsAttr(fd) {
  try {
    const ck = mfsReadCookie;
    const m = (ck('_ga') || '').match(/GA\d\.\d\.(\d+\.\d+)/);
    if (m) fd.set('ga_client_id', m[1]);
    const utk = ck('hubspotutk');
    if (utk) fd.set('hubspotutk', utk);
    const fromUrl = new URLSearchParams(location.search).get('gclid');
    const fromCk = (ck('_gcl_aw').match(/GCL\.\d+\.(.+)$/) || [])[1] || '';
    const gclid = fromUrl || fromCk;
    if (gclid) fd.set('gclid', gclid);
    // UTM: current URL wins (last touch on this page), else the first-touch cookie.
    const urlUtm = mfsUtmFromUrl();
    const ckUtm = mfsUtmFromCookie();
    MFS_UTM_KEYS.forEach((k) => {
      const v = urlUtm[k] || ckUtm[k] || '';
      if (v) fd.set(k, v);
    });
  } catch (e) {}
}

async function sendForm(contactsForm) {
  const formData = new FormData(contactsForm);
  const formBtn = contactsForm.querySelector('button');

  // Lead identity (mirrors the GA event naming) + attribution for HubSpot.
  const isDownload = !!contactsForm.getAttribute('data-link');
  const gaEvent = contactsForm.getAttribute('data-ga-event') || (isDownload ? 'download_catalog' : 'lead_form');
  const gaForm = contactsForm.getAttribute('data-ga-form') || (isDownload ? 'download_catalog' : 'contact');
  const gaType = contactsForm.getAttribute('data-ga-type') || (isDownload ? 'lead_magnet' : 'contact');
  formData.set('lead_event', gaEvent);
  formData.set('form_name', gaForm);
  formData.set('form_type', gaType);
  mfsHsAttr(formData);
  const formSuccess = contactsForm.querySelector('.contacts-form__success');

  const phoneInput = contactsForm.querySelector('input[type="tel"]');
  const emailInput = contactsForm.querySelector('input[type="email"]');

  formBtn.setAttribute('disabled', 'disabled');

  const response = await fetch(`${contacts.home_url}/wp-content/themes/maverickframe/forms/amo.php`, {
    method: 'POST',
    body: formData
  });

  const responseData = await response.text();

  formBtn.removeAttribute('disabled');

  if (responseData === 'Success') {
    window.dataLayer = window.dataLayer || [];

    // Enhanced Conversions: read the fields BEFORE reset() blanks them.
    // Phone only when it is already E.164 — Google drops anything else, and a
    // half-formatted number is worse than no number at all.
    const userData = {};
    const ecEmail = (emailInput?.value || '').trim().toLowerCase();
    if (ecEmail) userData.email = ecEmail;
    const ecPhone = (phoneInput?.value || '').replace(/[^\d+]/g, '');
    if (ecPhone.charAt(0) === '+') userData.phone_number = ecPhone;

    contactsForm.reset();

    phoneInput?.closest('label').classList.remove('error');
    emailInput.closest('label').classList.remove('error');

    // GA4 conversion event uses the per-form identity computed above
    // (lead_event / form_name / form_type), shared with the HubSpot payload.
    // user_data rides along for Google Ads Enhanced Conversions; GTM hashes it
    // (SHA-256) before it leaves the browser.
    window.dataLayer.push({ event: gaEvent, form_name: gaForm, form_type: gaType, user_data: userData });

    if (isDownload) {
      const link = document.createElement('a');

      link.setAttribute('href', contactsForm.getAttribute('data-link'));
      link.setAttribute('target', '_blank');
      link.setAttribute('download', 'download');
      link.click();
    }

    const formContainer = contactsForm.closest('.js-contacts-form-container');

    if (formContainer) {
      formContainer.classList.add('is-success');
    }

    if (formSuccess) {
      formSuccess.classList.add('is-active');
    }

    setTimeout(() => {
      if (formContainer) {
        formContainer.classList.remove('is-success');
      }

      if (formSuccess) {
        formSuccess.classList.remove('is-active');
      }
    }, 20000);

    return;
  }

  if (responseData === 'Error') {
    if ((phoneInput ? !phoneInput.value : true) && !emailInput.value) {
      phoneInput?.closest('label').classList.add('error');
      emailInput.closest('label').classList.add('error');
    }
  }
}

if (contactsForms.length > 0) {
  Array.from(contactsForms).forEach((contactsForm) => {
    const inputs = contactsForm.querySelectorAll('input');

    [...inputs].forEach((input) => input.addEventListener('focus', (e) => e.target.closest('label').classList.contains('error') && e.target.closest('label').classList.remove('error')));
  });
}

window.addEventListener('submit', (e) => {
  e.preventDefault();
  if (e.target.classList.contains('js-contacts-form')) {
    sendForm(e.target);
  }
});