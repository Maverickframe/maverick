document.body.addEventListener('click', (e) => {
  if (e.target.closest('.js-selective-works-item-collapse')) {
    const item = e.target.closest('.js-selective-works-item');
    item.classList.toggle('collapsed');
  }
});