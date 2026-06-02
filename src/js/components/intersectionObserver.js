const itemsToAnimate = document.querySelectorAll('.js-animate');

if (itemsToAnimate.length > 0) {
  const animateItems = (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target?.classList.add('animated');
      }
    });
  };

  const options = {
    root: null,
    rootMargin: '0px',
    threshold: 0.5
  };

  const observer = new IntersectionObserver(animateItems, options);

  itemsToAnimate.forEach((item) => observer.observe(item));
}