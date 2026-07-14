/**
 * ESTAVILLO — Motion
 * -----------------------------------------------------------------------
 * Micro-animaciones de revelado on-scroll para elementos [data-es-reveal]
 * (o con clase .es-reveal). Vanilla, IntersectionObserver, y silencio
 * total con prefers-reduced-motion.
 *
 * Regla de robustez (fix del bug de producción "case study invisible"):
 * el contenido SIEMPRE es visible por defecto — el estado oculto
 * (.es-reveal { opacity: 0 }) solo se activa cuando ESTE script agrega la
 * clase 'es-motion' al <html>, inmediatamente antes de empezar a observar.
 * Si el JS no corre (bloqueado, diferido por un optimizador, error), nada
 * se oculta nunca. Además el observer usa threshold: 0 — un threshold > 0
 * jamás se dispara para un elemento más alto que el viewport (su ratio de
 * intersección nunca alcanza el umbral), que es exactamente lo que dejaba
 * el body del Case Study en opacity: 0 para siempre.
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
			// mostrar todo sin animar (y sin activar nunca el estado oculto)
			document.documentElement.classList.add('es-motion-off');
			for (var i = 0; i < els.length; i++) {
				els[i].classList.add('es-in');
			}
			return;
		}

		// threshold: 0 — se revela apenas CUALQUIER píxel entra al viewport.
		// Nunca usar un threshold > 0 acá: no se dispara para elementos más
		// altos que el viewport (ver comentario de cabecera).
		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				if (entry.isIntersecting) {
					entry.target.classList.add('es-in');
					io.unobserve(entry.target);
				}
			});
		}, { threshold: 0 });

		// Recién acá se habilita el estado oculto (gate en <html>): si algo
		// falla antes de esta línea, ningún contenido queda invisible.
		document.documentElement.classList.add('es-motion');

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
