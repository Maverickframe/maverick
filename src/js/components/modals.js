import { initSnap } from './scroll-snap';
import { lockUI, unlockUI } from './uiManager';

const servicesData = Object.create(null);

document.querySelectorAll(
  'script[type="application/json"]'
).forEach((el) => {
  try {
    if (!el.id) return;
    servicesData[el.id] = JSON.parse(el.textContent || 'null');
  } catch (err) {
    // ignore invalid JSON blobs
  }
});

function renderNumber(item = {}, i = 1) {
  return `
    <div class="modal-what-we-do__number num-${i}">
      <div class="modal-what-we-do__number-value">
        ${item.number || ''}
      </div>
      <div class="modal-what-we-do__number-title">
        ${item.title || ''}
      </div>
    </div>
  `;
}

function buildStatsCards(data) {
  const media = data.media || [];
  const numbers = data.numbers || [];

  const cards = [];

  if (media[0]) {
    cards.push(`
      <div class="modal-what-we-do__media">
        ${media[0]}
      </div>
    `);
  }

  numbers.forEach((item, i) => {
    cards.push(renderNumber(item, i + 1));
  });

  media.slice(1).forEach((item) => {
    cards.push(`
      <div class="modal-what-we-do__media">
        ${item}
      </div>
    `);
  });

  return cards;
}

function renderStatsGrid(data) {
  const cards = buildStatsCards(data);
  if (!cards.length) return '';
  return cards.join('');
}

// Mobile stats carousel — migrated Splide → CSS scroll-snap (.mfs-snap). The markup
// is injected after page load, so setModalContent calls initSnap() on it explicitly
// (the bundle's auto-run only sees .mfs-snap present at chunk-eval time).
function renderStatsSlider(data) {
  const cards = buildStatsCards(data);
  if (!cards.length) return '';

  const slides = cards.map((cardHtml) => `
    <li class="mfs-snap__item">
      ${cardHtml}
    </li>
  `);

  return `
    <div class="modal-what-we-do__stats-slider js-what-we-do-stats-slider mfs-snap">
      <ul class="mfs-snap__track">
        ${slides.join('')}
      </ul>
      <div class="mfs-snap__dots"></div>
    </div>
  `;
}

function renderStats(data) {
  if (!data.images?.length && !data.numbers?.length) {
    return '';
  }

  return `
    <div class="modal-what-we-do__stats">
      <div class="modal-what-we-do__stats-desktop">
        ${renderStatsGrid(data)}
      </div>

      <div class="modal-what-we-do__stats-mobile">
        ${renderStatsSlider(data)}
      </div>
    </div>
  `;
}

// Products marquee — migrated Splide + AutoScroll → pure-CSS .mfs-marquee. The track
// is rendered twice (the second pass aria-hidden) so translateX(-50%) loops seamlessly;
// each item owns its trailing gap via margin (see modals.scss). Zero JS, no library.
function renderProductCard(product, clone = false) {
  const overlay = (product.title || product.location || product.client || product.year) ? `
    <div class="modal-design-services__product-overlay">

      <div class="modal-design-services__product-overlay-row modal-design-services__product-overlay-row_header">
        <h3 class="modal-design-services__product-overlay-title">
          ${product.title || ''}
        </h3>
      </div>

      ${product.location
    ? `<div class="modal-design-services__product-overlay-row">
            <span>Location:</span> ${product.location}
           </div>`
    : ''}

      ${product.client || product.year
    ? `<div class="modal-design-services__product-overlay-row">
            ${product.client ? `<div><span>Client:</span> ${product.client}</div>` : ''}
            ${product.year || ''}
           </div>`
    : ''}

    </div>
  ` : '';

  const openTag = product.link
    ? `<a class="modal-design-services__product" href="${product.link}"${clone ? ' tabindex="-1"' : ''}>`
    : '<div class="modal-design-services__product">';
  const closeTag = product.link ? '</a>' : '</div>';

  return `
    <li class="mfs-marquee__item"${clone ? ' aria-hidden="true"' : ''}>
      ${openTag}
        ${product.media || ''}
        ${overlay}
      ${closeTag}
    </li>
  `;
}

function renderProducts(products = []) {
  if (!products.length) return '';

  const lane = products.map((p) => renderProductCard(p, false)).join('');
  const clone = products.map((p) => renderProductCard(p, true)).join('');

  return `
    <div class="modal-design-services__products">
      <div class="js-design-services-slider mfs-marquee">
        <ul class="mfs-marquee__track">
          ${lane}
          ${clone}
        </ul>
      </div>
    </div>
  `;
}

