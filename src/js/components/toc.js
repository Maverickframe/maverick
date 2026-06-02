document.addEventListener('DOMContentLoaded', () => {
  const tocItems = Array.from(document.getElementsByClassName('js-toc-item'));
  const tocLinks = Array.from(document.querySelectorAll('.js-toc-item .toc__link'));

  let contentContainer, firstAnchor, lastAnchor, currentActiveItem;

  if (!tocItems.length || !tocLinks.length) return;

  currentActiveItem = null;

  const tocList = tocItems[0].parentNode;
  if (tocList) {
    tocList.addEventListener('click', e => {
      const li = e.target.closest('.js-toc-item');
      if (li && li !== currentActiveItem) {
        if (currentActiveItem) currentActiveItem.classList.remove('active');
        li.classList.add('active');
        currentActiveItem = li;
      }
    }, { passive: true });
  }

  const anchorToTocItem = new Map();
  const observedAnchors = [];

  tocLinks.forEach(link => {
    const href = link.getAttribute('href');
    if (href && href.charAt(0) === '#') {
      const id = href.slice(1);
      const anchor = document.getElementById(id) || document.getElementsByName(id)[0];
      const li = link.closest('.js-toc-item');
      if (anchor && li) {
        anchorToTocItem.set(anchor, li);
        observedAnchors.push(anchor);
      }
    }
  });

  if (!observedAnchors.length) return;

  const OFFSET = 100;

  const setActive = (activeLi) => {
    if (activeLi === currentActiveItem) return;
    if (currentActiveItem) currentActiveItem.classList.remove('active');
    if (activeLi) activeLi.classList.add('active');
    currentActiveItem = activeLi;
  };

  contentContainer = observedAnchors[0].parentElement;
  firstAnchor = observedAnchors[0];
  lastAnchor = observedAnchors[observedAnchors.length - 1];

  const applyTocHighlight = () => {
    if (contentContainer) {
      const cr = contentContainer.getBoundingClientRect();
      if (cr.top > OFFSET || cr.bottom < OFFSET) {
        setActive(null);
        return;
      }
    } else {
      const fr = firstAnchor.getBoundingClientRect();
      const lr = lastAnchor.getBoundingClientRect();
      if (fr.top > OFFSET || lr.bottom < OFFSET) {
        setActive(null);
        return;
      }
    }

    let anchorPastLine = null;
    for (let i = 0; i < observedAnchors.length; ++i) {
      const rect = observedAnchors[i].getBoundingClientRect();
      if (rect.top <= OFFSET) anchorPastLine = observedAnchors[i];
    }

    const anchor = anchorPastLine || firstAnchor;
    setActive(anchorToTocItem.get(anchor));
  };

  let ticking = false;
  const onScrollToc = () => {
    if (ticking) return;
    ticking = true;
    window.requestAnimationFrame(() => {
      applyTocHighlight();
      ticking = false;
    });
  };

  window.addEventListener('scroll', onScrollToc, { passive: true });

  window.setTimeout(() => {
    applyTocHighlight();
  }, 40);
});