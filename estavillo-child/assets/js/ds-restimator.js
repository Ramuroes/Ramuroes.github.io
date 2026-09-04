/**
 * ESTAVILLO — REstimator Design System: scroll-spy del rail
 * ---------------------------------------------------------------------------
 * Port del script inline que traía el documento original (12 líneas), acotado
 * a `.re-doc` y sacado del HTML: así el partial generado por
 * tools/build-ds.mjs es markup puro y este comportamiento se versiona como
 * cualquier otro asset del tema.
 *
 * Cambios sobre el original, todos de robustez — el criterio de "cuál sección
 * está activa" es exactamente el mismo:
 *  - selector scopeado a `.re-doc`, para no tocar ningún otro `.rail` del sitio;
 *  - lecturas de layout agrupadas en un requestAnimationFrame, en vez de medir
 *    N secciones en cada evento de scroll (el documento tiene 11 secciones y
 *    ~29.000 px de alto);
 *  - se actualiza `aria-current` además de la clase, así el estado activo
 *    también existe para un lector de pantalla.
 *
 * Mejora progresiva pura: sin este script el rail sigue siendo una lista de
 * anchors que navega perfecto, sólo no se resalta el ítem actual.
 *
 * @package estavillo-child
 */
(function () {
	'use strict';

	var rail = document.querySelector('.re-doc .rail');
	if (!rail) {
		return;
	}

	var links = Array.prototype.slice.call(rail.querySelectorAll('a.nv'));
	if (!links.length) {
		return;
	}

	var targets = links
		.map(function (a) {
			var href = a.getAttribute('href') || '';
			// Sólo anchors internos: cualquier otra cosa no es una sección.
			if (href.charAt(0) !== '#' || href.length < 2) {
				return null;
			}
			return document.getElementById(href.slice(1));
		});

	// Si ninguna sección existe (documento cambiado), no hay nada que espiar.
	if (!targets.some(Boolean)) {
		return;
	}

	var OFFSET = 120; // igual que el original: la sección "cuenta" al llegar acá
	var ticking = false;
	var current = -1;

	function sync() {
		ticking = false;

		var best = -1;
		for (var i = 0; i < targets.length; i++) {
			var t = targets[i];
			if (t && t.getBoundingClientRect().top <= OFFSET) {
				best = i;
			}
		}
		if (best === -1) {
			best = 0;
		}
		if (best === current) {
			return;
		}
		current = best;

		for (var j = 0; j < links.length; j++) {
			var on = j === best;
			links[j].classList.toggle('on', on);
			if (on) {
				links[j].setAttribute('aria-current', 'true');
			} else {
				links[j].removeAttribute('aria-current');
			}
		}
	}

	function request() {
		if (!ticking) {
			ticking = true;
			window.requestAnimationFrame(sync);
		}
	}

	window.addEventListener('scroll', request, { passive: true });
	window.addEventListener('resize', request, { passive: true });
	sync();
})();
