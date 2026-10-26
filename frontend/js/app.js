// UI behaviour shared by all views: sidebar toggle and static form handling.
(function () {
  'use strict';

  const mobileQuery = window.matchMedia('(max-width: 767.98px)');

  function showToast() {
    const toastEl = document.getElementById('previewToast');
    if (toastEl) {
      bootstrap.Toast.getOrCreateInstance(toastEl).show();
    }
  }

  document.addEventListener('click', function (event) {
    if (event.target.closest('#sidebarToggle')) {
      document.body.classList.toggle('sidebar-toggled');
    } else if (event.target.closest('#sidebarBackdrop')) {
      document.body.classList.remove('sidebar-toggled');
    }
  });

  // Reset the sidebar state when the screen crosses the mobile breakpoint
  mobileQuery.addEventListener('change', function () {
    document.body.classList.remove('sidebar-toggled');
  });

  // Close the sidebar after navigating on a phone
  window.addEventListener('hashchange', function () {
    if (mobileQuery.matches) {
      document.body.classList.remove('sidebar-toggled');
    }
  });

  // Static forms: no page reload and no backend yet (connected in later milestones)
  document.addEventListener('submit', function (event) {
    event.preventDefault();
    const form = event.target;
    const action = form.dataset.staticAction;

    if (action === 'login') {
      window.location.hash = '#/dashboard';
      return;
    }
    if (action === 'register') {
      window.location.hash = '#/login';
      return;
    }

    const modalEl = form.closest('.modal');
    if (modalEl) {
      bootstrap.Modal.getOrCreateInstance(modalEl).hide();
    }
    form.reset();
    showToast();
  });
})();
