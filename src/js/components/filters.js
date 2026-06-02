const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
  navigator.userAgent
);
let canLoadMoreItems = true;

function toggleDisableLoadMoreButton(loadmoreBtn, disable = false) {
  if (!loadmoreBtn) return;

  if (disable) {
    loadmoreBtn.setAttribute('disabled', 'disabled');
  } else {
    loadmoreBtn.removeAttribute('disabled');
  }
}

const loadMoreItems = async (
  action,
  portfolioItems,
  loadmoreBtn,
  more = false
) => {
  const queryString = window.location.search;
  const urlParams = new URLSearchParams(queryString);
  const currentCat = urlParams.get('cat') || 'all';

  const data = {
    action,
    cat: currentCat,
    page: params.current_page,
    post_id: params.post_id
  };

  toggleDisableLoadMoreButton(loadmoreBtn, true);

  const response = await fetch(params.ajaxurl, {
    method: 'POST',
    body: new URLSearchParams(data)
  });

  const responseData = await response.text();

  if (responseData) {
    params.current_page = +params.current_page + 1;

    toggleDisableLoadMoreButton(loadmoreBtn, false);
    canLoadMoreItems = true;

    const container = portfolioItems;
    if (more) {
      container.insertAdjacentHTML('beforeEnd', responseData);
    } else {
      container.innerHTML = responseData;
    }

    if (params.current_page >= params.max_page) {
      toggleDisableLoadMoreButton(loadmoreBtn, true);
    }
  } else {
    toggleDisableLoadMoreButton(loadmoreBtn, true);
  }
};

function filterPortfolio() {
  const portfolioItems = document.querySelector('.js-portfolio-items');
  const mobileFilter = document.querySelector('.js-portfolio-group-mobile');
  const loadmoreBtn = document.querySelector('.js-portfolio-more');

  if (!portfolioItems) {
    return;
  }

  const buttons = document.querySelectorAll(
    '.js-portfolio-filter-desktop button'
  );

  [...buttons].forEach((btn) => {
    btn.addEventListener('click', async (e) => {
      e.preventDefault();

      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });

      [...buttons].forEach((b) => b.classList.remove('active'));

      btn.classList.add('active');
      params.current_page = 0;

      const queryString = window.location.search;
      const urlParams = new URLSearchParams(queryString);
      urlParams.set('cat', btn.dataset.cat);
      window.history.replaceState(null, null, `?${urlParams.toString()}`);

      await loadMoreItems(
        'loadmore',
        portfolioItems,
        loadmoreBtn
      );
    });
  });

  if (mobileFilter) {
    mobileFilter.addEventListener('change', (e) => {
      const activeBtn = [...buttons].find(
        (btn) => btn.dataset.cat === e.target.value
      );

      activeBtn.click();
    });
  }

  if (loadmoreBtn) {
    loadmoreBtn.addEventListener('click', async (e) => {
      e.preventDefault();

      await loadMoreItems(
        'loadmore',
        portfolioItems,
        loadmoreBtn,
        true
      );
    });
  }

  if (!isMobile) {
    const footerHeight = document.querySelector('footer').offsetHeight;

    window.addEventListener('scroll', async () => {
      if (
        canLoadMoreItems
        && window.scrollY
          > document.body.scrollHeight - window.innerHeight - footerHeight - 100
      ) {
        canLoadMoreItems = false;

        await loadMoreItems(
          'loadmore',
          portfolioItems,
          loadmoreBtn,
          true
        );
      }
    });
  }
}

filterPortfolio();

function setActivePortfolioItems(count) {
  const nonShownItems = document.querySelectorAll(
    '.js-portfolio-front-item:not(.active)'
  );

  if ([...nonShownItems].length > 0) {
    const slicedArray = [...nonShownItems].slice(0, count);

    slicedArray.forEach((item) => item.classList.add('active'));
  }
}

async function loadMoreFront() {
  const portfolioItems = document.querySelector('.js-portfolio-front-items');
  const loadmoreBtn = document.querySelector('.js-portfolio-front-more');

  setActivePortfolioItems(isMobile ? 2 : 6);

  if (loadmoreBtn) {
    loadmoreBtn.addEventListener('click', async (e) => {
      e.preventDefault();

      const nonShownItems = document.querySelectorAll(
        '.js-portfolio-front-item:not(.active)'
      );

      if ([...nonShownItems].length > 0) {
        setActivePortfolioItems(isMobile ? 2 : 6);
      } else {
        await loadMoreItems(
          'loadmore_front',
          portfolioItems,
          loadmoreBtn,
          true
        );

        setActivePortfolioItems(isMobile ? 2 : 6);
      }
    });
  }
}

loadMoreFront();