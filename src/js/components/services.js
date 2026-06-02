const servicesLinks = document.querySelectorAll('.js-services-link');
const spItems = document.querySelectorAll('.sp-item');

function changeLinkActive(entries) {
  [...servicesLinks].forEach((link) => link.classList.remove('active'));

  entries.forEach((entry) => {
    if (!entry.isIntersecting) {
      return;
    }

    const activeLink = [...servicesLinks].find((link) => {
      const hash = link.getAttribute('href').replace(/[^#]*(.*)/, '$1').replace('#', '');

      return hash === entry.target.getAttribute('id');
    }) || servicesLinks[0];

    activeLink.classList.add('active');
  });
}

if (servicesLinks?.length > 0) {
  [...servicesLinks].forEach((link) => {
    link.addEventListener('click', () => {
      [...servicesLinks].forEach((l) => l.classList.remove('active'));

      link.classList.add('active');
    });
  });

  const bodyStyle = getComputedStyle(document.body);
  const fontSize = parseFloat(getComputedStyle(document.body).getPropertyValue('font-size'));
  const headerHeight = parseFloat(bodyStyle.getPropertyValue('--header-height')) * fontSize;

  const options = {
    root: null,
    rootMargin: `${headerHeight}px 0px`,
    threshold: 1
  };

  const observer = new IntersectionObserver(changeLinkActive, options);

  [...spItems].forEach((spItem) => {
    observer.observe(spItem);
  });
}