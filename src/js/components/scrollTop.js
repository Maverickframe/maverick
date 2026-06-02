const scrollTopBtn = document.querySelector('.js-scroll-top');

if (scrollTopBtn) {
  window.addEventListener('scroll', () => {
    if (window.scrollY > window.innerHeight) {
      if (!scrollTopBtn.classList.contains('is-active')) {
        scrollTopBtn.classList.add('is-active');
      }
    } else {
      scrollTopBtn.classList.remove('is-active');
    }
  });
}