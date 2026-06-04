const locks = new Set();

function updateUI() {
  const shouldLock = locks.size > 0;

  // Only the mobile/burger menu locks page scroll. Desktop dropdowns keep the
  // page scrollable — locking scroll hid the scrollbar and shifted the whole
  // layout by the scrollbar width (the menu "jitter" on open).
  document.body.classList.toggle('is-fixed', locks.has('menu'));

  let overlay = document.querySelector('.js-global-overlay');

  if (shouldLock) {
    if (!overlay) {
      overlay = document.createElement('div');
      overlay.className = 'blur-overlay js-global-overlay';
      document.body.appendChild(overlay);
    }

    overlay.className = 'blur-overlay js-global-overlay';

    if (locks.has('menu') || locks.has('dropdown')) {
      overlay.classList.add('blur-overlay_menu');
    }

    if (locks.has('menu')) {
      overlay.classList.add('js-menu-close');
    }
  } else {
    overlay?.remove();
  }
}

export function lockUI(name) {
  locks.add(name);
  updateUI();
}

export function unlockUI(name) {
  locks.delete(name);
  updateUI();
}