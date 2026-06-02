const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
  navigator.userAgent
);

function showMore(btn, container) {
  btn.addEventListener('click', (e) => {
    e.target.closest(container).classList.add('is-active');
  });
}

const devCaseBtns = document.querySelectorAll('.js-dev-case-more');

if (devCaseBtns.length > 0) {
  [...devCaseBtns].forEach((btn) => showMore(btn, '.js-dev-case'));
}

const devPortfolioItems = document.querySelectorAll('.js-dev-portfolio-item');
const devPortfolioBtn = document.querySelector('.js-dev-portfolio-more');

function showActivePortfolioItems(showCount) {
  [...devPortfolioItems]
    .filter((item) => !item.classList.contains('is-active'))
    .filter((_, index) => (index < showCount))
    .forEach((item) => item.classList.add('is-active'));

  if (devPortfolioItems[[...devPortfolioItems].length - 1].classList.contains('is-active')) {
    devPortfolioBtn.setAttribute('disabled', true);
  }
}

if (devPortfolioBtn && devPortfolioItems.length > 0) {
  devPortfolioBtn.addEventListener('click', () => showActivePortfolioItems(isMobile ? 2 : 6));

  showActivePortfolioItems(isMobile ? 2 : 12);
}