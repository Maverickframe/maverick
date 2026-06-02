import { lockUI, unlockUI } from './uiManager';

const menu = document.querySelector('.js-menu');
const burger = document.querySelector('.js-menu-btn');
const header = document.querySelector('.header');

const menuItems = menu?.querySelectorAll('.menu-item-has-children');
const MD_WIDTH = 1270;
const LG_WIDTH = 1750;

let burgerOpen = false;
let desktopDropdownOpen = false;
let desktopDropdownPinned = false;

let openTimeout;
let closeTimeout;

const isLargeDesktop = () => window.innerWidth >= LG_WIDTH;

const useOverlayMenu = () => {
  if (window.innerWidth < MD_WIDTH) return true;
  if (window.innerWidth >= LG_WIDTH) return false;

  return header?.classList.contains('is-tablet-menu');
};

let wasTabletMenu = header?.classList.contains('is-tablet-menu');

function updateHeaderBlack() {
  if (burgerOpen || desktopDropdownOpen || desktopDropdownPinned) {
    header.classList.add('is-black');
  } else {
    header.classList.remove('is-black');
  }
}

function closeAllDropdowns() {
  if (!menu || !header) return;

  menu.querySelectorAll('.menu-item-has-children.is-active')
    .forEach((item) => item.classList.remove('is-active'));
}

function handleTabletMenuChange() {
  const isTabletMenu = header?.classList.contains('is-tablet-menu');

  if (isTabletMenu === wasTabletMenu) return;

  wasTabletMenu = isTabletMenu;

  if (isTabletMenu) {
    closeAllDropdowns();
    desktopDropdownOpen = false;
    desktopDropdownPinned = false;
    unlockUI('dropdown');
  } else if (burgerOpen) {
    burgerOpen = false;
    header?.classList.remove('is-opened');
    unlockUI('menu');
  }

  updateHeaderBlack();
}

function setDropdownOpen(item) {
  clearTimeout(openTimeout);
  clearTimeout(closeTimeout);

  closeAllDropdowns();
  item.classList.add('is-active');

  desktopDropdownOpen = true;

  lockUI('dropdown');
  updateHeaderBlack();
}

function closeDropdown(item) {
  clearTimeout(openTimeout);
  clearTimeout(closeTimeout);

  item?.classList.remove('is-active');

  desktopDropdownPinned = false;
  desktopDropdownOpen = false;

  unlockUI('dropdown');
  updateHeaderBlack();
}

if (burger) {
  burger.addEventListener('click', (e) => {
    e.preventDefault();

    burgerOpen = !burgerOpen;

    header?.classList.toggle('is-opened', burgerOpen);

    if (burgerOpen) {
      lockUI('menu');
    } else {
      unlockUI('menu');

      closeAllDropdowns();
      desktopDropdownOpen = false;
      desktopDropdownPinned = false;
      unlockUI('dropdown');
    }

    updateHeaderBlack();
  });
}

menu?.addEventListener('click', (e) => {
  clearTimeout(openTimeout);
  clearTimeout(closeTimeout);

  const item = e.target.closest('.menu-item-has-children');
  if (!item) return;

  if (e.target.closest('.menu-item-has-children > a')) {
    e.preventDefault();
  }

  if (useOverlayMenu()) {
    item.classList.toggle('is-active');

    return;
  }

  if (item.classList.contains('is-active') && desktopDropdownPinned) {
    item.classList.remove('is-active');

    desktopDropdownPinned = false;
    desktopDropdownOpen = false;

    unlockUI('dropdown');
    updateHeaderBlack();
    return;
  }

  closeAllDropdowns();

  item.classList.add('is-active');

  desktopDropdownPinned = true;
  desktopDropdownOpen = true;

  lockUI('dropdown');
  updateHeaderBlack();
});

function initDesktopHover() {
  let currentItem = null;

  menuItems?.forEach((item) => {
    const dropdown = item.querySelector('.sub-menu');

    item.addEventListener('mouseenter', () => {
      if (!isLargeDesktop()) return;

      openTimeout = setTimeout(() => {
        if (currentItem && currentItem !== item && !desktopDropdownPinned) {
          closeDropdown(currentItem);
        }

        setDropdownOpen(item);
        currentItem = item;
      }, 180);
    });

    item.addEventListener('mouseleave', (e) => {
      if (!isLargeDesktop()) return;

      clearTimeout(openTimeout);

      const related = e.relatedTarget;
      if (dropdown?.contains(related) || item.contains(related)) return;

      closeTimeout = setTimeout(() => {
        if (!desktopDropdownPinned) {
          closeDropdown(item);
          if (currentItem === item) currentItem = null;
        }
      }, 200);
    });

    dropdown?.addEventListener('mouseenter', () => {
      if (!isLargeDesktop()) return;
      clearTimeout(closeTimeout);
    });

    dropdown?.addEventListener('mouseleave', (e) => {
      if (!isLargeDesktop()) return;

      const related = e.relatedTarget;
      if (item.contains(related)) return;

      closeTimeout = setTimeout(() => {
        if (!desktopDropdownPinned) {
          closeDropdown(item);
          if (currentItem === item) currentItem = null;
        }
      }, 200);
    });
  });
}

initDesktopHover();
handleTabletMenuChange();

document.addEventListener('click', (e) => {
  if (e.target.closest('.js-menu-close')) {
    burgerOpen = false;

    header?.classList.remove('is-opened');
    unlockUI('menu');

    closeAllDropdowns();
    desktopDropdownOpen = false;
    desktopDropdownPinned = false;
    unlockUI('dropdown');

    updateHeaderBlack();
    return;
  }

  if (!isLargeDesktop()) return;

  if (!e.target.closest('.js-menu')) {
    closeAllDropdowns();

    desktopDropdownPinned = false;
    desktopDropdownOpen = false;

    unlockUI('dropdown');
    updateHeaderBlack();
  }
});

window.addEventListener('scroll', handleTabletMenuChange, { passive: true });

window.addEventListener('resize', () => {
  clearTimeout(openTimeout);
  clearTimeout(closeTimeout);

  handleTabletMenuChange();

  if (!isLargeDesktop()) {
    desktopDropdownOpen = false;
    desktopDropdownPinned = false;

    closeAllDropdowns();
    unlockUI('dropdown');
    updateHeaderBlack();
  }

  if (isLargeDesktop() && burgerOpen) {
    burgerOpen = false;
    header?.classList.remove('is-opened');
    unlockUI('menu');
    updateHeaderBlack();
  }
});