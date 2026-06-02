document.addEventListener('DOMContentLoaded', () => {
  const tabsContainer = document.querySelector('.js-tabs-container');
  if (!tabsContainer) {
    return;
  }

  const tabBtns = document.querySelectorAll('.js-tab-btn');
  const tabContents = document.querySelectorAll('.js-tab-content');

  const activateTab = (tabId) => {
    tabBtns.forEach((btn) => {
      btn.classList.toggle('is-active', btn.dataset.tab === tabId);
    });
    tabContents.forEach((cont) => {
      cont.classList.toggle('is-active', cont.id === tabId);
    });
  };

  const defaultTabId = tabBtns.length ? tabBtns[0].dataset.tab : null;
  let currentTabId = defaultTabId;
  if (window.location.hash) {
    const candidate = window.location.hash.replace('#', '');
    if (document.querySelector(`.js-tab-btn[data-tab="${candidate}"]`)) {
      currentTabId = candidate;
    }
  }
  activateTab(currentTabId);

  tabBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
      const tabId = btn.getAttribute('data-tab');
      activateTab(tabId);

      window.scrollTo({
        top: tabsContainer.offsetTop - 100,
        behavior: 'smooth'
      });
    });
  });

  window.addEventListener('hashchange', () => {
    let h = window.location.hash.replace('#', '');
    if (!document.querySelector(`.js-tab-content#${h}`)) {
      h = defaultTabId;
    }
    activateTab(h);
  });
});