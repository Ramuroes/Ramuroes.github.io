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
 *  - system_map_nodes  (DEFAULT) — campo ambiente de nodos y trazas que se
 *    ensambla en la carga y responde con iluminación suave al hover/touch.
 *    Aliases: "system_map" (desktop), "system_map_subtle" (mobile).
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
			DEFAULT: 'system_map_nodes',
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
	Hero.register('system_map_nodes', buildNodesMap)
		.register('blueprint_flow', buildBlueprintFlow)
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
