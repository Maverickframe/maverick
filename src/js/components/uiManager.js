const locks = new Set();

function updateUI() {
  const shouldLock = locks.size > 0;

  document.body.classList.toggle('is-fixed', shouldLock);

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