function renderModal(data) {
  const hasExploreLink = Boolean(data.case_url);
  const bookBtnClass = hasExploreLink ? 'btn-secondary-black' : 'btn-main fill';

  return `
    <div class="modal-design-services__main-info">
      <h2 class="modal__title">${data.title || ''}</h2>
      <p class="modal__desc">${data.desc || ''}</p>

      <div class="modal__btns">
        ${hasExploreLink ? `
          <a class="btn-main" href="${data.case_url}" target="_blank">
            ${(window.MFS_I18N && window.MFS_I18N.exploreService) || 'Explore service'}
          </a>
        ` : ''}

        <button class="${bookBtnClass} js-modal-open" data-modal="book" type="button">
          ${(window.MFS_I18N && window.MFS_I18N.bookACall) || 'Book a call'}
        </button>
      </div>
    </div>

    ${
  data.how_items?.length
    ? `
          <div class="modal-design-services__how">
            ${data.how_title ? `<h3 class="modal-design-services__how-title">${data.how_title}</h3>` : ''}

            <ul class="modal-design-services__how-items">
              ${data.how_items.map((item) => `
                <li>${item.title || ''}</li>
              `).join('')}
            </ul>
          </div>
        `
    : ''
}

    ${renderStats(data)}

    ${renderProducts(data.products)}
  `;
}

let removeModalScrollListener = null;
let removeResizeListener = null;

function setModalContent(modal, serviceIndex, source = 'design-services-json') {
  const modalContent = modal.querySelector(
    '.js-design-services-modal-content'
  );
  if (!modalContent) return;

  const data = servicesData[source];

  if (!data || !data[serviceIndex]) return;

  modalContent.innerHTML = renderModal(data[serviceIndex]);

  requestAnimationFrame(() => {
    const modalInner = modal.querySelector('.modal__inner');
    const modalMain = modal.querySelector('.modal-design-services__main');
    const products = modal.querySelector('.modal-design-services__products');
    const statsSlider = modal.querySelector('.js-what-we-do-stats-slider');

    if (removeModalScrollListener) removeModalScrollListener();
    if (removeResizeListener) removeResizeListener();

    // Fit the products marquee into the space left below the info column. Pure layout
    // math — it never depended on the carousel library, so it survives the migration.
    const updateProductsHeight = () => {
      if (!products || !modalMain) return;
      if (!modal.classList.contains('is-opened') || window.innerWidth < 768) {
        products.style.removeProperty('--products-height-available');
        return;
      }

      const mainRect = modalMain.getBoundingClientRect();
      const productsRect = products.getBoundingClientRect();

      const availableHeight = Math.floor(
        mainRect.bottom - productsRect.top
      );

      if (availableHeight > 0) {
        products.style.setProperty(
          '--products-height-available',
          `${availableHeight}px`
        );
      }
    };

    // The mobile stats carousel is a CSS scroll-snap swiper; the helper wires its dots
    // (visible only <768px via CSS, so it's harmless to init at any width).
    if (statsSlider) initSnap(statsSlider);

    requestAnimationFrame(() => {
      updateProductsHeight();
    });

    if (modalInner) {
      const handleModalScroll = () => {
        updateProductsHeight();
      };

      modalInner.addEventListener('scroll', handleModalScroll, { passive: true });

      removeModalScrollListener = () => {
        modalInner.removeEventListener('scroll', handleModalScroll);
      };
    }

    const handleResize = () => {
      updateProductsHeight();
    };

    window.addEventListener('resize', handleResize, { passive: true });

    removeResizeListener = () => {
      window.removeEventListener('resize', handleResize);
    };
  });
}

function openModal(e) {
  const btn = e.target.closest('.js-modal-open');
  if (!btn) return;

  const modalName = btn.dataset.modal;
  if (!modalName) return;

  const modal = document.querySelector(`.js-modal[data-modal="${modalName}"]`);

  if (!modal) return;

  const { serviceIndex, servicesSource } = btn.dataset;

  if (serviceIndex !== undefined) {
    setModalContent(modal, serviceIndex, servicesSource);
  }

  modal.classList.add('is-opened');
  lockUI('modal');
}

function closeModal(e) {
  const modal = e.target.closest('.js-modal');
  if (!modal) return;

  modal.classList.remove('is-opened');

  unlockUI('modal');

  const content = modal.querySelector('.js-design-services-modal-content');
  if (content) content.innerHTML = '';
}

document.body.addEventListener('click', (e) => {
  if (e.target.closest('.js-modal-open')) openModal(e);
  if (e.target.closest('.js-modal-close')) closeModal(e);
});