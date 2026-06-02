function animate() {
  if (this.href.match(/#/)) {
    const speed = 0.2;
    const w = window.pageYOffset;
    const hash = this.href.replace(/[^#]*(.*)/, '$1');
    const bodyStyle = getComputedStyle(document.body);
    const headerHeight = parseInt(
      bodyStyle.getPropertyValue('--header-height'),
      10
    );

    const t = document.querySelector(hash).getBoundingClientRect().top - headerHeight;
    let start = null;

    const step = (time) => {
      if (start === null) start = time;
      const progress = time - start;
      const r = t < 0
        ? Math.max(w - progress / speed, w + t)
        : Math.min(w + progress / speed, w + t);
      window.scrollTo(0, r);
      if (r !== w + t) {
        requestAnimationFrame(step);
      } else {
        window.location.hash = hash;
      }
    };
    requestAnimationFrame(step);
  }
}

const linkNav = document.querySelectorAll('a');
linkNav.forEach((link) => link.addEventListener('click', animate));