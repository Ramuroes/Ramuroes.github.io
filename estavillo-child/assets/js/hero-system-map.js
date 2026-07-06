/**
 * ESTAVILLO — Hero engines
 * -----------------------------------------------------------------------
 * Mapas técnicos/blueprint animados en SVG inline + rAF. Cero librerías.
 *
 * Registro extensible: los motores se registran por nombre en
 * window.EstavilloHero (ver "registro de motores" abajo). Un dispatcher
 * elige el motor según la variante del hero y solo construye la geometría
 * que se usa. Motores nuevos se agregan sin reescribir el tema.
 *
 * Motores incorporados:
 *  - network_constellation (DEFAULT) — red viva de nodos (constelación) que
 *    se ensambla una vez (BFS desde un nodo raíz, foco verde con halo/anillo)
 *    y queda en un idle muy sutil (respiración + glow radar). Hover: campo de
 *    proximidad amortiguado; mobile: scroll + parallax detrás del texto.
 *    Alias: "network_constellation_subtle" (mobile).
 *  - system_map_nodes — campo ambiente de nodos y trazas con diamante de
 *    decisión naranja. Aliases: "system_map", "system_map_subtle". (No está
 *    en el Customizer por defecto, pero queda registrado.)
 *  - blueprint_flow (opcional) — el motivo Fig.00 del sistema visual: un
 *    flujo inputs → decide → resolve que se ensambla una vez y se mantiene
 *    (sin loop, sin persecución de cursor). Ver reference/design-system.
 *  - static_fallback — modificador: dibuja el motor por defecto en su frame
 *    final estático, sin listeners ni rAF.
 *
 * Semántica de color (§05, fija por tokens, NO por el acento global; el
 * naranja se usa con moderación: un solo punto de decisión por vista):
 *   verde  var(--es-signal)   = camino activo / resuelto / señal viva
 *   naranja var(--es-decision) = el único punto de decisión / foco
 *
 * Accesibilidad: prefers-reduced-motion y static_fallback pintan el frame
 * final al instante. El rAF duerme fuera de viewport y con pestaña oculta.
 */
