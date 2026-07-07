/**
 * ESTAVILLO — Nav (menú mobile)
 * -----------------------------------------------------------------------
 * Toggle del overlay de menú en mobile. Vanilla, accesible (aria-expanded,
 * foco, Escape, bloqueo de scroll). Cero librerías.
 */
(function () {
	'use strict';

	function init() {
		var menu = document.getElementById('es-mobile-menu');
		var openBtn = document.querySelector('[data-es-menu-open]');
		if (!menu || !openBtn) { return; }

		var closeEls = menu.querySelectorAll('[data-es-menu-close], [data-es-menu-link]');

		function open() {
			menu.hidden = false;
			document.body.style.overflow = 'hidden';
			openBtn.setAttribute('aria-expanded', 'true');
			// foco al primer control del overlay
			var first = menu.querySelector('[data-es-menu-close]');
			if (first) { first.focus(); }
		}

		function close() {
			menu.hidden = true;
			document.body.style.overflow = '';
			openBtn.setAttribute('aria-expanded', 'false');
			openBtn.focus();
		}

		openBtn.addEventListener('click', open);
		for (var i = 0; i < closeEls.length; i++) {
			closeEls[i].addEventListener('click', close);
		}
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && !menu.hidden) { close(); }
		});
		// si se pasa a desktop con el menú abierto, cerrarlo
		if (window.matchMedia) {
			var mq = window.matchMedia('(min-width: 920px)');
			var onChange = function () { if (mq.matches && !menu.hidden) { close(); } };
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
