document.addEventListener('DOMContentLoaded', () => {
  const btn = document.querySelector('.js-show-more-visuals-btn');
  const extras = document.querySelectorAll('.js-extra-item');

  if (btn) {
    btn.addEventListener('click', () => {
      extras.forEach((el) => el.classList.remove('extra-item'));
      btn.style.display = 'none';
    });
  }
});