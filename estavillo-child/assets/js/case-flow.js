/**
 * ESTAVILLO — Case Flow (.es-flow) interaction layer.
 *
 * Vanilla, sin dependencias, ~4 KB. Todo lo que hace es OPCIONAL: el HTML
 * que emite el bloque ya se lee completo sin este archivo (los paneles de
 * detalle son bloques estáticos visibles por CSS). Recién cuando este
 * script corre marca el contenedor con .is-enhanced y los paneles pasan a
 * ser popover (desktop) o acordeón (mobile). Si el JS falla o está
 * bloqueado, no se pierde ni una línea de contenido.
 *
 * Comportamiento por dispositivo (requisito del ticket):
 *  - desktop (>=1024px): hover y foco abren; salir del nodo cierra.
 *  - touch / mobile: tap abre, segundo tap cierra, tap afuera cierra.
 *  - teclado, en los dos: el trigger es un <button> nativo (Enter/Espacio),
 *    Escape cierra y devuelve el foco al trigger.
 *
 * El popover se mantiene SIEMPRE dentro del viewport midiendo su rect real
 * y escribiendo --es-flow-shift (desplazamiento lateral) o .is-flip
 * (abrir hacia arriba) — no se confía en que "seguro entra".
 *
 * @package estavillo-child
 */
(function () {
	'use strict';

	var DESKTOP = '(min-width: 1024px)';
	var EDGE = 12; // margen mínimo contra el borde del viewport

	function isDesktop() {
		return window.matchMedia && window.matchMedia(DESKTOP).matches;
	}

	function setup(flow) {
		var items = Array.prototype.slice.call(flow.querySelectorAll('[data-es-flow-item]'));
		if (!items.length) {
			return;
		}

		// A partir de acá el CSS puede esconder paneles con seguridad.
		flow.classList.add('is-enhanced');

		var openItem = null;

		function panelOf(item) {
			return item.querySelector('[data-es-flow-panel]');
		}

		function triggerOf(item) {
			return item.querySelector('[data-es-flow-trigger]');
		}

		/**
		 * Mantiene el popover dentro del viewport: primero resetea, mide, y
		 * recién ahí corrige. Sólo aplica en desktop (en mobile el panel es
		 * un bloque en flujo, no puede salirse).
		 */
		function place(item) {
			var panel = panelOf(item);
			if (!panel || !isDesktop()) {
				return;
			}

			item.classList.remove('is-flip');
			panel.style.setProperty('--es-flow-shift', '0px');

			var rect = panel.getBoundingClientRect();
			var shift = 0;

			if (rect.left < EDGE) {
				shift = EDGE - rect.left;
			} else if (rect.right > window.innerWidth - EDGE) {
				shift = window.innerWidth - EDGE - rect.right;
			}

			if (shift) {
				panel.style.setProperty('--es-flow-shift', Math.round(shift) + 'px');
			}

			// Si no entra abajo pero sí arriba, se abre hacia arriba.
			var after = panel.getBoundingClientRect();
			if (after.bottom > window.innerHeight - EDGE && after.height + EDGE < item.getBoundingClientRect().top) {
				item.classList.add('is-flip');
			}
		}

		function close(item) {
			if (!item) {
				return;
			}
			item.classList.remove('is-open', 'is-flip');
			var trigger = triggerOf(item);
			if (trigger) {
				trigger.setAttribute('aria-expanded', 'false');
			}
			if (openItem === item) {
				openItem = null;
			}
		}

		function open(item) {
			if (openItem && openItem !== item) {
				close(openItem);
			}
			item.classList.add('is-open');
			var trigger = triggerOf(item);
			if (trigger) {
				trigger.setAttribute('aria-expanded', 'true');
			}
			openItem = item;
			place(item);
		}

		function toggle(item) {
			if (item.classList.contains('is-open')) {
				close(item);
			} else {
				open(item);
			}
		}

		items.forEach(function (item) {
			var trigger = triggerOf(item);
			if (!trigger || !panelOf(item)) {
				return; // nodo sin detalle: no hay nada que abrir
			}

			// Click / tap / Enter / Espacio — un <button> nativo cubre los tres.
			trigger.addEventListener('click', function () {
				toggle(item);
			});

			// Desktop: hover abre sin pedir click.
			item.addEventListener('mouseenter', function () {
				if (isDesktop()) {
					open(item);
				}
			});

			item.addEventListener('mouseleave', function () {
				// No se cierra si el foco de teclado sigue adentro del nodo.
				if (isDesktop() && !item.contains(document.activeElement)) {
					close(item);
				}
			});

			// Foco de teclado: abre al entrar, cierra al salir del nodo entero.
			item.addEventListener('focusin', function () {
				if (isDesktop()) {
					open(item);
				}
			});

			item.addEventListener('focusout', function (ev) {
				if (!isDesktop()) {
					return;
				}
				if (!item.contains(ev.relatedTarget)) {
					close(item);
				}
			});

			var closeBtn = item.querySelector('[data-es-flow-close]');
			if (closeBtn) {
				closeBtn.addEventListener('click', function () {
					close(item);
					if (trigger) {
						trigger.focus();
					}
				});
			}
		});

		// Escape cierra y devuelve el foco al trigger (comportamiento predecible).
		flow.addEventListener('keydown', function (ev) {
			if ('Escape' === ev.key && openItem) {
				var trigger = triggerOf(openItem);
				close(openItem);
				if (trigger) {
					trigger.focus();
				}
			}
		});

		// Tap/click afuera cierra (requisito explícito en touch).
		document.addEventListener('click', function (ev) {
			if (openItem && !openItem.contains(ev.target)) {
				close(openItem);
			}
		});

		// Reposicionar si cambia el viewport con un popover abierto.
		window.addEventListener(
			'resize',
			function () {
				if (openItem) {
					place(openItem);
				}
			},
			{ passive: true }
		);

		// ---- indicador de progreso 01/08 (mobile) ----
		var current = flow.querySelector('[data-es-flow-current]');
		if (current && 'IntersectionObserver' in window) {
			var pad = function (n) {
				return (n < 10 ? '0' : '') + n;
			};
			var observer = new IntersectionObserver(
				function (entries) {
					entries.forEach(function (entry) {
						if (entry.isIntersecting) {
							var idx = entry.target.getAttribute('data-es-flow-index');
							if (idx) {
								current.textContent = pad(parseInt(idx, 10));
							}
						}
					});
				},
				{ rootMargin: '-45% 0px -45% 0px' }
			);
			items.forEach(function (item) {
				observer.observe(item);
			});
		}
	}

	function init() {
		Array.prototype.slice.call(document.querySelectorAll('[data-es-flow]')).forEach(setup);
	}

	if ('loading' === document.readyState) {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
