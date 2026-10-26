// Hash-based SPA router: loads views/<name>.html into the page without a reload.
(function () {
  'use strict';

  const defaultRoute = '/login';
  const routes = {
    '/login':         { view: 'login',         layout: 'auth', title: 'Login' },
    '/register':      { view: 'register',      layout: 'auth', title: 'Register' },
    '/dashboard':     { view: 'dashboard',     layout: 'app',  title: 'Dashboard' },
    '/channels':      { view: 'channels',      layout: 'app',  title: 'Chat channels' },
    '/announcements': { view: 'announcements', layout: 'app',  title: 'Announcements' },
    '/maintenance':   { view: 'maintenance',   layout: 'app',  title: 'Maintenance' },
    '/users':         { view: 'users',         layout: 'app',  title: 'Users' },
    '/profile':       { view: 'profile',       layout: 'app',  title: 'Profile' }
  };
  const notFound = { view: 'not-found', layout: 'app', title: 'Page not found' };

  const authView = document.getElementById('authView');
  const shell = document.getElementById('shell');
  const appView = document.getElementById('appView');
  let renderCount = 0;

  function currentPath() {
    return window.location.hash.replace(/^#/, '') || defaultRoute;
  }

  function setActiveLink(path) {
    document.querySelectorAll('#sidebar [data-route]').forEach(function (link) {
      const active = link.dataset.route === path;
      link.classList.toggle('active', active);
      if (active) {
        link.setAttribute('aria-current', 'page');
      } else {
        link.removeAttribute('aria-current');
      }
    });
  }

  async function render() {
    const path = currentPath();
    const route = routes[path] || notFound;
    const thisRender = ++renderCount;
    const target = route.layout === 'auth' ? authView : appView;

    let html;
    try {
      const response = await fetch('views/' + route.view + '.html');
      if (!response.ok) {
        throw new Error('HTTP ' + response.status);
      }
      html = await response.text();
    } catch (error) {
      html = '<div class="alert alert-danger m-3">Could not load this page. ' +
        'Open the app through XAMPP (http://localhost/...) instead of opening the file directly.</div>';
    }
    if (thisRender !== renderCount) {
      return; // a newer navigation started meanwhile
    }

    authView.classList.toggle('d-none', route.layout !== 'auth');
    shell.classList.toggle('d-none', route.layout === 'auth');
    target.innerHTML = html;
    document.title = route.title + ' | Building Management System';
    setActiveLink(path);
    window.scrollTo(0, 0);
  }

  window.addEventListener('hashchange', render);
  window.addEventListener('DOMContentLoaded', render);
})();
