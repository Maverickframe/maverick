// Fancybox CSS travels with this lazy chunk (was globally glued into base.scss →
// every page's bundle carried ~32 KB of gallery lightbox CSS it never used). Now
// it loads only when this chunk does — the gallery page and any [data-fancybox].
import '@fancyapps/ui/dist/fancybox/fancybox.css';
import { Fancybox } from '@fancyapps/ui/dist/fancybox/';

const tabs = document.querySelectorAll('.js-gallery-tab-btn');
const contents = document.querySelectorAll('.js-gallery-tab-content');

const select = document.querySelector('.js-gallery-mobile');
const selectTitle = document.querySelector('.js-gallery-select-title');

function activateTab(tabId, updateHash = true) {
  tabs.forEach((t) => t.classList.remove('active'));
  contents.forEach((c) => c.classList.remove('active'));

  document.querySelectorAll('.js-category.open').forEach((cat) => cat.classList.remove('open'));

  const btn = document.querySelector(`[data-tab="${tabId}"]`);
  const parent = btn?.closest('.js-category');

  if (btn) btn.classList.add('active');
  if (parent) parent.classList.add('open');

  const el = document.getElementById(tabId);
  if (el) el.classList.add('active');

  if (select && selectTitle) {
    const option = select.querySelector(`option[value="${tabId}"]`);
    if (option) {
      selectTitle.textContent = option.textContent;
      select.value = tabId;
    }
  }

  if (tabId && updateHash) window.history.replaceState(null, '', `#${tabId}`);
}

if (tabs.length > 0 && contents.length > 0) {
  Fancybox.bind('[data-fancybox]', {
    Carousel: {
      Thumbs: false
    }
  });

  if (select) {
    select.addEventListener('change', (e) => {
      activateTab(e.target.value);
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  tabs.forEach((tab) => {
    tab.addEventListener('click', (e) => {
      const parent = tab.closest('.js-category');

      if (tab.classList.contains('js-category-btn')) {
        const hasSub = parent && parent.querySelector('ul');
        if (hasSub) {
          e.preventDefault();
          parent.classList.toggle('open');
          return;
        }
      }

      activateTab(tab.dataset.tab);
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  });

  const hash = window.location.hash.replace('#', '');
  if (hash) {
    activateTab(hash, false);
  } else {
    activateTab('all', false);
  }
}