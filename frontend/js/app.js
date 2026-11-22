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

  // Password confirmation: compares the last two password fields of a form
  // (register: password + confirm, profile: new + confirm). Returns true if they differ.
  function passwordsMismatch(form) {
    const fields = form.querySelectorAll('input[type="password"]');
    if (fields.length < 2) {
      return false;
    }
    const confirm = fields[fields.length - 1];
    const first = fields[fields.length - 2];
    const mismatch = confirm.value !== first.value;
    confirm.setCustomValidity(mismatch ? 'Passwords do not match.' : '');
    return mismatch;
  }

  document.addEventListener('input', function (event) {
    if (event.target.form && event.target.type === 'password') {
      passwordsMismatch(event.target.form);
    }
  });

  // Static forms: no page reload and no backend yet (connected in later milestones)
  document.addEventListener('submit', function (event) {
    event.preventDefault();
    const form = event.target;
    const action = form.dataset.staticAction;

    if (passwordsMismatch(form)) {
      form.reportValidity();
      return;
    }

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