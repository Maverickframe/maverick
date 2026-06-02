import { CountUp } from 'countup.js';

const projectsNode = document.querySelector('#js-counter-projects');
const countriesNode = document.querySelector('#js-counter-countries');
const clientsNode = document.querySelector('#js-counter-clients');

if (projectsNode) {
  const countItems = () => {
    const projects = new CountUp('js-counter-projects', +projectsNode.dataset.value);
    const countries = new CountUp('js-counter-countries', +countriesNode.dataset.value);
    const clients = new CountUp('js-counter-clients', +clientsNode.dataset.value);

    projects.start();
    countries.start();
    clients.start();
  };

  const options = {
    root: null,
    rootMargin: '0px',
    threshold: 1.0
  };

  const observer = new IntersectionObserver(countItems, options);

  observer.observe(projectsNode);
}

const numberNodes = document.querySelectorAll('.js-counter');

if (numberNodes.length) {
  const animateNumbers = (entries, observer) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;

      const el = entry.target;
      const originalHTML = el.innerHTML.trim();

      const tempDiv = document.createElement('div');
      tempDiv.innerHTML = originalHTML;
      const text = tempDiv.textContent.trim();

      const match = text.match(/^([+-]?)([\d,.]+)/);
      if (match) {
        const sign = match[1];
        const num = parseFloat(match[2].replace(/,/g, ''));
        const suffixHTML = originalHTML.slice(match[0].length);

        const countUp = new CountUp(el, num, {
          startVal: 0,
          formattingFn: (n) => `${sign}${n}${suffixHTML}`
        });

        if (!countUp.error) countUp.start();
      }

      observer.unobserve(el);
    });
  };

  const observer = new IntersectionObserver(animateNumbers, {
    root: null,
    rootMargin: '0px',
    threshold: 0.8
  });

  numberNodes.forEach((node) => observer.observe(node));
}