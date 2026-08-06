/**
 * Case Stats — conteo de números al entrar en pantalla.
 *
 * MEJORA PROGRESIVA ESTRICTA. El número final lo imprime siempre el servidor
 * (ver blocks/case-stats/render.php del plugin): este archivo nunca crea ni
 * completa contenido, sólo reemplaza temporalmente un valor que ya está en el
 * HTML. Si el script no carga, falla, o el bloque tiene la animación apagada,
 * lo que se ve es exactamente el HTML renderizado.
 *
 * Tres reglas que definen el comportamiento:
 *
 * 1. prefers-reduced-motion: reduce → no se toca absolutamente nada. No es
 *    "animar más rápido": es no animar. El usuario ve el valor final directo.
 *
 * 2. Si el bloque YA está en pantalla cuando el script corre, tampoco se
 *    anima. La animación es un efecto de aparición al scrollear; si no hay
 *    nada que revelar porque el bloque ya se ve, poner el contador en 0 sólo
 *    produciría un parpadeo final→0→final. Por eso el "poner en cero" ocurre
 *    únicamente sobre bloques que están fuera de viewport: el usuario nunca
 *    llega a ver el estado cero aparecer.
 *
 * 3. Una sola vez por bloque: al terminar, se deja de observar. Nada de
 *    re-animar al volver a scrollear.
 *
 * Sin CLS: el número vive en un div de line-height fijo dentro de una celda
 * flex de basis fijo, así que su alto no depende del texto; y el CSS le pone
 * font-variant-numeric: tabular-nums, así que su ancho tampoco cambia dígito
 * a dígito mientras cuenta.
 *
 * Accesibilidad: el <span> que cuenta es aria-hidden y tiene al lado un
 * gemelo .es-visually-hidden con el valor final estable (lo emite el render,
 * no este script), así que un lector de pantalla nunca puede leer un valor
 * intermedio del conteo.
 *
 * @package estavillo-child
 */
