/**
 * ESTAVILLO — Hero "System Map"
 * -----------------------------------------------------------------------
 * Mapa técnico/blueprint animado en SVG inline + rAF. Cero librerías.
 *
 * Comportamiento (ver brief):
 *  - Carga: líneas finas y nodos aparecen gradualmente (~2.5s);
 *    el camino de decisión (acento) se dibuja al final, sutil.
 *  - Después: casi estático, con "respiración" muy leve en algunos nodos.
 *  - Hover / touch: nodos y conexiones cercanas se iluminan suavemente
 *    (capa acento con fade, sin partículas ni persecución de cursor).
 *  - prefers-reduced-motion o variante static_fallback: frame final
 *    estático, sin listeners ni rAF.
 *
 * Variantes (Customizer → Estavillo):
 *  - data-hero-desktop: system_map | static_fallback
 *  - data-hero-mobile:  system_map_subtle | static_fallback
 *    (la sutileza mobile la resuelve hero.css vía opacidad; acá solo
 *     se decide si se anima o no y se omite el seguimiento de puntero).
 *
 * Colores: siempre var(--es-*) — el acento verde/naranja y el modo claro
 * se resuelven solos desde tokens.css.
 */
(function () {
	'use strict';

	var NS = 'http://www.w3.org/2000/svg';
	var VIEW_W = 880;
	var VIEW_H = 640;
	var LOAD_MS = 2500; // duración total de la entrada
	var GLOW_RADIUS = 170; // radio de iluminación alrededor del puntero

	/* ---------- geometría autorada (determinista, sin RNG en layout) ---------- */

	// Trazas ortogonales de fondo: cada una es una lista de puntos [x,y].
	var TRACES = [
		[[60, 120], [300, 120], [300, 200], [420, 200]],
		[[120, 60], [120, 260], [240, 260]],
		[[480, 80], [700, 80], [700, 180]],
		[[760, 120], [760, 320], [640, 320]],
		[[100, 420], [260, 420], [260, 520], [380, 520]],
		[[540, 480], [720, 480], [720, 400]],
		[[200, 340], [360, 340], [360, 300]],
		[[620, 240], [560, 240], [560, 300], [500, 300]],
		[[320, 560], [560, 560]],
		[[800, 440], [800, 560], [660, 560]]
	];

	// Camino de decisión (acento): la ambigüedad encuentra su ruta.
	var DECISION = [[60, 340], [200, 340], [200, 300], [430, 300], [430, 360], [640, 360], [640, 300], [820, 300]];

	// Nodos circulares (uniones y extremos relevantes).
	var DOTS = [
		[60, 120], [300, 120], [300, 200], [420, 200], [120, 60], [240, 260],
		[480, 80], [700, 180], [760, 120], [640, 320], [100, 420], [380, 520],
		[540, 480], [720, 400], [200, 340], [620, 240], [500, 300], [320, 560],
		[560, 560], [660, 560], [800, 440]
	];

	// Nodos cuadrados (módulos menores).
	var SQUARES = [[240, 420], [700, 80], [360, 300]];

	// Marcos punteados (zonas del sistema, sin texto: abstractas).
	var FRAMES = [
		[470, 60, 250, 140],
		[90, 400, 200, 150]
	];

	// Marcas de registro (+) en esquinas.
	var MARKS = [[60, 60], [820, 80], [70, 580], [830, 560]];

	// Nodo de decisión principal (cuadrado acento sobre el camino).
	var ACCENT_NODE = [430, 360];
	// Nodo final del camino (anillo).
	var ACCENT_END = [820, 300];

	/* ---------- helpers ---------- */

	function make(name, attrs, parent) {
		var n = document.createElementNS(NS, name);
		for (var k in attrs) {
			if (Object.prototype.hasOwnProperty.call(attrs, k)) {
				n.setAttribute(k, attrs[k]);
			}
		}
		if (parent) {
			parent.appendChild(n);
		}
		return n;
	}

	function polyPoints(pts) {
		var out = [];
		for (var i = 0; i < pts.length; i++) {
			out.push(pts[i][0] + ',' + pts[i][1]);
		}
		return out.join(' ');
	}

	function polyLength(pts) {
		var len = 0;
		for (var i = 1; i < pts.length; i++) {
			len += Math.abs(pts[i][0] - pts[i - 1][0]) + Math.abs(pts[i][1] - pts[i - 1][1]);
		}
		return len;
	}

	function midpoint(pts) {
		var a = pts[Math.floor((pts.length - 1) / 2)];
		var b = pts[Math.ceil((pts.length - 1) / 2)];
		return [(a[0] + b[0]) / 2, (a[1] + b[1]) / 2];
	}

	function smooth(t) {
		if (t <= 0) { return 0; }
		if (t >= 1) { return 1; }
		return t * t * (3 - 2 * t);
	}

	/* ---------- construcción de un mapa ---------- */

	function buildMap(host) {
		var hero = host.closest('.es-hero') || host;
		var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		var mqMobile = window.matchMedia('(max-width: 1023px)');
		var variantDesktop = hero.getAttribute('data-hero-desktop') || 'system_map';
		var variantMobile = hero.getAttribute('data-hero-mobile') || 'system_map_subtle';

		function isMobile() { return mqMobile.matches; }
		function variant() { return isMobile() ? variantMobile : variantDesktop; }

		var isStatic = reduced || variant() === 'static_fallback';
		if (variant() === 'static_fallback') {
			hero.classList.add('es-hero--static');
		}

		var svg = make('svg', {
			viewBox: '0 0 ' + VIEW_W + ' ' + VIEW_H,
			preserveAspectRatio: 'xMidYMid meet',
			'aria-hidden': 'true',
			focusable: 'false'
		}, host);

		/* --- capa 1: retícula de puntos --- */
		var gGrid = make('g', {}, svg);
		gGrid.style.opacity = '0';
		for (var gx = 40; gx <= VIEW_W - 40; gx += 80) {
			for (var gy = 40; gy <= VIEW_H - 40; gy += 80) {
				var d = make('circle', { cx: gx, cy: gy, r: 1.1 }, gGrid);
				d.style.fill = 'var(--es-line-strong)';
			}
		}

		/* --- registro de elementos animables --- */
		// item = { el, glowEl, x, y, t0, t1, finalOp, len }
		var items = [];

		function register(el, glowEl, cx, cy, finalOp, len) {
			items.push({ el: el, glowEl: glowEl, x: cx, y: cy, finalOp: finalOp, len: len || 0, glow: 0, glowT: 0, breathe: false, phase: 0 });
		}

		var gBase = make('g', {}, svg);
		gBase.style.cssText = 'fill:none;stroke-linecap:round;stroke-linejoin:round;';
		var gGlow = make('g', {}, svg);
		gGlow.style.cssText = 'fill:none;stroke-linecap:round;stroke-linejoin:round;pointer-events:none;';

		/* --- marcos punteados --- */
		FRAMES.forEach(function (f) {
			var r = make('rect', { x: f[0], y: f[1], width: f[2], height: f[3], rx: 6, 'stroke-dasharray': '5 7' }, gBase);
			r.style.cssText = 'stroke:var(--es-line-strong);stroke-width:1;opacity:0;';
			register(r, null, f[0] + f[2] / 2, f[1] + f[3] / 2, 0.5, 0);
		});

		/* --- marcas de registro --- */
		MARKS.forEach(function (m) {
			var g = make('g', {}, gBase);
			var l1 = make('line', { x1: m[0] - 7, y1: m[1], x2: m[0] + 7, y2: m[1] }, g);
			var l2 = make('line', { x1: m[0], y1: m[1] - 7, x2: m[0], y2: m[1] + 7 }, g);
			l1.style.cssText = l2.style.cssText = 'stroke:var(--es-line-strong);stroke-width:1;';
			g.style.opacity = '0';
			register(g, null, m[0], m[1], 0.6, 0);
		});

		/* --- trazas + su capa de iluminación --- */
		TRACES.forEach(function (pts) {
			var len = polyLength(pts);
			var p = make('polyline', { points: polyPoints(pts), 'stroke-dasharray': len, 'stroke-dashoffset': len }, gBase);
			p.style.cssText = 'stroke:var(--es-ink-4);stroke-width:1.1;opacity:0;';
			var glow = make('polyline', { points: polyPoints(pts) }, gGlow);
			glow.style.cssText = 'stroke:var(--es-accent);stroke-width:1.2;opacity:0;';
			var mid = midpoint(pts);
			register(p, glow, mid[0], mid[1], 0.55, len);
		});

		/* --- camino de decisión (acento) --- */
		var decisionLen = polyLength(DECISION);
		var decision = make('polyline', { points: polyPoints(DECISION), 'stroke-dasharray': decisionLen, 'stroke-dashoffset': decisionLen }, gBase);
		decision.style.cssText = 'stroke:var(--es-accent);stroke-width:1.6;opacity:0;';
		var decisionItem = { el: decision, glowEl: null, x: 440, y: 330, finalOp: 0.95, len: decisionLen, glow: 0, glowT: 0, breathe: false, phase: 0, isDecision: true };
		items.push(decisionItem);

		/* --- nodos circulares --- */
		DOTS.forEach(function (n, i) {
			var c = make('circle', { cx: n[0], cy: n[1], r: 3 }, gBase);
			c.style.cssText = 'fill:var(--es-ink-3);opacity:0;';
			var glow = make('circle', { cx: n[0], cy: n[1], r: 3.4 }, gGlow);
			glow.style.cssText = 'fill:var(--es-accent);opacity:0;';
			register(c, glow, n[0], n[1], 0.85, 0);
			// respiración: uno de cada tres nodos, con fase distinta
			var it = items[items.length - 1];
			it.breathe = i % 3 === 0;
			it.phase = i * 1.7;
		});

		/* --- nodos cuadrados --- */
		SQUARES.forEach(function (s) {
			var r = make('rect', { x: s[0] - 5, y: s[1] - 5, width: 10, height: 10 }, gBase);
			r.style.cssText = 'stroke:var(--es-ink-3);stroke-width:1.1;fill:none;opacity:0;';
			var glow = make('rect', { x: s[0] - 5, y: s[1] - 5, width: 10, height: 10 }, gGlow);
			glow.style.cssText = 'stroke:var(--es-accent);stroke-width:1.2;fill:none;opacity:0;';
			register(r, glow, s[0], s[1], 0.8, 0);
		});

		/* --- nodo de decisión (cuadrado acento) y anillo final --- */
		var an = make('rect', { x: ACCENT_NODE[0] - 8, y: ACCENT_NODE[1] - 8, width: 16, height: 16 }, gBase);
		an.style.cssText = 'stroke:var(--es-accent);stroke-width:1.6;fill:var(--es-accent-soft);opacity:0;';
		register(an, null, ACCENT_NODE[0], ACCENT_NODE[1], 1, 0);
		items[items.length - 1].breathe = true;
		items[items.length - 1].phase = 0.5;

		var ring = make('circle', { cx: ACCENT_END[0], cy: ACCENT_END[1], r: 7 }, gBase);
		ring.style.cssText = 'stroke:var(--es-accent);stroke-width:1.4;fill:none;opacity:0;';
		var ringDot = make('circle', { cx: ACCENT_END[0], cy: ACCENT_END[1], r: 2.4 }, gBase);
		ringDot.style.cssText = 'fill:var(--es-accent);opacity:0;';
		register(ring, null, ACCENT_END[0], ACCENT_END[1], 0.95, 0);
		register(ringDot, null, ACCENT_END[0], ACCENT_END[1], 0.95, 0);

		/* --- timeline de entrada: barrido de arriba-izquierda a abajo-derecha --- */
		items.forEach(function (it) {
			var sweep = (it.x + it.y * 0.6) / (VIEW_W + VIEW_H * 0.6); // 0..1
			it.t0 = sweep * 1300; // inicio escalonado dentro del primer 1.3s
			it.t1 = it.t0 + (it.len > 0 ? 750 : 450);
		});
		// el camino de decisión entra al final, con dibujo más largo
		decisionItem.t0 = 1450;
		decisionItem.t1 = 2450;

		/* --- render de un instante t (ms desde inicio de carga) --- */
		function renderLoad(t) {
			items.forEach(function (it) {
				var p = smooth((t - it.t0) / (it.t1 - it.t0));
				it.el.style.opacity = String(it.finalOp * p);
				if (it.len > 0) {
					it.el.setAttribute('stroke-dashoffset', String(it.len * (1 - p)));
				}
			});
			gGrid.style.opacity = String(0.5 * smooth(t / 1200));
		}

		function renderFinal() {
			renderLoad(LOAD_MS + 1000);
		}

		/* ============ modo estático: frame final y listo ============ */
		if (isStatic) {
			renderFinal();
			return;
		}

		/* ============ modo animado ============ */

		var loadStart = null;
		var loadDone = false;
		var raf = 0;
		var visible = true;
		var pointer = { x: -9999, y: -9999, active: false };
		var touchTimer = 0;

		/* iluminación por cercanía: actualiza glows objetivo */
		function updateGlowTargets() {
			items.forEach(function (it) {
				if (!pointer.active) {
					it.glowT = 0;
					return;
				}
				var dx = it.x - pointer.x;
				var dy = it.y - pointer.y;
				var dist = Math.sqrt(dx * dx + dy * dy);
				it.glowT = smooth(1 - dist / GLOW_RADIUS);
			});
		}

		function frame(now) {
			raf = 0;
			var energy = 0;

			if (!loadDone) {
				if (loadStart === null) { loadStart = now; }
				var t = now - loadStart;
				renderLoad(t);
				if (t >= LOAD_MS) { loadDone = true; }
				energy = 1;
			} else {
				// respiración: oscilación leve de opacidad en nodos marcados
				var breathe = Math.sin(now / 2600);
				items.forEach(function (it) {
					var op = it.finalOp;
					if (it.breathe) {
						op += Math.sin(now / 2600 + it.phase) * 0.1;
					}
					// iluminación con inercia (lerp suave)
					var d = (it.glowT - it.glow) * 0.08;
					it.glow += d;
					energy += Math.abs(d);
					if (it.glowEl) {
						it.glowEl.style.opacity = String(it.glow * (it.len > 0 ? 0.5 : 0.9));
					}
					if (it.isDecision) {
						op = Math.min(1, op + it.glow * 0.05);
					}
					it.el.style.opacity = String(Math.max(0, op + (it.glowEl ? it.glow * 0.15 : 0)));
				});
				if (breathe !== undefined) { energy += 0.001; } // respiración mantiene un tick mínimo
			}

			// seguimos animando solo si hay algo que mover y somos visibles
			if (visible && !document.hidden) {
				raf = window.requestAnimationFrame(frame);
			}
		}

		function wake() {
			if (!raf && visible && !document.hidden) {
				raf = window.requestAnimationFrame(frame);
			}
		}

		/* --- visibilidad: el rAF duerme fuera de viewport / pestaña oculta --- */
		if ('IntersectionObserver' in window) {
			var io = new IntersectionObserver(function (entries) {
				visible = entries[0].isIntersecting;
				if (visible) { wake(); }
			}, { threshold: 0.05 });
			io.observe(hero);
		}
		document.addEventListener('visibilitychange', wake);

		/* --- puntero: hover en desktop, pulso en touch --- */
		function toViewCoords(e) {
			var r = svg.getBoundingClientRect();
			// preserveAspectRatio=meet: el viewBox se escala uniforme y centra
			var scale = Math.min(r.width / VIEW_W, r.height / VIEW_H);
			var ox = r.left + (r.width - VIEW_W * scale) / 2;
			var oy = r.top + (r.height - VIEW_H * scale) / 2;
			return { x: (e.clientX - ox) / scale, y: (e.clientY - oy) / scale };
		}

		hero.addEventListener('pointermove', function (e) {
			if (e.pointerType === 'touch') { return; }
			var p = toViewCoords(e);
			pointer.x = p.x;
			pointer.y = p.y;
			pointer.active = true;
			updateGlowTargets();
			wake();
		});

		hero.addEventListener('pointerleave', function () {
			pointer.active = false;
			updateGlowTargets();
			wake();
		});

		// touch: un pulso local que decae solo (sin seguir el dedo)
		hero.addEventListener('pointerdown', function (e) {
			if (e.pointerType !== 'touch') { return; }
			var p = toViewCoords(e);
			pointer.x = p.x;
			pointer.y = p.y;
			pointer.active = true;
			updateGlowTargets();
			wake();
			window.clearTimeout(touchTimer);
			touchTimer = window.setTimeout(function () {
				pointer.active = false;
				updateGlowTargets();
				wake();
			}, 1400);
		}, { passive: true });

		wake();
	}

	/* ---------- init ---------- */

	function init() {
		var hosts = document.querySelectorAll('[data-es-hero-map]');
		for (var i = 0; i < hosts.length; i++) {
			try {
				buildMap(hosts[i]);
			} catch (err) {
				// nunca romper la página por el decorado
				if (window.console && console.error) { console.error('es-hero-system-map', err); }
			}
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
