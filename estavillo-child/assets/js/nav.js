/**
 * ESTAVILLO — Nav (menú mobile)
 * -----------------------------------------------------------------------
 * Un solo botón toggle (hamburguesa que morfa a X, queda por encima del
 * overlay). Overlay accesible: aria-expanded, aria-label dinámico, foco,
 * bloqueo de scroll, cierre por X / Escape / click en link / click afuera,
 * y cierre automático al pasar a desktop. Micro-animación por CSS
 * (clase es-menu-open en <html>). Cero librerías.
 */
(function () {
	'use strict';

	function init() {
		var menu = document.getElementById('es-mobile-menu');
		var btn = document.querySelector('[data-es-menu-toggle]');
		if (!menu || !btn) { return; }

		var links = menu.querySelectorAll('[data-es-menu-link]');
		var labelOpen = btn.getAttribute('data-label-open') || 'Open menu';
		var labelClose = btn.getAttribute('data-label-close') || 'Close menu';
		var isOpen = false;
		var hideTimer = 0;

		function open() {
			if (isOpen) { return; }
			isOpen = true;
			window.clearTimeout(hideTimer);
			menu.hidden = false;
			// reflow para que la transición de entrada corra
			void menu.offsetWidth;
			document.documentElement.classList.add('es-menu-open');
			document.body.style.overflow = 'hidden';
			btn.setAttribute('aria-expanded', 'true');
			btn.setAttribute('aria-label', labelClose);
			var first = menu.querySelector('a');
			if (first) { first.focus(); }
		}

		function close() {
			if (!isOpen) { return; }
			isOpen = false;
			document.documentElement.classList.remove('es-menu-open');
			document.body.style.overflow = '';
			btn.setAttribute('aria-expanded', 'false');
			btn.setAttribute('aria-label', labelOpen);
			// ocultar recién al terminar la transición de salida
			var onEnd = function () {
				menu.removeEventListener('transitionend', onEnd);
				if (!isOpen) { menu.hidden = true; }
			};
			menu.addEventListener('transitionend', onEnd);
			hideTimer = window.setTimeout(function () { if (!isOpen) { menu.hidden = true; } }, 400);
			btn.focus();
		}

		function toggle() { if (isOpen) { close(); } else { open(); } }

		btn.addEventListener('click', toggle);
		for (var i = 0; i < links.length; i++) {
			links[i].addEventListener('click', close);
		}
		// click afuera: cualquier click en el overlay que no sea un link cierra
		menu.addEventListener('click', function (e) {
			if (!(e.target && e.target.closest && e.target.closest('a'))) { close(); }
		});
		document.addEventListener('keydown', function (e) {
			if ((e.key === 'Escape' || e.key === 'Esc') && isOpen) { close(); }
		});
		// al pasar a desktop, cerrar
		if (window.matchMedia) {
			var mq = window.matchMedia('(min-width: 920px)');
			var onChange = function () { if (mq.matches && isOpen) { close(); } };
			if (mq.addEventListener) { mq.addEventListener('change', onChange); }
			else if (mq.addListener) { mq.addListener(onChange); }
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
