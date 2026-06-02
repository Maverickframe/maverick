const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
  navigator.userAgent
);

function closePanel(btn, item) {
  const panel = btn.nextElementSibling;
  const closestItem = btn.closest(item);

  if (panel.style.maxHeight) {
    panel.style.maxHeight = null;
  }
  closestItem.classList.remove('active');
}

function faqSpoilers(btns, item, closeOthers = false) {
  [...btns].forEach((btn) => btn.addEventListener('click', (e) => {
    e.preventDefault();

    const closestItem = e.target.closest(item);

    if (!closestItem) {
      return;
    }

    if (closeOthers) {
      [...btns]
        .filter((spoiler) => spoiler !== btn)
        .forEach((spoiler) => closePanel(spoiler, item));
    }

    closestItem.classList.toggle('active');

    const panel = btn.nextElementSibling;

    if (panel.style.maxHeight) {
      panel.style.maxHeight = null;
    } else {
      panel.style.maxHeight = `${panel.scrollHeight}px`;
    }
  }));
}

const faq = document.querySelector('.js-faq');

if (faq) {
  const faqBtns = faq.querySelectorAll('.js-faq-btn');

  faqSpoilers(faqBtns, '.js-faq-item');
}

const footer = document.querySelector('.js-footer-acc');

if (footer) {
  const footerBtns = footer.querySelectorAll('.js-footer-acc-btn');

  faqSpoilers(footerBtns, '.js-footer-acc-item', isMobile);
}