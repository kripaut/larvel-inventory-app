// Inventory & Billing Management System - Main JS
// Author: Your Name
// Description: Handles UI interactions, loader state, and toast notifications

function toggleSidebar() {
  const sidebar = document.querySelector('.sidebar');
  if (sidebar) {
    sidebar.classList.toggle('open');
  }
}

function getLoader() {
  return document.getElementById('globalLoader');
}

function initLoader() {
  const loader = getLoader();
  if (!loader) {
    return;
  }

  loader.classList.add('loader-overlay');
  loader.classList.remove('d-flex', 'align-items-center', 'justify-content-center');
  loader.style.display = 'none';
}

function showLoader() {
  const loader = getLoader();
  if (loader) {
    loader.style.display = 'flex';
  }
}

function hideLoader() {
  const loader = getLoader();
  if (loader) {
    loader.style.display = 'none';
  }
}

function ensureLoaderHidden() {
  hideLoader();
  window.setTimeout(hideLoader, 150);
  window.setTimeout(hideLoader, 500);
}

function attachLoaderToLinks() {
  document.querySelectorAll('a[href]:not([href="#"]):not([target="_blank"])').forEach((link) => {
    link.addEventListener('click', () => {
      const href = link.getAttribute('href');
      if (href && !href.startsWith('mailto:') && !href.startsWith('javascript:')) {
        showLoader();
      }
    });
  });
}

function attachLoginHandler() {
  const loginForm = document.getElementById('loginForm');
  if (!loginForm) {
    return;
  }

  loginForm.addEventListener('submit', function(event) {
    const submitButton = loginForm.querySelector('button[type="submit"]');
    showLoader();

    if (submitButton) {
      submitButton.disabled = true;
      submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Logging in...';
    }

    window.setTimeout(() => {
      loginForm.submit();
    }, 700);

    event.preventDefault();
  });
}

function showToast(message, type = 'success') {
  const toast = document.createElement('div');
  toast.className = `toast align-items-center text-bg-${type} border-0 fade show position-fixed top-0 end-0 m-4`;
  toast.style.zIndex = 9999;
  toast.innerHTML = `<div class="d-flex"><div class="toast-body">${message}</div><button type="button" class="btn-close btn-close-white ms-2 me-2" data-bs-dismiss="toast" aria-label="Close"></button></div>`;
  document.body.appendChild(toast);
  setTimeout(() => { toast.remove(); }, 3500);
}

function setupUI() {
  const sidebarToggle = document.getElementById('sidebarToggle');
  if (sidebarToggle) {
    sidebarToggle.addEventListener('click', toggleSidebar);
  }

  document.body.classList.add('fade-in');
  initLoader();
  attachLoaderToLinks();
  attachLoginHandler();
  ensureLoaderHidden();

  window.showLoader = showLoader;
  window.hideLoader = hideLoader;
  window.showToast = showToast;
}

setupUI();

window.addEventListener('load', ensureLoaderHidden);
window.addEventListener('pageshow', ensureLoaderHidden);
window.addEventListener('beforeunload', showLoader);
