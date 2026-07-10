// Init runs immediately when lazy-imported (the entry bundle is a deferred
// module, so by the time this chunk evaluates the DOM is already parsed). The
// readyState guard below still handles the theoretical "still loading" case.
function initToc() {
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

  // Cache absolute document offsets so the scroll handler never touches layout.
  // Reading getBoundingClientRect() on every scroll forced a synchronous reflow
  // per event; instead we measure once (and on resize/load, when geometry can
  // actually change) and compare cheap window.pageYOffset against the cache.
  let anchorTops = [];
  let containerTop = 0;
  let containerBottom = 0;

  const scrollTop = () =>
    window.pageYOffset || document.documentElement.scrollTop || 0;

  const measure = () => {
    const y = scrollTop();
    anchorTops = observedAnchors.map((a) => a.getBoundingClientRect().top + y);
    if (contentContainer) {
      const cr = contentContainer.getBoundingClientRect();
      containerTop = cr.top + y;
      containerBottom = cr.bottom + y;
    }
  };

  const applyTocHighlight = () => {
    const line = scrollTop() + OFFSET;

    if (contentContainer) {
      if (containerTop > line || containerBottom < line) {
        setActive(null);
        return;
      }
    } else if (anchorTops.length) {
      if (anchorTops[0] > line || anchorTops[anchorTops.length - 1] < line) {
        setActive(null);
        return;
      }
    }

    let anchorIdx = -1;
    for (let i = 0; i < anchorTops.length; ++i) {
      if (anchorTops[i] <= line) anchorIdx = i;
    }

    const anchor = anchorIdx >= 0 ? observedAnchors[anchorIdx] : firstAnchor;
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

  const onResizeToc = () => {
    measure();
    applyTocHighlight();
  };

  window.addEventListener('scroll', onScrollToc, { passive: true });
  window.addEventListener('resize', onResizeToc);
  window.addEventListener('load', onResizeToc);

  window.setTimeout(() => {
    measure();
    applyTocHighlight();
  }, 40);
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initToc);
} else {
  initToc();
}