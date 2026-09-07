/**
 * ESTAVILLO — REstimator Design System: navegación del documento
 * ---------------------------------------------------------------------------
 * Dos cosas, con UN solo cálculo de "qué sección está activa":
 *
 *  1. Scroll-spy del rail (desktop). Port del script inline que traía el
 *     documento original, acotado a `.re-doc` y sacado del HTML.
 *  2. Subnav compacta (≤1100px). Debajo de ese ancho el Design System deja de
 *     tener rail sticky y lo convierte en un bloque estático: con 12 secciones
 *     eso son ~560px de links antes del primer contenido, o sea que en un
 *     teléfono el documento empieza fuera de la primera pantalla. La subnav lo
 *     reemplaza por una barra de una línea que dice en qué sección estás y
 *     despliega el resto.
 *
 * 1100px NO es un breakpoint nuevo: es el que el propio DS ya usa para soltar
 * el rail (assets/css/ds-restimator/doc.css).
 *
 * La subnav se CONSTRUYE A PARTIR DEL RAIL, no de una lista propia: si el
 * documento gana o pierde una sección, aparece sola y no hay dos fuentes que
 * mantener sincronizadas.
 *
 * Mejora progresiva: sin este script el rail sigue siendo la lista de anchors
 * de siempre y navega perfecto — sólo no se resalta el ítem actual y en mobile
 * se ve el bloque largo. El rail se oculta ÚNICAMENTE cuando la subnav ya está
 * montada (clase en <body>), así un fallo de JS nunca deja la página sin
 * navegación.
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

	var targets = links.map(function (a) {
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

	/*
	 * A partir de qué altura una sección "cuenta" como activa.
	 *
	 * 120px es el valor del script original y sigue siendo el de desktop. En
	 * mobile no alcanza: la sección aterriza bajo el header MÁS la subnav, o
	 * sea alrededor de 122px, así que con el umbral fijo el spy seguía
	 * marcando la sección ANTERIOR justo después de saltar — el trigger decía
	 * "07" habiendo elegido "08". Se mide la barra en vez de adivinarla.
	 */
	var OFFSET_BASE = 120;

	function offset() {
		if (!sub) {
			return OFFSET_BASE;
		}
		return Math.round(sub.root.getBoundingClientRect().bottom) + 8;
	}
	var ticking = false;
	var current = -1;

	/* ======================================================================
	   Subnav (≤1100px)
	   ====================================================================== */

	var MQ = '(max-width: 1100px)';
	var mq = window.matchMedia ? window.matchMedia(MQ) : null;
	var sub = null; // { root, trigger, label, num, menu, items }

	function buildSubnav() {
		if (sub) {
			return;
		}
		var main = document.querySelector('.re-doc .main');
		if (!main) {
			return;
		}

		var root = document.createElement('nav');
		root.className = 'ds-subnav';
		root.setAttribute('aria-label', rail.getAttribute('aria-label') || 'Design System');

		var trigger = document.createElement('button');
		trigger.type = 'button';
		trigger.className = 'ds-subnav__trigger';
		trigger.setAttribute('aria-expanded', 'false');
		trigger.id = 'ds-subnav-trigger';

		var num = document.createElement('span');
		num.className = 'ds-subnav__num';
		var label = document.createElement('span');
		label.className = 'ds-subnav__label';
		var chev = document.createElement('span');
		chev.className = 'ds-subnav__chev';
		chev.setAttribute('aria-hidden', 'true');
		chev.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>';
		trigger.appendChild(num);
		trigger.appendChild(label);
		trigger.appendChild(chev);

		var menu = document.createElement('div');
		menu.className = 'ds-subnav__menu';
		menu.id = 'ds-subnav-menu';
		menu.hidden = true;
		trigger.setAttribute('aria-controls', menu.id);

		var items = links.map(function (a) {
			var item = document.createElement('a');
			item.className = 'ds-subnav__item';
			item.href = a.getAttribute('href');
			var n = a.querySelector('.n');
			item.innerHTML =
				'<span class="ds-subnav__inum">' + (n ? n.textContent : '') + '</span>' +
				'<span class="ds-subnav__itext"></span>';
			// textContent y no innerHTML: el label del rail puede traer entidades
			// y acá sólo hace falta el texto.
			item.querySelector('.ds-subnav__itext').textContent = labelOf(a);
			menu.appendChild(item);
			return item;
		});

		root.appendChild(trigger);
		root.appendChild(menu);
		main.insertBefore(root, main.firstChild);

		sub = { root: root, trigger: trigger, label: label, num: num, menu: menu, items: items };
		wireSubnav();
		document.body.classList.add('es-ds-subnav-on');
		paint(current === -1 ? 0 : current);
	}

	function destroySubnav() {
		if (!sub) {
			return;
		}
		sub.root.parentNode.removeChild(sub.root);
		sub = null;
		document.body.classList.remove('es-ds-subnav-on');
	}

	/** Texto del ítem del rail sin su número. */
	function labelOf(a) {
		var n = a.querySelector('.n');
		var text = a.textContent || '';
		if (n) {
			text = text.replace(n.textContent, '');
		}
		return text.trim();
	}

	function isOpen() {
		return sub && sub.trigger.getAttribute('aria-expanded') === 'true';
	}

	function openMenu() {
		if (!sub) {
			return;
		}
		sub.menu.hidden = false;
		sub.trigger.setAttribute('aria-expanded', 'true');
	}

	function closeMenu(focusTrigger) {
		if (!sub) {
			return;
		}
		sub.menu.hidden = true;
		sub.trigger.setAttribute('aria-expanded', 'false');
		if (focusTrigger) {
			sub.trigger.focus();
		}
	}

	function wireSubnav() {
		sub.trigger.addEventListener('click', function () {
			if (isOpen()) {
				closeMenu(false);
			} else {
				openMenu();
			}
		});

		sub.items.forEach(function (item, i) {
			item.addEventListener('click', function () {
				// Se cierra ANTES de que el navegador salte al anchor: si no, el
				// menú abierto tapa la sección a la que se acaba de ir.
				closeMenu(false);
				paint(i);
			});
		});

		// Escape cierra y devuelve el foco al disparador; el click afuera cierra
		// sin moverlo.
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && isOpen()) {
				closeMenu(true);
			}
		});
		document.addEventListener('click', function (e) {
			if (isOpen() && !sub.root.contains(e.target)) {
				closeMenu(false);
			}
		});
	}

	/* ======================================================================
	   Estado activo — un cálculo, dos consumidores
	   ====================================================================== */

	function paint(best) {
		for (var j = 0; j < links.length; j++) {
			var on = j === best;
			links[j].classList.toggle('on', on);
			if (on) {
				links[j].setAttribute('aria-current', 'true');
			} else {
				links[j].removeAttribute('aria-current');
			}
		}
		if (sub) {
			var a = links[best];
			var n = a ? a.querySelector('.n') : null;
			sub.num.textContent = n ? n.textContent : '';
			sub.label.textContent = a ? labelOf(a) : '';
			sub.items.forEach(function (item, i) {
				item.classList.toggle('is-current', i === best);
				if (i === best) {
					item.setAttribute('aria-current', 'true');
				} else {
					item.removeAttribute('aria-current');
				}
			});
		}
	}

	function sync() {
		ticking = false;

		var best = -1;
		var off = offset();
		for (var i = 0; i < targets.length; i++) {
			var t = targets[i];
			if (t && t.getBoundingClientRect().top <= off) {
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
		paint(best);
	}

	function request() {
		if (!ticking) {
			ticking = true;
			window.requestAnimationFrame(sync);
		}
	}

	function applyMode() {
		if (mq && mq.matches) {
			buildSubnav();
		} else {
			closeMenu(false);
			destroySubnav();
		}
	}

	window.addEventListener('scroll', request, { passive: true });
	window.addEventListener('resize', request, { passive: true });

	if (mq) {
		// addEventListener('change') no existe en Safari viejo.
		if (mq.addEventListener) {
			mq.addEventListener('change', applyMode);
		} else if (mq.addListener) {
			mq.addListener(applyMode);
		}
	}

	applyMode();
	sync();
})();