(function () {
	'use strict';

	var NS = 'http://www.w3.org/2000/svg';
	var VIEW_W = 880;
	var VIEW_H = 640;

	/* ===================== helpers compartidos ===================== */

	function make(name, attrs, parent) {
		var n = document.createElementNS(NS, name);
		for (var k in attrs) {
			if (Object.prototype.hasOwnProperty.call(attrs, k)) {
				n.setAttribute(k, attrs[k]);
			}
		}
		if (parent) { parent.appendChild(n); }
		return n;
	}

	function polyPoints(pts) {
		var out = [];
		for (var i = 0; i < pts.length; i++) { out.push(pts[i][0] + ',' + pts[i][1]); }
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

	function diamondPoints(cx, cy, r) {
		return [[cx, cy - r], [cx + r, cy], [cx, cy + r], [cx - r, cy]];
	}

	function newSvg(host) {
		// El posicionamiento (top/right/tamaño/mask, desktop y mobile) lo
		// resuelve hero.css en `.es-hero__visual svg`. Acá NO seteamos
		// posición ni tamaño inline para no pisar esas reglas.
		var svg = make('svg', {
			viewBox: '0 0 ' + VIEW_W + ' ' + VIEW_H,
			preserveAspectRatio: 'xMidYMid meet',
			'aria-hidden': 'true',
			focusable: 'false'
		}, host);
		svg.style.display = 'block';
		return svg;
	}

	// coordenadas de puntero → espacio del viewBox (preserveAspectRatio=meet)
	function toViewCoords(svg, e) {
		var r = svg.getBoundingClientRect();
		var scale = Math.min(r.width / VIEW_W, r.height / VIEW_H);
		var ox = r.left + (r.width - VIEW_W * scale) / 2;
		var oy = r.top + (r.height - VIEW_H * scale) / 2;
		return { x: (e.clientX - ox) / scale, y: (e.clientY - oy) / scale };
	}

	function resolveContext(host) {
		var hero = host.closest('.es-hero') || host;
		var reduced = !!(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches);
		var mqMobile = window.matchMedia('(max-width: 1023px)');
		var vDesktop = hero.getAttribute('data-hero-desktop') || 'system_map_nodes';
		var vMobile = hero.getAttribute('data-hero-mobile') || 'system_map_subtle';
		return {
			hero: hero,
			reduced: reduced,
			isMobile: function () { return mqMobile.matches; },
			variant: function () { return mqMobile.matches ? vMobile : vDesktop; }
		};
	}

	/* ===================== motor A — system_map_nodes ===================== */

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
	var DECISION = [[60, 340], [200, 340], [200, 300], [430, 300], [430, 360], [640, 360], [640, 300], [820, 300]];
	var DOTS = [
		[60, 120], [300, 120], [300, 200], [420, 200], [120, 60], [240, 260],
		[480, 80], [700, 180], [760, 120], [640, 320], [100, 420], [380, 520],
		[540, 480], [720, 400], [200, 340], [620, 240], [500, 300], [320, 560],
		[560, 560], [660, 560], [800, 440]
	];
	var SQUARES = [[240, 420], [700, 80], [360, 300]];
	var FRAMES = [[470, 60, 250, 140], [90, 400, 200, 150]];
	var MARKS = [[60, 60], [820, 80], [70, 580], [830, 560]];
	var DECISION_NODE = [430, 360]; // punto de decisión (diamante naranja)
	var RESOLVE_NODE = [820, 300]; // resolución (anillo verde)
	var LOAD_MS = 2500;
	var GLOW_RADIUS = 170;

	function buildNodesMap(host, ctx) {
		var svg = newSvg(host);

		var gGrid = make('g', {}, svg);
		gGrid.style.opacity = '0';
		for (var gx = 40; gx <= VIEW_W - 40; gx += 80) {
			for (var gy = 40; gy <= VIEW_H - 40; gy += 80) {
				var d = make('circle', { cx: gx, cy: gy, r: 1.1 }, gGrid);
				d.style.fill = 'var(--es-line-strong)';
			}
		}

		var items = [];
		function register(el, glowEl, cx, cy, finalOp, len) {
			items.push({ el: el, glowEl: glowEl, x: cx, y: cy, finalOp: finalOp, len: len || 0, glow: 0, glowT: 0, breathe: false, phase: 0 });
			return items[items.length - 1];
		}

		var gBase = make('g', {}, svg);
		gBase.style.cssText = 'fill:none;stroke-linecap:round;stroke-linejoin:round;';
		var gGlow = make('g', {}, svg);
		gGlow.style.cssText = 'fill:none;stroke-linecap:round;stroke-linejoin:round;pointer-events:none;';

		FRAMES.forEach(function (f) {
			var r = make('rect', { x: f[0], y: f[1], width: f[2], height: f[3], rx: 6, 'stroke-dasharray': '5 7' }, gBase);
			r.style.cssText = 'stroke:var(--es-line-strong);stroke-width:1;opacity:0;';
			register(r, null, f[0] + f[2] / 2, f[1] + f[3] / 2, 0.5, 0);
		});

		MARKS.forEach(function (m) {
			var g = make('g', {}, gBase);
			var l1 = make('line', { x1: m[0] - 7, y1: m[1], x2: m[0] + 7, y2: m[1] }, g);
			var l2 = make('line', { x1: m[0], y1: m[1] - 7, x2: m[0], y2: m[1] + 7 }, g);
			l1.style.cssText = l2.style.cssText = 'stroke:var(--es-line-strong);stroke-width:1;';
			g.style.opacity = '0';
			register(g, null, m[0], m[1], 0.6, 0);
		});

		// trazas neutras + capa de iluminación verde (señal)
		TRACES.forEach(function (pts) {
			var len = polyLength(pts);
			var p = make('polyline', { points: polyPoints(pts), 'stroke-dasharray': len, 'stroke-dashoffset': len }, gBase);
			p.style.cssText = 'stroke:var(--es-ink-4);stroke-width:1.1;opacity:0;';
			var glow = make('polyline', { points: polyPoints(pts) }, gGlow);
			glow.style.cssText = 'stroke:var(--es-signal);stroke-width:1.2;opacity:0;';
			var mid = midpoint(pts);
			register(p, glow, mid[0], mid[1], 0.55, len);
		});

		// camino resuelto (verde = el sistema)
		var decisionLen = polyLength(DECISION);
		var decision = make('polyline', { points: polyPoints(DECISION), 'stroke-dasharray': decisionLen, 'stroke-dashoffset': decisionLen }, gBase);
		decision.style.cssText = 'stroke:var(--es-signal);stroke-width:1.7;opacity:0;';
		var pathItem = register(decision, null, 440, 330, 0.95, decisionLen);
		pathItem.isPath = true;

		// nodos circulares neutros + glow verde
		DOTS.forEach(function (n, i) {
			var c = make('circle', { cx: n[0], cy: n[1], r: 3 }, gBase);
			c.style.cssText = 'fill:var(--es-ink-3);opacity:0;';
			var glow = make('circle', { cx: n[0], cy: n[1], r: 3.4 }, gGlow);
			glow.style.cssText = 'fill:var(--es-signal);opacity:0;';
			var it = register(c, glow, n[0], n[1], 0.85, 0);
			it.breathe = i % 3 === 0;
			it.phase = i * 1.7;
		});

		SQUARES.forEach(function (s) {
			var r = make('rect', { x: s[0] - 5, y: s[1] - 5, width: 10, height: 10 }, gBase);
			r.style.cssText = 'stroke:var(--es-ink-3);stroke-width:1.1;fill:none;opacity:0;';
			var glow = make('rect', { x: s[0] - 5, y: s[1] - 5, width: 10, height: 10 }, gGlow);
			glow.style.cssText = 'stroke:var(--es-signal);stroke-width:1.2;fill:none;opacity:0;';
			register(r, glow, s[0], s[1], 0.8, 0);
		});

		// punto de decisión: diamante naranja (§05/§08) — el único foco
		var gDec = make('g', {}, gBase);
		var dOuter = make('polygon', { points: polyPoints(diamondPoints(DECISION_NODE[0], DECISION_NODE[1], 15)) }, gDec);
		dOuter.style.cssText = 'fill:none;stroke:var(--es-decision);stroke-width:4;opacity:.28;';
		var dInner = make('polygon', { points: polyPoints(diamondPoints(DECISION_NODE[0], DECISION_NODE[1], 10)) }, gDec);
		dInner.style.cssText = 'fill:var(--es-decision-soft);stroke:var(--es-decision);stroke-width:1.7;';
		var dDot = make('circle', { cx: DECISION_NODE[0], cy: DECISION_NODE[1], r: 3 }, gDec);
		dDot.style.cssText = 'fill:var(--es-decision);';
		gDec.style.opacity = '0';
		var decItem = register(gDec, null, DECISION_NODE[0], DECISION_NODE[1], 1, 0);
		decItem.breathe = true;
		decItem.phase = 0.5;

		// resolución: anillo verde
		var ring = make('circle', { cx: RESOLVE_NODE[0], cy: RESOLVE_NODE[1], r: 7 }, gBase);
		ring.style.cssText = 'stroke:var(--es-signal);stroke-width:1.4;fill:none;opacity:0;';
		var ringDot = make('circle', { cx: RESOLVE_NODE[0], cy: RESOLVE_NODE[1], r: 2.4 }, gBase);
		ringDot.style.cssText = 'fill:var(--es-signal);opacity:0;';
		register(ring, null, RESOLVE_NODE[0], RESOLVE_NODE[1], 0.95, 0);
		register(ringDot, null, RESOLVE_NODE[0], RESOLVE_NODE[1], 0.95, 0);

		items.forEach(function (it) {
			var sweep = (it.x + it.y * 0.6) / (VIEW_W + VIEW_H * 0.6);
			it.t0 = sweep * 1300;
			it.t1 = it.t0 + (it.len > 0 ? 750 : 450);
		});
		pathItem.t0 = 1450;
		pathItem.t1 = 2450;

		function renderLoad(t) {
			items.forEach(function (it) {
				var p = smooth((t - it.t0) / (it.t1 - it.t0));
				it.el.style.opacity = String(it.finalOp * p);
				if (it.len > 0) { it.el.setAttribute('stroke-dashoffset', String(it.len * (1 - p))); }
			});
			gGrid.style.opacity = String(0.5 * smooth(t / 1200));
		}

		if (ctx.isStatic) { renderLoad(LOAD_MS + 1000); return; }

		var loadStart = null, loadDone = false, raf = 0, visible = true;
		var pointer = { x: -9999, y: -9999, active: false };
		var touchTimer = 0;

		function updateGlowTargets() {
			items.forEach(function (it) {
				if (!pointer.active) { it.glowT = 0; return; }
				var dx = it.x - pointer.x, dy = it.y - pointer.y;
				it.glowT = smooth(1 - Math.sqrt(dx * dx + dy * dy) / GLOW_RADIUS);
			});
		}

		function frame(now) {
			raf = 0;
			if (!loadDone) {
				if (loadStart === null) { loadStart = now; }
				var t = now - loadStart;
				renderLoad(t);
				if (t >= LOAD_MS) { loadDone = true; }
			} else {
				items.forEach(function (it) {
					var op = it.finalOp;
					if (it.breathe) { op += Math.sin(now / 2600 + it.phase) * 0.1; }
					var dd = (it.glowT - it.glow) * 0.08;
					it.glow += dd;
					if (it.glowEl) { it.glowEl.style.opacity = String(it.glow * (it.len > 0 ? 0.5 : 0.9)); }
					it.el.style.opacity = String(Math.max(0, op + (it.glowEl ? it.glow * 0.15 : 0)));
				});
			}
			if (visible && !document.hidden) { raf = window.requestAnimationFrame(frame); }
		}
		function wake() { if (!raf && visible && !document.hidden) { raf = window.requestAnimationFrame(frame); } }

		if ('IntersectionObserver' in window) {
			new IntersectionObserver(function (entries) {
				visible = entries[0].isIntersecting;
				if (visible) { wake(); }
			}, { threshold: 0.05 }).observe(ctx.hero);
		}
		document.addEventListener('visibilitychange', wake);

		ctx.hero.addEventListener('pointermove', function (e) {
			if (e.pointerType === 'touch') { return; }
			var p = toViewCoords(svg, e);
			pointer.x = p.x; pointer.y = p.y; pointer.active = true;
			updateGlowTargets(); wake();
		});
		ctx.hero.addEventListener('pointerleave', function () {
			pointer.active = false; updateGlowTargets(); wake();
		});
		ctx.hero.addEventListener('pointerdown', function (e) {
			if (e.pointerType !== 'touch') { return; }
			var p = toViewCoords(svg, e);
			pointer.x = p.x; pointer.y = p.y; pointer.active = true;
			updateGlowTargets(); wake();
			window.clearTimeout(touchTimer);
			touchTimer = window.setTimeout(function () { pointer.active = false; updateGlowTargets(); wake(); }, 1400);
		}, { passive: true });

		wake();
	}

	/* ===================== motor B — blueprint_flow ===================== */
	/* El motivo Fig.00: inputs → decide → resolve, ensamblado una vez y
	   mantenido (sin loop, sin persecución de cursor). Orden de lectura
	   §17: estructura → el camino se dibuja → la decisión se enciende →
	   la resolución cierra; los labels entran al final. */

	function buildBlueprintFlow(host, ctx) {
		var svg = newSvg(host);
		var dense = !ctx.isMobile(); // mobile = versión simplificada

		var anims = []; // {el, kind, t0, t1, finalOp, len, pulseAt}

		function fade(el, finalOp, t0, t1) { anims.push({ el: el, kind: 'fade', finalOp: finalOp, t0: t0, t1: t1 }); }
		function pop(el, finalOp, t0, t1, pulseAt) {
			el.style.transformBox = 'fill-box';
			el.style.transformOrigin = 'center';
			anims.push({ el: el, kind: 'pop', finalOp: finalOp, t0: t0, t1: t1, pulseAt: pulseAt || 0 });
		}
		function draw(el, len, finalOp, t0, t1) {
			el.setAttribute('stroke-dasharray', len);
			el.setAttribute('stroke-dashoffset', len);
			anims.push({ el: el, kind: 'draw', len: len, finalOp: finalOp, t0: t0, t1: t1 });
		}

		var gScaffold = make('g', {}, svg);
		gScaffold.style.cssText = 'fill:none;stroke-linecap:round;stroke-linejoin:round;';
		var gNodes = make('g', {}, svg);
		gNodes.style.cssText = 'fill:none;stroke-linecap:round;stroke-linejoin:round;';
		var gPath = make('g', {}, svg);
		gPath.style.cssText = 'fill:none;stroke-linecap:round;stroke-linejoin:round;';
		var gLabels = make('g', {}, svg);

		var CY = 300;

		/* --- andamiaje neutro --- */
		// marcas de registro en esquinas
		[[60, 60], [820, 80], [70, 560], [830, 560]].forEach(function (m) {
			var g = make('g', {}, gScaffold);
			make('line', { x1: m[0] - 7, y1: m[1], x2: m[0] + 7, y2: m[1] }, g).style.cssText = 'stroke:var(--es-line-strong);stroke-width:1;';
			make('line', { x1: m[0], y1: m[1] - 7, x2: m[0], y2: m[1] + 7 }, g).style.cssText = 'stroke:var(--es-line-strong);stroke-width:1;';
			fade(g, 0.6, 40, 480);
		});
		// línea de cota superior + label mono
		var dim = make('g', {}, gScaffold);
		make('line', { x1: 150, y1: 150, x2: 730, y2: 150 }, dim).style.cssText = 'stroke:var(--es-ink-4);stroke-width:1;';
		make('line', { x1: 150, y1: 144, x2: 150, y2: 156 }, dim).style.cssText = 'stroke:var(--es-ink-4);stroke-width:1;';
		make('line', { x1: 730, y1: 144, x2: 730, y2: 156 }, dim).style.cssText = 'stroke:var(--es-ink-4);stroke-width:1;';
		var dimT = make('text', { x: 440, y: 143, 'text-anchor': 'middle' }, dim);
		dimT.textContent = '680';
		dimT.style.cssText = 'font-family:var(--es-mono);font-size:11px;letter-spacing:.12em;fill:var(--es-ink-4);';
		fade(dim, 0.75, 120, 560);
		// eje punteado por los inputs + lead-in / lead-out punteados
		var ax = make('line', { x1: 120, y1: 200, x2: 120, y2: 400, 'stroke-dasharray': '2 6' }, gScaffold);
		ax.style.cssText = 'stroke:var(--es-ink-4);stroke-width:1;';
		fade(ax, 0.5, 120, 540);
		var lin = make('line', { x1: 44, y1: CY, x2: 120, y2: CY, 'stroke-dasharray': '2 6' }, gScaffold);
		lin.style.cssText = 'stroke:var(--es-ink-4);stroke-width:1;';
		fade(lin, 0.5, 120, 540);
		var lout = make('line', { x1: 802, y1: CY, x2: 860, y2: CY, 'stroke-dasharray': '2 6' }, gScaffold);
		lout.style.cssText = 'stroke:var(--es-ink-4);stroke-width:1;';
		fade(lout, 0.5, 620, 1000);
		// guías neutras desde los inputs inactivos hacia el flujo
		[[120, 220], [120, 380]].forEach(function (p) {
			var g = make('line', { x1: p[0], y1: p[1], x2: 250, y2: p[1] }, gScaffold);
			g.style.cssText = 'stroke:var(--es-ink-4);stroke-width:1;opacity:0;';
			fade(g, 0.45, 200, 640);
		});
		// región OUTCOME (solo desktop)
		if (dense) {
			var oc = make('g', {}, gScaffold);
			make('rect', { x: 632, y: 430, width: 176, height: 66, rx: 3, 'stroke-dasharray': '4 5' }, oc).style.cssText = 'fill:none;stroke:var(--es-line-strong);stroke-width:1;';
			var ot1 = make('text', { x: 646, y: 452 }, oc); ot1.textContent = 'OUTCOME';
			ot1.style.cssText = 'font-family:var(--es-mono);font-size:10px;letter-spacing:.14em;fill:var(--es-ink-4);';
			var ot2 = make('text', { x: 646, y: 470 }, oc); ot2.textContent = 'Time saved';
			ot2.style.cssText = 'font-family:var(--es-mono);font-size:10px;fill:var(--es-ink-3);';
			var ot3 = make('text', { x: 646, y: 484 }, oc); ot3.textContent = 'Consistency';
			ot3.style.cssText = 'font-family:var(--es-mono);font-size:10px;fill:var(--es-ink-3);';
			fade(oc, 1, 1500, 2050);
		}

		/* --- nodos --- */
		// inputs (entidades): el central activo (verde), los otros neutros
		var inputs = dense ? [[120, 220, false], [120, CY, true], [120, 380, false]] : [[120, 250, false], [120, 350, true]];
		inputs.forEach(function (n, i) {
			var t0 = 80 + i * 60;
			if (n[2]) {
				var g = make('g', {}, gNodes);
				make('circle', { cx: n[0], cy: n[1], r: 11 }, g).style.cssText = 'fill:none;stroke:var(--es-signal-dim);stroke-width:1;';
				make('circle', { cx: n[0], cy: n[1], r: 5 }, g).style.cssText = 'fill:var(--es-signal);';
				g.style.opacity = '0';
				pop(g, 1, t0, t0 + 420);
			} else {
				var c = make('circle', { cx: n[0], cy: n[1], r: 5 }, gNodes);
				c.style.cssText = 'fill:none;stroke:var(--es-ink-3);stroke-width:1.4;opacity:0;';
				pop(c, 0.9, t0, t0 + 420);
			}
		});

		// proceso 1 (MAP)
		var map = make('g', {}, gNodes);
		make('rect', { x: 300, y: 272, width: 56, height: 56, rx: 4 }, map).style.cssText = 'fill:var(--es-paper);stroke:var(--es-ink-3);stroke-width:1.4;';
		make('circle', { cx: 314, cy: CY, r: 3.2 }, map).style.cssText = 'fill:var(--es-signal);';
		map.style.opacity = '0';
		pop(map, 1, 260, 700);

		// proceso 2
		var proc2 = make('rect', { x: 600, y: 272, width: 56, height: 56, rx: 4 }, gNodes);
		proc2.style.cssText = 'fill:var(--es-paper);stroke:var(--es-ink-3);stroke-width:1.4;opacity:0;';
		pop(proc2, 1, 520, 980);

		// resolución (anillo doble verde)
		var res = make('g', {}, gNodes);
		make('circle', { cx: 780, cy: CY, r: 22 }, res).style.cssText = 'fill:none;stroke:var(--es-signal);stroke-width:1.5;';
		make('circle', { cx: 780, cy: CY, r: 13 }, res).style.cssText = 'fill:none;stroke:var(--es-signal-2);stroke-width:1.2;';
		res.style.opacity = '0';
		pop(res, 1, 700, 1160);
		var resCore = make('circle', { cx: 780, cy: CY, r: 5 }, gNodes);
		resCore.style.cssText = 'fill:var(--es-signal);opacity:0;';
		pop(resCore, 1, 1650, 1980); // se rellena al final

		/* --- camino verde (resuelto): dos tramos que "paran" en los bordes --- */
		var segA = [[120, CY], [444, CY]]; // input → MAP → borde izq. del diamante
		var segB = [[496, CY], [758, CY]]; // borde der. del diamante → proc2 → anillo
		var pA = make('polyline', { points: polyPoints(segA) }, gPath);
		pA.style.cssText = 'stroke:var(--es-signal);stroke-width:1.8;opacity:0;';
		draw(pA, polyLength(segA), 1, 700, 1450);
		var pB = make('polyline', { points: polyPoints(segB) }, gPath);
		pB.style.cssText = 'stroke:var(--es-signal);stroke-width:1.8;opacity:0;';
		draw(pB, polyLength(segB), 1, 900, 1600);

		/* --- decisión (diamante naranja) — se enciende y pulsa una vez --- */
		var dec = make('g', {}, gNodes);
		make('polygon', { points: polyPoints(diamondPoints(470, CY, 30)) }, dec).style.cssText = 'fill:none;stroke:var(--es-decision);stroke-width:5;opacity:.26;';
		make('polygon', { points: polyPoints(diamondPoints(470, CY, 22)) }, dec).style.cssText = 'fill:var(--es-paper);stroke:var(--es-decision);stroke-width:1.7;';
		make('circle', { cx: 470, cy: CY, r: 4 }, dec).style.cssText = 'fill:var(--es-decision);';
		dec.style.opacity = '0';
		pop(dec, 1, 1480, 1820, 1600); // pulso en t≈1600

		/* --- labels mono (entran al final) --- */
		function label(x, y, txt, color, anchor) {
			var t = make('text', { x: x, y: y, 'text-anchor': anchor || 'start' }, gLabels);
			t.textContent = txt;
			t.style.cssText = 'font-family:var(--es-mono);font-size:11px;letter-spacing:.12em;fill:' + color + ';';
			return t;
		}
		// todos los labels van a gLabels y comparten un fade conjunto
		label(100, 196, 'INPUTS', 'var(--es-ink-3)');
		label(328, 356, 'MAP', 'var(--es-ink-3)', 'middle');
		label(470, 372, 'DECIDE', 'var(--es-decision)', 'middle');
		label(780, 344, 'RESOLVE', 'var(--es-signal)', 'middle');
		if (dense) { label(830, 600, 'REV 1.0', 'var(--es-ink-4)', 'end'); }
		gLabels.style.opacity = '0';
		fade(gLabels, 1, 1750, 2200);

		var TOTAL = 2300;

		function render(t) {
			for (var i = 0; i < anims.length; i++) {
				var a = anims[i];
				var p = smooth((t - a.t0) / (a.t1 - a.t0));
				if (a.kind === 'fade') {
					a.el.style.opacity = String(a.finalOp * p);
				} else if (a.kind === 'draw') {
					a.el.style.opacity = String(p > 0 ? a.finalOp : 0);
					a.el.setAttribute('stroke-dashoffset', String(a.len * (1 - p)));
				} else if (a.kind === 'pop') {
					a.el.style.opacity = String(a.finalOp * p);
					var s = 0.55 + 0.45 * p;
					// pulso único (overshoot ≤1.14) para el "encendido" de la decisión
					if (a.pulseAt) {
						var pd = (t - a.pulseAt) / 300;
						if (pd > 0 && pd < 1) { s += Math.sin(pd * Math.PI) * 0.14; }
					}
					a.el.style.transform = 'scale(' + s.toFixed(3) + ')';
				}
			}
		}

		if (ctx.isStatic) { render(TOTAL + 200); return; }

		var start = null, raf = 0, done = false, visible = true, started = false;
		function frame(now) {
			raf = 0;
			if (start === null) { start = now; }
			var t = now - start;
			render(t);
			if (t >= TOTAL) { done = true; return; } // ensambla y se detiene (sin loop)
			if (visible && !document.hidden) { raf = window.requestAnimationFrame(frame); }
		}
		function run() {
			if (done || started) { if (!raf && !done && visible && !document.hidden) { raf = window.requestAnimationFrame(frame); } return; }
			started = true;
			raf = window.requestAnimationFrame(frame);
		}

		render(0); // estado inicial (todo oculto)
		if ('IntersectionObserver' in window) {
			new IntersectionObserver(function (entries) {
				visible = entries[0].isIntersecting;
				if (visible) { run(); }
			}, { threshold: 0.08 }).observe(ctx.hero);
		} else {
			run();
		}
		document.addEventListener('visibilitychange', function () {
			if (!document.hidden && started && !done) { if (!raf) { raf = window.requestAnimationFrame(frame); } }
		});
	}

	/* ===================== motor C — network_constellation =====================
	   Red viva de nodos (constelación), adaptada del Home v4. Se ensambla una
	   vez: los nodos aparecen sueltos, las conexiones se dibujan propagándose
	   por BFS desde un nodo raíz, el foco se ilumina en verde con halo + anillo,
	   y todo se asienta. Idle: respiración muy sutil (halo + 2-3 nodos) y un glow
	   tipo radar. Hover (desktop, pointer fino): campo de proximidad amortiguado
	   — brillo, tamaño y un lean ≤3px, sin perseguir el cursor. Mobile: el scroll
	   desliza la atención + parallax leve; capa DETRÁS del texto, nunca un bloque
	   aparte. SVG full-bleed (preserveAspectRatio:none, viewBox en px), rAF que
	   duerme al asentarse. Verde = foco activo/resuelto; sin naranja (no hay
	   decisión en esta pieza). Cero librerías.

	   Nota: NO usa newSvg() ni el posicionamiento de columna derecha de hero.css
	   — construye su propio SVG con clase .es-net-svg (hero.css lo deja a sangre
	   completa detrás del texto). */
	function buildNetworkConstellation(host, ctx) {
		var hero = ctx.hero;
		var reduced = ctx.reduced;
		var hoverCapable = !!(window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches);

		var C = {};
		function parseCol(str) {
			str = (str || '').trim();
			if (str.charAt(0) === '#') {
				var h = str.substring(1);
				var f = h.length === 3 ? h.split('').map(function (c) { return c + c; }).join('') : h;
				return [parseInt(f.substr(0, 2), 16), parseInt(f.substr(2, 2), 16), parseInt(f.substr(4, 2), 16)];
			}
			var m = str.match(/([\d.]+)[,\s]+([\d.]+)[,\s]+([\d.]+)/);
			return m ? [+m[1], +m[2], +m[3]] : [128, 128, 128];
		}
		function recolor() {
			var cs = getComputedStyle(host);
			C = {
				ink4: parseCol(cs.getPropertyValue('--es-ink-4')),
				ink3: parseCol(cs.getPropertyValue('--es-ink-3')),
				ink2: parseCol(cs.getPropertyValue('--es-ink-2')),
				green: parseCol(cs.getPropertyValue('--es-signal')),
				line: parseCol(cs.getPropertyValue('--es-line-strong'))
			};
		}
		recolor();
		function mix(a, b, t) {
			return 'rgb(' + Math.round(a[0] + (b[0] - a[0]) * t) + ',' + Math.round(a[1] + (b[1] - a[1]) * t) + ',' + Math.round(a[2] + (b[2] - a[2]) * t) + ')';
		}

		var seed = 0;
		function rnd() { return ((seed = (seed * 1664525 + 1013904223) >>> 0) / 4294967296); }

		var nodes = [], edges = [], svg = null, halo = null, ring = null, gAll = null;
		var W = 0, H = 0, bandLayout = false, introDone = false, introT0 = 0;
		var INTRO = 2650, raf = 0, mouse = { x: -1e4, y: -1e4, on: false }, drift = { p: 0.5, cur: 0.5 };

		function eln(name, attrs, parent) { return make(name, attrs || {}, parent || svg); }

		function build(settled) {
			host.innerHTML = '';
			var r = host.getBoundingClientRect();
			W = Math.max(300, r.width | 0); H = Math.max(240, r.height | 0);
			bandLayout = window.matchMedia('(max-width: 1023px)').matches;
			seed = 20260704;
			svg = document.createElementNS(NS, 'svg');
			svg.setAttribute('viewBox', '0 0 ' + W + ' ' + H);
			svg.setAttribute('preserveAspectRatio', 'none');
			svg.setAttribute('aria-hidden', 'true');
			svg.setAttribute('class', 'es-net-svg');
			svg.style.cssText = 'position:absolute;inset:0;width:100%;height:100%;display:block;';
			host.appendChild(svg);

			var defs = eln('defs', {});
			var grad = eln('radialGradient', { id: 'esHaloG' }, defs);
			var s0 = eln('stop', { offset: '0%' }, grad); s0.style.stopColor = 'var(--es-signal)'; s0.style.stopOpacity = '.6';
			var s1 = eln('stop', { offset: '100%' }, grad); s1.style.stopColor = 'var(--es-signal)'; s1.style.stopOpacity = '0';
			gAll = eln('g', {});

			// zona de exclusión: no poblar sobre el bloque de texto (legibilidad)
			var ex = null;
			var txt = hero.querySelector('.es-hero__content, [data-hero-text]');
			if (txt) {
				var tr = txt.getBoundingClientRect();
				ex = { x0: tr.left - r.left - 34, y0: tr.top - r.top - 30, x1: tr.right - r.left + 44, y1: tr.bottom - r.top + 34 };
			}

			nodes = []; edges = [];
			var s = bandLayout ? 84 : 116;
			var cols = Math.ceil(W / s), rows = Math.ceil(H / s);
			for (var cxi = 0; cxi < cols; cxi++) for (var cyi = 0; cyi < rows; cyi++) {
				var x = cxi * s + s * 0.5 + (rnd() - 0.5) * s * 0.78;
				var y = cyi * s + s * 0.5 + (rnd() - 0.5) * s * 0.72;
				if (x < 22 || x > W - 22 || y < 22 || y > H - 22) continue;
				// desktop: excluye fuerte sobre el texto; mobile: aclara un poco
				if (ex && x > ex.x0 && x < ex.x1 && y > ex.y0 && y < ex.y1) {
					if (rnd() > (bandLayout ? 0.22 : 0.08)) continue;
				}
				nodes.push({ x: x, y: y, cur: 0, tgt: 0, damp: 0.055 + rnd() * 0.07, ph: rnd(), depth: 99, deg: 0 });
			}

			var seen = {};
			nodes.forEach(function (n, i) {
				var cand = nodes.map(function (m, j) { return { j: j, d: Math.hypot(m.x - n.x, m.y - n.y) }; })
					.filter(function (o) { return o.j !== i && o.d < s * 1.72; })
					.sort(function (a, b) { return a.d - b.d; }).slice(0, 3);
				cand.forEach(function (o, k) {
					var key = Math.min(i, o.j) + '-' + Math.max(i, o.j);
					if (seen[key]) return;
					if (k === 2 && rnd() < 0.55) return;
					seen[key] = 1;
					edges.push({ a: i, b: o.j, len: o.d, depth: 99 });
				});
			});
			var adj = nodes.map(function () { return []; });
			edges.forEach(function (ed, k) { adj[ed.a].push({ n: ed.b, e: k }); adj[ed.b].push({ n: ed.a, e: k }); });
			nodes.forEach(function (n, i) { n.deg = adj[i].length; });

			var rx = bandLayout ? W * 0.55 : W * 0.72, ry = bandLayout ? H * 0.68 : H * 0.42;
			var root = 0, bd = 1e18;
			nodes.forEach(function (n, i) { var d = (n.x - rx) * (n.x - rx) + (n.y - ry) * (n.y - ry); if (d < bd) { bd = d; root = i; } });
			function bfs() {
				nodes.forEach(function (n) { n.depth = 99; });
				var q = [root]; nodes[root].depth = 0;
				while (q.length) {
					var i = q.shift();
					adj[i].forEach(function (o) { if (nodes[o.n].depth > nodes[i].depth + 1) { nodes[o.n].depth = nodes[i].depth + 1; q.push(o.n); } });
				}
			}
			bfs();
			var guard = 0;
			while (guard++ < 40) {
				var orphan = -1;
				for (var oi = 0; oi < nodes.length; oi++) { if (nodes[oi].depth === 99) { orphan = oi; break; } }
				if (orphan < 0) break;
				var bi = -1, bj = -1, best = 1e18;
				nodes.forEach(function (n, i) {
					if (n.depth === 99) return;
					nodes.forEach(function (m, j) {
						if (m.depth !== 99) return;
						var d = (n.x - m.x) * (n.x - m.x) + (n.y - m.y) * (n.y - m.y);
						if (d < best) { best = d; bi = i; bj = j; }
					});
				});
				if (bi < 0) break;
				var len = Math.hypot(nodes[bi].x - nodes[bj].x, nodes[bi].y - nodes[bj].y);
				edges.push({ a: bi, b: bj, len: len, depth: 99 });
				adj[bi].push({ n: bj, e: edges.length - 1 }); adj[bj].push({ n: bi, e: edges.length - 1 });
				bfs();
			}
			edges.forEach(function (ed) { ed.depth = Math.min(nodes[ed.a].depth, nodes[ed.b].depth); });

			var rootN = nodes[root] || { x: W * 0.7, y: H * 0.4 };
			var hr = Math.max(120, Math.min(210, Math.min(W, H) * 0.34));
			halo = eln('circle', { cx: rootN.x, cy: rootN.y, r: hr, fill: 'url(#esHaloG)' }, gAll);
			halo.style.opacity = '0';

			edges.forEach(function (ed) {
				var A = nodes[ed.a], B = nodes[ed.b];
				ed.n = eln('line', { x1: A.x, y1: A.y, x2: B.x, y2: B.y }, gAll);
				ed.focus = ed.depth <= 0;
				ed.baseOp = ed.focus ? 0.75 : 0.42;
				ed.baseW = ed.focus ? 1.4 : 1;
				ed.t0 = 700 + ed.depth * 150 + rnd() * 130;
				ed.n.setAttribute('stroke-dasharray', String(ed.len));
				ed.n.setAttribute('stroke-dashoffset', String(ed.len));
				ed.n.style.stroke = ed.focus ? 'var(--es-signal-dim)' : 'var(--es-line-strong)';
				ed.n.style.strokeWidth = ed.baseW + 'px';
				ed.n.style.opacity = '0';
				ed.connT = ed.t0 + 470;
			});
			var breathePick = 0;
			nodes.forEach(function (n, i) {
				n.isRoot = i === root;
				n.focus = n.depth <= 1;
				n.baseR = n.isRoot ? 3.6 : n.focus ? 2.9 : 2.2;
				n.baseOp = n.isRoot ? 1 : n.focus ? 0.9 : 0.72;
				n.appearT = 90 + rnd() * 880;
				n.connT = 1e9;
				adj[i].forEach(function (o) { n.connT = Math.min(n.connT, edges[o.e].connT); });
				n.dot = eln('circle', { cx: n.x, cy: n.y, r: n.baseR }, gAll);
				n.dot.style.fill = 'var(--es-ink-4)';
				n.dot.style.opacity = '0';
				if (!n.focus && n.depth >= 2 && n.depth < 99 && breathePick < 3 && rnd() < 0.22) { n.breathe = true; breathePick++; }
			});
			ring = eln('circle', { cx: rootN.x, cy: rootN.y, r: 8.5, fill: 'none' }, gAll);
			var circ = 2 * Math.PI * 8.5;
			ring.setAttribute('stroke-dasharray', String(circ));
			ring.setAttribute('stroke-dashoffset', String(circ));
			ring.style.cssText = 'stroke:var(--es-signal);stroke-width:1;opacity:0;';

			if (settled) { settleNow(); } else { introDone = false; }
		}

		function settleNow() {
			introDone = true;
			edges.forEach(function (ed) {
				ed.n.setAttribute('stroke-dasharray', 'none');
				ed.n.setAttribute('stroke-dashoffset', '0');
				ed.n.style.opacity = String(ed.baseOp);
			});
			nodes.forEach(function (n) {
				n.dot.style.opacity = String(n.baseOp);
				n.dot.style.fill = n.focus ? 'var(--es-signal)' : (n.depth < 99 ? 'var(--es-ink-3)' : 'var(--es-ink-4)');
				if (n.breathe && !reduced) { n.dot.style.animation = 'es-net-breathe-node ' + (10 + n.ph * 4).toFixed(1) + 's ease-in-out ' + (-n.ph * 8).toFixed(1) + 's infinite'; }
			});
			halo.style.opacity = '0.09';
			if (!reduced) { halo.style.animation = 'es-net-breathe 9s ease-in-out infinite'; }
			ring.setAttribute('stroke-dashoffset', '0');
			ring.style.opacity = '0.85';
		}

		function introFrame(now) {
			var t = now - introT0;
			nodes.forEach(function (n) {
				var ka = smooth((t - n.appearT) / 480);
				var kc = smooth((t - n.connT) / 420);
				var kf = n.focus ? smooth((t - 1750) / 520) : 0;
				n.dot.style.opacity = String(Math.max(0, Math.min(n.baseOp, ka * 0.55 + kc * (n.baseOp - 0.55) * 0.6 + kf * n.baseOp * 0.4)));
				n.dot.setAttribute('r', String(n.baseR * (0.55 + 0.45 * ka)));
				if (n.focus) { n.dot.style.fill = kf > 0 ? mix(C.ink4, C.green, kf) : 'rgb(' + C.ink4.join(',') + ')'; }
				else if (kc > 0) { n.dot.style.fill = mix(C.ink4, C.ink3, kc); }
			});
			edges.forEach(function (ed) {
				var k = smooth((t - ed.t0) / 520);
				ed.n.setAttribute('stroke-dashoffset', String(ed.len * (1 - k)));
				ed.n.style.opacity = String(ed.baseOp * k);
			});
			var kh = smooth((t - 1900) / 620);
			halo.style.opacity = String(0.09 * kh);
			var kr = smooth((t - 1950) / 480);
			ring.setAttribute('stroke-dashoffset', String((2 * Math.PI * 8.5) * (1 - kr)));
			ring.style.opacity = String(0.85 * kr);
			if (t >= INTRO) { settleNow(); return false; }
			return true;
		}

		function fieldFrame() {
			var energy = 0;
			var R = bandLayout ? 190 : 250;
			var scale = hoverCapable ? 1 : 0.6;
			nodes.forEach(function (n) {
				var t = 0;
				if (mouse.on) { t = smooth(1 - Math.hypot(n.x - mouse.x, n.y - mouse.y) / R) * scale; }
				n.tgt = t;
				var dd = (n.tgt - n.cur) * n.damp * (1 + n.ph * 0.5);
				n.cur += dd; energy += Math.abs(dd);
				if (n.cur > 0.004) {
					var lean = 3 * n.cur;
					var dx = mouse.on ? (mouse.x - n.x) : 0, dy = mouse.on ? (mouse.y - n.y) : 0;
					var dl = Math.hypot(dx, dy) || 1;
					n.vx = n.x + dx / dl * lean; n.vy = n.y + dy / dl * lean;
					n.dot.setAttribute('cx', String(n.vx)); n.dot.setAttribute('cy', String(n.vy));
					n.dot.setAttribute('r', String(n.baseR + 1.7 * n.cur));
					n.dot.style.opacity = String(Math.min(1, n.baseOp + 0.3 * n.cur));
					if (!n.focus) {
						var base = n.depth < 99 ? C.ink3 : C.ink4;
						var col = mix(base, C.ink2, Math.min(1, n.cur * 0.9));
						if (n.cur > 0.72) { col = mix(parseCol(col), C.green, (n.cur - 0.72) / 0.28 * 0.4); }
						n.dot.style.fill = col;
					}
					n.moved = true;
				} else if (n.moved) {
					n.moved = false; n.vx = n.x; n.vy = n.y;
					n.dot.setAttribute('cx', String(n.x)); n.dot.setAttribute('cy', String(n.y));
					n.dot.setAttribute('r', String(n.baseR));
					n.dot.style.opacity = String(n.baseOp);
					if (!n.focus) { n.dot.style.fill = n.depth < 99 ? 'var(--es-ink-3)' : 'var(--es-ink-4)'; }
				}
			});
			edges.forEach(function (ed) {
				var A = nodes[ed.a], B = nodes[ed.b];
				var m = Math.max(A.cur, B.cur);
				if (m > 0.004) {
					ed.n.setAttribute('x1', String(A.vx || A.x)); ed.n.setAttribute('y1', String(A.vy || A.y));
					ed.n.setAttribute('x2', String(B.vx || B.x)); ed.n.setAttribute('y2', String(B.vy || B.y));
					ed.n.style.opacity = String(Math.min(1, ed.baseOp + 0.32 * m));
					ed.n.style.strokeWidth = (ed.baseW + 0.45 * m) + 'px';
					ed.moved = true;
				} else if (ed.moved) {
					ed.moved = false;
					ed.n.setAttribute('x1', String(A.x)); ed.n.setAttribute('y1', String(A.y));
					ed.n.setAttribute('x2', String(B.x)); ed.n.setAttribute('y2', String(B.y));
					ed.n.style.opacity = String(ed.baseOp);
					ed.n.style.strokeWidth = ed.baseW + 'px';
				}
			});
			if (!hoverCapable) {
				var dd2 = (drift.p - drift.cur) * 0.06;
				drift.cur += dd2; energy += Math.abs(dd2) * 4;
				gAll.setAttribute('transform', 'translate(0,' + ((0.5 - drift.cur) * 12).toFixed(2) + ')');
			}
			return energy;
		}

		function frame(now) {
			var alive = false;
			if (!introDone) alive = introFrame(now) || alive;
			var e = introDone ? fieldFrame() : 0;
			if (introDone && e < 0.0015) { raf = 0; return; }
			raf = window.requestAnimationFrame(frame);
		}
		function wake() { if (!raf) { raf = window.requestAnimationFrame(frame); } }
		function startIntro() {
			if (reduced) { settleNow(); return; }
			introT0 = performance.now();
			introDone = false;
			wake();
		}

		// listeners
		if (!reduced && hoverCapable) {
			hero.addEventListener('pointermove', function (e) {
				if (!introDone) return;
				var r = host.getBoundingClientRect();
				mouse.x = e.clientX - r.left; mouse.y = e.clientY - r.top; mouse.on = true;
				wake();
			});
			hero.addEventListener('pointerleave', function () { mouse.on = false; wake(); });
		}
		if (!reduced && !hoverCapable) {
			window.addEventListener('scroll', function () {
				if (!introDone) return;
				var r = host.getBoundingClientRect();
				var vh = window.innerHeight || 1;
				var p = Math.max(0, Math.min(1, (vh - r.top) / (vh + r.height)));
				drift.p = p;
				mouse.x = W * (0.25 + 0.5 * p); mouse.y = H * 0.62; mouse.on = true;
				wake();
			}, { passive: true });
		}
		var rsT = 0;
		window.addEventListener('resize', function () {
			window.clearTimeout(rsT);
			rsT = window.setTimeout(function () {
				var r = host.getBoundingClientRect();
				var modeNow = window.matchMedia('(max-width: 1023px)').matches;
				if (Math.abs(r.width - W) > 70 || Math.abs(r.height - H) > 70 || modeNow !== bandLayout) { build(true); }
			}, 260);
		});
		// re-tematizar si cambia el modo claro/oscuro (WP Dark Mode)
		if ('MutationObserver' in window) {
			new MutationObserver(recolor).observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme', 'class'] });
		}

		// boot
		build(ctx.isStatic);
		if (!ctx.isStatic) {
			if (hoverCapable && !bandLayout) {
				startIntro();
			} else if ('IntersectionObserver' in window) {
				var io = new IntersectionObserver(function (ents) {
					ents.forEach(function (en) { if (en.isIntersecting) { startIntro(); io.disconnect(); } });
				}, { threshold: 0.25 });
				io.observe(host);
			} else {
				startIntro();
			}
		}
	}

	/* ===================== registro de motores =====================
	   Arquitectura extensible: cada motor se registra por nombre. Agregar
	   una variante nueva = registrar un builder (host, ctx) — desde este
	   archivo o desde uno propio encolado DESPUÉS (con dependencia de
	   'es-hero-system-map'), sin reescribir el tema:

	     window.EstavilloHero
	       .register('mi_variante', function (host, ctx) { ... })
	       .alias('nombre_viejo', 'mi_variante');

	   Y sumar la clave al filtro PHP `es_hero_variants` (ver
	   inc/theme-options.php) para que aparezca en el Customizer.

	   Contrato del builder:
	     host  = elemento [data-es-hero-map]
	     ctx   = { hero, reduced, isMobile(), variant(), isStatic }
	     Debe respetar ctx.isStatic (frame final sin animar) y dormir el
	     rAF fuera de viewport / con pestaña oculta.

	   'static_fallback' no es un motor: es un modificador que dibuja el
	   motor por defecto en su frame final estático. */

	var Hero = window.EstavilloHero = window.EstavilloHero || (function () {
		var engines = {}; // nombre -> builder(host, ctx)
		var aliases = {}; // alias -> nombre canónico
		var api = {
			DEFAULT: 'network_constellation',
			register: function (name, builder) {
				if (name && typeof builder === 'function') { engines[name] = builder; }
				return api;
			},
			alias: function (from, to) { aliases[from] = to; return api; },
			resolve: function (name) {
				var seen = {};
				while (aliases[name] && !seen[name]) { seen[name] = 1; name = aliases[name]; }
				return name;
			},
			has: function (name) { return !!engines[api.resolve(name)]; },
			get: function (name) { return engines[api.resolve(name)] || null; },
			list: function () { return Object.keys(engines); }
		};
		return api;
	})();

	// motores incorporados + aliases (compatibilidad con valores guardados)
	Hero.register('network_constellation', buildNetworkConstellation)
		.register('system_map_nodes', buildNodesMap)
		.register('blueprint_flow', buildBlueprintFlow)
		.alias('network_constellation_subtle', 'network_constellation')
		.alias('system_map', 'system_map_nodes')
		.alias('system_map_subtle', 'system_map_nodes');

	/* ===================== dispatcher ===================== */

	function buildHost(host) {
		var ctx = resolveContext(host);
		var raw = ctx.variant();
		ctx.isStatic = ctx.reduced || raw === 'static_fallback';
		if (raw === 'static_fallback') { ctx.hero.classList.add('es-hero--static'); }

		// static_fallback → motor por defecto en modo estático.
		// variante desconocida → cae al default (nunca rompe).
		var name = raw === 'static_fallback' ? Hero.DEFAULT : raw;
		var engine = Hero.get(name) || Hero.get(Hero.DEFAULT);
		engine(host, ctx);
	}

	function init() {
		var hosts = document.querySelectorAll('[data-es-hero-map]');
		for (var i = 0; i < hosts.length; i++) {
			if (hosts[i].__esHeroBuilt) { continue; }
			hosts[i].__esHeroBuilt = true;
			try {
				buildHost(hosts[i]);
			} catch (err) {
				if (window.console && console.error) { console.error('es-hero engine', err); }
			}
		}
	}
	Hero.init = init; // por si se inserta un hero dinámicamente

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
