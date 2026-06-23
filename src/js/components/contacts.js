const contactsForms = document.querySelectorAll('.js-contacts-form');

// Read HubSpot/GA attribution from cookies + URL and attach to the POST so the
// PHP handler can forward it to HubSpot (hutk, GA client_id, gclid).
function mfsHsAttr(fd) {
  try {
    const ck = (n) => (document.cookie.match('(^|; )' + n + '=([^;]+)') || [])[2] || '';
    const m = (ck('_ga') || '').match(/GA\d\.\d\.(\d+\.\d+)/);
    if (m) fd.set('ga_client_id', m[1]);
    const utk = ck('hubspotutk');
    if (utk) fd.set('hubspotutk', utk);
    const fromUrl = new URLSearchParams(location.search).get('gclid');
    const fromCk = (ck('_gcl_aw').match(/GCL\.\d+\.(.+)$/) || [])[1] || '';
    const gclid = fromUrl || fromCk;
    if (gclid) fd.set('gclid', gclid);
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

    contactsForm.reset();

    phoneInput?.closest('label').classList.remove('error');
    emailInput.closest('label').classList.remove('error');

    // GA4 conversion event uses the per-form identity computed above
    // (lead_event / form_name / form_type), shared with the HubSpot payload.
    window.dataLayer.push({ event: gaEvent, form_name: gaForm, form_type: gaType });

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