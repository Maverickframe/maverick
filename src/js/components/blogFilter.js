const articlesContainer = document.getElementById('blog');

const articlesItems = document.querySelector('.js-articles-items');
const articlesLoadmoreBtn = document.querySelector('.js-articles-more');

const articlesNotFound = document.querySelector('.js-articles-notfound');
const articlesNotFoundReset = articlesNotFound?.querySelector('.js-articles-search-reset');

const articlesSearchForm = document.querySelector('.js-articles-search');
const articlesSearchInput = articlesSearchForm?.querySelector('input');

const articlesSearchIcon = articlesSearchForm?.querySelector('.js-articles-search-icon');
const articlesSearchReset = articlesSearchForm?.querySelector('.js-articles-search-reset');

const articlesCategoryToggleButton = document.querySelectorAll('.js-articles-category-toggle');
const articlesCategory = document.querySelector('.js-articles-category');
const articlesSubcategory = document.querySelector('.js-articles-subcategory');
const articlesSubcategoryBackBtn = document.querySelector('.js-articles-category-back');
const articlesCategoryFilters = document.querySelectorAll('.section__categories');
const articlesSortSelect = document.querySelector('.js-articles-sort');

function toggleAttributes(attr) {
  const elements = document.querySelectorAll(`[${attr}]`);

  elements.forEach((element) => {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) element.setAttribute(attr, 'false');
        else element.setAttribute(attr, 'true');
      });
    }, { threshold: 0.01 });

    observer.observe(element);

    element._toggleAttrObserver = observer;
  });
}

function toggleCategories() {
  if (articlesCategoryToggleButton) {
    [...articlesCategoryToggleButton].forEach((btn) => {
      btn.addEventListener('click', () => {
        btn.classList.toggle('is-active');

        if (btn.dataset.category === 'primary') {
          articlesCategory.classList.toggle('is-active');
        }
      });
    });
  }
}

function updateSubcategoryFilter(selectedCatId) {
  if (!articlesSubcategory) return;

  const subcatPills = articlesSubcategory.querySelectorAll('button[data-subcat][data-parent]');
  const hasCategory = selectedCatId && selectedCatId !== 'all';

  if (hasCategory) {
    articlesSubcategory.classList.add('is-active');
    subcatPills.forEach((pill) => {
      pill.style.display = pill.dataset.parent === selectedCatId ? '' : 'none';
    });
  } else {
    articlesSubcategory.classList.remove('is-active');
    subcatPills.forEach((pill) => {
      pill.style.display = '';
    });
  }

  if (articlesSubcategoryBackBtn) articlesSubcategoryBackBtn.addEventListener('click', () => applyFilter({ cat: 'all', subcat: 'all' }));
}

function toggleCat() {
  // if (articlesSortSelect) articlesSortSelect.value = params.orderby || 'latest';

  if (!articlesCategoryFilters) return;

  [...articlesCategoryFilters].forEach((f) => {
    f.querySelectorAll('button[data-cat],button[data-subcat],button[data-tag]').forEach((b) => {
      const { cat, subcat, tag } = b.dataset;

      b.classList.toggle(
        'is-active',
        (cat !== undefined && cat === (params.cat || 'all'))
        || (subcat !== undefined && subcat === (params.subcat || 'all'))
        || (tag !== undefined && tag === (params.tag || 'all'))
      );
    });

    f.addEventListener('click', (e) => {
      const btn = e.target.closest('button[data-cat],button[data-subcat],button[data-tag]');
      if (!btn || !f.contains(btn)) return;

      if (btn._filterApplied) return;
      btn._filterApplied = true;
      setTimeout(() => { btn._filterApplied = false; }, 0);

      const {
        cat, subcat, tag, parent
      } = btn.dataset;

      let filter;

      if (cat !== undefined) {
        filter = { cat, subcat: 'all' };
        updateSubcategoryFilter(cat);
      } else if (subcat !== undefined) filter = { cat: params.cat || parent, subcat };
      else if (tag !== undefined) filter = { tag };
      else filter = null;

      if (filter) applyFilter(filter);
    });
  });
}

async function loadItems(append) {
  if (!articlesItems) return;

  if (articlesLoadmoreBtn) articlesLoadmoreBtn.setAttribute('disabled', '');

  if (articlesContainer) articlesContainer.style.pointerEvents = 'none';

  // if (!append) {
  // articlesContainer.innerHTML = 'Loading...';
  // }

  const currentPage = append ? +params.current_page + 1 : params.current_page;
  const body = new URLSearchParams({
    action: 'loadmore_articles',
    post_type: params.post_type,
    current_page: currentPage,
    cat: params.cat || 'all',
    subcat: params.subcat || 'all',
    tag: params.tag || 'all',
    search: params.search || '',
    orderby: params.orderby || 'latest',
    lang: params.lang || 'en'
  });

  const res = await fetch(params.ajaxurl, { method: 'POST', body });
  const json = await res.json();

  if (json.data) {
    params.max_page = json.max_page;

    if (append) {
      articlesItems.insertAdjacentHTML('beforeend', json.data);

      params.current_page++;
    } else {
      articlesItems.innerHTML = json.data;
    }

    if (articlesContainer) articlesContainer.removeAttribute('style');

    if (articlesNotFound) articlesNotFound.classList.remove('is-active');

    if (articlesLoadmoreBtn) {
      if (params.current_page >= params.max_page) {
        articlesLoadmoreBtn.setAttribute('disabled', '');
      } else {
        articlesLoadmoreBtn.removeAttribute('disabled');
      }
    }
  } else {
    if (!append) articlesItems.innerHTML = '';

    if (articlesContainer) articlesContainer.removeAttribute('style');

    if (articlesNotFound) articlesNotFound.classList.add('is-active');

    if (articlesLoadmoreBtn) articlesLoadmoreBtn.setAttribute('disabled', '');
  }
}

function applyFilter(updates) {
  params.current_page = 1;

  if (updates) {
    params = {
      ...params,
      ...updates
    };
  } else {
    params = {
      ...params,
      cat: 'all',
      subcat: 'all',
      tag: 'all',
      search: '',
      orderby: 'latest'
    };

    articlesSearchForm.reset();
  }

  updateSubcategoryFilter(params.cat || 'all');
  toggleCat();
  loadItems();
}

// Init
(() => {
  if (!articlesItems) return;

  toggleCategories();
  toggleCat();

  toggleAttributes('aria-hidden');

  if (articlesSortSelect) {
    articlesSortSelect.addEventListener('change', (e) => {
      const { value } = e.target;

      applyFilter({ orderby: value === 'latest' ? null : value });
    });
  }

  if (articlesLoadmoreBtn) {
    articlesLoadmoreBtn.addEventListener('click', () => {
      loadItems(true);
    });
  }

  if (articlesSearchForm) {
    articlesSearchForm.addEventListener('submit', (e) => {
      e.preventDefault();

      applyFilter({ search: articlesSearchInput.value.trim() });
    });

    articlesSearchIcon.addEventListener('click', () => {
      articlesSearchForm.classList.toggle('is-active');
      articlesSearchInput.select();
    });

    articlesSearchReset.addEventListener('click', () => {
      if (params.search) applyFilter();
      else {
        articlesSearchForm.classList.toggle('is-active');
      }
    });
  }

  if (articlesNotFoundReset) {
    articlesNotFoundReset.addEventListener('click', () => {
      applyFilter();
    });
  }
})();