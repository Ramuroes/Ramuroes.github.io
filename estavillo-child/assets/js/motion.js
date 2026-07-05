/**
 * ESTAVILLO — Motion
 * -----------------------------------------------------------------------
 * Micro-animaciones de revelado on-scroll para elementos [data-es-reveal]
 * (o con clase .es-reveal). Vanilla, IntersectionObserver, y silencio
 * total con prefers-reduced-motion.
 *
 * Uso en templates:
 *   <div class="es-reveal" data-es-reveal></div>
 *   <div class="es-reveal" data-es-reveal style="--es-reveal-delay:120ms"></div>
 */
(function () {
	'use strict';

	function init() {
		var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		var els = document.querySelectorAll('[data-es-reveal], .es-reveal');

		if (reduced || !('IntersectionObserver' in window)) {
			// mostrar todo sin animar
			document.documentElement.classList.add('es-motion-off');
			for (var i = 0; i < els.length; i++) {
				els[i].classList.add('es-in');
			}
			return;
		}

		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					entry.target.classList.add('es-in');
					io.unobserve(entry.target);
				}
			});
		}, { threshold: 0.12 });

		for (var j = 0; j < els.length; j++) {
			els[j].classList.add('es-reveal');
			io.observe(els[j]);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
