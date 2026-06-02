function showMoreText(item) {
  const desc = item.querySelector('.js-desc-text');
  const btn = item.querySelector('.js-desc-more');

  if (!desc || !btn) return;

  const isLong = desc.scrollHeight > desc.clientHeight;

  if (isLong) {
    btn.addEventListener('click', () => {
      item.classList.add('expanded');
      btn.classList.add('hidden');
    });
  } else {
    btn.classList.add('hidden');
  }
}

document.querySelectorAll('.js-design-services-item').forEach(showMoreText);
document.querySelectorAll('.js-completeness-visual-embody-item').forEach(showMoreText);
document.querySelectorAll('.js-selective-works-item').forEach(showMoreText);
document.querySelectorAll('.js-reviews-item').forEach(showMoreText);