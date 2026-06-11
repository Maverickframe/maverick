// When a button that opens a shared form modal carries a data-offer (e.g. the
// pricing tariff a visitor clicked — Per Project / Hourly / Subscription /
// Undecided), reflect it in that form's hidden `title` field so the amoCRM deal
// name shows which option the lead came from. Buttons without data-offer reset
// the title back to its base value.

function tagOfferOnModalOpen(e) {
  const trigger = e.target.closest('.js-modal-open');
  if (!trigger) return;

  const modalName = trigger.getAttribute('data-modal');
  if (!modalName) return;

  const modal = document.querySelector('.js-modal[data-modal="' + modalName + '"]');
  const form = modal && modal.querySelector('form.js-contacts-form');
  if (!form) return;

  const titleField = form.querySelector('input[name="title"]');
  if (!titleField) return;

  // Remember the original title once, then rebuild from it each time so repeated
  // clicks never stack suffixes.
  if (titleField.dataset.base === undefined) {
    titleField.dataset.base = titleField.value;
  }

  const offer = trigger.getAttribute('data-offer');
  titleField.value = titleField.dataset.base + (offer ? ' — Estimate: ' + offer : '');
}

document.addEventListener('click', tagOfferOnModalOpen, true);