(function () {
	'use strict';

	var DURATION = 800; // ms — "rápida y discreta", no un contador de dashboard
	var STAGGER = 60; // ms entre stats, tope de 5 pasos
	var STAGGER_MAX_STEPS = 5;

	/**
	 * Descompone el texto de un stat en prefijo + número + sufijo.
	 *
	 * Soporta lo que el portfolio usa de verdad y algo más: "312", "+40%",
	 * "−20%" (con el signo menos tipográfico U+2212, que es el que usan los
	 * casos reales), "1.5x", "1,200", "$12k". Si no hay ningún dígito
	 * ("N/A", "Q3"), devuelve null y ese stat se deja como está.
	 *
	 * @param {string} raw Texto original del número.
	 * @return {Object|null}
	 */
	function parseValue(raw) {
		var m = /^([^\d]*)(\d[\d.,]*)([\s\S]*)$/.exec(raw);
		if (!m) {
			return null;
		}

		// Un separador al final no es un separador, es puntuación del sufijo.
		var body = m[2].replace(/[.,]+$/, '');
		if (!body) {
			return null;
		}
		var suffix = m[2].slice(body.length) + m[3];

		// ¿El último separador es coma decimal o separador de miles? Sólo es
		// de miles si lo siguen exactamente 3 dígitos Y el número entero
		// respeta el patrón de agrupación completo (1,200 / 1.200.000).
		var lastSep = Math.max(body.lastIndexOf('.'), body.lastIndexOf(','));
		var decimals = 0;
		var decSep = '.';
		if (lastSep > -1) {
			var tail = body.slice(lastSep + 1);
			var grouped = /^\d{1,3}(?:[.,]\d{3})+$/.test(body);
			if (!(3 === tail.length && grouped)) {
				decSep = body.charAt(lastSep);
				decimals = tail.length;
			}
		}

		var digits = body.replace(/[.,]/g, '');
		var value = parseFloat(
			decimals
				? digits.slice(0, digits.length - decimals) + '.' + digits.slice(digits.length - decimals)
				: digits
		);
		if (!isFinite(value)) {
			return null;
		}

		// Separador de miles realmente presente en el original, para poder
		// reconstruir el mismo formato en cada frame.
		var intSource = decimals ? body.slice(0, lastSep) : body;
		var groupMatch = /[.,]/.exec(intSource);

		return {
			prefix: m[1],
			suffix: suffix,
			value: value,
			decimals: decimals,
			decSep: decSep,
			groupSep: groupMatch ? groupMatch[0] : '',
		};
	}

	/**
	 * Reconstruye el texto del stat para un valor intermedio, con el mismo
	 * prefijo, sufijo, decimales y separador de miles que el original.
	 *
	 * @param {Object} parsed Resultado de parseValue().
	 * @param {number} value  Valor actual del conteo.
	 * @return {string}
	 */
	function formatValue(parsed, value) {
		var text = parsed.decimals ? value.toFixed(parsed.decimals) : String(Math.round(value));
		var parts = text.split('.');
		var intPart = parts[0];

		if (parsed.groupSep) {
			intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, parsed.groupSep);
		}

		return parsed.prefix + intPart + (parts[1] ? parsed.decSep + parts[1] : '') + parsed.suffix;
	}

	/**
	 * ¿Alguna parte del elemento está dentro del viewport ahora mismo?
	 *
	 * @param {Element} node
	 * @return {boolean}
	 */
	function isInViewport(node) {
		var rect = node.getBoundingClientRect();
		var height = window.innerHeight || document.documentElement.clientHeight;
		return rect.top < height && rect.bottom > 0;
	}

	function easeOutCubic(t) {
		return 1 - Math.pow(1 - t, 3);
	}

	/**
	 * Corre el conteo de todos los stats de un bloque, con un escalonado
	 * corto entre uno y otro. Un solo requestAnimationFrame para el grupo
	 * entero (no uno por número).
	 *
	 * @param {Object[]} targets Lista de { node, parsed, delay }.
	 */
	function run(targets) {
		var start = null;

		function frame(now) {
			if (null === start) {
				start = now;
			}
			var pending = false;

			for (var i = 0; i < targets.length; i++) {
				var target = targets[i];
				if (target.done) {
					continue;
				}
				var elapsed = now - start - target.delay;
				if (elapsed < 0) {
					pending = true;
					continue;
				}
				if (elapsed >= DURATION) {
					// Se restaura el string ORIGINAL, no un valor formateado:
					// así el final es idéntico byte a byte al HTML del server,
					// pase lo que pase con el parseo del formato.
					target.node.textContent = target.original;
					target.done = true;
					continue;
				}
				target.node.textContent = formatValue(
					target.parsed,
					target.parsed.value * easeOutCubic(elapsed / DURATION)
				);
				pending = true;
			}

			if (pending) {
				window.requestAnimationFrame(frame);
			}
		}

		window.requestAnimationFrame(frame);
	}

	/**
	 * Prepara un bloque: pone sus números en cero y devuelve los targets.
	 * Devuelve null si ningún número del bloque es animable.
	 *
	 * @param {Element} root Contenedor .es-case-stats[data-es-stats-animate].
	 * @return {Object[]|null}
	 */
	function prepare(root) {
		var nodes = root.querySelectorAll('[data-es-count]');
		var targets = [];

		for (var i = 0; i < nodes.length; i++) {
			var node = nodes[i];
			var original = node.textContent;
			var parsed = parseValue(original);
			if (!parsed) {
				continue; // "N/A", "Q3"… se quedan como están
			}
			targets.push({
				node: node,
				parsed: parsed,
				original: original,
				delay: Math.min(targets.length, STAGGER_MAX_STEPS) * STAGGER,
				done: false,
			});
		}

		if (!targets.length) {
			return null;
		}

		for (var j = 0; j < targets.length; j++) {
			targets[j].node.textContent = formatValue(targets[j].parsed, 0);
		}

		return targets;
	}

	function init() {
		var blocks = document.querySelectorAll('.es-case-stats[data-es-stats-animate]');
		if (!blocks.length) {
			return;
		}

		// Sin IntersectionObserver no hay disparador confiable y no vale la
		// pena un fallback con listeners de scroll: se deja el valor final.
		if (!('IntersectionObserver' in window)) {
			return;
		}

		// Regla 1: reduced-motion no anima nada, ni siquiera pone en cero.
		var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)');
		if (reduced && reduced.matches) {
			return;
		}

		// Fase de LECTURA completa antes de cualquier escritura: primero se
		// mide qué bloques están fuera de pantalla (regla 2), después se
		// escriben los ceros. Mezclar las dos cosas forzaría un reflow por
		// bloque.
		var candidates = [];
		for (var i = 0; i < blocks.length; i++) {
			if (!isInViewport(blocks[i])) {
				candidates.push(blocks[i]);
			}
		}
		if (!candidates.length) {
			return;
		}

		var pending = [];
		for (var j = 0; j < candidates.length; j++) {
			var targets = prepare(candidates[j]);
			if (targets) {
				pending.push({ root: candidates[j], targets: targets });
			}
		}
		if (!pending.length) {
			return;
		}

		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (!entry.isIntersecting) {
						return;
					}
					observer.unobserve(entry.target); // regla 3: una sola vez
					for (var k = 0; k < pending.length; k++) {
						if (pending[k].root === entry.target) {
							run(pending[k].targets);
							break;
						}
					}
				});
			},
			// Margen inferior negativo: el conteo arranca cuando el bloque
			// entró de verdad, no cuando asoma su primer pixel. threshold 0
			// a propósito — con un threshold por porcentaje, un bloque más
			// alto que el viewport (mobile, 7 stats a ancho completo) podría
			// no alcanzarlo nunca y quedarse en cero.
			{ rootMargin: '0px 0px -12% 0px', threshold: 0 }
		);

		for (var m = 0; m < pending.length; m++) {
			observer.observe(pending[m].root);
		}
	}

	if ('loading' === document.readyState) {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
