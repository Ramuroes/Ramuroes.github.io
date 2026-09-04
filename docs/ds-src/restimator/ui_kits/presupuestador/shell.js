/* Shared app chrome for the Presupuestador RE UI kit.
   Renders the sidebar + topbar into #re-sidebar / #re-topbar from
   body data-attributes:  data-active, data-crumbs (a|b|c), data-user.
   Keeps each screen focused on its own content. */
(function () {
  var I = {
    home: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 11l8-6 8 6"/><path d="M6 10v9a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1v-9"/><path d="M10 20v-5h4v5"/></svg>',
    calc: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="5" y="3" width="14" height="18" rx="2.5"/><line x1="8.5" y1="7" x2="15.5" y2="7"/><circle cx="9" cy="12" r="1" fill="currentColor" stroke="none"/><circle cx="12" cy="12" r="1" fill="currentColor" stroke="none"/><circle cx="15" cy="12" r="1" fill="currentColor" stroke="none"/><circle cx="9" cy="16" r="1" fill="currentColor" stroke="none"/><circle cx="12" cy="16" r="1" fill="currentColor" stroke="none"/><circle cx="15" cy="16" r="1" fill="currentColor" stroke="none"/></svg>',
    hist: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2" stroke-linecap="round"/></svg>',
    cat: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"><path d="M12 3l7.5 4v10L12 21l-7.5-4V7L12 3z"/><path d="M4.7 7L12 11l7.3-4M12 11v10"/></svg>',
    par: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><line x1="4" y1="8" x2="20" y2="8"/><line x1="4" y1="16" x2="20" y2="16"/><circle cx="9" cy="8" r="2.6" fill="var(--re-surface)"/><circle cx="15" cy="16" r="2.6" fill="var(--re-surface)"/></svg>',
    qa: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8.5"/><path d="M8.3 12.2l2.6 2.6 4.8-5.2"/></svg>',
    logout: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M14 4H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h7"/><path d="M16 8l4 4-4 4M10.5 12H20"/></svg>',
    search: '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><line x1="16.5" y1="16.5" x2="21" y2="21"/></svg>',
    help: '<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M9.4 9.3a2.6 2.6 0 0 1 5 .9c0 1.7-2.5 2.2-2.5 3.8"/><circle cx="12" cy="17.5" r="1" fill="currentColor" stroke="none"/></svg>',
    chev: '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="var(--re-ink-3)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>',
  };
  var NAV = [
    { group: 'Operación', items: [
      { id: 'calc', label: 'Calculadora', icon: I.calc, href: 'Calculator.html', home: true },
      { id: 'hist', label: 'Historial', icon: I.hist, href: 'History.html' },
    ]},
    { group: 'Configuración', items: [
      { id: 'cat', label: 'Catálogos', icon: I.cat, href: 'Catalogs.html' },
      { id: 'par', label: 'Parámetros', icon: I.par, href: '#', disabled: true },
      { id: 'qa', label: 'QA', icon: I.qa, href: 'QA.html' },
    ]},
  ];

  function sidebar(active) {
    var html = '<div class="logo-row"><div class="logo-mark">RE</div><div class="pname">Presupuestador<span>RE · v1.0</span></div></div>';
    NAV.forEach(function (g) {
      html += '<div class="nav-group">' + g.group + '</div>';
      g.items.forEach(function (it) {
        if (it.disabled) {
          html += '<span class="nav-item disabled" aria-disabled="true" title="Disponible en una próxima entrega">' + it.icon + '<span>' + it.label + '</span><em class="soon">pronto</em></span>';
          return;
        }
        var cls = 'nav-item' + (it.id === active ? ' active' : '');
        html += '<a class="' + cls + '" href="' + it.href + '">' + it.icon + '<span>' + it.label + '</span></a>';
      });
    });
    html += '<div class="grow"></div><div class="foot-nav"><a class="nav-item" href="#">' + I.logout + '<span>Cerrar sesión</span></a></div>';
    return html;
  }

  // Real routes only. Category labels (zones) are NOT links — they go nowhere.
  var CRUMB_HREF = {
    'Calculadora': 'Calculator.html', 'Historial': 'History.html',
    'Catálogos': 'Catalogs.html', 'QA': 'QA.html', 'QA de regresión': 'QA.html',
  };
  function topbar(crumbs, user) {
    var parts = (crumbs || 'Operación|Calculadora').split('|');
    var trail = parts.map(function (p, i) {
      if (i === parts.length - 1) return '<b>' + p + '</b>';           // current page
      if (CRUMB_HREF[p]) return '<a href="' + CRUMB_HREF[p] + '">' + p + '</a>'; // real route
      return '<span>' + p + '</span>';                                  // zone label, not a link
    }).join('<span class="sep">/</span>');
    var u = (user || 'Ramiro Estavillo|Administrador|RE').split('|');
    return '<div class="crumbs">' + trail + '</div>' +
      '<div class="right">' +
      '<div class="user-chip"><div class="avatar">' + u[2] + '</div><div class="who"><b>' + u[0] + '</b><span>' + u[1] + '</span></div>' + I.chev + '</div>' +
      '</div>';
  }

  function mount() {
    var b = document.body;
    var sb = document.getElementById('re-sidebar');
    var tb = document.getElementById('re-topbar');
    if (sb) sb.innerHTML = sidebar(b.getAttribute('data-active'));
    if (tb) tb.innerHTML = topbar(b.getAttribute('data-crumbs'), b.getAttribute('data-user'));
  }

  // Tiny shared toast helper
  window.REToast = function (msg) {
    var el = document.querySelector('.re-toast');
    if (!el) { el = document.createElement('div'); el.className = 're-toast'; document.body.appendChild(el); }
    el.textContent = msg; el.classList.add('show');
    clearTimeout(window.__retoast); window.__retoast = setTimeout(function () { el.classList.remove('show'); }, 2000);
  };

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', mount);
  else mount();
})();
